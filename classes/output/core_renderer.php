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
 * Everon core renderer — inject accessibility toolbar and Launch Pad into navbar utilities.
 *
 * @package    theme_everon
 * @copyright  2026 Project Theme / Jason Dorey
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_everon\output;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../locallib.php');

/**
 * Extends Boost Union's renderer so Everon can append navbar utilities
 * (accessibility, launch pad) without replacing the whole navbar template.
 */
class core_renderer extends \theme_boost_union\output\core_renderer {

    /**
     * Append Everon navbar utilities after parent plugin output.
     * Order: … messages → accessibility → launch pad → (user menu follows in template).
     *
     * @return string
     */
    public function navbar_plugin_output() {
        $output = parent::navbar_plugin_output();
        if (\theme_everon_accessibility_enabled()) {
            $output .= $this->render_from_template('theme_everon/accessibility_popover', []);
        }
        $launchctx = \theme_everon_launchpad_template_context();
        if ($launchctx !== null) {
            $output .= $this->render_from_template('theme_everon/launch_pad_popover', $launchctx);
        }
        return $output;
    }

    /**
     * Inject early a11y / Focus Mode boot CSS/JS in head (avoids flash).
     *
     * @return string
     */
    public function standard_head_html() {
        $output = '';
        if (\theme_everon_accessibility_enabled()) {
            $output .= \theme_everon_get_a11y_boot_html();
        }
        if (\theme_everon_focusmode_enabled()) {
            $output .= \theme_everon_get_focusmode_boot_html();
        }
        $output .= parent::standard_head_html();
        return $output;
    }

    /**
     * Append My Courses stats strip after the page heading when enabled.
     * Order: page title → stats → Course overview (main content).
     *
     * @return string
     */
    public function full_header() {
        $header = parent::full_header();
        if (!\theme_everon_mycourses_stats_should_offer($this->page)) {
            return $header;
        }
        $ctx = \theme_everon_mycourses_stats_template_context();
        if ($ctx === null) {
            return $header;
        }
        return $header . $this->render_from_template('theme_everon/mycourses_stats', $ctx);
    }

    /**
     * Enrich core usermenu action items for Everon tile mode (icons + everontiles flag).
     *
     * @param string $templatename Template name
     * @param mixed $context Context
     * @return string
     */
    public function render_from_template($templatename, $context) {
        // Usermenu is usually a layout partial (not rendered via this method directly).
        // Enrich the usermenu node on any template context that carries it, and also
        // when core/user_menu or core/user_action_menu_items are rendered standalone.
        if (is_array($context) && isset($context['usermenu']) && is_array($context['usermenu'])) {
            $context['usermenu'] = \theme_everon_usermenu_prepare_action_menu_context($context['usermenu']);
        }
        if ($templatename === 'core/user_menu' || $templatename === 'core/user_action_menu_items') {
            $context = \theme_everon_usermenu_prepare_action_menu_context($context);
        }
        return parent::render_from_template($templatename, $context);
    }

}
