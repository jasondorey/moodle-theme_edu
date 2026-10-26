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
 * Theme Everon - Language pack
 *
 * @package    theme_everon
 * @copyright  2023 Daniel Poggenpohl <daniel.poggenpohl@fernuni-hagen.de> and Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Let codechecker ignore some sniffs for this file as it is perfectly well ordered, just not alphabetically.
// phpcs:disable moodle.Files.LangFilesOrdering.UnexpectedComment
// phpcs:disable moodle.Files.LangFilesOrdering.IncorrectOrder

// General.
$string['pluginname'] = 'Everon';
$string['choosereadme'] = 'Everon is a Boost Union child theme for Project Theme. It ships stock Union look plus optional features such as the accessibility toolbar, Launch Pad, Focus Mode, My Courses layout, and Dashboard layout.';
$string['configtitle'] = 'Everon';
$string['settingsoverview_buc_desc'] = 'Everon is a Boost Union child for Project Theme. Prefer Union Look/Content settings; Everon adds features such as the accessibility toolbar, Launch Pad shortcuts, Focus Mode, My Courses card layout, and Dashboard layout.';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Inheritance';
$string['inheritanceinherit'] = 'Inherit';
$string['inheritanceduplicate'] = 'Duplicate';
$string['inheritanceoptionsexplanation'] = 'Most of the time, inheriting will be perfectly fine. However, it may happen that imperfect code is integrated into Boost Union which prevents simple SCSS inheritance for particular Boost Union features. If you encounter any issues with Boost Union features which seem not to work in Everon as well, try to switch this setting to \'Dupliate\' and, if this solves the problem, report an issue on Github (see the README.md file for details how to report an issue).';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Pre SCSS inheritance';
$string['prescssinheritancesetting_desc'] = 'With this setting, you control if the pre SCSS code from Boost Union should be inherited or duplicated.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Extra SCSS inheritance';
$string['extrascssinheritancesetting_desc'] = 'With this setting, you control if the extra SCSS code from Boost Union should be inherited or duplicated.';

/**************************************************************
 * EXTENSION POINT:
 * Add your language strings for your settings here.
 *************************************************************/

// Settings: Accessibility tab (ported from theme_custom).
$string['accessibilitytab'] = 'Accessibility';
$string['accessibilityheading'] = 'Accessibility toolbar';
$string['accessibilityheading_desc'] = 'Navbar utility that lets each user adjust content, colour, and assistive tools. Preferences are stored in the browser (localStorage), not in Moodle user preferences.';
$string['enableaccessibilitytool'] = 'Enable accessibility toolbar';
$string['enableaccessibilitytool_desc'] = 'Show the accessibility icon in the top navbar (next to notifications/messages) and load the toolbar. Default: Yes.';
$string['accessibilitymenu'] = 'Accessibility options';
$string['a11ycontentadjustments'] = 'Content Adjustments';
$string['a11ycoloradjustments'] = 'Color Adjustments';
$string['a11ytools'] = 'Tools';
$string['a11yadjustfontsize'] = 'Adjust Font Size';
$string['a11ydecreasefont'] = 'Decrease font size';
$string['a11yincreasefont'] = 'Increase font size';
$string['a11yhighlighttitle'] = 'Highlight Title';
$string['a11yhighlightlinks'] = 'Highlight Links';
$string['a11ydyslexiafont'] = 'Dyslexia Font';
$string['a11yletterspacing'] = 'Letter Spacing';
$string['a11ylineheight'] = 'Line Height';
$string['a11yfontweight'] = 'Font Weight';
$string['a11ydarkcontrast'] = 'Dark Contrast';
$string['a11ylightcontrast'] = 'Light Contrast';
$string['a11yhighcontrast'] = 'High Contrast';
$string['a11yhighsaturation'] = 'High Saturation';
$string['a11ylowsaturation'] = 'Low Saturation';
$string['a11ymonochrome'] = 'Monochrome';
$string['a11ydarkmode'] = 'Dark Mode';
$string['a11yreadingguide'] = 'Reading Guide';
$string['a11ystopanimations'] = 'Stop Animations';
$string['a11ybigcursor'] = 'Big Cursor';
$string['a11yresetall'] = 'Reset all accessibility settings';

// Settings: Launch Pad tab.
$string['launchpadtab'] = 'Launch Pad';
$string['launchpadheading'] = 'Launch Pad';
$string['launchpadheading_desc'] = 'Navbar icon that opens role-aware shortcut links. Site administrators is the default pack; add any other site roles as needed. Matching packs are merged and deduplicated by URL. The icon is hidden when Launch Pad is disabled or the current user has no links.';
$string['enablelaunchpad'] = 'Enable Launch Pad';
$string['enablelaunchpad_desc'] = 'Show the Launch Pad icon in the top navbar (after the accessibility tool, before the user menu) when the user has at least one configured link. Default: No — turn on after you add JSON links below.';
$string['launchpadmenu'] = 'Launch pad';
$string['launchpadlinks_help'] = 'JSON array of objects with <code>label</code>, <code>url</code> (site-relative path such as <code>/admin/search.php</code>), and optional <code>icon</code> (Font Awesome class, e.g. <code>fa-cog</code>). External URLs are rejected in v1. Example:<br><pre>[
  {"label": "Site administration", "url": "/admin/search.php", "icon": "fa-cog"},
  {"label": "Users", "url": "/admin/user.php", "icon": "fa-user"}
]</pre>';
$string['launchpadlinks_siteadmin'] = 'Links: Site administrators';
$string['launchpadlinks_siteadmin_desc'] = 'Shown when the user is a site administrator (<code>is_siteadmin()</code>).';
$string['launchpadlinks_manager'] = 'Links: Manager';
$string['launchpadlinks_manager_desc'] = 'Legacy MVP setting (migrated into a manager role pack when possible).';
$string['launchpadlinks_authenticated'] = 'Links: Authenticated users';
$string['launchpadlinks_authenticated_desc'] = 'Fallback for every logged-in non-guest user. Merged after siteadmin and role packs.';

$string['launchpadrolelinks'] = 'Role link packs';
$string['launchpadrolelinks_desc'] = 'Configure JSON link lists. Site administrators is always present (default). Use Add role for any other site role. Role packs match when the user has that role in any context. Store uses role ids; labels show the current role name.';
$string['launchpadbuiltinpacks'] = 'Built-in packs';
$string['launchpadconfiguredroles'] = 'Configured packs';
$string['launchpadaddrole'] = 'Add role';
$string['launchpadremoverole'] = 'Remove';
$string['launchpadchooserole'] = 'Choose a role…';
$string['launchpadlinks_role'] = 'Links: {$a}';
$string['launchpadrolemissing'] = 'Role #{$a} (missing)';
$string['launchpadrolemapinvalid'] = 'Launch Pad role links must be a JSON object mapping pack keys to link arrays.';

$string['launchpadjsoninvalid'] = 'Launch Pad links must be a JSON array of objects, each with non-empty label and url.';

// Settings: Focus Mode tab (ported from theme_custom course focus).
$string['focusmodetab'] = 'Focus Mode';
$string['focusmodeheading'] = 'Focus Mode';
$string['focusmodeheading_desc'] = 'A floating control on course content pages that hides secondary navigation and drawers and widens the main content. Preference is stored per course in the browser (localStorage).';
$string['enablefocusmode'] = 'Enable Focus Mode';
$string['enablefocusmode_desc'] = 'Show the Focus Mode button (bottom-right) on course home, section, and activity pages. Default: Yes.';
$string['focusmodeenable'] = 'Focus mode';
$string['focusmodedisable'] = 'Exit focus mode';
$string['focusmodenotice'] = 'Focus mode is on. Click × at the bottom right to turn it off.';


// Settings: User menu tiles tab.
$string['usermenutab'] = 'User menu';
$string['usermenuheading'] = 'User menu tiles';
$string['usermenuheading_desc'] = 'Restyle the profile usermenu as a Launch Pad–style tile grid (same Boost destinations). Panel chrome already matches Accessibility / Launch Pad. Turn off to keep the vertical link list.';
$string['enableusermenutiles'] = 'Enable user menu tiles';
$string['enableusermenutiles_desc'] = 'Show profile usermenu items as a 3-column tile grid with Font Awesome icons. Default: Yes. Set to No for the vertical list.';

// Settings: Compact navbar tab.
$string['navbartab'] = 'Navbar';
$string['navbarheading'] = 'Compact navbar';
$string['navbarheading_desc'] = 'Slightly tighter top bar, clearer active link underline, and consistent utility icon hit targets. Site logo and brand colour stay in Boost Union → Look (multi-client path). Login layout is unchanged (Boost Union login only).';
$string['enablecompactnavbar'] = 'Enable compact navbar';
$string['enablecompactnavbar_desc'] = 'Apply compact navbar chrome on all pages except login. Default: Yes. Turn off to use stock Boost Union navbar height.';

// Settings: My Courses tab (block_myoverview card layout skin).
$string['mycoursestab'] = 'My Courses';
$string['mycoursesheading'] = 'My Courses layout';
$string['mycoursesheading_desc'] = 'Optional wider card grid on the My courses page (/my/courses.php). Skins the stock Course overview block — does not replace it. Search, grouping, sort, and view modes stay core Moodle.';
$string['enablemycourseslayout'] = 'Enable My Courses layout';
$string['enablemycourseslayout_desc'] = 'Widen the My courses page, show a responsive 1–5 column card grid, restyle cards (shadow, circular progress on image, star on image). Default: Yes.';

// Settings: My Courses stats strip.
$string['mycoursesstatsheading'] = 'My Courses stats';
$string['mycoursesstatsheading_desc'] = 'Optional statistics strip at the top of My courses (/my/courses.php) for the current user: Enrolments, Completions, Badges, and Certificates. Each panel can be enabled independently.';
$string['enablemycoursesstats'] = 'Enable My Courses stats';
$string['enablemycoursesstats_desc'] = 'Show the statistics strip above the course overview on My courses. Default: Yes.';
$string['mycoursesstatsenrolments'] = 'Show Enrolments panel';
$string['mycoursesstatsenrolments_desc'] = 'Count of the current user’s active (in progress) courses. Default: Yes.';
$string['mycoursesstatscompletions'] = 'Show Completions panel';
$string['mycoursesstatscompletions_desc'] = 'Count of courses the current user has completed. Default: Yes.';
$string['mycoursesstatsbadges'] = 'Show Badges panel';
$string['mycoursesstatsbadges_desc'] = 'Count of badges awarded to the current user. Default: Yes.';
$string['mycoursesstatscertificates'] = 'Show Certificates panel';
$string['mycoursesstatscertificates_desc'] = 'Count of certificates awarded to the current user (tool_certificate or mod_customcert when installed; otherwise 0). Default: Yes.';
$string['mycoursesstatsaria'] = 'My courses statistics';
$string['mycoursesstatsenrolmentstitle'] = 'Enrolments';
$string['mycoursesstatsenrolmentssub'] = '{$a} Active courses';
$string['mycoursesstatscompletionstitle'] = 'Completions';
$string['mycoursesstatscompletionssub'] = '{$a} Completed courses';
$string['mycoursesstatsbadgestitle'] = 'Badges';
$string['mycoursesstatsbadgessub'] = '{$a} Awarded';
$string['mycoursesstatscertificatestitle'] = 'Certificates';
$string['mycoursesstatscertificatessub'] = '{$a} Awarded';

// Settings: Dashboard tab (wider shell + shared Course overview cards on /my/).
$string['dashboardtab'] = 'Dashboard';
$string['dashboardheading'] = 'Dashboard layout';
$string['dashboardheading_desc'] = 'Optional wider Dashboard shell on /my/ and shared Course overview card chrome with My Courses. Column layout (Calendar / Upcoming events on the right) uses Boost Union Outside (right) — configure under Feel → Blocks.';
$string['enabledashboardlayout'] = 'Enable Dashboard layout';
$string['enabledashboardlayout_desc'] = 'Widen the Dashboard page and apply the same Course overview card chrome as My Courses. Also unifies Timeline / Calendar / Upcoming block surfaces and softens empty states to match. Default: Yes. Enable Boost Union Outside (right) separately for the right column.';
$string['dashboardsurfacesnote'] = 'When Dashboard layout is enabled, Everon also applies shared block surface tokens (soft border, radius, shadow) and calmer Timeline / Upcoming empty-state styling so supporting blocks match course cards.';

// Privacy API.
$string['privacy:metadata'] = 'The Everon theme does not store any personal data about any user. Accessibility and Focus Mode preferences are stored in the browser only. Launch Pad links, My Courses layout, My Courses stats, Dashboard layout, and compact navbar are site-wide admin settings.';
