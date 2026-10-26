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
 * Launch Pad navbar popover — presentational open/close only.
 *
 * @module     theme_everon/launch_pad
 * @copyright  2026 Project Theme / Jason Dorey
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'theme_everon/navbar_a11y_popovers',
], function(ClusterPopovers) {

    const CONTAINER_ID = 'nav-launchpad-popover-container';

    /**
     * @param {HTMLElement} container
     */
    const bindPopover = (container) => {
        const toggle = container.querySelector('[data-region="popover-region-toggle"]');
        const panel = container.querySelector('[data-region="popover-region-container"]');
        if (!toggle || !panel) {
            return;
        }

        const close = () => {
            container.classList.add('collapsed');
            toggle.setAttribute('aria-expanded', 'false');
            panel.setAttribute('aria-hidden', 'true');
        };

        const open = () => {
            ClusterPopovers.closeAllExcept(container);
            container.classList.remove('collapsed');
            toggle.setAttribute('aria-expanded', 'true');
            panel.setAttribute('aria-hidden', 'false');
            const firstLink = panel.querySelector('.everon-launchpad__tile');
            if (firstLink) {
                firstLink.focus();
            }
        };

        const isOpen = () => !container.classList.contains('collapsed');

        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            if (isOpen()) {
                close();
            } else {
                open();
            }
        });

        toggle.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                toggle.click();
            }
        });

        document.addEventListener('click', (event) => {
            if (!isOpen()) {
                return;
            }
            if (event.target.closest('#' + CONTAINER_ID)) {
                return;
            }
            close();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && isOpen()) {
                close();
                toggle.focus();
            }
        });
    };

    return {
        init: function() {
            ClusterPopovers.init();
            const container = document.getElementById(CONTAINER_ID);
            if (!container || container.dataset.everonLaunchpadBound === '1') {
                return;
            }
            container.dataset.everonLaunchpadBound = '1';
            bindPopover(container);
        },
    };
});
