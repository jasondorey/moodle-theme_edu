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
 * Theme Everon - Version file
 *
 * Boost Union child theme for Project Theme (Jason Dorey).
 * Scaffolded from moodle-theme_boost_union_child.
 *
 * @package    theme_everon
 * @copyright  2026 Project Theme / Jason Dorey
 * @copyright  2023 Daniel Poggenpohl <daniel.poggenpohl@fernuni-hagen.de> and Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'theme_everon';
$plugin->version = 2026111005;
$plugin->release = 'v5.3-r6';
$plugin->requires = 2026100500; // Moodle 5.3.
$plugin->supported = [503, 503];
$plugin->maturity = MATURITY_STABLE;
// Pinned to Boost Union v5.2-r13 (https://github.com/moodle-an-hochschulen/moodle-theme_boost_union).
$plugin->dependencies = ['theme_boost_union' => 2026042019];
