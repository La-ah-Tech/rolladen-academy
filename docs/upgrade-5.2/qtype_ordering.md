# qtype_ordering — customization note (5.0.2 → 5.2.1 upgrade)

**Status:** Resolved by using Moodle 5.2.1's bundled version. This note preserves the
details in case the previous (customized) version must be restored.

## TL;DR

The old 5.0.2 deployment did **not** contain bespoke business logic in `qtype_ordering`.
Instead, core's bundled `question/type/ordering` had been **replaced with the standalone
"contrib" build** maintained by Gordon Bateson
(<https://github.com/gbateson/moodle-qtype_ordering>).

Moodle **5.2.1 bundles a newer version** of this plugin that supersedes the contrib build,
so the upgrade simply **drops the old customized copy and uses core's
`public/question/type/ordering`**. No patch needs to be re-applied.

## Versions involved

| Source | `$plugin->version` | Notes |
|---|---|---|
| Pristine Moodle 5.0.2 core (`a2f7f13`) | `2025041400` | Moodle-HQ's core fork of the plugin |
| Old deployment "saved current state" (`512cc90`) | `2025041400` | **Standalone Gordon Bateson contrib build** swapped in over core's fork (same version number, different code) |
| Moodle 5.2.1 core (current `public/`) | `2026042000` | Newer; supersedes both |

## Evidence this is a distribution swap, not a site hack

- Diff of pristine-5.0.2 → deployment touched **only** `question/type/ordering` (29 modified
  files) — nothing elsewhere in core.
- The diff **reverts** Moodle-HQ modernizations (e.g. `has_html_answers(): bool` → `has_html_answers()`)
  and **re-adds** contrib-only artifacts: `.travis.yml`, `CHANGES.txt`, `readme.txt`, and the
  extra AMD modules `autoscroll.js`, `dragdrop.js`, `key_codes.js`, `reorder.js`.
- Comparing the deployment copy to core 5.2.1: only ~6 files differ, and it is **381 deletions
  vs 74 insertions** — i.e. core 5.2.1 is a strict superset / newer.

## How to restore the old (customized) version if ever needed

The old version is fully recoverable from git history — nothing is lost.

```bash
# Inspect the old customized plugin tree
git show 512cc90cd7f:question/type/ordering/version.php

# Restore the whole old plugin into the new layout (public/), overwriting core's copy
git checkout 512cc90cd7f -- question/type/ordering
git mv question/type/ordering public/question/type/ordering   # relocate into 5.2 webroot
```

> ⚠️ If restored, the contrib build (version `2025041400`) is **older** than core 5.2.1's
> (`2026042000`). Moodle will refuse to "downgrade" a plugin, so you would also need to bump
> its `version.php` and verify it actually runs on Moodle 5.2 (PHP 8.3). Prefer pulling a
> current 5.2-compatible release from the upstream repo instead:
> <https://github.com/gbateson/moodle-qtype_ordering> (or the Moodle plugins directory).

## Files that differed (pristine 5.0.2 → old deployment)

```
.github/workflows/ci.yml          amd/src/reorder.js
.travis.yml                        backup/moodle1/lib.php
CHANGES.txt                        backup/moodle2/backup_qtype_ordering_plugin.class.php
readme.txt                         backup/moodle2/restore_qtype_ordering_plugin.class.php
amd/src/autoscroll.js              classes/privacy/provider.php
amd/src/drag_reorder.js            classes/question_hint_ordering.php
amd/src/dragdrop.js                db/install.xml
amd/src/key_codes.js               db/upgrade.php
edit_ordering_form.php             question.php
lang/en/qtype_ordering.php         questiontype.php
lib.php                            renderer.php
pix/icon.svg                       settings.php
styles.css                         tests/* (behat, fixtures, helper, unit)
version.php
```

## Git references

- Pristine 5.0.2 core: `a2f7f13d7c7` ("weekly release 5.0.2+")
- Old customized deployment: `512cc90cd7f` ("saved current state")
- Current target: `public/question/type/ordering` on branch `upgrade_to_502_stable`
