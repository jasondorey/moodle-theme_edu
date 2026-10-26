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
 * Focus Mode — wide layout with course navigation hidden.
 * FAB injected into Boost footer floats (no Mustache fork).
 *
 * @module     theme_everon/course_focus
 * @copyright  2026 Project Theme / Jason Dorey
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/str'], function(Str) {

    const SELECTORS = {
        button: '[data-action="everon-focus-toggle"]',
        page: '#page',
        pageContent: '#page-content',
        notice: '.theme-everon-focus-notice',
        floats: '[data-region="footer-container-popover"]',
        help: '.btn-footer-popover[data-action="footer-popover"]',
        unionButtons: '#boost-union-footer-buttons',
    };

    const UNION_VISIBLE_CLASS = 'theme-everon-union-footer-buttons--visible';

    const HTML_CLASS = 'theme-everon-focus-mode';
    const STORAGE_PREFIX = 'theme_everon_focus_';
    const STORAGE_ACTIVE = 'theme_everon_focus_active';
    const ACTIVE_CLASS = 'is-active';
    const BODY_AVAILABLE_CLASS = 'theme-everon-focus-available';
    const COURSE_VIEW_PATH = /\/course\/view\.php(\/|$)/;
    const COURSE_SECTION_PATH = /\/course\/section\.php(\/|$)/;
    const MOD_ACTIVITY_PATH = /\/mod\/[^/]+\/(?!index\.php)/;

    const storageKey = (courseId) => STORAGE_PREFIX + courseId;

    const readStoredState = (courseId) => {
        try {
            return localStorage.getItem(storageKey(courseId)) === '1';
        } catch (e) {
            return false;
        }
    };

    const writeStoredState = (courseId, enabled) => {
        try {
            localStorage.setItem(storageKey(courseId), enabled ? '1' : '0');
            if (enabled) {
                localStorage.setItem(STORAGE_ACTIVE, String(courseId));
            } else if (parseInt(localStorage.getItem(STORAGE_ACTIVE) || '0', 10) === courseId) {
                localStorage.removeItem(STORAGE_ACTIVE);
            }
        } catch (e) {
            // Ignore.
        }
    };

    const getActiveFocusCourseId = () => {
        try {
            const active = parseInt(localStorage.getItem(STORAGE_ACTIVE) || '0', 10);
            if (active > 1) {
                return active;
            }
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key && key.startsWith(STORAGE_PREFIX) && localStorage.getItem(key) === '1') {
                    return parseInt(key.slice(STORAGE_PREFIX.length), 10);
                }
            }
        } catch (e) {
            // Ignore.
        }
        return 0;
    };

    const getCourseIdFromPageConfig = () => {
        const pageconfig = window.themeEveronFocusPageConfig;
        if (pageconfig?.courseId) {
            const fromPage = parseInt(String(pageconfig.courseId), 10);
            if (fromPage > 1) {
                return fromPage;
            }
        }
        return 0;
    };

    const getCourseIdFromBodyClass = () => {
        const body = document.body;
        if (!body?.classList) {
            return 0;
        }
        for (const className of body.classList) {
            const match = /^course-(\d+)$/.exec(className);
            if (match) {
                const id = parseInt(match[1], 10);
                if (id > 1) {
                    return id;
                }
            }
        }
        return 0;
    };

    const hasModActivityBodyClass = (body) => {
        for (const className of body.classList) {
            if (className.startsWith('path-mod-')) {
                return true;
            }
            if (className.startsWith('path-') && className.includes('-mod-')) {
                return true;
            }
        }
        return false;
    };

    const isModActivityPath = () => {
        const body = document.body;
        const path = window.location.pathname;
        return (body && hasModActivityBodyClass(body)) || MOD_ACTIVITY_PATH.test(path);
    };

    const resolveCourseId = (courseIdFromInit = 0) => {
        const fromInit = parseInt(String(courseIdFromInit || '0'), 10);
        if (fromInit > 1) {
            return fromInit;
        }
        const fromPage = getCourseIdFromPageConfig();
        if (fromPage > 1) {
            return fromPage;
        }
        const bodyId = parseInt(document.body?.dataset.themeEveronFocusId || '0', 10);
        if (bodyId > 1) {
            return bodyId;
        }
        const fromBodyClass = getCourseIdFromBodyClass();
        if (fromBodyClass > 1) {
            return fromBodyClass;
        }
        const existing = document.querySelector(SELECTORS.button);
        if (existing?.dataset.courseid) {
            const fromButton = parseInt(existing.dataset.courseid, 10);
            if (fromButton > 1) {
                return fromButton;
            }
        }
        if (COURSE_VIEW_PATH.test(window.location.pathname)) {
            const fromUrl = parseInt(new URLSearchParams(window.location.search).get('id') || '0', 10);
            if (fromUrl > 1) {
                return fromUrl;
            }
        }
        const activeId = getActiveFocusCourseId();
        return activeId > 1 ? activeId : 0;
    };

    const isCourseContentPage = () => {
        if (getCourseIdFromPageConfig() > 1) {
            return true;
        }
        const body = document.body;
        if (!body) {
            return false;
        }
        if (body.classList.contains(BODY_AVAILABLE_CLASS)) {
            return true;
        }
        const path = window.location.pathname;
        if (COURSE_VIEW_PATH.test(path) || COURSE_SECTION_PATH.test(path)) {
            return true;
        }
        if (isModActivityPath()) {
            return resolveCourseId() > 1;
        }
        return false;
    };

    const shouldOfferFocus = (courseId) => {
        if (!isCourseContentPage()) {
            return false;
        }
        const resolved = courseId || resolveCourseId();
        return resolved > 1;
    };

    const closeDrawers = () => {
        document.querySelector(SELECTORS.page)?.classList.remove('show-drawer-left', 'show-drawer-right');
    };

    const isFocusModeActive = () => {
        return document.documentElement.classList.contains(HTML_CLASS)
            || document.body?.classList.contains(HTML_CLASS);
    };

    const syncFocusLayoutStyles = (enabled) => {
        if (typeof window.themeEveronSyncFocusLayoutStyles === 'function') {
            window.themeEveronSyncFocusLayoutStyles(enabled);
        }
    };

    const applyFocusMode = (enabled) => {
        document.documentElement.classList.toggle(HTML_CLASS, enabled);
        document.body?.classList.toggle(HTML_CLASS, enabled);
        syncFocusLayoutStyles(enabled);
        if (enabled) {
            closeDrawers();
        }
    };

    const updateButton = (button, enabled, enableLabel, disableLabel) => {
        button.classList.toggle(ACTIVE_CLASS, enabled);
        button.setAttribute('aria-pressed', enabled ? 'true' : 'false');
        button.setAttribute('aria-label', enabled ? disableLabel : enableLabel);
        button.setAttribute('title', enabled ? disableLabel : enableLabel);
        button.querySelector('.theme-everon-focus-btn__icon--off')?.toggleAttribute('hidden', enabled);
        button.querySelector('.theme-everon-focus-btn__icon--on')?.toggleAttribute('hidden', !enabled);
    };

    const updateNotice = (enabled, noticeText) => {
        const host = document.querySelector(SELECTORS.pageContent);
        if (!host) {
            return;
        }
        let notice = document.querySelector(SELECTORS.notice);
        if (!enabled) {
            notice?.remove();
            return;
        }
        if (!notice) {
            notice = document.createElement('div');
            notice.className = 'theme-everon-focus-notice';
            notice.setAttribute('role', 'status');
            host.prepend(notice);
        }
        notice.textContent = noticeText;
    };

    const createFocusButton = (courseId) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = [
            'btn',
            'btn-icon',
            'rounded-circle',
            'bg-secondary',
            'btn-footer-everon-focus',
            'theme-everon-focus-btn',
        ].join(' ');
        button.dataset.action = 'everon-focus-toggle';
        button.dataset.courseid = String(courseId);
        button.setAttribute('aria-pressed', 'false');
        button.innerHTML = [
            '<span class="theme-everon-focus-btn__icon theme-everon-focus-btn__icon--off" aria-hidden="true">',
            '<i class="fa fa-up-right-and-down-left-from-center" aria-hidden="true"></i>',
            '</span>',
            '<span class="theme-everon-focus-btn__icon theme-everon-focus-btn__icon--on" aria-hidden="true" hidden>',
            '<i class="fa fa-xmark" aria-hidden="true"></i>',
            '</span>',
        ].join('');
        return button;
    };

    /**
     * Fold Boost Union #boost-union-footer-buttons into the Everon flex stack and
     * show it only while at least one Union float (back-to-top / a11y) is visible.
     *
     * @param {Element} floats
     */
    const syncUnionFooterButtons = (floats) => {
        const union = floats.querySelector(SELECTORS.unionButtons)
            || document.querySelector(SELECTORS.unionButtons);
        if (!union) {
            return;
        }
        if (union.parentElement !== floats) {
            const focusBtn = floats.querySelector(SELECTORS.button);
            if (focusBtn) {
                floats.insertBefore(union, focusBtn);
            } else {
                floats.appendChild(union);
            }
        }

        const updateVisibility = () => {
            const visible = Array.from(union.querySelectorAll('button, a')).some((el) => {
                if (el.hidden) {
                    return false;
                }
                const style = window.getComputedStyle(el);
                return style.display !== 'none' && style.visibility !== 'hidden';
            });
            union.classList.toggle(UNION_VISIBLE_CLASS, visible);
        };

        updateVisibility();

        if (union.dataset.everonUnionSync === '1') {
            return;
        }
        union.dataset.everonUnionSync = '1';

        const observer = new MutationObserver(updateVisibility);
        observer.observe(union, {
            attributes: true,
            childList: true,
            subtree: true,
            attributeFilter: ['style', 'class', 'hidden', 'aria-hidden'],
        });
        window.addEventListener('scroll', updateVisibility, {passive: true});
        // Union injects #back-to-top asynchronously — recheck shortly after.
        window.setTimeout(updateVisibility, 0);
        window.setTimeout(updateVisibility, 350);
    };

    const ensureFocusButton = (courseIdFromInit = 0) => {
        const courseId = resolveCourseId(courseIdFromInit) || getActiveFocusCourseId();
        if (!shouldOfferFocus(courseId)) {
            return null;
        }
        const floats = document.querySelector(SELECTORS.floats);
        if (!floats) {
            return null;
        }
        let button = document.querySelector(SELECTORS.button);
        if (!button) {
            button = createFocusButton(courseId);
            const helpButton = floats.querySelector(SELECTORS.help);
            if (helpButton) {
                floats.insertBefore(button, helpButton);
            } else {
                floats.appendChild(button);
            }
        }
        floats.classList.add('theme-everon-footer-floats');
        syncUnionFooterButtons(floats);
        button.classList.add('bg-secondary');
        button.dataset.courseid = String(courseId);
        button.hidden = false;
        button.removeAttribute('aria-hidden');
        return button;
    };

    const bindFocusButton = (button, courseId) => {
        if (button.dataset.everonFocusBound === '1') {
            return;
        }
        button.dataset.everonFocusBound = '1';

        Promise.all([
            Str.get_string('focusmodeenable', 'theme_everon'),
            Str.get_string('focusmodedisable', 'theme_everon'),
            Str.get_string('focusmodenotice', 'theme_everon'),
        ]).then(([enableLabel, disableLabel, noticeText]) => {
            const applyState = (enabled) => {
                applyFocusMode(enabled);
                updateButton(button, enabled, enableLabel, disableLabel);
                updateNotice(enabled, noticeText);
            };
            const setState = (enabled) => {
                applyState(enabled);
                writeStoredState(courseId, enabled);
            };

            const enabled = readStoredState(courseId) || isFocusModeActive();
            if (enabled) {
                applyState(true);
            } else {
                updateButton(button, false, enableLabel, disableLabel);
            }

            button.addEventListener('click', (event) => {
                event.preventDefault();
                setState(!isFocusModeActive());
            });
        }).catch(() => {
            const enabled = readStoredState(courseId) || isFocusModeActive();
            if (enabled) {
                applyFocusMode(true);
            }
            button.classList.toggle(ACTIVE_CLASS, enabled);
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const next = !isFocusModeActive();
                applyFocusMode(next);
                writeStoredState(courseId, next);
                button.classList.toggle(ACTIVE_CLASS, next);
                updateNotice(next, 'Focus mode is on. Click × at the bottom right to turn it off.');
            });
        });
    };

    const restoreFocusOnLoad = (courseIdFromInit = 0) => {
        const courseId = resolveCourseId(courseIdFromInit);
        if (!courseId || courseId === 1) {
            return;
        }
        const fromInit = parseInt(String(courseIdFromInit || '0'), 10);
        const onContentPage = isCourseContentPage()
            || fromInit > 1
            || (isModActivityPath() && readStoredState(courseId));
        if (!onContentPage) {
            return;
        }
        if (readStoredState(courseId) || isFocusModeActive()) {
            applyFocusMode(true);
        }
    };

    /**
     * @param {number} [courseIdFromInit=0]
     */
    const init = (courseIdFromInit = 0) => {
        restoreFocusOnLoad(courseIdFromInit);

        const attempt = (retriesLeft) => {
            const button = ensureFocusButton(courseIdFromInit);
            if (!button) {
                if (retriesLeft > 0) {
                    window.setTimeout(() => attempt(retriesLeft - 1), 50);
                }
                return;
            }
            const courseId = parseInt(button.dataset.courseid || '0', 10);
            if (!courseId) {
                return;
            }
            bindFocusButton(button, courseId);
        };

        attempt(30);
    };

    return {init: init};
});
