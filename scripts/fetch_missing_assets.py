#!/usr/bin/env python3
"""Download live assets that pages reference but the mirror is missing.

Usage:  python3 scripts/fetch_missing_assets.py [--dry-run]
"""

from __future__ import annotations

import importlib.util
import re
import sys
import time
import urllib.parse
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
spec = importlib.util.spec_from_file_location("lm", ROOT / "scripts" / "live_mirror.py")
lm = importlib.util.module_from_spec(spec)
spec.loader.exec_module(lm)


def collect() -> set[str]:
    refs: set[str] = set()
    for f in sorted(lm.PAGES.rglob("*.html")):
        html = f.read_text(encoding="utf-8", errors="replace")
        refs |= {a for a in lm.local_asset_refs(html) if lm.is_asset(a)}
    refs |= lm.discover_more_assets()
    return refs


def main() -> int:
    dry = "--dry-run" in sys.argv
    refs = collect()
    todo = []
    for p in sorted(refs):
        dest = lm.asset_path(p)
        if dest.exists() and dest.stat().st_size > 0:
            continue
        todo.append(p)
    print(f"refs={len(refs)} missing={len(todo)}")
    if dry:
        for p in todo[:80]:
            print("  ", p)
        return 0
    ok = fail = 0
    for i, p in enumerate(todo, 1):
        url = lm.BASE + urllib.parse.quote(urllib.parse.unquote(p), safe="/%")
        try:
            data = lm.fetch(url)
        except Exception as exc:  # noqa: BLE001 - never abort a long run
            print(f"  ! {p} -> {exc}")
            data = None
        if data:
            dest = lm.asset_path(p)
            dest.parent.mkdir(parents=True, exist_ok=True)
            dest.write_bytes(data)
            ok += 1
        else:
            fail += 1
        if i % 40 == 0:
            print(f"[{i}/{len(todo)}] ok={ok} fail={fail} {p}")
        time.sleep(lm.DELAY / 2)
    print(f"done: fetched={ok} failed={fail}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
