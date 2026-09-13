(function () {
    'use strict';

    function init() {
        var sidebar = document.getElementById('admin-sidebar');
        var toggle = document.getElementById('mobile-menu-toggle');
        var backdrop = document.getElementById('mobile-drawer-backdrop');

        if (!sidebar) return;

        var drawerKey = 'ffp.admin.drawer-open';
        var groupKey = 'ffp.admin.nav-groups';

        function isMobile() {
            return window.matchMedia('(max-width: 900px)').matches;
        }

        function restoreDrawerWithoutAnimation() {
            if (!isMobile()) return;

            var shouldOpen = false;
            try {
                shouldOpen = sessionStorage.getItem(drawerKey) === '1';
            } catch (error) {
                shouldOpen = false;
            }

            if (!shouldOpen) return;

            sidebar.classList.add('mobile-open', 'nav-state-restoring');
            if (backdrop) backdrop.classList.add('open');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'true');
                toggle.setAttribute('aria-label', 'Close navigation');
            }
            document.body.classList.add('drawer-open');

            window.requestAnimationFrame(function () {
                window.requestAnimationFrame(function () {
                    sidebar.classList.remove('nav-state-restoring');
                });
            });
        }

        // Do this as early as possible so a persisted open drawer does not
        // animate from closed -> open during every admin page navigation.
        restoreDrawerWithoutAnimation();

        // Prevent the legacy portal click-to-close handler from running on
        // normal navigation links. Native navigation remains untouched.
        sidebar.querySelectorAll('a[href]').forEach(function (link) {
            link.addEventListener('click', function (event) {
                if (!isMobile() || link.target === '_blank' || link.hasAttribute('download')) return;
                event.stopPropagation();
                try {
                    sessionStorage.setItem(drawerKey, '1');
                } catch (error) {
                    // Ignore unavailable storage.
                }
            }, true);
        });

        // There must be only one owner for details-group toggling. Prevent the
        // legacy portal handler from toggling the same group a second time.
        sidebar.querySelectorAll('.nav-group > .nav-parent').forEach(function (parent) {
            parent.addEventListener('click', function (event) {
                event.stopPropagation();

                var group = parent.closest('.nav-group');
                if (!group) return;

                window.setTimeout(function () {
                    var open = group.open;
                    parent.setAttribute('aria-expanded', String(open));

                    var key = group.getAttribute('data-nav-key');
                    if (!key) return;

                    var groups = [];
                    try {
                        var saved = JSON.parse(localStorage.getItem(groupKey) || '[]');
                        groups = Array.isArray(saved) ? saved : [];
                    } catch (error) {
                        groups = [];
                    }

                    groups = groups.filter(function (item) { return item !== key; });
                    if (open) groups.push(key);

                    try {
                        localStorage.setItem(groupKey, JSON.stringify(groups.slice(-30)));
                    } catch (error) {
                        // Ignore unavailable storage.
                    }
                }, 0);
            }, true);
        });

        window.addEventListener('pageshow', restoreDrawerWithoutAnimation);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
