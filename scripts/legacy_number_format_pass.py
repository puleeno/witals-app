#!/usr/bin/env python3
"""
Legacy PHP 8 compatibility pass #3.

PHP 8 turned `number_format('')` (and any non numeric string) into a TypeError.
The endcms code passes raw DB columns straight into number_format() on55 call
sites; this wraps the *first* argument in `(float)( ... )` which is exactly the
coercion PHP 5/7 used to do implicitly.
"""
import sys
from pathlib import Path

FUNCTIONS = ('number_format',)


def skip_string(text, i):
    q = text[i]
    i += 1
    n = len(text)
    while i < n:
        if text[i] == '\\':
            i += 2
            continue
        if text[i] == q:
            return i + 1
        i += 1
    return n


def first_arg_end(text, start):
    """`start` is the index just after '('. Returns index of ',' or ')'."""
    depth = 0
    i = start
    n = len(text)
    while i < n:
        c = text[i]
        if c in '\'"':
            i = skip_string(text, i)
            continue
        if c == '/' and i + 1 < n and text[i + 1] == '/':
            j = text.find('\n', i)
            i = n if j < 0 else j
            continue
        if c == '/' and i + 1 < n and text[i + 1] == '*':
            j = text.find('*/', i + 2)
            i = n if j < 0 else j + 2
            continue
        if c == '(' or c == '[':
            depth += 1
        elif c == ')' or c == ']':
            if depth == 0:
                return i
            depth -= 1
        elif c == ',' and depth == 0:
            return i
        i += 1
    return n


def patch(text):
    out = []
    i = 0
    count = 0
    n = len(text)
    while i < n:
        c = text[i]
        if c == "'" or c == '"':
            j = skip_string(text, i)
            out.append(text[i:j])
            i = j
            continue
        if c == '/' and i + 1 < n and text[i + 1] == '/':
            j = text.find('\n', i)
            j = n if j < 0 else j
            out.append(text[i:j])
            i = j
            continue
        if c == '/' and i + 1 < n and text[i + 1] == '*':
            j = text.find('*/', i + 2)
            j = n if j < 0 else j + 2
            out.append(text[i:j])
            i = j
            continue

        hit = None
        for fn in FUNCTIONS:
            if text.startswith(fn + '(', i):
                hit = fn
                break
        if hit is None:
            out.append(c)
            i += 1
            continue
        open_paren = i + len(hit)
        # already wrapped?
        if text.startswith('(float)(', open_paren + 1):
            out.append(text[i:open_paren + 1])
            i = open_paren + 1
            continue
        end = first_arg_end(text, open_paren + 1)
        out.append(text[i:open_paren + 1])
        out.append('(float)(')
        out.append(text[open_paren + 1:end])
        out.append(')')
        i = end
        count += 1
    return ''.join(out), count


def main(argv):
    root = Path(argv[1]) if len(argv) > 1 else Path('legacy')
    files = sorted(root.rglob('*.php'))
    total = 0
    changed = []
    for f in files:
        code = f.read_text(encoding='utf-8', errors='surrogateescape')
        new, cnt = patch(code)
        if cnt:
            f.write_text(new, encoding='utf-8', errors='surrogateescape')
            total += cnt
            changed.append(f'{f}: {cnt}')
    for c in changed:
        print(c)
    print(f'{total} call(s) patched in {len(changed)} file(s)', file=sys.stderr)


if __name__ == '__main__':
    main(sys.argv)
