@theme @theme_everon @theme_everon_darkmode @javascript
Feature: Everon dark mode smoke under Boost colour modes
  In order to keep Everon usable when Boost colour modes are dark
  As a user
  I need Dashboard, My courses, course pages, and navbar popovers to render with data-bs-theme=dark

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following config values are set as admin:
      | config                   | value | plugin       |
      | enablecolourmodes        | 1     | theme_boost  |
      | enableaccessibilitytool  | yes   | theme_everon |
      | enablelaunchpad          | yes   | theme_everon |
      | enableusermenutiles      | yes   | theme_everon |

  Scenario: Dashboard, My courses, and a course page keep data-bs-theme dark
    Given the following "user preferences" exist:
      | user  | preference             | value |
      | admin | theme_boost_colourmode | dark  |
    And I log in as "admin"
    When I follow "Dashboard"
    Then the document Bootstrap colour mode should be "dark"
    And "body.theme-everon-dashboard" "css_element" should exist
    And "#region-main" "css_element" should exist
    When I follow "My courses"
    Then the document Bootstrap colour mode should be "dark"
    And "body.theme-everon-mycourses" "css_element" should exist
    When I am on "Course 1" course homepage
    Then the document Bootstrap colour mode should be "dark"
    And "body.path-course" "css_element" should exist

  Scenario: Navbar usermenu, Launch Pad, and accessibility popovers open under dark mode
    Given the following "user preferences" exist:
      | user  | preference             | value |
      | admin | theme_boost_colourmode | dark  |
    And I log in as "admin"
    And I follow "Dashboard"
    Then the document Bootstrap colour mode should be "dark"
    When I open the Everon user menu
    Then "#user-action-menu" "css_element" should be visible
    And ".everon-usermenu__grid" "css_element" should exist
    When I open the Everon launch pad
    Then "#nav-launchpad-popover-panel" "css_element" should be visible
    And ".everon-launchpad__tile" "css_element" should exist
    When I open the Everon accessibility panel
    Then "#nav-accessibility-popover-panel" "css_element" should be visible
    And I should see "Dark Mode" in the "#nav-accessibility-popover-panel" "css_element"
    And ".custom-navbar__a11y-option" "css_element" should exist

  Scenario: Accessibility Dark Mode tile bridges to Boost data-bs-theme dark
    Given the following "user preferences" exist:
      | user  | preference             | value |
      | admin | theme_boost_colourmode | light |
    And I log in as "admin"
    And I follow "Dashboard"
    Then the document Bootstrap colour mode should be "light"
    When I open the Everon accessibility panel
    And I toggle the Everon accessibility Dark Mode tile
    Then the document Bootstrap colour mode should be "dark"
    And "#nav-accessibility-popover-container [data-a11y-toggle=darkMode][aria-pressed=true]" "css_element" should exist
