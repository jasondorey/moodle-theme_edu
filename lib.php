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
 * Theme Everon - Library
 *
 * @package    theme_everon
 * @copyright  2026 Project Theme / Jason Dorey
 * @copyright  2023 Daniel Poggenpohl <daniel.poggenpohl@fernuni-hagen.de> and Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Constants which are use throughout this theme.
define('THEME_EVERON_SETTING_INHERITANCE_INHERIT', 0);
define('THEME_EVERON_SETTING_INHERITANCE_DUPLICATE', 1);

/**
 * Returns the main SCSS content.
 *
 * @param \core\output\theme_config $theme The theme config object.
 * @return string
 */
function theme_everon_get_main_scss_content($theme) {
    global $CFG;

    // Require the necessary libraries.
    require_once($CFG->dirroot . '/theme/boost_union/lib.php');

    // As a start, get the compiled main SCSS from Boost Union.
    // This way, Everon will ship the same SCSS code as Boost Union itself.
    $scss = theme_boost_union_get_main_scss_content(\core\output\theme_config::load('boost_union'));

    // And add Everon's main SCSS file to the stack.
    $scss .= file_get_contents($CFG->dirroot . '/theme/everon/scss/post.scss');

    return $scss;
}

/**
 * Get SCSS to prepend.
 *
 * @param \core\output\theme_config $theme The theme config object.
 * @return string
 */
function theme_everon_get_pre_scss($theme) {
    global $CFG;

    // Require the necessary libraries.
    require_once($CFG->dirroot . '/theme/boost_union/lib.php');

    // As a start, initialize the Pre SCSS code with an empty string.
    $scss = '';

    // Then, if configured, get the compiled pre SCSS code from Boost Union.
    // This should not be necessary as Moodle core calls the *_get_pre_scss() functions from all parent themes as well.
    // However, as soon as Boost Union would use $theme->settings in this function, $theme would be this theme here and
    // not Boost Union. The Boost Union developers are aware of this topic, but faults can always happen.
    // If such a fault happens, the Everon administrator can switch the inheritance to 'Duplicate'.
    // This way, we will add the pre SCSS code with the explicit use of the Boost Union configuration to the stack.
    $inheritanceconfig = get_config('theme_everon', 'prescssinheritance');
    if ($inheritanceconfig == THEME_EVERON_SETTING_INHERITANCE_DUPLICATE) {
        $scss .= theme_boost_union_get_pre_scss(\core\output\theme_config::load('boost_union'));
    }

    // And add Everon's pre SCSS file to the stack.
    $scss .= file_get_contents($CFG->dirroot . '/theme/everon/scss/pre.scss');

    /**********************************************************
     * EXTENSION POINT:
     * Compose and add additional pre-SCSS code here.
     * It will be added on top of Boost Union's pre-SCSS code.
     *********************************************************/

    return $scss;
}

/**
 * Inject additional SCSS.
 *
 * @param \core\output\theme_config $theme The theme config object.
 * @return string
 */
function theme_everon_get_extra_scss($theme) {
    global $CFG;

    // Require the necessary libraries.
    require_once($CFG->dirroot . '/theme/boost_union/lib.php');

    // As a start, initialize the Extra SCSS code with an empty string.
    $scss = '';

    // Then, if configured, get the compiled extra SCSS code from Boost Union.
    // This should not be necessary as Moodle core calls the *_get_extra_scss() functions from all parent themes as well.
    // However, as soon as Boost Union would use $theme->settings in this function, $theme would be this theme here and
    // not Boost Union. The Boost Union developers are aware of this topic, but faults can always happen.
    // If such a fault happens, the Everon administrator can switch the inheritance to 'Duplicate'.
    // This way, we will add the extra SCSS code with the explicit use of the Boost Union configuration to the stack.
    $inheritanceconfig = get_config('theme_everon', 'extrascssinheritance');
    if ($inheritanceconfig == THEME_EVERON_SETTING_INHERITANCE_DUPLICATE) {
        $scss .= theme_boost_union_get_extra_scss(\core\output\theme_config::load('boost_union'));
    }

    /**********************************************************
     * EXTENSION POINT:
     * Compose and add additional SCSS code here.
     * It will be added on top of Boost Union's SCSS code.
     *********************************************************/

    // Course-card + dashboard block surfaces must compile into theme/styles.php (themerev).
    // Previously they were only $page->requires->css() links with no revision, so browsers
    // could keep a pre-dark-remap sheet that forced .block.card to #ffffff under Boost dark.
    foreach (['course_cards.css', 'dashboard_surfaces.css'] as $sheet) {
        $path = $CFG->dirroot . '/theme/everon/style/' . $sheet;
        if (is_readable($path)) {
            $scss .= "\n/* theme_everon/style/{$sheet} */\n" . file_get_contents($path);
        }
    }

    return $scss;
}

/**
 * Plugin version string used to cache-bust standalone Everon stylesheets.
 *
 * @return string
 */
function theme_everon_stylesheet_rev(): string {
    global $CFG;

    static $rev = null;
    if ($rev !== null) {
        return $rev;
    }

    $plugin = new stdClass();
    include($CFG->dirroot . '/theme/everon/version.php');
    $rev = (string) $plugin->version;
    return $rev;
}

/**
 * Theme-relative stylesheet URL with plugin version query for cache busting.
 *
 * @param string $relativepath Path under /theme/everon/ (e.g. style/dashboard.css).
 * @return \core\url
 */
function theme_everon_stylesheet_url(string $relativepath): \core\url {
    $relativepath = ltrim($relativepath, '/');
    return new \core\url('/theme/everon/' . $relativepath, ['rev' => theme_everon_stylesheet_rev()]);
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_everon_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    // No Everon setting fileareas currently (login panel image removed; use Boost Union login).
    send_file_not_found();
}

/**
 * Callback function for theme_boost_union to allow Everon to add cards to the Boost Union settings overview page.
 * This function is expected to return an array of arrays containing values with the keys 'label', 'desc', 'btn' and 'url'.
 *
 * @return array
 */
function theme_everon_extend_busettingsoverview() {

    $cards[] = [
        'label' => get_string('pluginname', 'theme_everon'),
        'desc' => get_string('settingsoverview_buc_desc', 'theme_everon'),
        'btn' => 'primary',
        'url' => new \core\url('/admin/settings.php', ['section' => 'theme_everon']),
    ];

    return $cards;
}

/**
 * Callback function which allows themes to alter the CSS URLs.
 * We use this function to change the CSS URL to the flavour CSS URL if a flavour applies to the current page.
 *
 * @copyright 2024 Alexander Bias <bias@alexanderbias.de>
 *
 * @param mixed $urls The CSS URLs (passed as reference).
 */
function theme_everon_alter_css_urls(&$urls) {
    global $CFG;

    // Require Boost Union library.
    require_once($CFG->dirroot . '/theme/boost_union/lib.php');

    // Call Boost Union's theme_boost_union_alter_css_urls() function which implements the logic to change the CSS URL for flavours.
    theme_boost_union_alter_css_urls($urls);
}

/**
 * Page init — load accessibility, Launch Pad, and Focus Mode assets when enabled.
 *
 * @param \moodle_page $page
 * @return void
 */
function theme_everon_page_init(\moodle_page $page): void {
    global $CFG;

    require_once($CFG->dirroot . '/theme/everon/locallib.php');

    $www = '/theme/everon';

    // Shared popover chrome + profile usermenu — always on with Everon (not login).
    if ($page->pagelayout !== 'login') {
        $page->requires->css(theme_everon_stylesheet_url('style/popover_chrome.css'));
        $page->requires->css(theme_everon_stylesheet_url('style/usermenu.css'));
        if (theme_everon_usermenutiles_enabled()) {
            $page->add_body_class('theme-everon-usermenu-tiles');
            $page->requires->css(theme_everon_stylesheet_url('style/usermenu_tiles.css'));
        }
    }

    // Skip login layout for navbar utilities (a11y / Launch Pad / Focus / layouts).
    if ($page->pagelayout === 'login') {
        return;
    }

    if (theme_everon_accessibility_enabled()) {
        // Fonts must load outside theme/styles.php so relative @font-face URLs resolve.
        $page->requires->css(theme_everon_stylesheet_url('fonts/opendyslexic.css'));
        $page->requires->css(theme_everon_stylesheet_url('style/accessibility.css'));
        $page->requires->js_call_amd('theme_everon/navbar_accessibility', 'init');
    }

    if (theme_everon_launchpad_should_show()) {
        $page->requires->css(theme_everon_stylesheet_url('style/launch_pad.css'));
        $page->requires->js_call_amd('theme_everon/launch_pad', 'init');
    }

    if (\theme_everon\focus_mode::should_offer($page)) {
        $courseid = \theme_everon\focus_mode::resolve_course_id($page);
        $page->add_body_class('theme-everon-focus-available');
        $page->requires->css(theme_everon_stylesheet_url('style/course_focus.css'));
        $page->requires->js($www . '/javascript/course_focus_styles.js');
        $page->requires->js_call_amd('theme_everon/course_focus', 'init', [$courseid]);
    }

    if (theme_everon_mycourses_should_offer($page)) {
        $page->add_body_class('theme-everon-mycourses');
        // course_cards.css is compiled via theme_everon_get_extra_scss() (themerev).
        $page->requires->css(theme_everon_stylesheet_url('style/my_courses.css'));
    }

    if (theme_everon_mycourses_stats_should_offer($page)) {
        $page->add_body_class('theme-everon-mycourses-stats');
        $page->requires->css(theme_everon_stylesheet_url('style/my_courses_stats.css'));
    }

    if (theme_everon_dashboard_should_offer($page)) {
        $page->add_body_class('theme-everon-dashboard');
        // course_cards.css + dashboard_surfaces.css compile via Extra SCSS (themerev).
        $page->requires->css(theme_everon_stylesheet_url('style/dashboard.css'));
    }

    if (theme_everon_compact_navbar_should_offer($page)) {
        $page->add_body_class('theme-everon-compact-navbar');
        $page->requires->css(theme_everon_stylesheet_url('style/navbar.css'));
    }
}
