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
 * Admin setting: Launch Pad role link packs (Site administrators + Add role).
 *
 * @package    theme_everon
 * @copyright  2026 Project Theme / Jason Dorey
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Single JSON-map setting with dynamic role packs UI.
 */
class theme_everon_admin_setting_launchpad_roles extends admin_setting {

    /**
     * @param string $name
     * @param string $visiblename
     * @param string $description
     */
    public function __construct($name, $visiblename, $description) {
        parent::__construct($name, $visiblename, $description, '{}');
    }

    /**
     * @return string|null
     */
    public function get_setting() {
        theme_everon_launchpad_migrate_mvp_if_needed();
        $value = $this->config_read($this->name);
        if ($value === null || $value === false || $value === '') {
            return '{}';
        }
        return (string) $value;
    }

    /**
     * @return string
     */
    public function get_defaultsetting() {
        $map = [
            'siteadmin' => theme_everon_launchpad_default_siteadmin_links(),
        ];
        $encoded = json_encode($map, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return $encoded === false ? '{"siteadmin":[]}' : $encoded;
    }

    /**
     * @param string $data
     * @return string Empty on success, else error.
     */
    public function write_setting($data) {
        $data = trim((string) $data);
        if ($data === '') {
            $data = '{}';
        }

        $decoded = json_decode($data, true);
        if (!is_array($decoded)) {
            return get_string('launchpadrolemapinvalid', 'theme_everon');
        }

        $clean = theme_everon_launchpad_normalize_rolelinks_map($decoded);
        if (is_string($clean)) {
            return $clean;
        }

        $encoded = $clean === []
            ? '{}'
            : json_encode($clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return ($this->config_write($this->name, $encoded) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * @param string $data
     * @param string $query
     * @return string
     */
    public function output_html($data, $query = '') {
        global $PAGE, $OUTPUT;

        theme_everon_launchpad_migrate_mvp_if_needed();

        $map = theme_everon_launchpad_decode_rolelinks_map(is_string($data) ? $data : '');
        $roles = theme_everon_launchpad_addable_roles();

        $rolenamebyid = [];
        foreach ($roles as $role) {
            $rolenamebyid[(int) $role->id] = $role->localname;
        }
        // Include already-configured roles that might not be in the addable list (deleted).
        foreach (array_keys($map) as $key) {
            if (preg_match('/^role:(\d+)$/', $key, $m)) {
                $rid = (int) $m[1];
                if (!isset($rolenamebyid[$rid])) {
                    $rolenamebyid[$rid] = get_string('launchpadrolemissing', 'theme_everon', $rid);
                }
            }
        }

        $encodedmap = json_encode($map, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($encodedmap === false) {
            $encodedmap = '{}';
        }

        $fullname = $this->get_full_name();
        $id = $this->get_id();
        $rootid = $id . '_ui';

        $jsonhelp = get_string('launchpadlinks_help', 'theme_everon');
        $addlabel = get_string('launchpadaddrole', 'theme_everon');
        $removelabel = get_string('launchpadremoverole', 'theme_everon');
        $choose = get_string('launchpadchooserole', 'theme_everon');
        $packsheading = get_string('launchpadconfiguredroles', 'theme_everon');

        $roleoptions = '';
        foreach ($roles as $role) {
            $roleoptions .= html_writer::tag('option', format_string($role->localname), [
                'value' => (int) $role->id,
                'data-shortname' => $role->shortname,
            ]);
        }

        $html = html_writer::start_div('theme-everon-launchpad-roles', [
            'id' => $rootid,
            'data-region' => 'launchpad-roles',
        ]);

        // Hidden field Moodle saves (must remain a named form control).
        $html .= html_writer::tag('textarea', s($encodedmap), [
            'name' => $fullname,
            'id' => $id,
            'class' => 'sr-only',
            'rows' => 1,
            'cols' => 60,
            'style' => 'position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden;',
            'data-region' => 'launchpad-roles-store',
            'aria-hidden' => 'true',
            'tabindex' => '-1',
        ]);

        $html .= html_writer::tag('p', $jsonhelp, ['class' => 'form-text text-muted mb-3']);

        // Unified list: Site administrators (default, not removable) + added role packs.
        $html .= html_writer::tag('h4', $packsheading, ['class' => 'h5 mt-2']);
        $html .= html_writer::start_div('', ['data-region' => 'role-packs']);
        $html .= $this->render_pack_block('siteadmin', get_string('launchpadlinks_siteadmin', 'theme_everon'),
            $map['siteadmin'] ?? [], false);
        foreach ($map as $key => $links) {
            if (!preg_match('/^role:(\d+)$/', $key, $m)) {
                continue;
            }
            $rid = (int) $m[1];
            $label = get_string('launchpadlinks_role', 'theme_everon', $rolenamebyid[$rid] ?? $rid);
            $html .= $this->render_pack_block($key, $label, is_array($links) ? $links : [], true, $rid);
        }
        $html .= html_writer::end_div();

        $html .= html_writer::start_div('d-flex flex-wrap align-items-end gap-2 mt-3', [
            'data-region' => 'add-role',
        ]);
        $html .= html_writer::start_div();
        $html .= html_writer::tag('label', $addlabel, [
            'class' => 'form-label',
            'for' => $id . '_addrole',
        ]);
        $html .= html_writer::start_tag('select', [
            'id' => $id . '_addrole',
            'class' => 'form-select',
            'data-region' => 'add-role-select',
        ]);
        $html .= html_writer::tag('option', $choose, ['value' => '']);
        $html .= $roleoptions;
        $html .= html_writer::end_tag('select');
        $html .= html_writer::end_div();
        $html .= html_writer::tag('button', $addlabel, [
            'type' => 'button',
            'class' => 'btn btn-secondary',
            'data-action' => 'add-role',
        ]);
        $html .= html_writer::end_div();

        // Template for new role packs (cloned by JS).
        $html .= html_writer::start_div('d-none', ['data-region' => 'pack-template']);
        $html .= $this->render_pack_block('role:__ID__', get_string('launchpadlinks_role', 'theme_everon', '__NAME__'),
            [], true, 0);
        $html .= html_writer::end_div();

        $html .= html_writer::end_div();

        $PAGE->requires->js_call_amd('theme_everon/launchpad_roles_settings', 'init', [[
            'rootId' => $rootid,
            'storeId' => $id,
            'strings' => [
                'remove' => $removelabel,
                'linksrole' => get_string('launchpadlinks_role', 'theme_everon', '{$a}'),
                'choose' => $choose,
            ],
        ]]);

        return format_admin_setting($this, $this->visiblename, $html, $this->description, true, '', null, $query);
    }

    /**
     * Render one pack block (builtin or role).
     *
     * @param string $key
     * @param string $title
     * @param array $links
     * @param bool $removable
     * @param int $roleid
     * @return string
     */
    protected function render_pack_block(string $key, string $title, array $links, bool $removable, int $roleid = 0): string {
        $json = $links === []
            ? ''
            : (string) json_encode($links, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $attrs = [
            'class' => 'theme-everon-launchpad-pack border rounded p-3 mb-3',
            'data-region' => 'pack',
            'data-pack-key' => $key,
        ];
        if ($roleid) {
            $attrs['data-role-id'] = $roleid;
        }

        $out = html_writer::start_div('', $attrs);
        $out .= html_writer::start_div('d-flex justify-content-between align-items-center mb-2');
        $out .= html_writer::tag('strong', $title, ['data-region' => 'pack-title']);
        if ($removable) {
            $out .= html_writer::tag('button', get_string('launchpadremoverole', 'theme_everon'), [
                'type' => 'button',
                'class' => 'btn btn-sm btn-outline-danger',
                'data-action' => 'remove-pack',
            ]);
        }
        $out .= html_writer::end_div();
        $out .= html_writer::tag('textarea', s($json), [
            'class' => 'form-control font-monospace',
            'rows' => 6,
            'cols' => 60,
            'data-region' => 'pack-json',
            'spellcheck' => 'false',
        ]);
        $out .= html_writer::end_div();
        return $out;
    }
}
