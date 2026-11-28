#!/usr/bin/env python3
"""Fetch live pages that are linked from the mirror but were not crawled
(sitemap only covered part of the item URLs).

Usage:  python3 scripts/fetch_missing.py [--dry-run]
"""

from __future__ import annotations

import importlib.util
import re
import sys
import time
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
spec = importlib.util.spec_from_file_location("lm", ROOT / "scripts" / "live_mirror.py")
lm = importlib.util.module_from_spec(spec)
spec.loader.exec_module(lm)

SKIP_PREFIX = (
    "/addcart/", "/uploaded/", "/images/", "/temp/", "/css/", "/js/",
    "/fonts/", "/upload/", "/admin/", "/dashboard/", "/api/",
)
ASSET_EXT = (
    ".css", ".js", ".png", ".jpg", ".jpeg", ".gif", ".svg", ".webp",
    ".ico", ".woff", ".woff2", ".ttf", ".otf", ".eot", ".map", ".pdf",
    ".zip", ".rar", ".mp4", ".webm", ".txt", ".xml",
)


def is_item_link(h: str) -> bool:
    if not h.startswith("/") or h.startswith("//"):
        return False
    if h.startswith(SKIP_PREFIX):
        return False
    if h.endswith("/"):
        return False
    last = h.split("/")[-1]
    if "." in last and last.lower().endswith(ASSET_EXT):
        return False
    return True


def main() -> int:
    dry = "--dry-run" in sys.argv
    sitemap = set(lm.sitemap_urls())
    links: set[str] = set()
    for f in sorted(lm.PAGES.glob("*.html")):
        html = f.read_text(encoding="utf-8", errors="replace")
        for h in re.findall(r"href=['\"](/[^'\"#?]+)['\"]", html):
            h = h.split("?")[0]
            if is_item_link(h):
                links.add(h)

    missing = sorted(l for l in links if l not in sitemap)
    # keep only links whose target category page exists (link came from a real page)
    print(f"item links={len(links)} sitemap={len(sitemap)} missing={len(missing)}")
    if dry:
        for m in missing:
            print("  ", m)
        return 0

    ok = fail = 0
    for i, path in enumerate(missing, 1):
        dest = lm.page_path(path)
        if dest.exists() and dest.stat().st_size > 2000:
            continue
        data = lm.fetch(lm.BASE + path)
        if data and len(data) > 1000:
            dest.parent.mkdir(parents=True, exist_ok=True)
            dest.write_bytes(data)
            ok += 1
            print(f"[{i}/{len(missing)}] {path} -> {dest.name} ({len(data)} B)")
        else:
            fail += 1
            print(f"[{i}/{len(missing)}] FAIL {path}")
        time.sleep(lm.DELAY)
    print(f"done: fetched={ok} failed={fail}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
