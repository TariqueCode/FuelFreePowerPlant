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

        function normalizeUrl(value) {
            try {
                var url = new URL(value, window.location.origin);
                var path = url.pathname.replace(/\/+$/, '') || '/';
                var params = new URLSearchParams(url.search);
                params.sort();
                var search = params.toString();
                return path + (search ? '?' + search : '');
            } catch (error) {
                return String(value || '').replace(/\/+$/, '') || '/';
            }
        }

        function normalizePath(value) {
            return normalizeUrl(value).split('?')[0];
        }

        function syncActiveNavigation() {
            var currentUrl = normalizeUrl(window.location.href);
            var currentPath = normalizePath(window.location.href);
            var links = Array.prototype.slice.call(sidebar.querySelectorAll('.nav-sub a[href], .nav > a[href]'));
            var exactMatches = links.filter(function (link) {
                return normalizeUrl(link.href) === currentUrl;
            });
            var pathMatches = links.filter(function (link) {
                var target = normalizeUrl(link.href);
                return !target.includes('?') && target.split('?')[0] === currentPath;
            });
            var matches = exactMatches.length ? exactMatches : pathMatches;
            var activeLink = matches[0] || null;

            links.forEach(function (link) {
                link.classList.toggle('active', matches.indexOf(link) !== -1);
            });

            sidebar.querySelectorAll('.nav-group').forEach(function (group) {
                var hasActive = !!group.querySelector('a.active');
                group.open = hasActive || group.open;
                var parent = group.querySelector(':scope > .nav-parent');
                if (parent) parent.setAttribute('aria-expanded', String(group.open));
            });

            return activeLink;
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

        function syncPageBuilderCardLayout() {
            var cards = document.querySelectorAll('.pbi-card');
            if (!cards.length) return;

            var width = window.innerWidth || document.documentElement.clientWidth || 1200;
            var columns = width <= 420 ? 'minmax(0,1fr) 86px' : width <= 620 ? 'minmax(0,1fr) 104px' : width <= 900 ? 'minmax(0,1fr) 116px' : 'minmax(0,1fr) 128px';

            cards.forEach(function (card) {
                var icon = card.querySelector('.pbi-icon');
                var kind = card.querySelector('.pbi-kind');
                var excerpt = card.querySelector('.pbi-body > p');
                var body = card.querySelector('.pbi-body');
                var actions = card.querySelector('.pbi-actions');

                if (icon) {
                    icon.style.display = 'grid';
                    icon.style.gridColumn = '1';
                    icon.style.gridRow = '1';
                    icon.style.alignSelf = 'center';
                    icon.style.marginLeft = width <= 480 ? '9px' : width <= 720 ? '10px' : width <= 1050 ? '13px' : '16px';
                }
                if (kind) kind.style.display = 'none';
                if (excerpt) excerpt.style.display = 'none';

                card.style.gridTemplateColumns = columns;
                card.style.minHeight = width <= 620 ? (width <= 420 ? '88px' : '94px') : width <= 900 ? '100px' : '104px';

                if (body) {
                    body.style.gridColumn = '1';
                    body.style.gridRow = '1';
                    body.style.padding = width <= 480 ? '11px 8px 11px 56px' : width <= 720 ? '14px 12px 14px 66px' : '18px 22px 18px 76px';
                    body.style.minWidth = '0';
                }

                if (actions) {
                    actions.style.gridColumn = '2';
                    actions.style.gridRow = '1';
                    actions.style.height = '100%';
                    actions.style.display = 'flex';
                    actions.style.alignItems = 'center';
                    actions.style.justifyContent = 'center';
                    actions.style.gap = width <= 420 ? '6px' : width <= 620 ? '7px' : '12px';
                    actions.style.padding = width <= 420 ? '0 6px' : width <= 620 ? '0 7px' : '0 12px';
                }
            });
        }

        syncActiveNavigation();
        restoreDrawerWithoutAnimation();
        syncPageBuilderCardLayout();

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

        window.addEventListener('resize', syncPageBuilderCardLayout);
        window.addEventListener('pageshow', function () {
            syncActiveNavigation();
            restoreDrawerWithoutAnimation();
            syncPageBuilderCardLayout();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
