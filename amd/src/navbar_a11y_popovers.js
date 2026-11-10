// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
define([
    'jquery',
    'core/pubsub',
    'core_message/message_drawer_events',
    'theme_boost/bootstrap/dropdown',
], function($, PubSub, MessageDrawerEvents, DropdownMod) {

    const Dropdown = DropdownMod && DropdownMod.default ? DropdownMod.default : DropdownMod;

    const USERNAV = '#usernavigation';
    const POPOVER_SELECTOR = USERNAV + ' .popover-region';
    const MESSAGE_CONTAINER_ID = 'nav-message-popover-container';
    const MESSAGE_DRAWER_SELECTOR = '[data-region="right-hand-drawer"]';
    const USER_MENU_TOGGLE_ID = 'user-menu-toggle';
    const USER_ACTION_MENU_ID = 'user-action-menu';

    let mutexBound = false;

    const closePopoverRegion = (region) => {
        if (!region || region.classList.contains('collapsed')) {
            return;
        }
        region.classList.add('collapsed');
        const toggle = region.querySelector('[data-region="popover-region-toggle"]');
        const panel = region.querySelector('[data-region="popover-region-container"]');
        if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
        }
        if (panel) {
            panel.setAttribute('aria-hidden', 'true');
        }
        // Synchronous so a focus trap (accessibility panel) can release before the next menu focuses.
        region.dispatchEvent(new CustomEvent('theme_everon:popoverregionclosed'));
    };

    const closeMessageDrawer = () => {
        const drawer = document.querySelector(MESSAGE_DRAWER_SELECTOR + ' [data-region="message-drawer"]');
        const drawerRoot = drawer ? drawer.closest(MESSAGE_DRAWER_SELECTOR) : null;
        if (drawerRoot && !drawerRoot.classList.contains('hidden')) {
            PubSub.publish(MessageDrawerEvents.HIDE);
        }
        const container = document.getElementById(MESSAGE_CONTAINER_ID);
        if (container) {
            const toggle = container.querySelector('[data-region="popover-region-toggle"]');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'false');
            }
        }
    };

    /**
     * Close Everon / Union navbar popover-regions (a11y, Launch Pad, notifications, etc.).
     * Does not touch the Bootstrap profile usermenu.
     *
     * @param {HTMLElement|string|null} exceptRegion Region (or id) to keep open.
     */
    const closeNavbarPopoversExcept = (exceptRegion) => {
        const exceptEl = typeof exceptRegion === 'string' ?
            document.getElementById(exceptRegion) :
            exceptRegion;

        const messageContainer = document.getElementById(MESSAGE_CONTAINER_ID);
        const keepMessage = exceptEl && (
            exceptEl === messageContainer ||
            (messageContainer && messageContainer.contains(exceptEl))
        );

        document.querySelectorAll(POPOVER_SELECTOR).forEach((region) => {
            if (exceptEl && (region === exceptEl || exceptEl.contains(region))) {
                return;
            }
            closePopoverRegion(region);
        });

        if (!keepMessage) {
            closeMessageDrawer();
        }
    };

    /**
     * Hide the core Bootstrap profile usermenu if it is open.
     */
    const closeUserMenu = () => {
        const toggle = document.getElementById(USER_MENU_TOGGLE_ID);
        const menu = document.getElementById(USER_ACTION_MENU_ID);
        if (!toggle) {
            return;
        }
        const isOpen = (menu && menu.classList.contains('show')) ||
            toggle.getAttribute('aria-expanded') === 'true';
        if (!isOpen) {
            return;
        }
        try {
            const instance = Dropdown.getInstance(toggle) || Dropdown.getOrCreateInstance(toggle);
            instance.hide();
            return;
        } catch (e) {
            // Fall through to toggle click.
        }
        toggle.click();
    };

    /**
     * Close every other navbar overlay except the given popover-region.
     * Always closes the profile usermenu (a11y/Launch Pad open with stopPropagation,
     * so Bootstrap would not see an outside click).
     *
     * @param {HTMLElement|string|null} exceptRegion
     */
    const closeAllExcept = (exceptRegion) => {
        closeNavbarPopoversExcept(exceptRegion);
        closeUserMenu();
    };

    /**
     * When the profile usermenu opens, close a11y / Launch Pad / message drawer.
     */
    const bindUserMenuMutex = () => {
        if (mutexBound) {
            return;
        }
        mutexBound = true;

        document.addEventListener('show.bs.dropdown', (event) => {
            const target = event.target;
            if (!(target instanceof Element)) {
                return;
            }
            if (target.id === USER_MENU_TOGGLE_ID || target.closest('.usermenu')) {
                closeNavbarPopoversExcept(null);
            }
        });
    };

    return {
        closeAllExcept: closeAllExcept,
        closeUserMenu: closeUserMenu,
        closeNavbarPopoversExcept: closeNavbarPopoversExcept,
        init: function() {
            bindUserMenuMutex();
        },
    };
});
