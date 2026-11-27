#!/usr/bin/env python3
"""Extract the live xanhvina.com content from ./live-mirror/pages into SQL.

Output: ./live-mirror/content.sql  (re-runnable: TRUNCATE + INSERT)

What is extracted
-----------------
  category   65+ categories (nav tree, footer columns, parent/child, data_type,
             content of policy/info pages, intro of product categories)
  product    every product page (JSON-LD + price cards + description)
  news       every news page (title / intro / body / image)
  service    every service (project) page
  logo       homepage sliders + site logo
  static     footer company block + copyright
  settingshow  hotline / social / e-mail settings JSON
  sys_image  product image galleries
  search_content  full-text index (name/intro/content + unsign columns)

Usage:
    python3 scripts/extract_live_content.py            # write content.sql
    python3 scripts/extract_live_content.py --report   # also print a summary
"""

from __future__ import annotations

import html as htmllib
import importlib.util
import json
import re
import sys
import time
import unicodedata
from collections import Counter, defaultdict
from html.parser import HTMLParser
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
spec = importlib.util.spec_from_file_location("lm", ROOT / "scripts" / "live_mirror.py")
lm = importlib.util.module_from_spec(spec)
spec.loader.exec_module(lm)

PAGES = lm.PAGES
OUT = ROOT / "live-mirror" / "content.sql"

VOID = {
    "area", "base", "br", "col", "embed", "hr", "img", "input", "link",
    "meta", "param", "source", "track", "wbr",
}

NAV_TOP = [
    "/flashsale/", "/cay-gia/", "/co-la-reu-gia/", "/hoa-gia/",
    "/trai-cay-gia/", "/chau-cay-canh/", "/thi-cong-tieu-canh/", "/lien-he/",
]
NEWS_ROOTS = {
    "/tin-tuc/", "/goc-tu-van/", "/tin-san-pham/", "/goc-trang-tri/", "/meo-hay/",
}
SERVICE_CATS = {
    "/tuong-cay-gia/", "/buc-cay-gia/", "/tieu-canh-cay-gia/",
    "/thi-cong-tieu-canh/", "/tuong-hoa-gia/",
}
CONTACT_CATS = {"/lien-he/", "/gioi-thieu/"}
# footer columns: parent category -> footercol number
FOOTER_COLS = {
    "/san-pham/": 15,
    "/thi-cong-tieu-canh/": 80,
    "/dieu-khoan-chinh-sach/": 81,
}
INFO_ROOTS = {"/dieu-khoan-chinh-sach/"} | set(FOOTER_COLS) - FOOTER_COLS.keys()

NAME_FALLBACK = {
    "/buc-cay-gia/": "Bồn Cây Giả, Bục Cây Giả",
    "/chinh-sach-dai-ly-amp-ctv-xanhvina/": "Chính sách Đại lý & CTV",
    "/dieu-khoan-chinh-sach/": "Điều khoản chính sách",
    "/gioi-thieu/": "Giới thiệu",
    "/tin-tuc/": "Tin tức",
    "/tuong-hoa-gia/": "Tường Hoa Giả",
    "/nguyen-thu/": "Nguyen Thu",
}


# --------------------------------------------------------------------------
# small helpers
# --------------------------------------------------------------------------
def esc(value) -> str:
    s = "" if value is None else str(value)
    for a, b in (
        ("\\", "\\\\"), ("\x00", "\\0"), ("\n", "\\n"), ("\r", "\\r"),
        ("\x1a", "\\Z"), ("'", "\\'"),
    ):
        s = s.replace(a, b)
    return "'" + s + "'"


def strip_tags(html: str) -> str:
    html = re.sub(r"(?is)<(script|style).*?</\1>", " ", html or "")
    html = re.sub(r"<[^>]+>", " ", html)
    return re.sub(r"\s+", " ", htmllib.unescape(html)).strip()


def squeeze(html: str) -> str:
    return re.sub(r"[ \t]+", " ", html or "").strip()


def unsign(text: str) -> str:
    text = (text or "").replace("đ", "d").replace("Đ", "D")
    text = unicodedata.normalize("NFD", text)
    text = "".join(c for c in text if not unicodedata.combining(c))
    return unicodedata.normalize("NFC", text).lower()


class Grab(HTMLParser):
    """Return the inner HTML of the nth element carrying a given class."""

    def __init__(self, cls: str, nth: int = 0):
        super().__init__(convert_charrefs=True)
        self.cls = cls
        self.nth = nth
        self.depth = 0
        self.captured = False
        self.done = False
        self.count = 0
        self.out: list[str] = []

    def handle_starttag(self, tag, attrs):
        if self.done:
            return
        classes = (dict(attrs).get("class") or "").split()
        if not self.captured:
            if self.cls in classes:
                self.count += 1
                if self.count == self.nth + 1:
                    self.captured = True
                    self.depth = 1
                    self.out = [self.get_starttag_text() or f"<{tag}>"]
            return
        self.out.append(self.get_starttag_text() or f"<{tag}>")
        if tag not in VOID:
            self.depth += 1

    def handle_startendtag(self, tag, attrs):
        if self.captured and not self.done:
            self.out.append(self.get_starttag_text() or f"<{tag}/>")

    def handle_endtag(self, tag):
        if self.done or not self.captured:
            return
        self.out.append(f"</{tag}>")
        if tag not in VOID:
            self.depth -= 1
            if self.depth <= 0:
                self.done = True
                self.captured = False

    def handle_data(self, data):
        if self.captured and not self.done:
            self.out.append(data)

    def result(self) -> str:
        return "".join(self.out)


def grab(html: str, cls: str, nth: int = 0) -> str:
    g = Grab(cls, nth)
    g.feed(html or "")
    g.close()
    return g.result()


def element_open(html: str, start: int) -> tuple[int, int] | None:
    """If html[start:] begins with <div ...>, return (inner_start, inner_end)."""
    m = re.match(r"<div\b[^>]*>", html[start:])
    if not m:
        return None
    inner_start = start + m.end()
    g = Grab("__none__")  # depth-only scan
    g.feed(html[start:])
    g.close()
    # walk manually: reuse Grab logic by scanning tags
    depth = 0
    i = start
    n = len(html)
    while i < n:
        j = html.find("<", i)
        if j < 0:
            return None
        if html.startswith("<!--", j):
            k = html.find("-->", j)
            i = (k + 3) if k >= 0 else n
            continue
        if html.startswith("<script", j) or html.startswith("<style", j):
            close = html.find("</script>", j) if html[j + 1] == "s" else html.find("</style>", j)
            i = (close + 9) if close >= 0 else n
            continue
        mt = re.match(r"</?([a-zA-Z][\w-]*)", html[j:])
        if not mt:
            i = j + 1
            continue
        tag = mt.group(1).lower()
        k = html.find(">", j)
        if k < 0:
            return None
        closing = html[j + 1] == "/"
        self_close = html[k - 1] == "/"
        if tag == "div":
            if closing:
                depth -= 1
                if depth == 0:
                    return inner_start, j
            elif not self_close:
                depth += 1
        i = k + 1
    return None


def meta_tag(html: str, name: str) -> str:
    m = re.search(
        rf'<meta[^>]*name=["\']{re.escape(name)}["\'][^>]*content=["\']([^"\']*)',
        html or "", re.I,
    ) or re.search(
        rf'<meta[^>]*content=["\']([^"\']*)["\'][^>]*name=["\']{re.escape(name)}',
        html or "", re.I,
    )
    return htmllib.unescape(m.group(1)).strip() if m else ""


def page_title(html: str) -> str:
    m = re.search(r"<title[^>]*>(.*?)</title>", html or "", re.S | re.I)
    return re.sub(r"\s+", " ", htmllib.unescape(m.group(1))).strip() if m else ""


def first_h1(html: str) -> str:
    m = re.search(r"<h1[^>]*>(.*?)</h1>", html or "", re.S | re.I)
    return strip_tags(m.group(1)) if m else ""


def crumb_links(html: str) -> list[tuple[str, str]]:
    i = (html or "").find('class="crumb"')
    if i < 0:
        return []
    seg = html[i:i + 1500]
    end = seg.find("</div>")
    if end > 0:
        seg = seg[:end]
    out = []
    for m in re.finditer(r"<a\s+href=['\"]([^'\"]+)['\"][^>]*>(.*?)</a>", seg, re.S):
        out.append((m.group(1), strip_tags(m.group(2))))
    return out


def local_paths() -> set[str]:
    xml = (ROOT / "live-mirror" / "sitemap.xml").read_text(encoding="utf-8", errors="replace")
    paths = {re.sub(r"^https?://[^/]+", "", u.strip()) for u in re.findall(r"<loc>\s*(.*?)\s*</loc>", xml)}
    for f in PAGES.glob("*.html"):
        pass
    # every internal link found in the mirrored pages
    for f in sorted(PAGES.glob("*.html")):
        html = f.read_text(encoding="utf-8", errors="replace")
        for h in re.findall(r"href=['\"](/[^'\"#?]+)['\"]", html):
            if h.startswith(("/addcart/", "/uploaded/", "/images/", "/temp/", "/css/", "/js/", "/lib/")):
                continue
            if h in ("//zalo.me/0898596933",) or h.startswith("//"):
                continue
            paths.add(h.split("?")[0])
    return paths


def is_page(path: str) -> bool:
    f = lm.page_path(path)
    if not f.exists() or f.stat().st_size < 800:
        return False
    head = f.read_text(encoding="utf-8", errors="replace")[:4000]
    t = page_title(head)
    if t.startswith("404") or "404 Not Found" in t or "Page not found" in t:
        return False
    return True


# --------------------------------------------------------------------------
# 1. collect pages
# --------------------------------------------------------------------------
def collect():
    paths = sorted(p for p in local_paths() if p and is_page(p))
    cats = sorted(p for p in paths if p.endswith("/"))
    items = sorted(p for p in paths if not p.endswith("/"))
    return cats, items


# --------------------------------------------------------------------------
# 2. labels (anchor text) + nav / footer structure
# --------------------------------------------------------------------------
def collect_labels() -> dict[str, Counter]:
    labels: dict[str, Counter] = defaultdict(Counter)
    for f in sorted(PAGES.glob("*.html")):
        html = f.read_text(encoding="utf-8", errors="replace")
        for m in re.finditer(r"<a\s+href=['\"](/[^'\"#?]*)['\"][^>]*>(.*?)</a>", html, re.S):
            href = m.group(1)
            txt = strip_tags(m.group(2))
            if not txt or len(txt) > 90:
                continue
            labels[href][txt] += 1
    return labels


def parse_nav() -> list[dict]:
    html = (PAGES / "index.html").read_text(encoding="utf-8", errors="replace")
    i = html.find('<ul class="nav')
    j = html.find("</header>", i)
    seg = html[i:j] if i >= 0 and j > i else ""

    class Nav(HTMLParser):
        def __init__(self):
            super().__init__(convert_charrefs=True)
            self.ul = 0
            self.items: list[dict] = []
            self.cur = None

        def handle_starttag(self, tag, attrs):
            if tag == "ul":
                self.ul += 1
            elif tag == "a" and self.cur is None:
                href = dict(attrs).get("href", "")
                if href.startswith("/"):
                    self.cur = {"href": href, "text": "", "level": self.ul}

        def handle_endtag(self, tag):
            if tag == "ul":
                self.ul -= 1
            elif tag == "a" and self.cur is not None:
                self.cur["text"] = re.sub(r"\s+", " ", self.cur["text"]).strip()
                if self.cur["text"]:
                    self.items.append(self.cur)
                self.cur = None

        def handle_data(self, data):
            if self.cur is not None:
                self.cur["text"] += data

    nav = Nav()
    nav.feed(seg)
    nav.close()
    return nav.items


def parse_footer_cols(html: str) -> dict[int, list[str]]:
    cols: dict[int, list[str]] = {}
    for m in re.finditer(r'<ul class="menuboxfooter" id="boxcol(\d+)">(.*?)</ul>', html or "", re.S):
        col = int(m.group(1))
        cols[col] = re.findall(r'href="(/[^"]+)"', m.group(2))
    return cols


# --------------------------------------------------------------------------
# 3. product prices from every product card on the site
# --------------------------------------------------------------------------
def scan_cards(html: str) -> list[dict]:
    out = []
    for m in re.finditer(r'class="producthome-item', html or ""):
        seg = html[m.start():m.start() + 3000]
        hm = re.search(r'href="(/[^"]+)"', seg)
        if not hm:
            continue
        old = re.search(r'producthot__price-old"[^>]*>(.*?)</span>', seg, re.S)
        cur = re.search(r'producthot__price-current"[^>]*>(.*?)</span>', seg, re.S)
        out.append({
            "href": hm.group(1),
            "old": strip_tags(old.group(1)) if old else "",
            "cur": strip_tags(cur.group(1)) if cur else "",
        })
    return out


def price_number(text: str) -> int:
    digits = re.sub(r"[^\d]", "", text or "")
    return int(digits) if digits else 0


# --------------------------------------------------------------------------
# 4. item extraction
# --------------------------------------------------------------------------
def jsonld(html: str) -> list[dict]:
    out = []
    for m in re.finditer(r'<script type="application/ld\+json">(.*?)</script>', html or "", re.S):
        try:
            data = json.loads(m.group(1))
        except Exception:
            continue
        if isinstance(data, dict):
            out.append(data)
        elif isinstance(data, list):
            out.extend(x for x in data if isinstance(x, dict))
    return out


def jsonld_by_type(blocks: list[dict], tipe: str) -> dict:
    for b in blocks:
        if b.get("@type") == tipe:
            return b
    return {}


def image_list(value) -> list[str]:
    if not value:
        return []
    if isinstance(value, str):
        value = [value]
    out = []
    for v in value:
        if isinstance(v, dict):
            v = v.get("url") or v.get("@id") or ""
        if not isinstance(v, str):
            continue
        v = v.replace("https://xanhvina.com", "").replace("http://xanhvina.com", "")
        if v.startswith("//"):
            v = v[1:]
        if v.startswith("/"):
            out.append(v)
    return out


def page_images(html: str, limit: int = 12) -> list[str]:
    imgs = []
    for m in re.finditer(r'<img[^>]+src=["\']([^"\']+)["\']', html or ""):
        src = m.group(1).replace("https://xanhvina.com", "").replace("http://xanhvina.com", "")
        if src.startswith("//"):
            src = src[1:]
        if not src.startswith("/"):
            continue
        if src.startswith(("/css/", "/js/", "/images/", "/upload/")):
            continue
        if src not in imgs:
            imgs.append(src)
        if len(imgs) >= limit:
            break
    return imgs


def published_ts(html: str, blocks: list[dict]) -> int:
    for b in blocks:
        for key in ("datePublished", "dateCreated", "dateModified"):
            v = b.get(key)
            if isinstance(v, str) and v[:4].isdigit():
                try:
                    return int(time.mktime(time.strptime(v[:19], "%Y-%m-%dT%H:%M:%S")))
                except Exception:
                    pass
    m = re.search(r'"datePublished"\s*:\s*"([^"]+)"', html or "")
    if m and m.group(1)[:4].isdigit():
        try:
            return int(time.mktime(time.strptime(m.group(1)[:19], "%Y-%m-%dT%H:%M:%S")))
        except Exception:
            pass
    return 1719792000  # 2024-07-01, keeps ordering stable


def variants(html: str) -> str:
    items = re.findall(r'<div[^>]*class="[^"]*product-ver-item[^"]*"[^>]*>(.*?)</div>', html or "", re.S)
    raw = re.findall(r'<div[^>]*class="[^"]*product-ver-item[^"]*"[^>]*>', html or "")
    out = []
    for tag, text in zip(raw, items):
        name = re.search(r'data-item="([^"]*)"', tag)
        price = re.search(r'data-price="([^"]*)"', tag)
        if not name:
            continue
        out.append({
            "tenphienban": htmllib.unescape(name.group(1)),
            "giaphienban": price_number(price.group(1)) if price else 0,
            "motaphienban": strip_tags(text),
        })
    return json.dumps(out, ensure_ascii=False) if out else ""


def cut_trailing(html: str, markers: list[str]) -> str:
    pos = len(html)
    for mk in markers:
        i = html.find(mk)
        if 0 <= i < pos:
            pos = i
    return html[:pos]


# --------------------------------------------------------------------------
# 5. SQL emission
# --------------------------------------------------------------------------
class Sql:
    def __init__(self):
        self.buf: list[str] = [
            "-- xanhvina.com live content (extracted from live-mirror/pages)",
            "SET NAMES utf8mb4;",
            "SET FOREIGN_KEY_CHECKS=0;",
            "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';",
            "",
        ]

    def truncate(self, *tables: str):
        for t in tables:
            self.buf.append(f"TRUNCATE TABLE `{t}`;")
        self.buf.append("")

    def insert(self, table: str, cols: list[str], rows: list[list], chunk: int = 150):
        if not rows:
            return
        head = f"INSERT INTO `{table}` ({', '.join('`%s`' % c for c in cols)}) VALUES"
        for i in range(0, len(rows), chunk):
            part = rows[i:i + chunk]
            values = ",\n".join(
                "(" + ", ".join(esc(v) for v in row) + ")" for row in part
            )
            self.buf.append(f"{head}\n{values};")
        self.buf.append("")

    def write(self, path: Path = OUT):
        path.parent.mkdir(parents=True, exist_ok=True)
        path.write_text("\n".join(self.buf) + "\n", encoding="utf-8")
        return path


# --------------------------------------------------------------------------
def main() -> int:
    report = "--report" in sys.argv
    cats, items = collect()
    print(f"categories={len(cats)} items={len(items)}")

    pages = {p: lm.page_path(p).read_text(encoding="utf-8", errors="replace") for p in cats}
    pages.update(
        {p: lm.page_path(p).read_text(encoding="utf-8", errors="replace") for p in items}
    )

    labels = collect_labels()
    nav = parse_nav()
    nav_top = [(n["href"], n["text"]) for n in nav if n["level"] == 1]
    nav_children: dict[str, list[str]] = defaultdict(list)
    stack: list[str] = []
    for n in nav:
        if n["level"] == 1:
            stack = [n["href"]]
        elif n["level"] == 2 and stack:
            nav_children[stack[0]].append(n["href"])
        elif n["level"] >= 3 and len(stack) >= 2:
            nav_children[stack[1]].append(n["href"])

    footer_cols = parse_footer_cols(pages.get("/", ""))

    # ---- labels for categories -----------------------------------------
    def label_for(path: str) -> str:
        if path in NAME_FALLBACK:
            # still prefer an explicit menu label when it exists
            pass
        for source in (nav_top, [(h, t) for c in footer_cols.values() for h, c2 in [(0, 0)] for t in []]):
            pass
        # 1. menu labels (nav + footer + menuleft)
        menu_texts = Counter()
        for href, txt in nav_top:
            if href == path:
                menu_texts[txt] += 100
        for col in footer_cols.values():
            for href in col:
                if href == path:
                    menu_texts[labels.get(href, Counter()).most_common(1)[0][0]] += 100
        # 2. anchor labels over the whole site
        for txt, n in labels.get(path, Counter()).items():
            menu_texts[txt] += n
        if menu_texts:
            best = menu_texts.most_common(1)[0][0]
            if best and not best.startswith("/"):
                return best
        # 3. h1 / title
        html = pages.get(path, "")
        h1 = first_h1(html)
        if h1 and len(h1) <= 90:
            return h1
        t = page_title(html)
        t = re.split(r"\s*[|\-–]\s*", t)[0].strip()
        if t:
            return t
        return NAME_FALLBACK.get(path, path.strip("/").split("/")[-1])

    # ---- parent tree -----------------------------------------------------
    parents: dict[str, str] = {}
    # (a) crumbs
    for path in cats:
        cl = crumb_links(pages.get(path, ""))
        parent = ""
        if cl:
            if cl[-1][0] == path:
                for href, _ in cl[:-1]:
                    if href != "/":
                        parent = href
            else:
                href = cl[-1][0]
                if href != "/":
                    parent = href
        if parent and parent != path and parent in cats:
            parents[path] = parent
    # (b) footer columns win for their own children
    for parent_col, children in (
        ("/san-pham/", footer_cols.get(15, [])),
        ("/thi-cong-tieu-canh/", footer_cols.get(80, [])),
        ("/dieu-khoan-chinh-sach/", footer_cols.get(81, [])),
    ):
        for child in children:
            if child in cats:
                parents[child] = parent_col
    # (c) nav children
    for top, children in nav_children.items():
        for child in children:
            if child in cats and child not in parents:
                parents[child] = top

    # ---- data types ------------------------------------------------------
    def data_type(path: str) -> str:
        if path == "/":
            return "home"
        if path in NEWS_ROOTS:
            return "news"
        if path in SERVICE_CATS:
            return "service"
        if path in CONTACT_CATS:
            return "contact"
        if path in FOOTER_COLS or path.startswith(
            ("/chinh-sach-", "/dieu-khoan-", "/ve-chung-toi/")
        ):
            return "info"
        return "product"

    # ---- category ordering (nav first, then children, then the rest) -----
    order: list[str] = []
    seen: set[str] = set()

    def walk(p: str):
        if p in seen or p not in cats:
            return
        seen.add(p)
        order.append(p)
        kids = sorted(c for c, pa in parents.items() if pa == p)
        for k in kids:
            walk(k)

    for href, _ in nav_top:
        walk(href)
    for p in sorted(cats, key=lambda s: (parents.get(s, "") != "", s)):
        walk(p)
    walk("/")

    # ---- cards / prices --------------------------------------------------
    cards: dict[str, dict] = {}
    card_order: dict[str, list[str]] = defaultdict(list)   # category -> slugs in order
    home_sections: dict[str, list[str]] = {}
    for path, html in pages.items():
        for c in scan_cards(html):
            href = c["href"]
            slug = href.rstrip("/").split("/")[-1]
            base = slug[:-5] if slug.endswith(".html") else slug
            if base and (not c["old"] or base not in cards):
                cards[base] = c
            cat_dir = "/" + href.strip("/").split("/")[0] + "/" if href.count("/") >= 2 else ""
            if cat_dir and cat_dir in cats and base:
                if base not in card_order[cat_dir]:
                    card_order[cat_dir].append(base)
    # homepage sections keep their own order
    home_html = pages.get("/", "")
    for sec in ("/flashsale/", "/top-san-pham-ban-chay/", "/cay-gia/", "/co-la-reu-gia/", "/hoa-gia/"):
        i = home_html.find(f'href="{sec}"', home_html.find("flashsale-wrapper"))
        home_sections[sec] = card_order.get(sec, [])

    # ---- products / news / services --------------------------------------
    products, news_rows, services, sys_images = [], [], [], []
    prod_by_slug: dict[str, int] = {}
    thu_tu_by_cat: dict[str, dict[str, int]] = defaultdict(dict)

    def cat_of(item_path: str) -> str:
        if item_path.startswith("//") or item_path.count("/") < 2:
            return ""
        seg = "/" + item_path.strip("/").split("/")[0] + "/"
        if seg in cats:
            return seg
        cl = crumb_links(pages.get(item_path, ""))
        for href, _ in reversed(cl):
            if href.endswith("/") and href in cats:
                return href
        return ""

    def assign_thu_tu(cat: str, slug: str, pos: int):
        if cat and slug:
            thu_tu_by_cat[cat][slug] = 1000 - pos

    # category page order -> thu_tu
    for cat, slugs in card_order.items():
        for pos, slug in enumerate(slugs):
            assign_thu_tu(cat, slug, pos)

    pid = 0
    nid = 0
    sid = 0
    for path in sorted(items):
        html = pages[path]
        cat = cat_of(path)
        dtype = data_type(cat) if cat else "product"
        slug = path.rstrip("/").split("/")[-1]
        base = slug[:-5] if slug.endswith(".html") else slug
        blocks = jsonld(html)
        ld_product = jsonld_by_type(blocks, "Product")
        ld_news = jsonld_by_type(blocks, "NewsArticle")

        if dtype == "news" or (not ld_product and ld_news and dtype != "service"):
            name = first_h1(html) or strip_tags(ld_news.get("headline", "")) or page_title(html)
            intro = strip_tags(grab(html, "newsdetail-intro"))[:500]
            body = cut_trailing(
                grab(html, "tocContent") or grab(html, "newsdetail"),
                ["<div class=\"newsothers\"", "<div class=\"sharethis-inline-share-buttons\"",
                 "Bài viết liên quan", "Sản phẩm tương tự"],
            )
            body = re.sub(r"(?is)<script.*?</script>", "", body)
            imgs = page_images(html)
            image = ""
            for src in imgs:
                if src.startswith("/uploaded/"):
                    image = src
                    break
            if not image and imgs:
                image = imgs[0]
            nid += 1
            news_rows.append({
                "id": nid, "cat": cat, "name": name[:255], "slug": slug,
                "intro": intro, "content": squeeze(body), "image": image,
                "date": published_ts(html, blocks),
                "desc": meta_tag(html, "description")[:500],
                "kw": meta_tag(html, "keywords")[:500],
                "path": path,
            })
            continue

        if dtype == "service":
            name = first_h1(html) or strip_tags(ld_product.get("name", "")) or page_title(html)
            body = grab(html, "intro-detail") or grab(html, "service-wrapper")
            body = re.sub(r"(?is)<script.*?</script>", "", body)
            info = strip_tags(grab(html, "service-info"))
            intro = info or strip_tags(body)[:300]
            imgs = page_images(html)
            image = next((s for s in imgs if s.startswith("/uploaded/")), imgs[0] if imgs else "")
            sid += 1
            services.append({
                "id": sid, "cat": cat, "name": name[:255], "slug": slug,
                "intro": intro[:500], "content": squeeze(body), "image": image,
                "date": published_ts(html, blocks),
                "desc": meta_tag(html, "description")[:500],
                "path": path,
            })
            continue

        # ---- product ---------------------------------------------------
        name = strip_tags(ld_product.get("name", "")) or first_h1(html) or page_title(html)
        imgs = image_list(ld_product.get("image")) or page_images(html)
        card = cards.get(base, {})
        cur = price_number(card.get("cur", ""))
        if not cur:
            cur = price_number(strip_tags(grab(html, "__none__")))  # never matches
        # detail page price wins
        pricekm_block = re.search(r'id="pricekm"[^>]*>(.*?)</div>', html, re.S)
        if pricekm_block:
            txt = strip_tags(pricekm_block.group(1))
            if "liên hệ" in txt.lower():
                cur = 0
            elif price_number(txt):
                cur = price_number(txt)
        old = price_number(card.get("old", ""))
        if old <= cur:
            old = cur
        km = int(round((old - cur) / old * 100)) if old > 0 and cur < old else 0
        body = grab(html, "product-content")
        body = re.sub(r"(?is)<script.*?</script>", "", body)
        desc = strip_tags(ld_product.get("description", "")) or meta_tag(html, "description")
        intro = desc[:500]
        pid += 1
        prod_by_slug[base] = pid
        products.append({
            "id": pid, "cat": cat, "name": name[:255], "slug": slug, "base": base,
            "path": path, "sku": str(ld_product.get("sku", ""))[:100],
            "price": old, "pricekm": cur, "km": km,
            "image": imgs[0] if imgs else "", "images": imgs[:8],
            "intro": intro, "content": squeeze(body), "desc": desc[:500],
            "kw": meta_tag(html, "keywords")[:500],
            "date": published_ts(html, blocks),
            "ver": variants(html),
        })

    # homepage order -> thu_tu override (so the home sections match the demo)
    for sec, slugs in home_sections.items():
        for pos, slug in enumerate(slugs[:8]):
            if slug in thu_tu_by_cat[sec]:
                thu_tu_by_cat[sec][slug] = 2000 - pos

    # ---- category rows ---------------------------------------------------
    cat_rows = []
    id_by_cat: dict[str, int] = {}
    for i, path in enumerate(order, start=1):
        id_by_cat[path] = i
    # home first, ids stable
    html_home = pages.get("/", "")
    static_footer = grab(html_home, "footer-block")  # company block (first one)

    for path in order:
        pid_c = id_by_cat[path]
        html = pages.get(path, "")
        is_home = path == "/"
        name = "Trang chủ" if is_home else label_for(path)
        parent = parents.get(path, "")
        parentid = id_by_cat.get(parent, 0)
        dtype = data_type(path)
        vitri = []
        if path in [h for h, _ in nav_top]:
            vitri.append("menubar")
        if path == "/san-pham/":
            vitri.append("menutop")
        if path in FOOTER_COLS:
            vitri.append("boxmenufooter")
        if is_home:
            vitri += ["slideshow", "logosite"]
        content = ""
        intro = ""
        if dtype == "info":
            content = extract_info_body(html)
        elif dtype == "contact":
            content = extract_info_body(html)
        elif dtype in ("product", "service"):
            intro = extract_cat_intro(html)
            content = extract_cat_body(html)
        cat_rows.append({
            "id": pid_c, "name": name[:255], "parent": parentid,
            "thu_tu": (NAV_TOP.index(path) + 1) if path in NAV_TOP else 100 + order.index(path),
            "url": path, "dtype": dtype,
            "vitri": ":" + ":".join(vitri) + ":" if vitri else "",
            "footercol": FOOTER_COLS.get(path, 0),
            "content": squeeze(content), "intro": squeeze(intro)[:2000],
            "desc": meta_tag(html, "description")[:500],
            "kw": meta_tag(html, "keywords")[:500],
            "title": (page_title(html) or name)[:400],
            "imageadv": "/uploaded/ico/flash-sale-icon.png" if path == "/flashsale/" else "",
        })

    # ---- write SQL -------------------------------------------------------
    sql = Sql()
    sql.truncate(
        "category", "product", "news", "service", "logo", "static",
        "settingshow", "sys_image", "search_content",
    )

    sql.insert("category", [
        "id_category", "name", "parentid", "thu_tu", "active", "content",
        "keywords", "description", "url", "vitri", "data_type", "title",
        "footercol", "imageadv", "intro", "lang", "last_modify",
    ], [
        [r["id"], r["name"], r["parent"], r["thu_tu"], 1, r["content"],
         r["kw"], r["desc"], r["url"], r["vitri"], r["dtype"], r["title"],
         r["footercol"], r["imageadv"], r["intro"], "", int(time.time())]
        for r in cat_rows
    ])

    def thu_tu(cat: str, slug: str) -> int:
        return thu_tu_by_cat.get(cat, {}).get(slug, 0)

    sql.insert("product", [
        "id_product", "id_category", "name", "image", "price", "km", "pricekm",
        "intro", "content", "ngay_dang", "active", "thu_tu", "hit", "status",
        "ma", "title", "keywords", "description", "product_ver", "url", "moi",
    ], [
        [p["id"], id_by_cat.get(p["cat"], 0), p["name"], p["image"],
         p["price"], p["km"], p["pricekm"], p["intro"], p["content"],
         p["date"], 1, thu_tu(p["cat"], p["base"]), 0, 1, p["sku"],
         p["name"], p["kw"], p["desc"], p["ver"], p["slug"], 1]
        for p in products
    ])

    sql.insert("news", [
        "id_news", "id_category", "name", "title", "description", "keywords",
        "image", "intro", "content", "ngay_dang", "thu_tu", "active", "hit",
        "inhome", "url",
    ], [
        [n["id"], id_by_cat.get(n["cat"], 0), n["name"], n["name"], n["desc"],
         n["kw"], n["image"], n["intro"], n["content"], n["date"],
         thu_tu(n["cat"], n["base"] if "base" in n else n["slug"].replace(".html", "")),
         1, 0, 1, n["slug"]]
        for n in news_rows
    ])

    sql.insert("service", [
        "id_service", "id_category", "name", "title", "description", "image",
        "intro", "content", "ngay_dang", "thu_tu", "active", "hit", "inhome", "url",
    ], [
        [s["id"], id_by_cat.get(s["cat"], 0), s["name"], s["name"], s["desc"],
         s["image"], s["intro"], s["content"], s["date"],
         thu_tu(s["cat"], s["slug"].replace(".html", "")), 1, 0, 1, s["slug"]]
        for s in services
    ])

    # ---- logos -----------------------------------------------------------
    home_id = id_by_cat.get("/", 0)
    logo_rows = [
        [1, home_id, "Logo Xanhvina", "/uploaded/logo/logo.png", "/", 0, 1, "", "", "", 0],
        [2, home_id, "Banner 01", "/uploaded/logo/banner-01.jpg", "/", 1, 1, "", "", "", 0],
        [3, home_id, "Banner 02", "/uploaded/logo/banner-02.jpg", "/", 2, 1, "", "", "", 0],
    ]
    sql.insert("logo", [
        "id_logo", "id_category", "name", "image", "link", "thu_tu", "active",
        "small_image", "normal_image", "target", "ngay_dang",
    ], logo_rows)

    # ---- static text -----------------------------------------------------
    footer_block = extract_footer_company(html_home)
    static_rows = [
        [1, footer_block[0], footer_block[1], 1, 0, 1, "footer", ""],
        [2, "Copyright", "Copyright © 2023 by Xanhvina", 2, 0, 1, "copyright", ""],
        [3, "Trang Liên hệ", "", 3, 0, 1, "", ""],
    ]
    sql.insert("static", ["id_static", "name", "content", "thu_tu", "id_catstatic",
                          "id_user", "inwhere", "lang"], static_rows)

    # ---- settings --------------------------------------------------------
    settings = {
        "newsinpage": "9", "producthome": "8", "albumpage": "3",
        "productinpage": "12", "photopage": "3",
        "hotline": "0898 596 933",
        "hotlinesupport": "0898 596 933",
        "addresssupport": "Số 45, Thanh Liệt, Thanh Trì, Hà Nội",
        "companyname": "CÔNG TY TNHH THƯƠNG MẠI VÀ SẢN XUẤT XANHVINA",
        "companyaddress": "Số 45, Thanh Liệt, Thanh Trì, Hà Nội",
        "companyphone": "0898 596 933 - 0971.048.490",
        "companyemail": "info.xanhvina@gmail.com",
        "companywebsite": "https://xanhvina.com",
        "emailsupport": "info.xanhvina@gmail.com",
        "facebook": "https://www.facebook.com/congtyxanhvina",
        "fanpage": "https://www.facebook.com/congtyxanhvina",
        "youtube": "https://www.youtube.com/@xanhvina",
        "twitter": "",
        "linkedin": "",
        "instagram": "",
        "googleplus": "",
        "blog": "",
        "apikey": "",
        "call_buy": "0898 596 933",
    }
    sql.insert("settingshow", ["id", "name", "content", "id_user"],
               [[1, "1", json.dumps(settings, ensure_ascii=False), 0]])

    # ---- product galleries ----------------------------------------------
    sys_rows = []
    k = 0
    for p in products:
        for img in p["images"]:
            if not img:
                continue
            k += 1
            sys_rows.append([k, p["name"][:500], "", 0, img, None, "product",
                             "id_product", p["id"], "", 1])
    sql.insert("sys_image", [
        "id", "name", "image_desc", "thu_tu", "image", "id_category",
        "table_name", "id_item", "id_value", "type_code", "active",
    ], sys_rows)

    # ---- search index ----------------------------------------------------
    search_rows = []
    sid_s = 0

    def add_search(name, intro, content, url, image, table, id_item, id_value, cat):
        nonlocal sid_s
        sid_s += 1
        search_rows.append([
            sid_s, name, unsign(name), intro, unsign(intro), content,
            unsign(content), int(time.time()), unsign(name)[:500], "", 1,
            unsign(name)[:500], "", url, cat, id_item, id_value, table, image,
        ])

    for p in products:
        add_search(p["name"], p["intro"], strip_tags(p["content"])[:6000],
                   "/" + p["cat"].strip("/") + "/" + p["slug"] if p["cat"] else "/" + p["slug"],
                   p["image"], "product", "id_product", p["id"],
                   id_by_cat.get(p["cat"], 0))
    for n in news_rows:
        add_search(n["name"], n["intro"], strip_tags(n["content"])[:6000],
                   n["path"], n["image"], "news", "id_news", n["id"],
                   id_by_cat.get(n["cat"], 0))
    for s in services:
        add_search(s["name"], s["intro"], strip_tags(s["content"])[:6000],
                   s["path"], s["image"], "service", "id_service", s["id"],
                   id_by_cat.get(s["cat"], 0))
    for c in cat_rows:
        if c["dtype"] in ("info", "contact", "news"):
            add_search(c["name"], c["intro"], strip_tags(c["content"])[:4000],
                       c["url"], "", "category", "id_category", c["id"], c["id"])

    sql.insert("search_content", [
        "id_search", "name", "name_unsign", "intro", "intro_unsign", "content",
        "content_unsign", "ngay_dang", "keywords", "tags", "active",
        "name_unsign2", "lang", "url", "id_category", "id_item",
        "id_item_value", "table_name", "image",
    ], search_rows)

    path = sql.write()

    if report:
        print(f"categories      : {len(cat_rows)}")
        print(f"products        : {len(products)}")
        print(f"news            : {len(news_rows)}")
        print(f"services        : {len(services)}")
        print(f"sys_image rows  : {len(sys_rows)}")
        print(f"search rows     : {len(search_rows)}")
        print(f"footer cols     : { {k: len(v) for k, v in footer_cols.items()} }")
        print(f"nav top         : {nav_top}")
        missing_parent = [c["url"] for c in cat_rows if c["parent"] == 0 and c["url"] != "/"]
        print(f"root categories : {len(missing_parent)} -> {missing_parent}")
        print(f"sql             : {path} ({path.stat().st_size} bytes)")
    else:
        print(f"wrote {path} ({path.stat().st_size} bytes)")
    return 0


# --------------------------------------------------------------------------
def extract_info_body(html: str) -> str:
    """Content of a policy / info / contact page."""
    i = (html or "").find('<div class="cat-content"')
    if i >= 0:
        bounds = element_open(html, i)
        if bounds:
            inner = html[bounds[0]:bounds[1]]
            if len(strip_tags(inner)) > 40:
                return inner
            # empty cat-content: the real body is the next sibling div
            j = bounds[1]
            m = re.match(r"(?:\s*<div class=\"c\d+\"></div>)+", html[j:])
            if m:
                j += m.end()
            nxt = element_open(html, j)
            if nxt:
                return html[nxt[0]:nxt[1]]
    # fallback: everything after the first </h1>
    k = (html or "").find("</h1>")
    if k < 0:
        return ""
    start = k + 5
    end = (html or "").find("<!-- footer -->", start)
    if end < 0:
        end = (html or "").find("<footer", start)
    if end < 0:
        end = len(html)
    seg = html[start:end]
    seg = cut_trailing(seg, ["<div class=\"sharethis-inline-share-buttons\"",
                             "<div class=\"newsothers\"", "<div class=\"newslist-wrapper\""])
    seg = re.sub(r"(?is)<script.*?</script>", "", seg)
    # drop the structural closing divs at the end
    while True:
        m = re.search(r"(?:\s*</?div[^>]*>|\s*<div class=\"c\d+\"></div>|\s*<!--.*?-->)\s*$", seg, re.S)
        if not m:
            break
        seg = seg[:m.start()]
    return seg.strip()


def extract_cat_intro(html: str) -> str:
    block = grab(html, "flashsale-wrapper")
    if not block:
        return ""
    inner = grab(block, "text-center") if 'class="text-center"' in block else block
    return inner or block


def extract_cat_body(html: str) -> str:
    return ""


def extract_footer_company(html: str) -> tuple[str, str]:
    """(footer static name, footer static content) from the live footer."""
    i = (html or "").find('footer-title')
    if i < 0:
        return "CÔNG TY TNHH THƯƠNG MẠI VÀ SẢN XUẤT XANHVINA", ""
    # name = first footer-title text
    m = re.search(r'<div class="footer-title[^"]*"[^>]*>(.*?)</div>', html[i - 200:i + 400], re.S)
    name = strip_tags(m.group(1)) if m else "CÔNG TY TNHH THƯƠNG MẠI VÀ SẢN XUẤT XANHVINA"
    # content = the <ul> right after it
    j = html.find("<ul>", i)
    k = html.find("</ul>", j)
    content = html[j:k + 5] if j > 0 and k > j else ""
    return name, squeeze(content)


if __name__ == "__main__":
    raise SystemExit(main())
