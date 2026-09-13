(function () {
    'use strict';

    function initAdminNavigation() {
        var sidebar = document.getElementById('admin-sidebar');
        var toggle = document.getElementById('mobile-menu-toggle');
        var backdrop = document.getElementById('mobile-drawer-backdrop');

        if (!sidebar) return;

        var drawerKey = 'ffp.admin.drawer-open';
        var groupKey = 'ffp.admin.nav-groups';

        function isMobile() {
            return window.matchMedia('(max-width: 900px)').matches;
        }

        function setDrawer(open, persist) {
            if (!isMobile()) return;

            sidebar.classList.toggle('mobile-open', open);
            if (backdrop) backdrop.classList.toggle('open', open);
            if (toggle) {
                toggle.setAttribute('aria-expanded', String(open));
                toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
            }
            document.body.classList.toggle('drawer-open', open);

            if (persist) {
                try {
                    sessionStorage.setItem(drawerKey, open ? '1' : '0');
                } catch (error) {
                    // Storage can be unavailable in privacy-restricted browsers.
                }
            }
        }

        function restoreDrawer() {
            if (!isMobile()) return;

            var shouldOpen = false;
            try {
                shouldOpen = sessionStorage.getItem(drawerKey) === '1';
            } catch (error) {
                shouldOpen = false;
            }

            setDrawer(shouldOpen, false);
        }

        if (toggle && backdrop) {
            toggle.addEventListener('click', function () {
                setDrawer(!sidebar.classList.contains('mobile-open'), true);
            });

            backdrop.addEventListener('click', function () {
                setDrawer(false, true);
            });
        }

        sidebar.querySelectorAll('a[href]').forEach(function (link) {
            link.addEventListener('click', function () {
                if (!isMobile() || link.target === '_blank' || link.hasAttribute('download')) return;

                // Keep the drawer open through the next page load. This prevents
                // the sidebar from disappearing whenever an admin submenu page opens.
                try {
                    sessionStorage.setItem(drawerKey, '1');
                } catch (error) {
                    // Ignore unavailable storage and allow normal navigation.
                }
            });
        });

        function readGroups() {
            try {
                var value = JSON.parse(localStorage.getItem(groupKey) || '[]');
                return Array.isArray(value) ? value : [];
            } catch (error) {
                return [];
            }
        }

        function writeGroups(groups) {
            try {
                localStorage.setItem(groupKey, JSON.stringify(groups.slice(-30)));
            } catch (error) {
                // Ignore unavailable storage.
            }
        }

        function restoreGroups() {
            var saved = readGroups();
            sidebar.querySelectorAll('.nav-group').forEach(function (group) {
                var key = group.getAttribute('data-nav-key');
                if (!key) return;

                if (saved.indexOf(key) !== -1) {
                    group.open = true;
                    group.classList.add('open');
                    var parent = group.querySelector(':scope > .nav-parent');
                    if (parent) parent.setAttribute('aria-expanded', 'true');
                }
            });
        }

        sidebar.querySelectorAll('.nav-group > .nav-parent').forEach(function (parent) {
            parent.addEventListener('click', function () {
                var group = parent.closest('.nav-group');
                if (!group) return;

                // <details>/<summary> owns the real open state. Keep the legacy
                // class in sync for existing theme CSS without fighting it.
                window.setTimeout(function () {
                    var open = group.open;
                    group.classList.toggle('open', open);
                    parent.setAttribute('aria-expanded', String(open));

                    var key = group.getAttribute('data-nav-key');
                    if (!key) return;

                    var groups = readGroups().filter(function (item) { return item !== key; });
                    if (open) groups.push(key);
                    writeGroups(groups);
                }, 0);
            });
        });

        window.addEventListener('resize', function () {
            if (!isMobile()) {
                sidebar.classList.remove('mobile-open');
                if (backdrop) backdrop.classList.remove('open');
                document.body.classList.remove('drawer-open');
            } else {
                restoreDrawer();
            }
        });

        restoreGroups();
        restoreDrawer();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminNavigation, { once: true });
    } else {
        initAdminNavigation();
    }
})();
