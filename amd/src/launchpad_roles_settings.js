// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
/**
 * Launch Pad admin settings — Add/Remove role packs; sync JSON map to hidden field.
 *
 * @module     theme_everon/launchpad_roles_settings
 * @copyright  2026 Project Theme / Jason Dorey
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {

    /**
     * @param {HTMLElement} root
     * @returns {object}
     */
    const collectMap = (root) => {
        const map = {};
        root.querySelectorAll('[data-region="pack"]').forEach((pack) => {
            if (pack.closest('[data-region="pack-template"]')) {
                return;
            }
            const key = pack.getAttribute('data-pack-key');
            if (!key || key === 'authenticated') {
                return;
            }
            const ta = pack.querySelector('[data-region="pack-json"]');
            const raw = (ta && ta.value ? ta.value : '').trim();
            if (raw === '') {
                map[key] = [];
                return;
            }
            try {
                const parsed = JSON.parse(raw);
                map[key] = Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                map[key] = raw;
            }
        });
        return map;
    };

    /**
     * @param {HTMLElement} root
     * @param {HTMLTextAreaElement} store
     */
    const syncStore = (root, store) => {
        const map = collectMap(root);
        const ordered = {};
        if (Object.prototype.hasOwnProperty.call(map, 'siteadmin')) {
            ordered.siteadmin = map.siteadmin;
        } else {
            ordered.siteadmin = [];
        }
        Object.keys(map).forEach((k) => {
            if (k === 'siteadmin') {
                return;
            }
            ordered[k] = map[k];
        });
        store.value = JSON.stringify(ordered);
    };

    /**
     * @param {HTMLSelectElement} select
     * @param {HTMLElement} rolePacks
     */
    const refreshSelectOptions = (select, rolePacks) => {
        const used = new Set();
        rolePacks.querySelectorAll('[data-region="pack"][data-role-id]').forEach((pack) => {
            used.add(String(pack.getAttribute('data-role-id')));
        });
        Array.from(select.options).forEach((opt) => {
            if (!opt.value) {
                return;
            }
            opt.disabled = used.has(opt.value);
            opt.hidden = used.has(opt.value);
        });
        if (select.value && used.has(select.value)) {
            select.value = '';
        }
    };

    /**
     * @param {object} config
     */
    const init = (config) => {
        const root = document.getElementById(config.rootId);
        const store = document.getElementById(config.storeId);
        if (!root || !store) {
            return;
        }

        const rolePacks = root.querySelector('[data-region="role-packs"]');
        const select = root.querySelector('[data-region="add-role-select"]');
        const templateWrap = root.querySelector('[data-region="pack-template"]');
        const addBtn = root.querySelector('[data-action="add-role"]');
        if (!rolePacks || !select || !templateWrap || !addBtn) {
            return;
        }

        const sync = () => {
            syncStore(root, store);
            refreshSelectOptions(select, rolePacks);
        };

        root.addEventListener('input', (event) => {
            if (event.target && event.target.matches('[data-region="pack-json"]')) {
                sync();
            }
        });
        root.addEventListener('change', (event) => {
            if (event.target && event.target.matches('[data-region="pack-json"]')) {
                sync();
            }
        });

        root.addEventListener('click', (event) => {
            const removeBtn = event.target.closest('[data-action="remove-pack"]');
            if (removeBtn) {
                const pack = removeBtn.closest('[data-region="pack"]');
                // Never remove the default siteadmin pack.
                if (pack && rolePacks.contains(pack) && pack.getAttribute('data-pack-key') !== 'siteadmin') {
                    pack.remove();
                    sync();
                }
            }
        });

        addBtn.addEventListener('click', () => {
            const roleId = select.value;
            if (!roleId) {
                return;
            }
            const opt = select.selectedOptions[0];
            const name = opt ? opt.textContent.trim() : roleId;
            const tpl = templateWrap.querySelector('[data-region="pack"]');
            if (!tpl) {
                return;
            }
            const clone = tpl.cloneNode(true);
            clone.setAttribute('data-pack-key', 'role:' + roleId);
            clone.setAttribute('data-role-id', roleId);
            const title = clone.querySelector('[data-region="pack-title"]');
            if (title) {
                const template = config.strings.linksrole || 'Links: {$a}';
                title.textContent = template.replace('{$a}', name);
            }
            const ta = clone.querySelector('[data-region="pack-json"]');
            if (ta) {
                ta.value = '';
            }
            rolePacks.appendChild(clone);
            select.value = '';
            sync();
        });

        const form = root.closest('form');
        if (form) {
            form.addEventListener('submit', () => {
                sync();
            });
        }

        sync();
    };

    return {init: init};
});
