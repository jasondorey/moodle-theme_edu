/**
 * Apply stored accessibility settings before first paint (avoids light flash when dark mode is on).
 * Loaded synchronously from theme_everon head — must stay plain JS (no AMD).
 */
(function(global) {
    'use strict';

    var STORAGE_KEY = 'theme_everon_a11y_v1';
    var HTML_CLASS = 'theme-everon-a11y-active';
    var CLASS_MAP = {
        highlightTitle: 'theme-everon-a11y-highlight-title',
        highlightLinks: 'theme-everon-a11y-highlight-links',
        dyslexiaFont: 'theme-everon-a11y-dyslexia',
        letterSpacing: 'theme-everon-a11y-letter-spacing',
        lineHeight: 'theme-everon-a11y-line-height',
        fontWeight: 'theme-everon-a11y-font-weight',
        darkContrast: 'theme-everon-a11y-dark-contrast',
        lightContrast: 'theme-everon-a11y-light-contrast',
        highContrast: 'theme-everon-a11y-high-contrast',
        highSaturation: 'theme-everon-a11y-high-saturation',
        lowSaturation: 'theme-everon-a11y-low-saturation',
        monochrome: 'theme-everon-a11y-monochrome',
        darkMode: 'theme-everon-a11y-dark-mode',
        readingGuide: 'theme-everon-a11y-reading-guide-on',
        stopAnimations: 'theme-everon-a11y-stop-animations',
        bigCursor: 'theme-everon-a11y-big-cursor'
    };
    var DEFAULTS = {
        fontSize: 100,
        highlightTitle: false,
        highlightLinks: false,
        dyslexiaFont: false,
        letterSpacing: false,
        lineHeight: false,
        fontWeight: false,
        darkContrast: false,
        lightContrast: false,
        highContrast: false,
        highSaturation: false,
        lowSaturation: false,
        monochrome: false,
        darkMode: false,
        readingGuide: false,
        stopAnimations: false,
        bigCursor: false
    };

    /**
     * @returns {object}
     */
    function loadState() {
        try {
            var raw = global.localStorage.getItem(STORAGE_KEY);
            if (!raw) {
                return Object.assign({}, DEFAULTS);
            }
            return Object.assign({}, DEFAULTS, JSON.parse(raw));
        } catch (e) {
            return Object.assign({}, DEFAULTS);
        }
    }

    /**
     * @param {object} state
     */
    function applyState(state) {
        var html = document.documentElement;
        html.classList.add(HTML_CLASS);
        html.style.setProperty('--theme-everon-a11y-font-scale', String(state.fontSize / 100));

        Object.keys(CLASS_MAP).forEach(function(key) {
            var className = CLASS_MAP[key];
            if (state[key]) {
                html.classList.add(className);
            } else {
                html.classList.remove(className);
            }
        });
    }

    function applyStored() {
        applyState(loadState());
    }

    global.themeEveronA11y = {
        STORAGE_KEY: STORAGE_KEY,
        CLASS_MAP: CLASS_MAP,
        DEFAULTS: DEFAULTS,
        loadState: loadState,
        applyState: applyState,
        applyStored: applyStored
    };

    applyStored();
}(window));
