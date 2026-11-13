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
 * Event observers for MuTMS compatibility.
 *
 * @package    theme_everon
 * @copyright  2026 Project Theme / Jason Dorey
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_everon\observer;

defined('MOODLE_INTERNAL') || die();

/**
 * Keep Everon tenant CSS sentinels in sync with MuTMS appearance changes.
 */
final class mutms {

    /**
     * After a tenant appearance update, refresh the Everon CSS sentinel.
     *
     * @param \core\event\base $event
     * @return void
     */
    public static function appearance_updated(\core\event\base $event): void {
        if (!\theme_everon\local\mutms::is_enabled()) {
            return;
        }
        $tenantid = (int) $event->objectid;
        if ($tenantid <= 0) {
            return;
        }
        \theme_everon\local\mutms::sync_tenant_sentinel($tenantid);
        // Theme caches are already reset by tool_mutenancy appearance save.
    }
}
