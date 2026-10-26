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
 * Admin setting: JSON array of Launch Pad links (legacy helper / shared validate).
 *
 * @package    theme_everon
 * @copyright  2026 Project Theme / Jason Dorey
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Validates and normalises Launch Pad link JSON on save.
 */
class theme_everon_admin_setting_launchpad_json extends admin_setting_configtextarea {

    /**
     * Validate and store the setting.
     *
     * @param string $data
     * @return string Empty string on success, or an error message.
     */
    public function write_setting($data) {
        global $CFG;
        require_once($CFG->dirroot . '/theme/everon/locallib.php');

        $data = trim((string) $data);
        if ($data === '') {
            return parent::write_setting('');
        }

        $decoded = json_decode($data, true);
        $clean = theme_everon_launchpad_normalize_links_list($decoded);
        if (is_string($clean)) {
            return $clean;
        }

        $encoded = $clean === []
            ? '[]'
            : json_encode($clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return parent::write_setting($encoded);
    }
}
