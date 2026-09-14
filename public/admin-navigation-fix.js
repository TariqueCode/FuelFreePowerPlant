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

        function removePageBuilderFrameworkCard() {
            document.querySelectorAll('.pbi-framework').forEach(function (card) {
                card.remove();
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
                    icon.style.setProperty('display', 'grid', 'important');
                    icon.style.setProperty('grid-column', '1', 'important');
                    icon.style.setProperty('grid-row', '1', 'important');
                    icon.style.setProperty('align-self', 'center', 'important');
                    icon.style.setProperty('margin-left', width <= 480 ? '9px' : width <= 720 ? '10px' : width <= 1050 ? '13px' : '16px', 'important');
                }
                if (kind) kind.style.setProperty('display', 'none', 'important');
                if (excerpt) excerpt.style.setProperty('display', 'none', 'important');

                card.style.setProperty('grid-template-columns', columns, 'important');
                card.style.setProperty('min-height', width <= 620 ? (width <= 420 ? '88px' : '94px') : width <= 900 ? '100px' : '104px', 'important');

                if (body) {
                    body.style.setProperty('grid-column', '1', 'important');
                    body.style.setProperty('grid-row', '1', 'important');
                    body.style.setProperty('padding', width <= 480 ? '11px 8px 11px 56px' : width <= 720 ? '14px 12px 14px 66px' : '18px 22px 18px 76px', 'important');
                    body.style.setProperty('min-width', '0', 'important');
                }

                if (actions) {
                    actions.style.setProperty('grid-column', '2', 'important');
                    actions.style.setProperty('grid-row', '1', 'important');
                    actions.style.setProperty('height', '100%', 'important');
                    actions.style.setProperty('display', 'flex', 'important');
                    actions.style.setProperty('align-items', 'center', 'important');
                    actions.style.setProperty('justify-content', 'center', 'important');
                    actions.style.setProperty('gap', width <= 420 ? '6px' : width <= 620 ? '7px' : '12px', 'important');
                    actions.style.setProperty('padding', width <= 420 ? '0 6px' : width <= 620 ? '0 7px' : '0 12px', 'important');
                }
            });
        }

        removePageBuilderFrameworkCard();
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
            removePageBuilderFrameworkCard();
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
