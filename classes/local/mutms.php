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
 * MuTMS compatibility helpers for theme Everon.
 *
 * @package    theme_everon
 * @copyright  2026 Project Theme / Jason Dorey
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_everon\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Bridges MuTMS (especially tool_mutenancy tenant appearance) into Everon.
 *
 * MuTMS tenant Boost appearance writes overrides under theme_boost. When Everon is
 * the active theme, core looks up theme_everon settings and has_custom_css(everon).
 * This helper mirrors Boost SCSS settings onto Everon's runtime settings and keeps a
 * per-tenant sentinel override so tenant CSS cache keys work.
 */
final class mutms {

    /** Sentinel config name stored under theme_everon for tenant CSS cache busting. */
    public const SENTINEL_NAME = 'mutms_tenant_css';

    /** theme_boost settings that affect compiled CSS. */
    public const BOOST_CSS_SETTINGS = [
        'preset',
        'brandcolor',
        'scsspre',
        'scss',
        'backgroundimage',
        'loginbackgroundimage',
    ];

    /**
     * Whether MuTMS compatibility is enabled (admin setting).
     * Default: disabled when the setting has never been saved.
     *
     * @return bool
     */
    public static function is_enabled(): bool {
        $value = get_config('theme_everon', 'enablemutmscompatibility');
        if ($value === false || $value === null || $value === '') {
            return false;
        }
        return $value === 'yes' || $value === '1' || $value === 1;
    }

    /**
     * Whether tool_mutenancy is available and active.
     *
     * @return bool
     */
    public static function mutenancy_active(): bool {
        if (!function_exists('mutenancy_is_active')) {
            return false;
        }
        return mutenancy_is_active();
    }

    /**
     * Mirror tenant-aware theme_boost CSS settings onto the active Everon theme object.
     *
     * Parent theme SCSS callbacks receive the child theme_config, so brandcolour / SCSS
     * must live on $THEME->settings for Everon. Background image fileareas stay on
     * theme_boost; those are injected separately via {@see self::background_scss()}.
     *
     * @param object $theme theme_config or the raw $THEME stdClass from config.php
     * @return void
     */
    public static function mirror_boost_settings(object $theme): void {
        if (!self::is_enabled()) {
            return;
        }
        if (!isset($theme->settings) || !is_object($theme->settings)) {
            $theme->settings = new \stdClass();
        }
        if (!function_exists('mutenancy_get_config')) {
            // Without the MuTMS core patch, fall back to plain get_config.
            $boost = get_config('theme_boost');
        } else {
            $boost = mutenancy_get_config('theme_boost');
        }
        if (!is_object($boost)) {
            return;
        }
        foreach (['preset', 'brandcolor', 'scsspre', 'scss'] as $key) {
            if (isset($boost->$key) && $boost->$key !== '' && $boost->$key !== null) {
                $theme->settings->$key = $boost->$key;
            }
        }
    }

    /**
     * Extra SCSS for tenant Boost background images when Everon is active.
     *
     * Files are stored in the theme_boost filearea (tenant or system context). Using
     * Everon's setting_file_url would look in the wrong component.
     *
     * @return string
     */
    public static function background_scss(): string {
        if (!self::is_enabled() || !self::mutenancy_active()) {
            return '';
        }
        if (!class_exists(\tool_mutenancy\local\appearance::class)) {
            return '';
        }

        $scss = '';
        $bg = \tool_mutenancy\local\appearance::get_boost_setting_image_url('backgroundimage');
        if ($bg) {
            $url = $bg->out(false);
            $scss .= "@media (min-width: 768px) {\n";
            $scss .= "body { background-image: url('{$url}'); background-size: cover; }\n";
            $scss .= "}\n";
        }

        $loginbg = \tool_mutenancy\local\appearance::get_boost_setting_image_url('loginbackgroundimage');
        if ($loginbg) {
            $url = $loginbg->out(false);
            $scss .= "body.pagelayout-login #page .login-layout-left {\n";
            $scss .= "background-image: url('{$url}'); background-size: cover; background-position: center;\n";
            $scss .= "}\n";
        }

        return $scss;
    }

    /**
     * Sync theme_everon sentinel overrides for every tenant that has theme_boost CSS overrides.
     *
     * Called when the compatibility setting changes and after tenant appearance updates.
     *
     * @return void
     */
    public static function sync_all_tenant_sentinels(): void {
        if (!self::mutenancy_active()) {
            return;
        }
        if (!class_exists(\tool_mutenancy\local\tenant::class) ||
                !class_exists(\tool_mutenancy\local\config::class)) {
            return;
        }

        global $DB;
        if (!$DB->get_manager()->table_exists('tool_mutenancy_tenant')) {
            return;
        }
        $tenants = $DB->get_records('tool_mutenancy_tenant', null, 'id ASC', 'id');
        foreach ($tenants as $tenant) {
            self::sync_tenant_sentinel((int) $tenant->id);
        }
    }

    /**
     * Ensure theme_everon has a sentinel override iff theme_boost has CSS overrides.
     *
     * @param int $tenantid
     * @return void
     */
    public static function sync_tenant_sentinel(int $tenantid): void {
        if ($tenantid <= 0 || !class_exists(\tool_mutenancy\local\config::class)) {
            return;
        }

        $enabled = self::is_enabled();
        $boostoverrides = \tool_mutenancy\local\config::fetch_overrides($tenantid, 'theme_boost');
        $needs = false;
        if ($enabled && !empty($boostoverrides)) {
            foreach (self::BOOST_CSS_SETTINGS as $name) {
                if (array_key_exists($name, $boostoverrides)) {
                    $needs = true;
                    break;
                }
            }
        }

        if ($needs) {
            \tool_mutenancy\local\config::override($tenantid, self::SENTINEL_NAME, '1', 'theme_everon');
        } else {
            // Remove sentinel when compat is off or Boost has no CSS overrides.
            \tool_mutenancy\local\config::override($tenantid, self::SENTINEL_NAME, null, 'theme_everon');
        }
    }

    /**
     * Admin setting updatedcallback.
     *
     * @return void
     */
    public static function setting_updated(): void {
        self::sync_all_tenant_sentinels();
        theme_reset_all_caches();
    }
}
