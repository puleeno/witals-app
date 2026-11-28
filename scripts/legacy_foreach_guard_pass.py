#!/usr/bin/env python3
"""
Legacy PHP 8 compatibility pass #4.

The endcms list functions return `array(...rows..., 'pages' => '<div>...')`,
so `foreach ($db as $rs)` also yields the pagination HTML string. Reading
`$rs['id_product']` off that string is a fatal `Error` on PHP 8 (it was only a
notice on PHP 5).

Every `foreach` whose body reads a *string* key off the loop value gets an
`if (!is_array($v)) { continue; }` guard, which is exactly the behaviour the
old code had (`intval(null) > 0` was false).
"""
import re
import sys
from pathlib import Path

FOREACH_RE = re.compile(r'foreach\s*\((?P<head>[^;{}]*?)\)\s*(?P<brace>\{)?')
KEYACCESS_RE = re.compile(r'\$[A-Za-z_]\w*\s*\[\s*[\'"][A-Za-z_][A-Za-z0-9_]*[\'"]\s*\]')


def match_brace(text, start):
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


def value_var(head):
    head = head.strip()
    m = re.search(r'\bas\s+(?P<kv>\$\w+\s*=>\s*)?(?P<v>\$\w+)\s*$', head)
    if not m:
        return None
    return m.group('v')


def process(text):
    inserts = []
    pos = 0
    while True:
        m = FOREACH_RE.search(text, pos)
        if not m:
            break
        if not m.group('brace'):
            pos = m.end()
            continue
        open_brace = m.end() - 1
        close = match_brace(text, open_brace)
        if close < 0:
            pos = m.end()
            continue
        body = text[open_brace + 1:close]
        var = value_var(m.group('head'))
        if var and not re.search(r'is_array\(\s*' + re.escape(var) + r'\s*\)', body):
            # does the body read a string key off the loop value?
            if re.search(re.escape(var) + r'\s*\[\s*[\'"][A-Za-z_][A-Za-z0-9_]*[\'"]\s*\]', body):
                line_start = text.rfind('\n', 0, open_brace) + 1
                indent = re.match(r'[ \t]*', text[line_start:open_brace]).group(0)
                inner = indent + '    '
                guard = (f'{inner}if (!is_array({var})) {{ continue; }}\n')
                inserts.append((open_brace + 1, guard))
        pos = close + 1

    for at, guard in reversed(inserts):
        text = text[:at] + guard + text[at:]
    return text, len(inserts)


def main(argv):
    root = Path(argv[1]) if len(argv) > 1 else Path('legacy')
    total = 0
    for f in sorted(root.rglob('*.php')):
        code = f.read_text(encoding='utf-8', errors='surrogateescape')
        new, cnt = process(code)
        if cnt:
            f.write_text(new, encoding='utf-8', errors='surrogateescape')
            print(f'{f}: {cnt}')
            total += cnt
    print(f'{total} guard(s) inserted', file=sys.stderr)


if __name__ == '__main__':
    main(sys.argv)
