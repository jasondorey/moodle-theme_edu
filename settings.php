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
 * Theme Everon - Settings file
 *
 * @package    theme_everon
 * @copyright  2023 Daniel Poggenpohl <daniel.poggenpohl@fernuni-hagen.de> and Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_boost_union\admin_settingspage_tabs_with_tertiary;

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig || has_capability('theme/boost_union:configure', context_system::instance())) {
    // How this file works:
    // Boost Union's settings are divided into multiple settings pages which resides in its own settings category.
    // You will understand it as soon as you look at /theme/boost_union/settings.php.
    // This settings file here is built in a way that it adds another settings page to this existing settings
    // category. You can add all child-theme-specific settings to this settings page here.

    // However, there is still the $settings variable which is expected by Moodle core to be filled with the theme
    // settings and which is automatically linked from the theme selector page.
    // To avoid that there appears a broken "Everon" settings page, we redirect the user to a settings
    // overview page if he opens this page.
    $mainsettingspageurl = new \core\url('/admin/settings.php', ['section' => 'themesettingeveron']);
    if ($ADMIN->fulltree && $PAGE->has_set_url() && $PAGE->url->compare($mainsettingspageurl)) {
        redirect(new \core\url('/admin/settings.php', ['section' => 'theme_everon']));
    }

    // Create empty settings page structure to make the site administration work on non-admin pages.
    if (!$ADMIN->fulltree) {
        // Create Everon settings page
        // (and allow users with the theme/boost_union:configure capability to access it).
        $tab = new admin_settingpage(
            'theme_everon',
            get_string('configtitle', 'theme_everon', null, true),
            'theme/boost_union:configure'
        );
        $ADMIN->add('theme_boost_union', $tab);

        // Create full settings page structure.
        // phpcs:disable moodle.ControlStructures.ControlSignature.Found
    } else if ($ADMIN->fulltree) {
        // Require the necessary libraries.
        require_once($CFG->dirroot . '/theme/boost_union/lib.php');
        require_once($CFG->dirroot . '/theme/boost_union/locallib.php');
        require_once($CFG->dirroot . '/theme/everon/lib.php');
        require_once($CFG->dirroot . '/theme/everon/locallib.php');
        require_once($CFG->dirroot . '/theme/everon/classes/admin_setting_launchpad_json.php');
        require_once($CFG->dirroot . '/theme/everon/classes/admin_setting_launchpad_roles.php');

        // Prepare options array for select settings.
        // Due to MDL-58376, we will use binary select settings instead of checkbox settings throughout this theme.
        $yesnooption = [THEME_BOOST_UNION_SETTING_SELECT_YES => get_string('yes'),
                THEME_BOOST_UNION_SETTING_SELECT_NO => get_string('no'), ];


        // Create Everon settings page with tabs and tertiary navigation
        // (and allow users with the theme/boost_union:configure capability to access it).
        $page = new admin_settingspage_tabs_with_tertiary(
            'theme_everon',
            get_string('configtitle', 'theme_everon', null, true),
            'theme/boost_union:configure'
        );


        // Create general settings tab.
        $tab = new admin_settingpage(
            'theme_everon_general',
            get_string('generalsettings', 'theme_boost', null, true)
        );

        // Create inheritance heading.
        $name = 'theme_everon/inheritanceheading';
        $title = get_string('inheritanceheading', 'theme_everon', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Prepare inheritance options.
        $inheritanceoptions = [
                THEME_EVERON_SETTING_INHERITANCE_INHERIT =>
                        get_string('inheritanceinherit', 'theme_everon'),
                THEME_EVERON_SETTING_INHERITANCE_DUPLICATE =>
                        get_string('inheritanceduplicate', 'theme_everon'),
        ];

        // Setting: Pre SCSS inheritance setting.
        $name = 'theme_everon/prescssinheritance';
        $title = get_string('prescssinheritancesetting', 'theme_everon', null, true);
        $description = get_string('prescssinheritancesetting_desc', 'theme_everon', null, true) . '<br />' .
                get_string('inheritanceoptionsexplanation', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_EVERON_SETTING_INHERITANCE_INHERIT,
            $inheritanceoptions
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Extra SCSS inheritance setting.
        $name = 'theme_everon/extrascssinheritance';
        $title = get_string('extrascssinheritancesetting', 'theme_everon', null, true);
        $description = get_string('extrascssinheritancesetting_desc', 'theme_everon', null, true) . '<br />' .
                get_string('inheritanceoptionsexplanation', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_EVERON_SETTING_INHERITANCE_INHERIT,
            $inheritanceoptions
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);

        /**********************************************************
         * EXTENSION POINT:
         * Add your Everon settings here.
         *********************************************************/

        // Accessibility toolbar (ported from theme_custom).
        $tab = new admin_settingpage(
            'theme_everon_accessibility',
            get_string('accessibilitytab', 'theme_everon', null, true)
        );

        $name = 'theme_everon/accessibilityheading';
        $title = get_string('accessibilityheading', 'theme_everon', null, true);
        $description = get_string('accessibilityheading_desc', 'theme_everon', null, true);
        $setting = new admin_setting_heading($name, $title, $description);
        $tab->add($setting);

        $name = 'theme_everon/enableaccessibilitytool';
        $title = get_string('enableaccessibilitytool', 'theme_everon', null, true);
        $description = get_string('enableaccessibilitytool_desc', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_BOOST_UNION_SETTING_SELECT_YES,
            $yesnooption
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Activity icon filters (Safari purpose colours + dark-mode monologo invert).
        $name = 'theme_everon/activityiconfiltersheading';
        $title = get_string('activityiconfiltersheading', 'theme_everon', null, true);
        $description = get_string('activityiconfiltersheading_desc', 'theme_everon', null, true);
        $setting = new admin_setting_heading($name, $title, $description);
        $tab->add($setting);

        $name = 'theme_everon/enableactivityiconfilters';
        $title = get_string('enableactivityiconfilters', 'theme_everon', null, true);
        $description = get_string('enableactivityiconfilters_desc', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_BOOST_UNION_SETTING_SELECT_YES,
            $yesnooption
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $page->add($tab);

        // Launch Pad (role-aware navbar shortcuts).
        $tab = new admin_settingpage(
            'theme_everon_launchpad',
            get_string('launchpadtab', 'theme_everon', null, true)
        );

        $name = 'theme_everon/launchpadheading';
        $title = get_string('launchpadheading', 'theme_everon', null, true);
        $description = get_string('launchpadheading_desc', 'theme_everon', null, true);
        $setting = new admin_setting_heading($name, $title, $description);
        $tab->add($setting);

        $name = 'theme_everon/enablelaunchpad';
        $title = get_string('enablelaunchpad', 'theme_everon', null, true);
        $description = get_string('enablelaunchpad_desc', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_BOOST_UNION_SETTING_SELECT_NO,
            $yesnooption
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_everon/launchpadrolelinks';
        $title = get_string('launchpadrolelinks', 'theme_everon', null, true);
        $description = get_string('launchpadrolelinks_desc', 'theme_everon', null, true);
        $setting = new theme_everon_admin_setting_launchpad_roles($name, $title, $description);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $page->add($tab);

        // Focus Mode (distraction-free course layout).
        $tab = new admin_settingpage(
            'theme_everon_focusmode',
            get_string('focusmodetab', 'theme_everon', null, true)
        );

        $name = 'theme_everon/focusmodeheading';
        $title = get_string('focusmodeheading', 'theme_everon', null, true);
        $description = get_string('focusmodeheading_desc', 'theme_everon', null, true);
        $setting = new admin_setting_heading($name, $title, $description);
        $tab->add($setting);

        $name = 'theme_everon/enablefocusmode';
        $title = get_string('enablefocusmode', 'theme_everon', null, true);
        $description = get_string('enablefocusmode_desc', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_BOOST_UNION_SETTING_SELECT_YES,
            $yesnooption
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $page->add($tab);


        // User menu tiles (Launch Pad–style grid; same Boost destinations).
        $tab = new admin_settingpage(
            'theme_everon_usermenu',
            get_string('usermenutab', 'theme_everon', null, true)
        );

        $name = 'theme_everon/usermenuheading';
        $title = get_string('usermenuheading', 'theme_everon', null, true);
        $description = get_string('usermenuheading_desc', 'theme_everon', null, true);
        $setting = new admin_setting_heading($name, $title, $description);
        $tab->add($setting);

        $name = 'theme_everon/enableusermenutiles';
        $title = get_string('enableusermenutiles', 'theme_everon', null, true);
        $description = get_string('enableusermenutiles_desc', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_BOOST_UNION_SETTING_SELECT_YES,
            $yesnooption
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $page->add($tab);

        // Compact navbar (global chrome; logo via Boost Union Look).
        $tab = new admin_settingpage(
            'theme_everon_navbar',
            get_string('navbartab', 'theme_everon', null, true)
        );

        $name = 'theme_everon/navbarheading';
        $title = get_string('navbarheading', 'theme_everon', null, true);
        $description = get_string('navbarheading_desc', 'theme_everon', null, true);
        $setting = new admin_setting_heading($name, $title, $description);
        $tab->add($setting);

        $name = 'theme_everon/enablecompactnavbar';
        $title = get_string('enablecompactnavbar', 'theme_everon', null, true);
        $description = get_string('enablecompactnavbar_desc', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_BOOST_UNION_SETTING_SELECT_YES,
            $yesnooption
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $page->add($tab);

        // My Courses layout (wider card grid skin for block_myoverview).
        $tab = new admin_settingpage(
            'theme_everon_mycourses',
            get_string('mycoursestab', 'theme_everon', null, true)
        );

        $name = 'theme_everon/mycoursesheading';
        $title = get_string('mycoursesheading', 'theme_everon', null, true);
        $description = get_string('mycoursesheading_desc', 'theme_everon', null, true);
        $setting = new admin_setting_heading($name, $title, $description);
        $tab->add($setting);

        $name = 'theme_everon/enablemycourseslayout';
        $title = get_string('enablemycourseslayout', 'theme_everon', null, true);
        $description = get_string('enablemycourseslayout_desc', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_BOOST_UNION_SETTING_SELECT_YES,
            $yesnooption
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // My Courses stats strip.
        $name = 'theme_everon/mycoursesstatsheading';
        $title = get_string('mycoursesstatsheading', 'theme_everon', null, true);
        $description = get_string('mycoursesstatsheading_desc', 'theme_everon', null, true);
        $setting = new admin_setting_heading($name, $title, $description);
        $tab->add($setting);

        $name = 'theme_everon/enablemycoursesstats';
        $title = get_string('enablemycoursesstats', 'theme_everon', null, true);
        $description = get_string('enablemycoursesstats_desc', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_BOOST_UNION_SETTING_SELECT_YES,
            $yesnooption
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_everon/mycoursesstatsenrolments';
        $title = get_string('mycoursesstatsenrolments', 'theme_everon', null, true);
        $description = get_string('mycoursesstatsenrolments_desc', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_BOOST_UNION_SETTING_SELECT_YES,
            $yesnooption
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_everon/mycoursesstatscompletions';
        $title = get_string('mycoursesstatscompletions', 'theme_everon', null, true);
        $description = get_string('mycoursesstatscompletions_desc', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_BOOST_UNION_SETTING_SELECT_YES,
            $yesnooption
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_everon/mycoursesstatsbadges';
        $title = get_string('mycoursesstatsbadges', 'theme_everon', null, true);
        $description = get_string('mycoursesstatsbadges_desc', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_BOOST_UNION_SETTING_SELECT_YES,
            $yesnooption
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $name = 'theme_everon/mycoursesstatscertificates';
        $title = get_string('mycoursesstatscertificates', 'theme_everon', null, true);
        $description = get_string('mycoursesstatscertificates_desc', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_BOOST_UNION_SETTING_SELECT_YES,
            $yesnooption
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        $page->add($tab);

        // Dashboard layout (wider shell + shared Course overview card chrome on /my/).
        $tab = new admin_settingpage(
            'theme_everon_dashboard',
            get_string('dashboardtab', 'theme_everon', null, true)
        );

        $name = 'theme_everon/dashboardheading';
        $title = get_string('dashboardheading', 'theme_everon', null, true);
        $description = get_string('dashboardheading_desc', 'theme_everon', null, true);
        $setting = new admin_setting_heading($name, $title, $description);
        $tab->add($setting);

        $name = 'theme_everon/enabledashboardlayout';
        $title = get_string('enabledashboardlayout', 'theme_everon', null, true);
        $description = get_string('enabledashboardlayout_desc', 'theme_everon', null, true);
        $setting = new admin_setting_configselect(
            $name,
            $title,
            $description,
            THEME_BOOST_UNION_SETTING_SELECT_YES,
            $yesnooption
        );
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Block surface polish is included when Dashboard layout is Yes (no separate toggle).
        $name = 'theme_everon/dashboardsurfacesnote';
        $title = '';
        $description = get_string('dashboardsurfacesnote', 'theme_everon', null, true);
        $setting = new admin_setting_heading($name, $title, $description);
        $tab->add($setting);

        $page->add($tab);

        // Add settings page to the admin settings category.
        $ADMIN->add('theme_boost_union', $page);
    }
}
