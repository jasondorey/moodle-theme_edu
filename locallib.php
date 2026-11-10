<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme Everon - Local library
 *
 * @package    theme_everon
 * @copyright  2026 Project Theme / Jason Dorey
 * @copyright  2023 Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Whether the Everon accessibility toolbar is enabled (admin setting).
 * Default: enabled when the setting has never been saved.
 *
 * @return bool
 */
function theme_everon_accessibility_enabled(): bool {
    $value = get_config('theme_everon', 'enableaccessibilitytool');
    if ($value === false || $value === null || $value === '') {
        return true;
    }
    return $value === 'yes' || $value === '1' || $value === 1;
}

/**
 * Inline head markup: apply stored a11y settings before first paint.
 * Ported from theme_custom (GPL).
 *
 * @return string
 */
function theme_everon_get_a11y_boot_html(): string {
    global $CFG;

    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $bootfile = $CFG->dirroot . '/theme/everon/javascript/a11y_boot.js';
    $js = is_readable($bootfile) ? file_get_contents($bootfile) : '';
    if ($js === false) {
        $js = '';
    }

    // Dark/light paint is owned by Boost colour modes (data-bs-theme). Do not inject
    // critical CSS for the retired theme-everon-a11y-dark-mode path.
    $cached = '';
    if ($js !== '') {
        $cached .= '<script id="theme-everon-a11y-boot">' . $js . '</script>';
    }

    return $cached;
}

/**
 * Template context for the accessibility popover (Boost colour-mode bridge metadata).
 *
 * @return array
 */
function theme_everon_accessibility_popover_context(): array {
    $context = [
        'colourmodeenabled' => false,
        'colourmodecookieattributes' => '',
    ];

    if (!class_exists(\theme_boost\colour_mode::class)) {
        return $context;
    }

    if (!\theme_boost\colour_mode::is_enabled()) {
        return $context;
    }

    $context['colourmodeenabled'] = true;
    $context['colourmodecookieattributes'] = \theme_boost\colour_mode::get_cookie_attributes();
    return $context;
}

/**
 * Whether Everon Focus Mode is enabled (admin setting).
 * Default: enabled when the setting has never been saved.
 *
 * @return bool
 */
function theme_everon_focusmode_enabled(): bool {
    $value = get_config('theme_everon', 'enablefocusmode');
    if ($value === false || $value === null || $value === '') {
        return true;
    }
    return $value === 'yes' || $value === '1' || $value === 1;
}

/**
 * Whether the Everon My Courses layout skin is enabled (admin setting).
 * Default: enabled when the setting has never been saved.
 *
 * @return bool
 */
function theme_everon_mycourses_layout_enabled(): bool {
    $value = get_config('theme_everon', 'enablemycourseslayout');
    if ($value === false || $value === null || $value === '') {
        return true;
    }
    return $value === 'yes' || $value === '1' || $value === 1;
}

/**
 * Whether the My Courses layout should load on this page.
 *
 * @param \moodle_page $page
 * @return bool
 */
function theme_everon_mycourses_should_offer(\moodle_page $page): bool {
    if (!theme_everon_mycourses_layout_enabled()) {
        return false;
    }
    return $page->pagelayout === 'mycourses';
}

/**
 * Whether a Yes/No Everon config is enabled (default Yes when unset).
 *
 * @param string $name Config name without plugin prefix.
 * @return bool
 */
function theme_everon_config_yes_default(string $name): bool {
    $value = get_config('theme_everon', $name);
    if ($value === false || $value === null || $value === '') {
        return true;
    }
    return $value === 'yes' || $value === '1' || $value === 1;
}

/**
 * Whether the Everon My Courses stats strip is enabled (admin master setting).
 * Default: enabled when the setting has never been saved.
 *
 * @return bool
 */
function theme_everon_mycourses_stats_enabled(): bool {
    return theme_everon_config_yes_default('enablemycoursesstats');
}

/**
 * Whether a specific My Courses stats panel is enabled.
 *
 * @param string $panel One of enrolments, completions, badges, certificates.
 * @return bool
 */
function theme_everon_mycourses_stats_panel_enabled(string $panel): bool {
    $map = [
        'enrolments' => 'mycoursesstatsenrolments',
        'completions' => 'mycoursesstatscompletions',
        'badges' => 'mycoursesstatsbadges',
        'certificates' => 'mycoursesstatscertificates',
    ];
    if (!isset($map[$panel])) {
        return false;
    }
    return theme_everon_config_yes_default($map[$panel]);
}

/**
 * Whether the My Courses stats strip should load on this page.
 *
 * @param \moodle_page $page
 * @return bool
 */
function theme_everon_mycourses_stats_should_offer(\moodle_page $page): bool {
    if (!theme_everon_mycourses_stats_enabled()) {
        return false;
    }
    if ($page->pagelayout !== 'mycourses') {
        return false;
    }
    if (!isloggedin() || isguestuser()) {
        return false;
    }
    foreach (['enrolments', 'completions', 'badges', 'certificates'] as $panel) {
        if (theme_everon_mycourses_stats_panel_enabled($panel)) {
            return true;
        }
    }
    return false;
}

/**
 * Count active (in-progress) enrolled courses for a user.
 *
 * @param int $userid
 * @return int
 */
function theme_everon_mycourses_stats_count_active_courses(int $userid): int {
    global $CFG;

    require_once($CFG->libdir . '/enrollib.php');
    require_once($CFG->dirroot . '/course/lib.php');
    require_once($CFG->libdir . '/completionlib.php');

    if ($userid <= 0) {
        return 0;
    }

    $user = \core_user::get_user($userid, '*', MUST_EXIST);
    $courses = enrol_get_all_users_courses($userid, true);
    $count = 0;
    foreach ($courses as $course) {
        if (course_classify_for_timeline($course, $user) === COURSE_TIMELINE_INPROGRESS) {
            $count++;
        }
    }
    return $count;
}

/**
 * Count completed courses for a user (course_completions.timecompleted > 0).
 *
 * @param int $userid
 * @return int
 */
function theme_everon_mycourses_stats_count_completions(int $userid): int {
    global $DB;

    if ($userid <= 0) {
        return 0;
    }

    return (int) $DB->count_records_select(
        'course_completions',
        'userid = ? AND timecompleted IS NOT NULL AND timecompleted > 0',
        [$userid]
    );
}

/**
 * Count badges awarded to a user.
 *
 * @param int $userid
 * @return int
 */
function theme_everon_mycourses_stats_count_badges(int $userid): int {
    global $DB;

    if ($userid <= 0) {
        return 0;
    }

    return (int) $DB->count_records('badge_issued', ['userid' => $userid]);
}

/**
 * Count certificates awarded to a user (soft-detect plugin backends).
 *
 * @param int $userid
 * @return int
 */
function theme_everon_mycourses_stats_count_certificates(int $userid): int {
    global $DB;

    if ($userid <= 0) {
        return 0;
    }

    $dbman = $DB->get_manager();

    if (get_config('tool_certificate', 'version')) {
        $table = new \xmldb_table('tool_certificate_issues');
        if ($dbman->table_exists($table)) {
            return (int) $DB->count_records('tool_certificate_issues', ['userid' => $userid]);
        }
    }

    if (get_config('mod_customcert', 'version')) {
        $table = new \xmldb_table('customcert_issues');
        if ($dbman->table_exists($table)) {
            return (int) $DB->count_records('customcert_issues', ['userid' => $userid]);
        }
    }

    return 0;
}

/**
 * Mustache context for the My Courses stats strip, or null when nothing to render.
 *
 * @param int|null $userid Defaults to current user.
 * @return array|null
 */
function theme_everon_mycourses_stats_template_context(?int $userid = null): ?array {
    global $USER;

    $userid = $userid ?? (int) $USER->id;
    if ($userid <= 0 || !isloggedin() || isguestuser($userid)) {
        return null;
    }
    if (!theme_everon_mycourses_stats_enabled()) {
        return null;
    }

    $panels = [];
    $definitions = [
        'enrolments' => [
            'id' => 'enrolments',
            'titlestring' => 'mycoursesstatsenrolmentstitle',
            'substring' => 'mycoursesstatsenrolmentssub',
            'iconclass' => 'fa fa-clipboard-list fa-fw',
            'countfn' => 'theme_everon_mycourses_stats_count_active_courses',
        ],
        'completions' => [
            'id' => 'completions',
            'titlestring' => 'mycoursesstatscompletionstitle',
            'substring' => 'mycoursesstatscompletionssub',
            'iconclass' => 'fa fa-clipboard-check fa-fw',
            'countfn' => 'theme_everon_mycourses_stats_count_completions',
        ],
        'badges' => [
            'id' => 'badges',
            'titlestring' => 'mycoursesstatsbadgestitle',
            'substring' => 'mycoursesstatsbadgessub',
            'iconclass' => 'fa fa-medal fa-fw',
            'countfn' => 'theme_everon_mycourses_stats_count_badges',
        ],
        'certificates' => [
            'id' => 'certificates',
            'titlestring' => 'mycoursesstatscertificatestitle',
            'substring' => 'mycoursesstatscertificatessub',
            'iconclass' => 'fa fa-certificate fa-fw',
            'countfn' => 'theme_everon_mycourses_stats_count_certificates',
        ],
    ];

    foreach ($definitions as $key => $def) {
        if (!theme_everon_mycourses_stats_panel_enabled($key)) {
            continue;
        }
        $count = (int) call_user_func($def['countfn'], $userid);
        $panels[] = [
            'id' => $def['id'],
            'title' => get_string($def['titlestring'], 'theme_everon'),
            'subtext' => get_string($def['substring'], 'theme_everon', $count),
            'count' => $count,
            'iconclass' => $def['iconclass'],
        ];
    }

    if ($panels === []) {
        return null;
    }

    // Mark all but the last panel so Mustache can render dividers between neighbours only.
    $last = count($panels) - 1;
    foreach ($panels as $i => &$panel) {
        $panel['hasdivider'] = ($i < $last);
    }
    unset($panel);

    return [
        'arialabel' => get_string('mycoursesstatsaria', 'theme_everon'),
        'panels' => $panels,
        'haspanels' => true,
    ];
}

/**
 * Whether the Everon Dashboard layout skin is enabled (admin setting).
 * Default: enabled when the setting has never been saved.
 *
 * @return bool
 */
function theme_everon_dashboard_layout_enabled(): bool {
    $value = get_config('theme_everon', 'enabledashboardlayout');
    if ($value === false || $value === null || $value === '') {
        return true;
    }
    return $value === 'yes' || $value === '1' || $value === 1;
}

/**
 * Whether the Dashboard layout should load on this page.
 *
 * @param \moodle_page $page
 * @return bool
 */
function theme_everon_dashboard_should_offer(\moodle_page $page): bool {
    if (!theme_everon_dashboard_layout_enabled()) {
        return false;
    }
    return $page->pagelayout === 'mydashboard';
}

/**
 * Whether Everon compact navbar is enabled (admin setting).
 * Default: enabled when the setting has never been saved.
 *
 * @return bool
 */
function theme_everon_compact_navbar_enabled(): bool {
    $value = get_config('theme_everon', 'enablecompactnavbar');
    if ($value === false || $value === null || $value === '') {
        return true;
    }
    return $value === 'yes' || $value === '1' || $value === 1;
}

/**
 * Whether compact navbar CSS should load (all non-login layouts when enabled).
 *
 * @param \moodle_page $page
 * @return bool
 */
function theme_everon_compact_navbar_should_offer(\moodle_page $page): bool {
    if (!theme_everon_compact_navbar_enabled()) {
        return false;
    }
    return $page->pagelayout !== 'login';
}

/**
 * Inline head markup: restore Focus Mode before first paint on course content pages.
 *
 * @return string
 */
function theme_everon_get_focusmode_boot_html(): string {
    global $CFG, $PAGE;

    static $jscached = null;
    if ($jscached === null) {
        $bootfile = $CFG->dirroot . '/theme/everon/javascript/course_focus_boot.js';
        $js = is_readable($bootfile) ? file_get_contents($bootfile) : '';
        $jscached = ($js === false || $js === '') ? '' : $js;
    }

    if ($jscached === '') {
        return '';
    }

    $html = '';
    if (!empty($PAGE) && $PAGE->context) {
        $courseid = \theme_everon\focus_mode::resolve_course_id($PAGE);
        if ($courseid > 1 && \theme_everon\focus_mode::is_course_content_page($PAGE)) {
            $html .= '<script id="theme-everon-focus-boot-config">window.themeEveronFocusPageConfig='
                . json_encode(['courseId' => $courseid], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)
                . ';</script>';
        }
    }

    $html .= '<script id="theme-everon-focus-boot">' . $jscached . '</script>';
    return $html;
}

/**
 * Whether the Everon Launch Pad is enabled (admin setting).
 * Default: disabled until configured.
 *
 * @return bool
 */
function theme_everon_launchpad_enabled(): bool {
    $value = get_config('theme_everon', 'enablelaunchpad');
    return $value === 'yes' || $value === '1' || $value === 1;
}

/**
 * Built-in pack keys (always available in settings UI).
 *
 * @return string[]
 */
function theme_everon_launchpad_builtin_keys(): array {
    return ['siteadmin'];
}


/**
 * Default Site administrators Launch Pad links (Moodle 5.2 paths).
 *
 * @return array<int, array{label:string, url:string, icon?:string}>
 */
function theme_everon_launchpad_default_siteadmin_links(): array {
    return [
        ['label' => 'Browse list of users', 'url' => '/admin/user.php', 'icon' => 'fa-users'],
        ['label' => 'Manage courses and categories', 'url' => '/course/management.php', 'icon' => 'fa-graduation-cap'],
        ['label' => 'Plugins overview', 'url' => '/admin/plugins.php', 'icon' => 'fa-puzzle-piece'],
        ['label' => 'Performance overview', 'url' => '/report/performance/index.php', 'icon' => 'fa-gauge-high'],
        ['label' => 'Custom reports', 'url' => '/reportbuilder/index.php', 'icon' => 'fa-table'],
        ['label' => 'Debugging', 'url' => '/admin/settings.php?section=debugging', 'icon' => 'fa-bug'],
        ['label' => 'Scheduled tasks', 'url' => '/admin/tool/task/scheduledtasks.php', 'icon' => 'fa-clock'],
    ];
}


/**
 * Normalise a list of link objects (shared by settings validation).
 *
 * @param mixed $decoded
 * @return array|string Clean link list, or error string.
 */
function theme_everon_launchpad_normalize_links_list($decoded) {
    if (!is_array($decoded)) {
        return get_string('launchpadjsoninvalid', 'theme_everon');
    }
    if ($decoded !== [] && array_keys($decoded) !== range(0, count($decoded) - 1)) {
        return get_string('launchpadjsoninvalid', 'theme_everon');
    }

    $clean = [];
    foreach ($decoded as $item) {
        if (!is_array($item)) {
            return get_string('launchpadjsoninvalid', 'theme_everon');
        }
        $label = isset($item['label']) ? trim((string) $item['label']) : '';
        $url = isset($item['url']) ? trim((string) $item['url']) : '';
        if ($label === '' || $url === '') {
            return get_string('launchpadjsoninvalid', 'theme_everon');
        }
        $entry = [
            'label' => $label,
            'url' => $url,
        ];
        if (!empty($item['icon'])) {
            $entry['icon'] = trim((string) $item['icon']);
        }
        $clean[] = $entry;
    }
    return $clean;
}

/**
 * Normalise the full role-links map for storage.
 *
 * @param array $decoded
 * @return array|string Clean map, or error string.
 */
function theme_everon_launchpad_normalize_rolelinks_map(array $decoded) {
    $clean = [];

    // Preserve insertion order; always emit builtins first if present / empty.
    $orderedkeys = [];
    foreach (theme_everon_launchpad_builtin_keys() as $builtin) {
        $orderedkeys[] = $builtin;
    }
    foreach (array_keys($decoded) as $key) {
        if (in_array($key, $orderedkeys, true)) {
            continue;
        }
        $orderedkeys[] = $key;
    }

    foreach ($orderedkeys as $key) {
        if ($key === 'siteadmin') {
            $raw = $decoded[$key] ?? [];
            if ($raw === '' || $raw === null) {
                $raw = [];
            }
            if (is_string($raw)) {
                $raw = json_decode($raw, true);
            }
            $links = theme_everon_launchpad_normalize_links_list($raw === null ? [] : $raw);
            if (is_string($links)) {
                return $links;
            }
            $clean[$key] = $links;
            continue;
        }
        // Drop legacy authenticated pack key if still present in posted data.
        if ($key === 'authenticated') {
            continue;
        }
        if (!preg_match('/^role:(\d+)$/', $key, $m)) {
            // Skip unknown keys (_order, legacy, etc.).
            continue;
        }
        if (!array_key_exists($key, $decoded)) {
            continue;
        }
        $raw = $decoded[$key];
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        $links = theme_everon_launchpad_normalize_links_list($raw === null ? [] : $raw);
        if (is_string($links)) {
            return $links;
        }
        $clean[$key] = $links;
    }

    return $clean;
}

/**
 * Decode stored map JSON into an array (builtins guaranteed).
 *
 * @param string $raw
 * @return array<string, array>
 */
function theme_everon_launchpad_decode_rolelinks_map(string $raw): array {
    $decoded = [];
    if (trim($raw) !== '') {
        $tmp = json_decode($raw, true);
        if (is_array($tmp)) {
            $decoded = $tmp;
        }
    }
    $map = [];
    foreach (theme_everon_launchpad_builtin_keys() as $builtin) {
        $map[$builtin] = [];
        if (isset($decoded[$builtin]) && is_array($decoded[$builtin])) {
            $norm = theme_everon_launchpad_normalize_links_list($decoded[$builtin]);
            $map[$builtin] = is_array($norm) ? $norm : [];
        }
    }
    foreach ($decoded as $key => $links) {
        if (!preg_match('/^role:(\d+)$/', (string) $key)) {
            continue;
        }
        if (!is_array($links)) {
            continue;
        }
        $norm = theme_everon_launchpad_normalize_links_list($links);
        $map[$key] = is_array($norm) ? $norm : [];
    }
    return $map;
}

/**
 * One-time migration from MVP launchpadlinks_* settings into launchpadrolelinks.
 *
 * @return void
 */
function theme_everon_launchpad_migrate_mvp_if_needed(): void {
    $done = get_config('theme_everon', 'launchpadrolelinks_migrated');
    if ($done === '1' || $done === 1) {
        return;
    }

    $existing = get_config('theme_everon', 'launchpadrolelinks');
    if (is_string($existing) && trim($existing) !== '' && trim($existing) !== '{}') {
        set_config('launchpadrolelinks_migrated', '1', 'theme_everon');
        return;
    }

    $map = [
        'siteadmin' => theme_everon_launchpad_default_siteadmin_links(),
    ];

    $raw = get_config('theme_everon', 'launchpadlinks_siteadmin');
    if (is_string($raw) && trim($raw) !== '') {
        $decoded = json_decode($raw, true);
        $norm = theme_everon_launchpad_normalize_links_list(is_array($decoded) ? $decoded : []);
        if (is_array($norm) && $norm !== []) {
            $map['siteadmin'] = $norm;
        }
    }
    // Legacy launchpadlinks_authenticated is intentionally dropped (no authenticated pack).

    // Best-effort: manager MVP JSON → first manager-archetype role pack.
    $mgrraw = get_config('theme_everon', 'launchpadlinks_manager');
    if (is_string($mgrraw) && trim($mgrraw) !== '') {
        $decoded = json_decode($mgrraw, true);
        $norm = theme_everon_launchpad_normalize_links_list(is_array($decoded) ? $decoded : []);
        if (is_array($norm) && $norm !== []) {
            $managerroles = get_archetype_roles('manager');
            if (!empty($managerroles)) {
                $first = reset($managerroles);
                $map['role:' . (int) $first->id] = $norm;
            }
        }
    }

    $encoded = json_encode($map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    set_config('launchpadrolelinks', $encoded === false ? '{}' : $encoded, 'theme_everon');
    set_config('launchpadrolelinks_migrated', '1', 'theme_everon');
}

/**
 * Roles available in the Add role dropdown (exclude guest + frontpage).
 *
 * @return array<int, stdClass> role id => role object with localname
 */
function theme_everon_launchpad_addable_roles(): array {
    $roles = role_get_names(\context_system::instance());
    $out = [];
    foreach ($roles as $role) {
        if (in_array($role->shortname, ['guest', 'frontpage'], true)) {
            continue;
        }
        $out[(int) $role->id] = $role;
    }
    return $out;
}

/**
 * Role ids assigned to the user in any context.
 *
 * @param int $userid
 * @return int[]
 */
function theme_everon_launchpad_user_role_ids(int $userid): array {
    global $DB;

    if ($userid <= 0) {
        return [];
    }
    $ids = $DB->get_fieldset_sql(
        'SELECT DISTINCT roleid FROM {role_assignments} WHERE userid = ?',
        [$userid]
    );
    return array_map('intval', $ids ?: []);
}

/**
 * Drop legacy authenticated pack from stored map if still present.
 *
 * @return void
 */
function theme_everon_launchpad_drop_authenticated_pack_if_needed(): void {
    $raw = get_config('theme_everon', 'launchpadrolelinks');
    if (!is_string($raw) || trim($raw) === '') {
        return;
    }
    $decoded = json_decode($raw, true);
    if (!is_array($decoded) || !array_key_exists('authenticated', $decoded)) {
        return;
    }
    unset($decoded['authenticated']);
    $clean = theme_everon_launchpad_normalize_rolelinks_map($decoded);
    if (is_string($clean)) {
        unset($decoded['authenticated']);
        $clean = $decoded;
        if (!isset($clean['siteadmin']) || !is_array($clean['siteadmin'])) {
            $clean = ['siteadmin' => []] + $clean;
        }
    }
    $encoded = json_encode($clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    set_config('launchpadrolelinks', $encoded === false ? '{"siteadmin":[]}' : $encoded, 'theme_everon');
}

/**
 * Load the configured role-links map (after migration).
 *
 * @return array<string, array>
 */
function theme_everon_launchpad_get_rolelinks_map(): array {
    theme_everon_launchpad_migrate_mvp_if_needed();
    theme_everon_launchpad_drop_authenticated_pack_if_needed();
    $raw = get_config('theme_everon', 'launchpadrolelinks');
    return theme_everon_launchpad_decode_rolelinks_map(is_string($raw) ? $raw : '');
}

/**
 * Pack keys that match the given user, in merge order.
 * siteadmin (if admin) → configured role packs the user holds (any context).
 *
 * @param int|null $userid
 * @return string[]
 */
function theme_everon_launchpad_matching_pack_keys(?int $userid = null): array {
    global $USER;

    $userid = $userid ?? (int) $USER->id;
    if (!isloggedin() || isguestuser($userid)) {
        return [];
    }

    $map = theme_everon_launchpad_get_rolelinks_map();
    $matched = [];

    if (is_siteadmin($userid) && array_key_exists('siteadmin', $map)) {
        $matched[] = 'siteadmin';
    }

    $userroles = theme_everon_launchpad_user_role_ids($userid);
    $userroleslookup = array_fill_keys($userroles, true);
    foreach ($map as $key => $unused) {
        if (!preg_match('/^role:(\d+)$/', $key, $m)) {
            continue;
        }
        $rid = (int) $m[1];
        if (isset($userroleslookup[$rid])) {
            $matched[] = $key;
        }
    }

    return $matched;
}

/**
 * Sanitize an optional Font Awesome icon class token (e.g. fa-cog).
 *
 * @param string $icon
 * @return string
 */
function theme_everon_launchpad_sanitize_icon(string $icon): string {
    $icon = trim($icon);
    if ($icon === '') {
        return '';
    }
    $parts = preg_split('/\s+/', $icon) ?: [];
    $kept = [];
    foreach ($parts as $part) {
        if (preg_match('/^fa(-[a-z0-9]+)+$/i', $part)) {
            $kept[] = strtolower($part);
        }
    }
    if ($kept === []) {
        return '';
    }
    $glyph = '';
    foreach ($kept as $part) {
        if ($part !== 'fa' && str_starts_with($part, 'fa-')) {
            $glyph = $part;
            break;
        }
    }
    return $glyph;
}

/**
 * Resolve a configured URL to a same-site absolute moodle URL, or null if disallowed.
 *
 * @param string $raw
 * @return \core\url|null
 */
function theme_everon_launchpad_sanitize_url(string $raw): ?\core\url {
    global $CFG;

    $raw = trim($raw);
    if ($raw === '') {
        return null;
    }

    $lower = strtolower($raw);
    if (str_starts_with($lower, 'javascript:') || str_starts_with($lower, 'data:') ||
            str_starts_with($lower, 'vbscript:')) {
        return null;
    }

    if (str_starts_with($raw, '//')) {
        return null;
    }

    $wwwroot = rtrim($CFG->wwwroot, '/');

    if (preg_match('#^https?://#i', $raw)) {
        $candidate = rtrim($raw, '/');
        if ($candidate === $wwwroot || str_starts_with($raw, $wwwroot . '/')) {
            return new \core\url($raw);
        }
        return null;
    }

    if (!str_starts_with($raw, '/')) {
        return null;
    }

    if (str_contains($raw, '\\') || preg_match('#^[a-z][a-z0-9+.-]*:#i', $raw)) {
        return null;
    }

    return new \core\url($raw);
}

/**
 * Sanitise a raw link list from the map into renderable links.
 *
 * @param array $items
 * @return array<int, array{label:string, url:string, icon:string, hasicon:bool}>
 */
function theme_everon_launchpad_sanitize_link_items(array $items): array {
    $links = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $label = isset($item['label']) ? trim(clean_param((string) $item['label'], PARAM_TEXT)) : '';
        $urlraw = isset($item['url']) ? trim((string) $item['url']) : '';
        if ($label === '' || $urlraw === '') {
            continue;
        }
        $url = theme_everon_launchpad_sanitize_url($urlraw);
        if ($url === null) {
            continue;
        }
        $icon = theme_everon_launchpad_sanitize_icon(isset($item['icon']) ? (string) $item['icon'] : '');
        $links[] = [
            'label' => $label,
            'url' => $url->out(false),
            'icon' => $icon,
            'hasicon' => $icon !== '',
        ];
    }
    return $links;
}

/**
 * Merged, deduped (by URL) links for the current user.
 *
 * @param int|null $userid
 * @return array<int, array{label:string, url:string, icon:string, hasicon:bool}>
 */
function theme_everon_launchpad_get_links_for_user(?int $userid = null): array {
    $keys = theme_everon_launchpad_matching_pack_keys($userid);
    if ($keys === []) {
        return [];
    }

    $map = theme_everon_launchpad_get_rolelinks_map();
    $merged = [];
    $seen = [];
    foreach ($keys as $key) {
        if (!isset($map[$key]) || !is_array($map[$key])) {
            continue;
        }
        foreach (theme_everon_launchpad_sanitize_link_items($map[$key]) as $link) {
            if (isset($seen[$link['url']])) {
                continue;
            }
            $seen[$link['url']] = true;
            $merged[] = $link;
        }
    }
    return $merged;
}

/**
 * Whether the Launch Pad icon should render for the current user.
 *
 * @return bool
 */
function theme_everon_launchpad_should_show(): bool {
    if (!theme_everon_launchpad_enabled()) {
        return false;
    }
    if (!isloggedin() || isguestuser()) {
        return false;
    }
    return theme_everon_launchpad_get_links_for_user() !== [];
}

/**
 * Template context for the Launch Pad popover.
 *
 * @return array|null Null when the control should not render.
 */
function theme_everon_launchpad_template_context(): ?array {
    if (!theme_everon_launchpad_should_show()) {
        return null;
    }
    $links = theme_everon_launchpad_get_links_for_user();
    return [
        'links' => $links,
        'haslinks' => $links !== [],
    ];
}




/**
 * Whether Everon profile usermenu tiles are enabled (admin setting).
 * Default: enabled when the setting has never been saved.
 *
 * @return bool
 */
function theme_everon_usermenutiles_enabled(): bool {
    return theme_everon_config_yes_default('enableusermenutiles');
}

/**
 * Font Awesome free solid class map for known usermenu titleidentifiers.
 *
 * @return array<string, string> Map of titleidentifier => fa-* class (without leading "fa ").
 */
function theme_everon_usermenu_tile_icon_map(): array {
    return [
        'profile,moodle' => 'fa-circle-user',
        'grades,grades' => 'fa-chart-column',
        'calendar,core_calendar' => 'fa-calendar-days',
        'privatefiles,moodle' => 'fa-folder-open',
        'reports,core_reportbuilder' => 'fa-table',
        'preferences,moodle' => 'fa-gear',
        'switchroleto,moodle' => 'fa-people-arrows',
        'switchrolereturn,moodle' => 'fa-rotate-left',
        'logout,moodle' => 'fa-right-from-bracket',
    ];
}

/**
 * Resolve a free FA icon class for one usermenu item.
 *
 * @param array|object $item Menu item (link / submenu-link / divider).
 * @param string $languagetitle Localised "Language" string for submenu detection.
 * @return string FA class token such as fa-link (no leading "fa ").
 */
function theme_everon_usermenu_tile_icon_for_item($item, string $languagetitle): string {
    $item = (array) $item;
    // Core marks the penultimate item with divider=true while it remains a link
    // (list-mode separator). Only pure divider nodes should skip icon mapping.
    $ispuredivider = (($item['itemtype'] ?? '') === 'divider')
        || (!empty($item['divider']) && empty($item['link']) && empty($item['submenulink'])
            && empty($item['url']) && empty($item['title']));
    if ($ispuredivider) {
        return 'fa-link';
    }

    $id = isset($item['titleidentifier']) ? (string) $item['titleidentifier'] : '';
    $map = theme_everon_usermenu_tile_icon_map();
    if ($id !== '' && isset($map[$id])) {
        return $map[$id];
    }

    $issubmenu = !empty($item['submenulink']) || (($item['itemtype'] ?? '') === 'submenu-link');
    if ($issubmenu) {
        $title = isset($item['title']) ? (string) $item['title'] : '';
        if ($title === $languagetitle || $id === 'language,moodle') {
            return 'fa-globe';
        }
        // Other submenu entries (e.g. Union smart menus).
        return 'fa-folder';
    }

    return 'fa-link';
}

/**
 * Enrich user_action_menu_items template context for Everon tiles (or leave list mode).
 *
 * @param array|object $context Mustache context.
 * @return array
 */
function theme_everon_usermenu_prepare_action_menu_context($context): array {
    if (is_object($context)) {
        $context = (array) $context;
    }
    if (!is_array($context)) {
        $context = [];
    }

    $tiles = theme_everon_usermenutiles_enabled();
    $context['everontiles'] = $tiles;

    if (!$tiles || empty($context['items']) || !is_array($context['items'])) {
        return $context;
    }

    $languagetitle = get_string('language');
    $enriched = [];
    foreach ($context['items'] as $item) {
        $asobj = is_object($item);
        $row = $asobj ? clone $item : (object) $item;

        // Dividers are omitted from the tile grid (Mustache skips them when everontiles).
        $row->faicon = theme_everon_usermenu_tile_icon_for_item($row, $languagetitle);
        $row->hasfaicon = empty($row->pixicon) && empty($row->imgsrc);

        $enriched[] = $asobj ? $row : (array) $row;
    }
    $context['items'] = $enriched;

    return $context;
}

/***************************************************************
 * EXTENSION POINT:
 * Add whatever Everon local functions you need here.
 **************************************************************/
