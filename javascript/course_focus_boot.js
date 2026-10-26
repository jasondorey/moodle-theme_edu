/**
 * Restore Focus Mode before first paint (course content pages).
 * Loaded synchronously from theme_everon head — plain JS (no AMD).
 *
 * @package    theme_everon
 * @copyright  2026 Project Theme / Jason Dorey
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
(function(global) {
    'use strict';

    var HTML_CLASS = 'theme-everon-focus-mode';
    var STORAGE_PREFIX = 'theme_everon_focus_';
    var STORAGE_ACTIVE = 'theme_everon_focus_active';
    var COURSE_VIEW_PATH = /\/course\/view\.php(\/|$)/;
    var COURSE_SECTION_PATH = /\/course\/section\.php(\/|$)/;
    var MOD_ACTIVITY_PATH = /\/mod\/[^/]+\/(?!index\.php)/;

    function getActiveFocusCourseId() {
        try {
            var active = parseInt(global.localStorage.getItem(STORAGE_ACTIVE) || '0', 10);
            if (active > 1) {
                return active;
            }
            for (var i = 0; i < global.localStorage.length; i++) {
                var key = global.localStorage.key(i);
                if (key && key.indexOf(STORAGE_PREFIX) === 0 && global.localStorage.getItem(key) === '1') {
                    return parseInt(key.slice(STORAGE_PREFIX.length), 10);
                }
            }
        } catch (e) {
            // Ignore.
        }
        return 0;
    }

    function getCourseIdFromBodyClass() {
        var body = global.document.body;
        if (!body || !body.classList) {
            return 0;
        }
        for (var i = 0; i < body.classList.length; i++) {
            var match = /^course-(\d+)$/.exec(body.classList[i]);
            if (match) {
                var id = parseInt(match[1], 10);
                if (id > 1) {
                    return id;
                }
            }
        }
        return 0;
    }

    function getCourseIdFromPageConfig() {
        var pageconfig = global.themeEveronFocusPageConfig;
        if (pageconfig && pageconfig.courseId) {
            var fromPage = parseInt(String(pageconfig.courseId), 10);
            if (fromPage > 1) {
                return fromPage;
            }
        }
        return 0;
    }

    function getCourseIdFromBody() {
        var fromPage = getCourseIdFromPageConfig();
        if (fromPage > 1) {
            return fromPage;
        }
        var body = global.document.body;
        if (!body) {
            return 0;
        }
        var fromData = parseInt(body.getAttribute('data-theme-everon-focus-id') || '0', 10);
        if (fromData > 1) {
            return fromData;
        }
        var fromBodyClass = getCourseIdFromBodyClass();
        if (fromBodyClass > 1) {
            return fromBodyClass;
        }
        return 0;
    }

    function readStoredState(courseId) {
        try {
            return global.localStorage.getItem(STORAGE_PREFIX + courseId) === '1';
        } catch (e) {
            return false;
        }
    }

    function resolveCourseIdForRestore() {
        var fromBody = getCourseIdFromBody();
        if (fromBody > 1) {
            return fromBody;
        }
        var activeId = getActiveFocusCourseId();
        if (activeId > 1 && readStoredState(activeId)) {
            return activeId;
        }
        return 0;
    }

    function hasModActivityBodyClass(body) {
        for (var i = 0; i < body.classList.length; i++) {
            var className = body.classList[i];
            if (className.indexOf('path-mod-') === 0) {
                return true;
            }
            if (className.indexOf('path-') === 0 && className.indexOf('-mod-') !== -1) {
                return true;
            }
        }
        return false;
    }

    function shouldRestoreFocus() {
        var courseId = resolveCourseIdForRestore();
        if (courseId <= 1) {
            return false;
        }
        if (!readStoredState(courseId)) {
            return false;
        }
        var path = global.location.pathname;
        if (COURSE_VIEW_PATH.test(path) || COURSE_SECTION_PATH.test(path)) {
            return true;
        }
        var body = global.document.body;
        if (body && (hasModActivityBodyClass(body) || MOD_ACTIVITY_PATH.test(path))) {
            return true;
        }
        if (getCourseIdFromPageConfig() > 1) {
            return true;
        }
        return false;
    }

    function applyFocusClass() {
        if (!shouldRestoreFocus()) {
            return;
        }
        global.document.documentElement.classList.add(HTML_CLASS);
        if (global.document.body) {
            global.document.body.classList.add(HTML_CLASS);
        }
        if (typeof global.themeEveronSyncFocusLayoutStyles === 'function') {
            global.themeEveronSyncFocusLayoutStyles(true);
        }
    }

    applyFocusClass();
    if (global.document.readyState === 'loading') {
        global.document.addEventListener('DOMContentLoaded', applyFocusClass);
    }
    global.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            applyFocusClass();
        }
    });
})(typeof window !== 'undefined' ? window : this);
