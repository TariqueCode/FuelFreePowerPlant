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
            var columns = width <= 480
                ? '48px minmax(0,1fr) 72px'
                : width <= 720
                    ? '58px minmax(0,1fr) 82px'
                    : width <= 1050
                        ? '62px minmax(0,1fr) 100px'
                        : '70px minmax(0,1fr) 128px';

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
                    body.style.setProperty('grid-column', '2', 'important');
                    body.style.setProperty('grid-row', '1', 'important');
                    body.style.setProperty('padding', width <= 480 ? '11px 8px' : width <= 720 ? '14px 12px' : '18px 20px', 'important');
                    body.style.setProperty('min-width', '0', 'important');
                }

                if (actions) {
                    actions.style.setProperty('grid-column', '3', 'important');
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

        function syncPageBuilderRibbon() {
            if (!window.location.pathname.match(/^\/admin\/page-builder(?:\/|$)/)) return;

            var editor = document.querySelector('.gcp-editor-card .ff-cms-editor');
            if (!editor) return;
            var ribbon = editor.querySelector('.ff-ribbon');
            if (!ribbon) return;

            var editorRect = editor.getBoundingClientRect();
            var stickyTop = 0;
            var shouldPin = editorRect.top <= stickyTop && editorRect.bottom > ribbon.offsetHeight;

            if (shouldPin) {
                if (!ribbon.classList.contains('ff-viewport-pinned')) {
                    var placeholder = ribbon.parentElement && ribbon.parentElement.querySelector('.ff-ribbon-placeholder');
                    if (!placeholder) {
                        placeholder = document.createElement('div');
                        placeholder.className = 'ff-ribbon-placeholder';
                        placeholder.setAttribute('aria-hidden', 'true');
                        ribbon.parentElement.insertBefore(placeholder, ribbon);
                    }
                    placeholder.style.height = ribbon.offsetHeight + 'px';
                    ribbon.classList.add('ff-viewport-pinned');
                }

                var rect = editor.getBoundingClientRect();
                ribbon.style.setProperty('position', 'fixed', 'important');
                ribbon.style.setProperty('top', '0px', 'important');
                ribbon.style.setProperty('left', rect.left + 'px', 'important');
                ribbon.style.setProperty('width', rect.width + 'px', 'important');
                ribbon.style.setProperty('z-index', '2000', 'important');
            } else {
                if (ribbon.classList.contains('ff-viewport-pinned')) {
                    var placeholder = ribbon.parentElement && ribbon.parentElement.querySelector('.ff-ribbon-placeholder');
                    if (placeholder) placeholder.remove();
                    ribbon.classList.remove('ff-viewport-pinned');
                }
                ribbon.style.removeProperty('position');
                ribbon.style.removeProperty('top');
                ribbon.style.removeProperty('left');
                ribbon.style.removeProperty('width');
                ribbon.style.removeProperty('z-index');
            }
        }

        removePageBuilderFrameworkCard();
        syncActiveNavigation();
        restoreDrawerWithoutAnimation();
        syncPageBuilderCardLayout();
        syncPageBuilderRibbon();

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

        window.addEventListener('resize', function () {
            syncPageBuilderCardLayout();
            syncPageBuilderRibbon();
        });
        window.addEventListener('scroll', syncPageBuilderRibbon, { passive: true });
        window.addEventListener('pageshow', function () {
            removePageBuilderFrameworkCard();
            syncActiveNavigation();
            restoreDrawerWithoutAnimation();
            syncPageBuilderCardLayout();
            syncPageBuilderRibbon();
        });

        window.setTimeout(syncPageBuilderRibbon, 100);
        window.setTimeout(syncPageBuilderRibbon, 500);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
