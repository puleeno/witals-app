#!/usr/bin/env python3
"""
Mirror the live xanhvina.com site into ./live-mirror/

  live-mirror/pages/...   rendered HTML of every sitemap URL (used to rebuild
                          templates + extract DB content)
  live-mirror/assets/...  every static asset referenced by those pages
                          (css / js / images / fonts / uploaded files)

Usage:
    python3 scripts/live_mirror.py            # pages + assets
    python3 scripts/live_mirror.py --assets   # assets only (pages already there)
"""

from __future__ import annotations

import argparse
import mimetypes
import re
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from collections import deque
from pathlib import Path

BASE = "https://xanhvina.com"
ROOT = Path(__file__).resolve().parent.parent
MIRROR = ROOT / "live-mirror"
PAGES = MIRROR / "pages"
ASSETS = MIRROR / "assets"
SITEMAP = MIRROR / "sitemap.xml"

UA = (
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:109.0) "
    "Gecko/20100101 Firefox/115.0"
)

ASSET_EXT = {
    ".css", ".js", ".png", ".jpg", ".jpeg", ".gif", ".svg", ".webp",
    ".ico", ".woff", ".woff2", ".ttf", ".otf", ".eot", ".map", ".mp4",
    ".webm", ".txt", ".xml", ".pdf",
}
# extensions that identify a *page* (skipped when collecting assets)
PAGE_EXT = {".html", ".htm", ".php"}

DELAY = 0.12
TIMEOUT = 30


def fetch(url: str) -> bytes | None:
    req = urllib.request.Request(url, headers={"User-Agent": UA})
    try:
        with urllib.request.urlopen(req, timeout=TIMEOUT) as resp:
            return resp.read()
    except (urllib.error.HTTPError, urllib.error.URLError, TimeoutError) as exc:
        print(f"  ! {url} -> {exc}", file=sys.stderr)
        return None


def ensure_sitemap() -> str:
    SITEMAP.parent.mkdir(parents=True, exist_ok=True)
    if not SITEMAP.exists() or SITEMAP.stat().st_size < 1000:
        data = fetch(f"{BASE}/sitemap.xml")
        if not data:
            raise SystemExit("cannot download sitemap.xml")
        SITEMAP.write_bytes(data)
    return SITEMAP.read_text(encoding="utf-8", errors="replace")


def sitemap_urls() -> list[str]:
    xml = ensure_sitemap()
    urls = re.findall(r"<loc>\s*(.*?)\s*</loc>", xml)
    out = []
    for u in urls:
        u = u.strip()
        if u.startswith(BASE):
            out.append(u[len(BASE):] or "/")
        elif u.startswith("/"):
            out.append(u)
    # de-dup, keep order
    seen, ordered = set(), []
    for p in out:
        if p not in seen:
            seen.add(p)
            ordered.append(p)
    return ordered


def page_path(url_path: str) -> Path:
    """'/cay-gia/foo' -> pages/cay-gia/foo.html ; '/' -> pages/index.html"""
    p = urllib.parse.unquote(url_path).split("?")[0]
    if p in ("", "/"):
        return PAGES / "index.html"
    p = p.strip("/")
    safe = re.sub(r"[^0-9A-Za-zÀ-ỹ._-]", "_", p)
    return PAGES / (safe + ".html")


def asset_path(url_path: str) -> Path:
    p = urllib.parse.unquote(urllib.parse.urlparse(url_path).path)
    p = p.lstrip("/")
    safe = re.sub(r"[^0-9A-Za-zÀ-ỹ._/-]", "_", p)
    return ASSETS / safe


def local_asset_refs(html: str) -> set[str]:
    refs: set[str] = set()
    for attr in ("href", "src", "data-src", "data-lazy", "poster"):
        for m in re.findall(rf'{attr}="(/[^"#?]+)"', html):
            refs.add(m)
        for m in re.findall(rf"{attr}='(/[^'#?]+)'", html):
            refs.add(m)
    # url(...) inside inline <style>
    for m in re.findall(r"url\(\s*['\"]?(/[^)'\" ]+)", html):
        refs.add(m)
    return refs


def is_asset(path: str) -> bool:
    ext = Path(urllib.parse.urlparse(path).path).suffix.lower()
    if ext in PAGE_EXT:
        return False
    # /css/... /js/... /images/... /uploaded/... /lib/... always assets
    if ext in ASSET_EXT or path.startswith(("/css/", "/js/", "/images/", "/uploaded/", "/lib/", "/midnight/", "/fonts/")):
        return True
    return False


def crawl_pages(paths: list[str]) -> tuple[int, set[str]]:
    PAGES.mkdir(parents=True, exist_ok=True)
    assets: set[str] = set()
    done = 0
    total = len(paths)
    queue = deque(paths)
    while queue:
        path = queue.popleft()
        out = page_path(path)
        if out.exists() and out.stat().st_size > 200:
            html = out.read_text(encoding="utf-8", errors="replace")
            assets |= {a for a in local_asset_refs(html) if is_asset(a)}
            done += 1
            continue
        data = fetch(BASE + urllib.parse.quote(path, safe="/"))
        if data is None:
            continue
        html = data.decode("utf-8", errors="replace")
        out.parent.mkdir(parents=True, exist_ok=True)
        out.write_text(html, encoding="utf-8")
        assets |= {a for a in local_asset_refs(html) if is_asset(a)}
        done += 1
        if done % 25 == 0 or done == total:
            print(f"pages {done}/{total}  assets queued {len(assets)}")
        time.sleep(DELAY)
    return done, assets


def download_assets(paths: set[str]) -> int:
    # also seed the asset list with the core files we know about
    paths |= {
        "/css/style.css", "/css/grid.css", "/css/font.css",
        "/css/widget-call-left.css", "/js/script.js", "/js/autocomplete.js",
        "/css/fontawesome-5.12.1/css/all.min.css",
    }
    # /temp/ holds on-the-fly resized copies the app regenerates itself, and
    # it is by far the biggest bucket - fetch /uploaded (the real files) first
    # and only fall back to /temp/ leftovers at the very end.
    def sort_key(p: str) -> tuple:
        if p.startswith("/uploaded/"):
            return (0, p)
        if p.startswith("/temp/"):
            return (2, p)
        return (1, p)

    todo = []
    for p in sorted(paths, key=sort_key):
        dest = asset_path(p)
        if dest.exists() and dest.stat().st_size > 0:
            continue
        todo.append(p)
    print(f"assets to download: {len(todo)}")
    done = 0
    for p in todo:
        dest = asset_path(p)
        data = fetch(BASE + urllib.parse.quote(p, safe="/"))
        if data is None:
            continue
        dest.parent.mkdir(parents=True, exist_ok=True)
        dest.write_bytes(data)
        done += 1
        if done % 40 == 0:
            print(f"assets {done}/{len(todo)}")
        time.sleep(DELAY / 2)
    return done


def discover_more_assets() -> set[str]:
    """Second pass: css files reference fonts/images via url(...)."""
    found: set[str] = set()
    if not ASSETS.exists():
        return found
    for css in ASSETS.rglob("*.css"):
        text = css.read_text(encoding="utf-8", errors="replace")
        for m in re.findall(r"url\(\s*['\"]?([^)'\" ]+)", text):
            m = m.strip()
            if m.startswith("data:") or m.startswith("http"):
                continue
            # resolve relative to the css file
            rel = urllib.parse.urljoin("/" + str(css.relative_to(ASSETS)), m)
            if is_asset(rel) or Path(rel).suffix.lower() in ASSET_EXT:
                found.add(urllib.parse.urlparse(rel).path)
    return found


def main() -> None:
    ap = argparse.ArgumentParser()
    ap.add_argument("--assets", action="store_true", help="assets only")
    args = ap.parse_args()

    MIRROR.mkdir(parents=True, exist_ok=True)
    all_assets: set[str] = set()

    if not args.assets:
        paths = sitemap_urls()
        print(f"sitemap urls: {len(paths)}")
        _, all_assets = crawl_pages(paths)

    # asset discovery from css url(...) too
    all_assets |= discover_more_assets()
    download_assets(all_assets)
    all_assets |= discover_more_assets()
    download_assets(all_assets)

    n_pages = len(list(PAGES.rglob("*.html"))) if PAGES.exists() else 0
    n_assets = len([p for p in ASSETS.rglob("*") if p.is_file()]) if ASSETS.exists() else 0
    print(f"DONE: {n_pages} pages, {n_assets} assets in {MIRROR}")


if __name__ == "__main__":
    main()
