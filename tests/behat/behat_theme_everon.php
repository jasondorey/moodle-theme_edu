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
 * Everon theme Behat steps for Boost colour-mode / dark-mode smoke coverage.
 *
 * @package    theme_everon
 * @category   test
 * @copyright  2026 Project Theme / Jason Dorey
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Mink\Exception\ExpectationException;

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Custom Behat steps for theme_everon.
 *
 * @package    theme_everon
 * @category   test
 * @copyright  2026 Project Theme / Jason Dorey
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_everon extends behat_base {

    /**
     * Assert the resolved Bootstrap colour mode on the document element.
     *
     * @Then /^the document Bootstrap colour mode should be "(?P<mode>light|dark)"$/
     * @param string $mode
     */
    public function the_document_bootstrap_colour_mode_should_be(string $mode): void {
        $this->wait_for_pending_js();
        $actual = $this->getSession()->evaluateScript(
            'return document.documentElement.getAttribute("data-bs-theme");'
        );
        if ($actual !== $mode) {
            throw new ExpectationException(
                'Expected data-bs-theme="' . $mode . '" but found ' . var_export($actual, true),
                $this->getSession()
            );
        }
    }

    /**
     * Open the Everon accessibility navbar popover.
     *
     * @When /^I open the Everon accessibility panel$/
     */
    public function i_open_the_everon_accessibility_panel(): void {
        $this->execute('behat_general::i_click_on', [
            '#nav-accessibility-popover-container [data-region="popover-region-toggle"]',
            'css_element',
        ]);
        $this->wait_for_pending_js();
    }

    /**
     * Open the Everon Launch Pad navbar popover.
     *
     * @When /^I open the Everon launch pad$/
     */
    public function i_open_the_everon_launch_pad(): void {
        $this->execute('behat_general::i_click_on', [
            '#nav-launchpad-popover-container [data-region="popover-region-toggle"]',
            'css_element',
        ]);
        $this->wait_for_pending_js();
    }

    /**
     * Open the Boost / Everon user menu in the navbar.
     *
     * @When /^I open the Everon user menu$/
     */
    public function i_open_the_everon_user_menu(): void {
        $this->execute('behat_general::i_click_on', [
            '#user-menu-toggle',
            'css_element',
        ]);
        $this->wait_for_pending_js();
    }

    /**
     * Click the accessibility Dark Mode tile (bridges to Boost colour modes).
     *
     * @When /^I toggle the Everon accessibility Dark Mode tile$/
     */
    public function i_toggle_the_everon_accessibility_dark_mode_tile(): void {
        $this->execute('behat_general::i_click_on', [
            '#nav-accessibility-popover-container [data-a11y-toggle="darkMode"]',
            'css_element',
        ]);
        $this->wait_for_pending_js();
    }
}
