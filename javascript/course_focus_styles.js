/**
 * Injected Focus Mode layout CSS (fallback when aggregated theme CSS is stale).
 *
 * @package    theme_everon
 * @copyright  2026 Project Theme / Jason Dorey
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
(function(global) {
    'use strict';

    var STYLE_ID = 'theme-everon-focus-live-css';
    var HTML_CLASS = 'theme-everon-focus-mode';

    var CSS = [
        'html.' + HTML_CLASS + ' .secondary-navigation,',
        'body.' + HTML_CLASS + ' .secondary-navigation,',
        'html.' + HTML_CLASS + ' .tertiary-navigation,',
        'body.' + HTML_CLASS + ' .tertiary-navigation,',
        'html.' + HTML_CLASS + ' .drawer-toggles,',
        'body.' + HTML_CLASS + ' .drawer-toggles,',
        'html.' + HTML_CLASS + ' .drawer-toggler,',
        'body.' + HTML_CLASS + ' .drawer-toggler{display:none!important}',
        'html.' + HTML_CLASS + ' #page.drawers,',
        'body.' + HTML_CLASS + ' #page.drawers{margin-left:0!important;margin-right:0!important}',
        'body.' + HTML_CLASS + '.limitedwidth #page.drawers .main-inner,',
        'body.' + HTML_CLASS + '.limitedwidth.uses-drawers #page.drawers .main-inner,',
        'html.' + HTML_CLASS + ' body.limitedwidth #page.drawers .main-inner,',
        'html.' + HTML_CLASS + ' body.limitedwidth.uses-drawers #page.drawers .main-inner,',
        'body.' + HTML_CLASS + ' #page.drawers .main-inner,',
        'html.' + HTML_CLASS + ' #page.drawers .main-inner{',
        'max-width:min(92rem,calc(100vw - 2.5rem))!important;width:100%!important;',
        'margin-left:auto!important;margin-right:auto!important}',
        'body.' + HTML_CLASS + '.limitedwidth .header-maxwidth,',
        'html.' + HTML_CLASS + ' body.limitedwidth .header-maxwidth,',
        'body.' + HTML_CLASS + ' .header-maxwidth,',
        'html.' + HTML_CLASS + ' .header-maxwidth{',
        'max-width:min(92rem,calc(100vw - 2.5rem))!important;',
        'padding-left:0!important;padding-right:0!important}'
    ].join('');

    function syncFocusLayoutStyles(enabled) {
        var style = document.getElementById(STYLE_ID);
        if (enabled) {
            if (!style) {
                style = document.createElement('style');
                style.id = STYLE_ID;
                style.textContent = CSS;
                document.head.appendChild(style);
            }
            return;
        }
        if (style) {
            style.remove();
        }
    }

    function isFocusModeActive() {
        return document.documentElement.classList.contains(HTML_CLASS)
            || (document.body && document.body.classList.contains(HTML_CLASS));
    }

    global.themeEveronSyncFocusLayoutStyles = syncFocusLayoutStyles;

    if (isFocusModeActive()) {
        syncFocusLayoutStyles(true);
    }
})(typeof window !== 'undefined' ? window : this);
