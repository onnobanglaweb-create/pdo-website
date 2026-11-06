#!/usr/bin/env python3
"""Minimal gettext extractor for WordPress PHP sources."""
import re, sys, os, datetime, glob

FUNCS = {
    '__': (1, None, None), '_e': (1, None, None),
    'esc_html__': (1, None, None), 'esc_html_e': (1, None, None),
    'esc_attr__': (1, None, None), 'esc_attr_e': (1, None, None),
    '_x': (1, None, 2), 'esc_html_x': (1, None, 2), 'esc_attr_x': (1, None, 2),
    '_ex': (1, None, 2),
    '_n': (1, 2, None), '_nx': (1, 2, 4),
}

STRING = r"""(?:'((?:[^'\\]|\\.)*)'|"((?:[^"\\]|\\.)*)")"""
ARG = re.compile(STRING)


def unescape(raw, quote):
    if quote == "'":
        return raw.replace("\\'", "'").replace('\\\\', '\\')
    return (raw.replace('\\"', '"').replace('\\n', '\n')
               .replace('\\t', '\t').replace('\\\\', '\\'))


def po_escape(value):
    return (value.replace('\\', '\\\\').replace('"', '\\"')
                 .replace('\n', '\\n').replace('\t', '\\t'))


def split_args(src, start):
    """Return the raw argument list of a call whose '(' is at `start`."""
    depth, i, out, cur = 0, start, [], ''
    in_q = None
    while i < len(src):
        c = src[i]
        if in_q:
            if c == '\\':
                cur += src[i:i + 2]
                i += 2
                continue
            cur += c
            if c == in_q:
                in_q = None
            i += 1
            continue
        if c in '"\'':
            in_q = c
            cur += c
        elif c == '(':
            depth += 1
            if depth > 1:
                cur += c
        elif c == ')':
            depth -= 1
            if depth == 0:
                out.append(cur)
                return out, i
            cur += c
        elif c == ',' and depth == 1:
            out.append(cur)
            cur = ''
        else:
            cur += c
        i += 1
    return out, i


def literal(arg):
    arg = arg.strip()
    m = ARG.fullmatch(arg)
    if not m:
        return None
    if m.group(1) is not None:
        return unescape(m.group(1), "'")
    return unescape(m.group(2), '"')


def extract(root, domain):
    entries = {}
    call = re.compile(r'(?<![\w$>:])(' + '|'.join(sorted(FUNCS, key=len, reverse=True)) + r')\s*\(')
    for path in sorted(glob.glob(os.path.join(root, '**', '*.php'), recursive=True)):
        src = open(path, encoding='utf-8', errors='replace').read()
        rel = os.path.relpath(path, os.path.dirname(root))
        for m in call.finditer(src):
            fn = m.group(1)
            args, _ = split_args(src, m.end() - 1)
            s_i, p_i, c_i = FUNCS[fn]
            if len(args) <= s_i - 1:
                continue
            singular = literal(args[s_i - 1])
            if singular is None:
                continue
            # domain is the last argument
            dom = literal(args[-1]) if args else None
            if dom != domain:
                continue
            plural = literal(args[p_i - 1]) if p_i and len(args) >= p_i else None
            ctx = literal(args[c_i - 1]) if c_i and len(args) >= c_i else None
            line = src.count('\n', 0, m.start()) + 1
            # A translator comment counts only when nothing but whitespace and
            # wrapper calls (esc_html(, printf(, sprintf( …) sit between it and
            # the gettext call itself.
            comment = None
            head = src[:m.start()]
            cm = None
            for cm in re.finditer(r'/\*\s*(translators:.*?)\s*\*/', head, re.S | re.I):
                pass
            if cm:
                gap = head[cm.end():]
                if ';' not in gap and '}' not in gap and gap.count('\n') <= 2 and re.fullmatch(r'[\s\w(,.\'"$>\[\]\-]*', gap):
                    comment = ' '.join(cm.group(1).split())
            key = (ctx, singular, plural)
            e = entries.setdefault(key, {'refs': [], 'comment': comment})
            e['refs'].append(f'{rel}:{line}')
            if comment and not e['comment']:
                e['comment'] = comment
    return entries


def write_pot(entries, out, package, domain):
    now = datetime.datetime.now(datetime.timezone.utc).strftime('%Y-%m-%d %H:%M%z')
    lines = [
        '# Copyright (C) 2026 Peoples Development Organisation',
        f'# This file is distributed under the GPL-2.0-or-later license.',
        'msgid ""',
        'msgstr ""',
        f'"Project-Id-Version: {package}\\n"',
        '"Report-Msgid-Bugs-To: https://github.com/onnobanglaweb-create/pdo-website/issues\\n"',
        f'"POT-Creation-Date: {now}\\n"',
        '"MIME-Version: 1.0\\n"',
        '"Content-Type: text/plain; charset=UTF-8\\n"',
        '"Content-Transfer-Encoding: 8bit\\n"',
        '"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n"',
        '"Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n"',
        '"Language-Team: LANGUAGE <LL@li.org>\\n"',
        '"Plural-Forms: nplurals=2; plural=(n != 1);\\n"',
        f'"X-Domain: {domain}\\n"',
        '',
    ]
    for (ctx, singular, plural), data in sorted(entries.items(), key=lambda kv: (kv[0][1].lower(), kv[0][0] or '')):
        if data['comment']:
            lines.append(f"#. {data['comment']}")
        lines.append('#: ' + ' '.join(data['refs'][:12]))
        if ctx:
            lines.append(f'msgctxt "{po_escape(ctx)}"')
        lines.append(f'msgid "{po_escape(singular)}"')
        if plural:
            lines.append(f'msgid_plural "{po_escape(plural)}"')
            lines.append('msgstr[0] ""')
            lines.append('msgstr[1] ""')
        else:
            lines.append('msgstr ""')
        lines.append('')
    open(out, 'w', encoding='utf-8').write('\n'.join(lines))
    return len(entries)


if __name__ == '__main__':
    root, domain, out, package = sys.argv[1:5]
    n = write_pot(extract(root, domain), out, package, domain)
    print(f'{out}: {n} strings')
