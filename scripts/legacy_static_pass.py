#!/usr/bin/env python3
"""
Legacy PHP 8 compatibility pass #2.

PHP 8 removed "static calls to non-static methods". The endcms code calls
`Category::getParentId()`, `paging::pagingFonten()`, `url_process::getUrlCategory()`
... statically all over the place. Every method that is called statically and
never touches `$this` is rewritten to `public static function ...`.
"""
import re
import sys
from pathlib import Path

METHOD_RE = re.compile(
    r'(?P<mods>(?:(?:public|protected|private|static|final|abstract)\s+)*)'
    r'function\s+(?P<ref>&?)(?P<name>[A-Za-z_]\w*)\s*\('
)
CLASS_RE = re.compile(r'(?<![\w])(?:abstract\s+)?class\s+([A-Za-z_]\w*)')
CALL_RE = re.compile(r'(?<![\w$])([A-Za-z_]\w*)::([A-Za-z_]\w*)\s*\(')
THIS_RE = re.compile(r'\$this\b')


def strip_comments(code: str) -> str:
    """Remove comments while keeping string literals (and therefore offsets) intact."""
    out = []
    i = 0
    n = len(code)
    while i < n:
        c = code[i]
        if c == "'" or c == '"':
            q = c
            out.append(c)
            i += 1
            while i < n:
                out.append(code[i])
                if code[i] == '\\' and i + 1 < n:
                    out.append(code[i + 1])
                    i += 2
                    continue
                if code[i] == q:
                    i += 1
                    break
                i += 1
            continue
        if c == '/' and i + 1 < n and code[i + 1] == '/':
            while i < n and code[i] != '\n':
                i += 1
            continue
        if c == '#':
            while i < n and code[i] != '\n':
                i += 1
            continue
        if c == '/' and i + 1 < n and code[i + 1] == '*':
            end = code.find('*/', i + 2)
            if end < 0:
                break
            out.append('\n' * code[i:end].count('\n'))
            i = end + 2
            continue
        out.append(c)
        i += 1
    return ''.join(out)


def match_brace(text: str, start: int) -> int:
    depth = 0
    i = start
    n = len(text)
    while i < n:
        c = text[i]
        if c in '\'"':
            q = c
            i += 1
            while i < n:
                if text[i] == '\\':
                    i += 2
                    continue
                if text[i] == q:
                    break
                i += 1
        elif c == '/' and i + 1 < n and text[i + 1] == '/':
            while i < n and text[i] != '\n':
                i += 1
            continue
        elif c == '#':
            while i < n and text[i] != '\n':
                i += 1
            continue
        elif c == '/' and i + 1 < n and text[i + 1] == '*':
            i = text.find('*/', i + 2)
            if i < 0:
                return -1
            i += 2
            continue
        elif c == '{':
            depth += 1
        elif c == '}':
            depth -= 1
            if depth == 0:
                return i
        i += 1
    return -1


def analyse(files):
    """class -> method -> {decl_pos, is_static, uses_this}"""
    classes = {}
    for f in files:
        code = f.read_text(encoding='utf-8', errors='surrogateescape')
        visible = strip_comments(code)
        for cm in CLASS_RE.finditer(visible):
            cname = cm.group(1)
            brace = visible.find('{', cm.end())
            if brace < 0:
                continue
            end = match_brace(visible, brace)
            if end < 0:
                continue
            methods = classes.setdefault(cname, {})
            pos = brace + 1
            while True:
                mm = METHOD_RE.search(visible, pos, end)
                if not mm:
                    break
                body_brace = visible.find('{', mm.end())
                if body_brace < 0 or body_brace > end:
                    pos = mm.end()
                    continue
                body_end = match_brace(visible, body_brace)
                if body_end < 0:
                    pos = mm.end()
                    continue
                mods = mm.group('mods')
                methods[mm.group('name')] = {
                    'file': f,
                    'decl_start': mm.start(),
                    'decl_mods_start': mm.start('mods'),
                    'decl_mods': mods,
                    'is_static': 'static' in mods.split(),
                    'uses_this': bool(THIS_RE.search(visible[body_brace:body_end])),
                }
                pos = body_end + 1
    return classes


def main(argv):
    root = Path(argv[1]) if len(argv) > 1 else Path('legacy')
    files = sorted(root.rglob('*.php'))
    classes = analyse(files)

    # Collect the statically called, non-static, $this-free methods.
    targets = {}  # (class, method) -> info
    for f in files:
        code = f.read_text(encoding='utf-8', errors='surrogateescape')
        for cm in CALL_RE.finditer(strip_comments(code)):
            cls, mth = cm.group(1), cm.group(2)
            if cls in ('parent', 'self', 'static'):
                continue
            info = classes.get(cls, {}).get(mth)
            if info and not info['is_static']:
                targets[(cls, mth)] = info

    skipped = []

    # Patch file by file using raw text searches.
    by_file = {}
    for (cls, mth), info in targets.items():
        if info['uses_this']:
            skipped.append(f'{cls}::{mth} (uses $this)')
            continue
        by_file.setdefault(info['file'], []).append((cls, mth))

    changed = []
    for f, methods in by_file.items():
        code = f.read_text(encoding='utf-8', errors='surrogateescape')
        original = code
        for cls, mth in methods:
            pattern = re.compile(
                r'(?P<mods>(?:(?:public|protected|private|static|final|abstract)\s+)*)'
                r'function\s+(?P<ref>&?)' + re.escape(mth) + r'\s*\('
            )
            # Locate the class body in raw text
            cm = re.search(r'(?<![\w])(?:abstract\s+)?class\s+' + re.escape(cls) + r'\b', code)
            if not cm:
                continue
            brace = code.find('{', cm.end())
            if brace < 0:
                continue
            end = match_brace(code, brace)
            if end < 0:
                continue
            m = pattern.search(code, brace + 1, end)
            if not m:
                continue
            mods = m.group('mods')
            if 'static' in mods.split():
                continue
            insertion = m.start('mods')
            # keep a single space after the inserted keyword
            prefix = mods.rstrip()
            new_mods = (prefix + ' static ') if prefix else 'static '
            code = code[:insertion] + new_mods + 'function ' + code[m.start('ref'):]
        if code != original:
            f.write_text(code, encoding='utf-8', errors='surrogateescape')
            changed.append(f'{f}: ' + ', '.join(f'{c}::{m}' for c, m in methods))

    for line in changed:
        print(line)
    if skipped:
        print('\nSKIPPED (uses $this):', file=sys.stderr)
        for s in skipped:
            print('  ' + s, file=sys.stderr)
    print(f'{len(changed)} file(s) patched', file=sys.stderr)


if __name__ == '__main__':
    main(sys.argv)
