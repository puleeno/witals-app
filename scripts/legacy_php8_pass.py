#!/usr/bin/env python3
"""
Legacy PHP 8 compatibility pass.

The endcms code writes `while (...) { $data[] = $row; } return $data;` without
ever initialising `$data`. On PHP 5 that was a notice, on PHP 8 it returns null
and blows up on count()/foreach()/in_array(). This script inserts
`$data = array();` (once, before the first append) for every variable that is
appended to and returned but never assigned anywhere else in the same scope.
"""
import re
import sys
from pathlib import Path

ASSIGN_RE = lambda v: re.compile(r'(?<![\w$])' + re.escape(v) + r'\s*=(?!=)')
APPEND_RE = lambda v: re.compile(r'(?<![\w$])' + re.escape(v) + r'\s*\[\s*\]\s*=(?!=)')
RETURN_RE = lambda v: re.compile(r'return\s*\(\s*' + re.escape(v) + r'\s*\)\s*;|return\s+' + re.escape(v) + r'\s*;')
VAR_RE = re.compile(r'\$([a-zA-Z_][a-zA-Z0-9_]*)')


def match_brace(text: str, start: int) -> int:
    """Return index of the brace closing the '{' at `start`."""
    depth = 0
    i = start
    n = len(text)
    while i < n:
        c = text[i]
        if c == "'" or c == '"':
            quote = c
            i += 1
            while i < n:
                if text[i] == '\\':
                    i += 2
                    continue
                if text[i] == quote:
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


def line_start(text: str, pos: int) -> int:
    return text.rfind('\n', 0, pos) + 1


def indent_of(text: str, pos: int) -> str:
    ls = line_start(text, pos)
    return re.match(r'[ \t]*', text[ls:pos]).group(0)


LOOP_RE = re.compile(r'(?<![\w$])(while|for|foreach)\s*\(')


def statement_anchor(text: str, pos: int) -> int:
    """
    Move `pos` (an append statement) back to the start of the outermost loop
    that encloses it, so the initialisation is placed *before* the loop and not
    inside it (which would reset the array on every iteration).
    """
    best = pos
    for m in LOOP_RE.finditer(text, 0, pos):
        paren = m.end() - 1  # '('
        depth = 0
        i = paren
        while i < len(text):
            c = text[i]
            if c == '(':
                depth += 1
            elif c == ')':
                depth -= 1
                if depth == 0:
                    break
            elif c in '\'"':
                q = c
                i += 1
                while i < len(text) and text[i] != q:
                    i += 2 if text[i] == '\\' else 1
            i += 1
        else:
            continue
        j = i + 1
        while j < len(text) and text[j] in ' \t\r\n':
            j += 1
        if j >= len(text) or text[j] != '{':
            # loop without braces: body is the single next statement
            if i < pos and ';' not in text[i + 1:pos]:
                best = min(best, m.start())
            continue
        close = match_brace(text, j)
        if close < 0 or not (j < pos < close):
            continue
        best = min(best, m.start())
    return best


def find_scopes(text: str):
    """Yield (body_start, body_end) for every function / global scope."""
    for m in re.finditer(r'(?<![\w$>])function\s+&?[a-zA-Z_][\w]*\s*\(', text):
        # skip method calls: '->function(' / '::function('
        before = text[max(0, m.start() - 2):m.start()]
        if before.endswith('->') or before.endswith('::'):
            continue
        brace = text.find('{', m.end())
        if brace < 0:
            continue
        # parameter defaults may contain braces (closures) - walk to first '{'
        end = match_brace(text, brace)
        if end > 0:
            yield (brace + 1, end)


def process(path: Path) -> bool:
    text = path.read_text(encoding='utf-8', errors='surrogateescape')

    scopes = list(find_scopes(text))
    # Treat everything outside of functions as one extra scope.
    spans = [(0, len(text))] + scopes

    insertions = []
    seen_lines = set()

    for start, end in spans:
        body = text[start:end]
        if not body:
            continue

        # Candidate variables: appended to inside this scope.
        candidates = set()
        for m in VAR_RE.finditer(body):
            candidates.add('$' + m.group(1))

        for v in sorted(candidates):
            if not APPEND_RE(v).search(body):
                continue
            if ASSIGN_RE(v).search(body):
                continue  # already initialised somewhere in this scope
            if not RETURN_RE(v).search(body):
                continue

            m = APPEND_RE(v).search(body)
            pos = statement_anchor(text, start + m.start())
            ls = line_start(text, pos)
            if ls in seen_lines:
                continue
            seen_lines.add(ls)
            insertions.append((ls, v))

    if not insertions:
        return False

    insertions.sort(reverse=True)
    for pos, v in insertions:
        ind = indent_of(text, pos)
        text = text[:pos] + f'{ind}{v} = array();\n' + text[pos:]

    path.write_text(text, encoding='utf-8', errors='surrogateescape')
    return True


def main(argv):
    changed = []
    for arg in argv[1:]:
        p = Path(arg)
        if p.is_dir():
            files = sorted(p.rglob('*.php'))
        else:
            files = [p]
        for f in files:
            try:
                if process(f):
                    changed.append(str(f))
            except Exception as exc:  # noqa: BLE001
                print(f'SKIP {f}: {exc}', file=sys.stderr)
    for c in changed:
        print(c)
    print(f'{len(changed)} file(s) patched', file=sys.stderr)


if __name__ == '__main__':
    main(sys.argv)
