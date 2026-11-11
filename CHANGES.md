moodle-theme_everon
==============================

Changes
-------

### Rolling release

* 2026-11-11 - Dashboard / Course overview / Timeline surfaces follow Boost `data-bs-theme="dark"` (Everon block + course-card tokens); keep light mode and navbar popovers unchanged (v5.3-r8)
* 2026-11-11 - Accessibility Content Adjustments panel: force dark tile/header/font-bar paints under Boost `data-bs-theme="dark"` (button UA overrides); keep light mode and usermenu/Launch Pad unchanged (v5.3-r7)
* 2026-11-11 - Navbar popovers (user menu tiles, Launch Pad, accessibility panel) follow Boost `data-bs-theme="dark"` via shared popover chrome tokens (v5.3-r6)
* 2026-11-11 - Navbar utility icons match Moodle 5.3 outline weight: starred courses `i/star` → `i/star-o` (`fa-regular fa-star`); a11y + Launch Pad keep universal-access / location-crosshairs metaphors as outline SVGs (FA Free has no regular glyphs) (v5.3-r5)
* 2026-11-10 - Accessibility Dark Mode tile drives Boost colour modes (`data-bs-theme` / `theme_boost_colourmode`); retire Everon `theme-everon-a11y-dark-mode` CSS path; hide Boost colour-mode navbar menu (v5.3-r4)
* 2026-11-10 - Accessibility navbar panel: move focus into the panel on open, matching notification FocusLock; return focus to the toggle on Escape only (v5.3-r3)
* 2026-11-10 - Remove the forced Noto Sans default so Everon uses the parent theme font (v5.3-r2)
* 2026-11-10 - Branch starts Moodle 5.3 support (v5.3-r1)
* 2026-10-22 - Default font: Noto Sans replaces Rubik (bundled WOFF2 400/500/600/700, OFL; `fonts/noto-sans.css`; Pre-SCSS `$font-family-sans-serif` / `$font-family-base`) (v5.2-r36)
* 2026-10-22 - Launch Pad navbar icon: FA Free `location-crosshairs` (`icon fa fa-location-crosshairs fa-fw`); was `circle-chevron-down` (v5.2-r35)
* 2026-10-22 - Profile usermenu tiles: 3-col Launch Pad–style grid (FA icons by titleidentifier); setting enableusermenutiles default Yes; Off restores vertical list (v5.2-r34)
* 2026-10-22 - Remove course content density entirely (setting, Course tab, CSS, helpers) — restore stock Union/Boost course content look; keep Dashboard surfaces + compact navbar (v5.2-r33)
* 2026-10-22 - Modernization pack: course content density (Course tab); Dashboard block surfaces + empty states (with Dashboard layout); compact navbar (Navbar tab, Union logo path documented) (v5.2-r32)
* 2026-10-22 - Course cards: denser body — stop card-body flex-grow empty band; ⋮ beside title; collapse footer; tighter padding (v5.2-r31)
* 2026-10-22 - Recently accessed courses: constrain carousel card width to match overview grid (fix mega-cards from shared width:100%) (v5.2-r30)
* 2026-10-22 - My Courses stats strip: place after page title (append in full_header), before Course overview (v5.2-r29)
* 2026-10-22 - Recently accessed courses: same circular on-image progress as Course overview (Mustache + shared CSS) (v5.2-r28)
* 2026-10-22 - Navbar menus: mutual exclusive open — a11y/Launch Pad close Bootstrap usermenu and vice versa (v5.2-r27)
* 2026-10-22 - Focus Mode FAB: fold Union `#boost-union-footer-buttons` into Everon float stack (help → Focus → back-to-top); no Union feature disable (v5.2-r26)
* 2026-10-22 - Profile usermenu: match a11y/Launch Pad panel chrome (light grey, orange border, caret, Rubik list hover); shared `popover_chrome.css` tokens (v5.2-r25)
* 2026-10-22 - Course cards: conic-gradient circular progress ring (accurate percent arc on image) (v5.2-r24)
* 2026-10-22 - Dashboard: widen Outside (right) to 28% of shell (Union 28% + Everon flex lock) (v5.2-r22)
* 2026-10-22 - My Courses stats strip: Enrolments / Completions / Badges / Certificates panels; master + per-panel Everon settings (v5.2-r21)
* 2026-10-22 - Course cards: circular on-image progress tracker (replace linear bar); shared My Courses + Dashboard (v5.2-r20)
* 2026-10-22 - Dashboard: align Outside (right) top with main content (below page-header) (v5.2-r19)
* 2026-10-22 - Rubik as site-wide default font: bundled WOFF2 (400/500/600/700, OFL), `fonts/rubik.css` `@font-face`, Pre-SCSS `$font-family-sans-serif` / `$font-family-base` (v5.2-r18)
* 2026-10-22 - Dashboard shell: center main+Outside (right) wrapper with margin-inline auto (match My Courses) (v5.2-r17)
* 2026-10-22 - Dashboard layout: Union Outside (right) + widen /my/ + shared Course overview card CSS with My Courses; Everon setting default Yes (v5.2-r16)
* 2026-10-22 - My Courses layout: wider shell + 1–5 column card grid skin for block_myoverview; progress-bar Mustache; Everon setting default Yes (v5.2-r15)
* 2026-10-22 - Remove Everon split login (Login tab, panel image settings/SCSS/pix); login falls through to Boost Union only (v5.2-r14)
* 2026-10-21 - Focus Mode: course-content FAB (enter/exit), banner, hide nav/drawers, widen layout; Everon setting default Yes (v5.2-r13)
* 2026-10-21 - Launch Pad panel restyle: a11y-aligned 3-column white tiles on light gray panel (v5.2-r12)
* 2026-10-21 - Launch Pad: default Site administrators pack (7 admin shortcuts for Moodle 5.2) (v5.2-r11)
* 2026-10-21 - Launch Pad settings UX: drop Authenticated pack; unified configured packs list; Site administrators default-only built-in (v5.2-r10)
* 2026-10-21 - Launch Pad dynamic role packs: built-in siteadmin/authenticated + Add role UI; migrate MVP JSON (v5.2-r9)
* 2026-10-21 - Launch Pad navbar icon: FA Free `circle-chevron-down` (`icon fa fa-circle-chevron-down fa-fw`); removed grip-dots polyfill (v5.2-r8)
* 2026-10-21 - Launch Pad navbar icon: Font Awesome classic solid grip-dots (`fa fa-grip-dots`; CSS polyfill — Pro glyph not in Moodle FA Free) (v5.2-r7)
* 2026-10-21 - Launch Pad Phase A MVP: navbar grid icon, siteadmin/manager/authenticated JSON profiles, merge+dedupe, default off (v5.2-r6)
* 2026-10-21 - Accessibility toolbar ported from theme_custom (navbar icon + panel, localStorage prefs, Everon setting to enable/disable) (v5.2-r5)
* 2026-10-21 - Full Phase 1 visual rollback: empty SCSS boilerplate, remove Look-tab tokens + all Mustache overrides; Everon is a clean Union child again (v5.2-r4)
* 2026-10-21 - Revert Phase 1 Mustache/template overrides (progress-bar + login copies) (v5.2-r3)
* 2026-10-21 - Phase 1 Norse-like polish (later rolled back): dark header, cards, Look tokens, progress-bar Mustache (v5.2-r2)
* 2026-06-14 - Tests: Support Behat scenarios which reference 'Boost Union' directly as string
* 2026-06-13 - Update moodle-plugin-ci.yml to match Boost Union's state
* 2026-01-03 - Add example branch which shows how to modify a mustache template from Boost Union.
* 2025-10-15 - Make codechecker happy again
* 2025-10-15 - Tests: Switch Github actions workflows to reusable workflows by Moodle an Hochschulen e.V.
* 2025-05-25 - Raise version and dependency to Boost Union 5.0
* 2025-05-25 - Change Moodle-Plugin-CI to run on Moodle Core 5.0
* 2025-05-25 - Adopt the screenshot for the theme overview page from Boost Union.
* 2025-03-30 - Add tertiary navigation to settings page after improvemeht in Boost Union, resolves #7, raise the dependency of Boost Union 4.5 likewise.
* 2024-12-29 - Support Boost Union's feature to set brand colors (and other SCSS-based things) in flavours
* 2024-10-22 - Adopt Boost Union class name changes from Boost Union 4.5, raise the dependency to Boost Union 4.5 likewise.
* 2024-10-22 - Change Moodle-Plugin-CI to run on Moodle Core 4.5
* 2024-08-11 - Updated Moodle Plugin CI to latest upstream recommendations
* 2024-07-25 - Development: Rename master branch to main, please update your clones.
* 2024-07-23 - Change Moodle-Plugin-CI to run on Moodle Core 4.4 and Boost Union 4.4
* 2024-07-23 - Make codechecker happy again.
* 2024-04-06 - Adopt Everon settings to the settings changes in the upcoming Boost Union v4.3-r11 release and for Moodle 4.4.
* 2024-03-13 - Make sure that all Boost Union settings are added to the $THEME object even if the Boost Union config was already included in some other place, resolves #3.
* 2024-02-23 - Add example branch which shows how to modify a mustache template from Moodle core.
* 2024-02-23 - Add example branch which shows how to allow the admin to configure a SCSS variable with an admin setting.
* 2024-02-20 - Cleanup and enhance the codebase for adoption and publication by Moodle an Hochschulen e.V.
* 2024-01-10 - Add Github actions
* 2023-09-15 - Initial boilerplate by Daniel Poggenpohl
