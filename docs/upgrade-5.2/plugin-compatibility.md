# Third-party plugin compatibility — Moodle 5.0.2 → 5.2.1

Target core: **Moodle 5.2.1** (branch `502`, version `2026042001.00`, requires PHP 8.3).
Policy: **keep every plugin unless testing on 5.2 proves it broken.**

Source of truth for releases: the Moodle plugins directory
(`https://moodle.org/plugins/<component>`), checked 2026-06-16. "Installed" = the version
currently vendored at the old root path in this repo.

> Note on layout: a 5.2 plugin's internal structure is unchanged — each plugin folder is
> dropped into `public/<type>/<name>` as-is. The only structural change in 5.x
> (`subplugins.php` → `subplugins.json`) does not affect any plugin here (offlinequiz and
> unilabel, the only ones with subplugins, already ship `subplugins.json`).

## Legend
- ✅ **Native 5.2** — vendor publishes a release that declares Moodle 5.2 support.
- ⚠️ **5.0/5.1 latest** — newest release targets 5.0 or 5.1; usually runs on 5.2 within the
  same major series, but **must be tested** (per policy).
- ❌ **No 5.x** — no release supports any Moodle 5.x; will very likely fatal on PHP 8.3 / 5.2.
  Keep only if testing passes; otherwise port or replace.

---

## Group 1 — Native 5.2 release exists (low risk: update + move to `public/`)

| Component | Installed | 5.2 release | Declares |
|---|---|---|---|
| theme_boost_union | 2024060106 (4.4-r6) | **v5.2-r6** (2026042008), 14 Jun 2026 | Moodle 5.2 |
| theme_moove | 2024082400 (4.4.3) | **5.2.0** (2026042000), 20 Apr 2026 | Moodle 5.2 |
| mod_unilabel | 2024050902 | **5.2** (2026051400), 14 May 2026 | 5.0 / 5.1 / 5.2 |
| mod_openmeetings | 2024011701 | **5.0.0** (2026040601), 6 Apr 2026 | …5.0 / 5.1 / 5.2 |
| block_completion_progress | 2024042200 | **2026042700**, 27 Apr 2026 | 4.5 / 5.0 / 5.1 / 5.2 |

## Group 2 — Latest targets 5.0/5.1 (update to latest, then test on 5.2)

| Component | Installed | Best release | Declares | Note |
|---|---|---|---|---|
| mod_offlinequiz | 2024071001 | v5.1.1 (2026012901), 10 Jun 2026 | 5.1 | AMC, actively maintained — 5.2 release likely soon |
| mod_journal | 2023091500 (4.2.1) | 5.1.2 (2026012100), 21 Jan 2026 | …5.0 / 5.1 | |
| mod_lightboxgallery | 2024112101 (4.0.9) | 4.5.3 (2026032500), 25 Mar 2026 | 4.5 / 5.0 / 5.1 | |
| assignsubmission_maharaws | 2025032100 | 2026020200, 2 Feb 2026 | 4.5 / 5.0 / 5.1 | |
| mod_subcourse | 2024072401 | 2025032001, 19 Feb 2026 | 4.5 / 5.0 | only reaches 5.0 |

## Group 3 — No Moodle 5.x release (high risk: test as-is, expect breakage)

These have **no release for any 5.x**. Installed versions are already at/near the newest
available. On PHP 8.3 / Moodle 5.2 they call long-removed APIs and will most likely fatal.

| Component | Installed | Newest available | Max Moodle | Action if test fails |
|---|---|---|---|---|
| mod_hvp | 2024120900 (1.27.2) | 1.28.1, 8 May 2026 | **4.5** | Replace with core **mod_h5pactivity** (official H5P); migrate content |
| block_exalib | 2022042100 (4.6.0) | 4.6.0 (latest) | **4.0** | Port or drop |
| enrol_invitation | 2024101300 (2.3.0) | 2.3.0 (latest) | **4.5** | Port or replace (other invitation enrol plugins exist) |
| mod_evoting | 2024042302 (v4.0) | v4.0 (latest) | **4.5** | Port or replace |
| mod_quizgame | 2022112200 (v4.1) | v4.1-r1 | **4.2** | Port or drop |
| block_slider | 2020011800 | 0.4.0 (2020051300) | **3.8** | Drop (theme/boost_union slideshow covers this) |
| filter_slider | 2020050300 (1.2) | 1.2 (latest) | **3.8** | Drop |
| mod_revealjs | 2018031900 (v0.9.4) | v0.9.4 (latest) | **3.4** | Drop or replace |
| mod_eduplayer | 2017022300 (1.3) | 1.3 (latest) | **3.2** | Likely dead — drop or replace |
| mod_scormcloud | 2016011201 | 2016011201 (latest) | **3.1** | Likely dead — drop or replace |

## Group 4 — Bespoke / in-house (no upstream)

| Component | Installed | Source | Action |
|---|---|---|---|
| tool_brcli | 2019031500 (v1.2) | none — in-house "Backup & Restore CLI" | Move to `public/admin/tool/brcli`; test against 5.2 backup/restore + CLI APIs; fix as needed |

## Group 5 — Delete (non-loadable fragments, feature removed from core)

Both are **incomplete merge debris** (subdirectories only, no `version.php`) — not loadable
plugins, and both were **removed from Moodle core**. Nothing functional to "keep".

| Path | Status | If feature still needed |
|---|---|---|
| mod/chat | fragment; `mod_chat` removed from core 5.2 | Fresh install of a maintained contrib `mod_chat` for 5.2 (verify one exists) |
| blocks/section_links | fragment (`db/` only); removed from core | Fresh install of a maintained contrib build if available |

---

## Summary

- **5 plugins** have a native 5.2 release → straightforward.
- **5 plugins** are at 5.0/5.1 → update + test on 5.2 (usually fine).
- **10 plugins** have **no 5.x release** → must be tested as-is and will likely need porting,
  replacement, or dropping. `mod_hvp` has a clear path: core `mod_h5pactivity`.
- **1 bespoke** (`tool_brcli`) → in-house test/port.
- **2 fragments** (`mod/chat`, `blocks/section_links`) → delete (not recoverable by moving).

The migration risk is concentrated entirely in Group 3. Groups 1/2/4 move into `public/`
cleanly; Group 5 is deletion.
