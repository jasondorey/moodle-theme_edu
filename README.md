moodle-theme_everon
===================

**Everon** is a Boost Union child theme for Project Theme (Jason Dorey).

It is scaffolded from [moodle-theme_boost_union_child](https://github.com/moodle-an-hochschulen/moodle-theme_boost_union_child) and inherits Boost Union at runtime (`$THEME->parents = ['boost_union', 'boost']`).

## Requirements

- Moodle **5.2+** (`$plugin->requires` / `$plugin->supported` = 502)
- [theme_boost_union](https://github.com/moodle-an-hochschulen/moodle-theme_boost_union) **v5.2-r13+** (dependency pinned to `2026042019`)

## Installation

Install to folder:

```text
/theme/everon
```

1. Install / upgrade **Boost Union** for Moodle 5.2 first.
2. Copy this plugin to `/theme/everon` (or install `theme_everon.zip` via Site administration → Plugins → Install plugins).
3. Visit Site administration → Notifications to install.
4. Site administration → Appearance → Theme selector → enable **Everon**.

See [everon-install.md](../../docs/everon-install.md) in the Project store for full notes.

## Initial look

Stock **Moodle / Boost Union** branding. Login uses Boost Union only (no Everon split-login / panel-image feature). `EXTENSION POINT` markers in SCSS, settings, and lang are intentionally preserved.

## Extension points

Search the codebase for `EXTENSION POINT` to find where to add SCSS, settings, lang strings, and Behat scenarios.

## Copyright

- Project Theme / Jason Dorey (Everon product scaffold)
- Boilerplate: Moodle an Hochschulen e.V. / Daniel Poggenpohl / Alexander Bias (GPL v3+)
