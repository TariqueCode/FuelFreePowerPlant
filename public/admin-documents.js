(function () {
    'use strict';

    // Keep the administration drawer and expanded navigation groups stable while
    // moving between admin pages. This is intentionally initialized before the
    // document-manager guard because the shared portal loads this asset globally.
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

                // Keep the drawer open through the next page load so selecting a
                // submenu does not make the administration navigation disappear.
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

    function isDocumentsPage() {
        return !!document.getElementById('fm-upload-dialog') || !!document.getElementById('folder-grid');
    }

    if (!isDocumentsPage()) return;

    var uploadDialog = document.getElementById('fm-upload-dialog');
    var uploadQueue = document.getElementById('fm-upload-queue');
    var startUpload = document.getElementById('fm-start-upload');
    var fileInput = document.getElementById('file-upload-input');
    var folderGrid = document.getElementById('folder-grid');

    function queueHasItems() {
        return !!uploadQueue && Array.prototype.some.call(uploadQueue.children, function (child) {
            return child.nodeType === 1 && !child.matches('.fm-empty, [hidden]');
        });
    }

    function queueIsComplete() {
        if (!queueHasItems()) return false;

        var text = (uploadQueue.textContent || '').toLowerCase();
        if (/uploading|pending|queued|starting|processing|failed|error|retry/.test(text)) return false;

        var progressBars = uploadQueue.querySelectorAll('progress, [role="progressbar"], .fm-upload-progress, .fm-progress');
        for (var i = 0; i < progressBars.length; i += 1) {
            var bar = progressBars[i];
            var value = Number(bar.value);
            var max = Number(bar.max || 100);
            if (Number.isFinite(value) && Number.isFinite(max) && max > 0 && value < max) return false;
            var width = parseFloat((bar.style && bar.style.width) || '100');
            if (Number.isFinite(width) && width < 99.9 && /progress/.test(String(bar.className || '').toLowerCase())) return false;
        }

        return /complete|completed|100\s*%/.test(text) || uploadQueue.querySelectorAll('[data-upload-status="complete"], .is-complete, .complete').length > 0;
    }

    function syncUploadButton() {
        if (!startUpload) return;

        if (queueIsComplete()) {
            startUpload.hidden = true;
            startUpload.disabled = true;
            startUpload.setAttribute('aria-hidden', 'true');
            return;
        }

        if (!queueHasItems()) {
            startUpload.hidden = false;
            startUpload.disabled = false;
            startUpload.removeAttribute('aria-hidden');
            return;
        }

        startUpload.hidden = false;
        startUpload.disabled = false;
        startUpload.removeAttribute('aria-hidden');
    }

    if (uploadQueue) {
        new MutationObserver(function () {
            syncUploadButton();
        }).observe(uploadQueue, { childList: true, subtree: true, characterData: true, attributes: true });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (startUpload) {
                startUpload.hidden = false;
                startUpload.disabled = false;
                startUpload.removeAttribute('aria-hidden');
            }
        });
    }

    if (uploadDialog) {
        uploadDialog.addEventListener('click', function (event) {
            var close = event.target.closest('[data-close-dialog]');
            if (close && startUpload) {
                startUpload.hidden = false;
                startUpload.disabled = false;
                startUpload.removeAttribute('aria-hidden');
            }
        });
    }

    function folderCardFromElement(element) {
        return element && element.closest ? element.closest('.fm-folder-card') : null;
    }

    var pendingFolderDelete = null;

    document.addEventListener('click', function (event) {
        var deleteButton = event.target.closest('[data-action="delete-folder"]');
        if (!deleteButton) return;
        pendingFolderDelete = {
            url: deleteButton.getAttribute('data-url') || '',
            card: folderCardFromElement(deleteButton),
            name: deleteButton.getAttribute('data-name') || ''
        };
    }, true);

    function removeDeletedFolder(url) {
        if (!pendingFolderDelete || !pendingFolderDelete.card) return;
        if (pendingFolderDelete.url && url && pendingFolderDelete.url !== url) return;

        var card = pendingFolderDelete.card;
        if (!document.documentElement.contains(card)) {
            pendingFolderDelete = null;
            return;
        }

        card.style.pointerEvents = 'none';
        card.style.opacity = '0';
        card.style.transform = 'translateY(-4px)';
        card.style.transition = 'opacity .18s ease, transform .18s ease';

        window.setTimeout(function () {
            if (card.parentNode) card.parentNode.removeChild(card);
            updateFolderCount();
            pendingFolderDelete = null;
        }, 190);
    }

    function updateFolderCount() {
        if (!folderGrid) return;
        var cards = folderGrid.querySelectorAll('.fm-folder-card');
        var count = document.querySelector('.fm-panel:first-of-type .fm-count');
        if (count) count.textContent = cards.length + ' folder(s)';

        if (!cards.length) {
            var empty = document.createElement('div');
            empty.className = 'fm-empty';
            empty.innerHTML = '<i class="fa-regular fa-folder-open"></i><b>No folders here</b><span>Create a folder to keep this workspace organized.</span><button type="button" class="fm-text-btn" data-open-folder>Create a folder</button>';
            folderGrid.appendChild(empty);
        }
    }

    function successfulResponse(response, url) {
        if (!response || !response.ok) return;
        if (pendingFolderDelete && pendingFolderDelete.url === url) {
            removeDeletedFolder(url);
        }
    }

    if (window.fetch) {
        var originalFetch = window.fetch;
        window.fetch = function () {
            var args = arguments;
            var requestUrl = '';
            try {
                requestUrl = typeof args[0] === 'string' ? args[0] : (args[0] && args[0].url) || '';
            } catch (e) {}

            return originalFetch.apply(this, args).then(function (response) {
                successfulResponse(response, requestUrl);
                syncUploadButton();
                return response;
            });
        };
    }

    var originalOpen = XMLHttpRequest.prototype.open;
    var originalSend = XMLHttpRequest.prototype.send;

    XMLHttpRequest.prototype.open = function (method, url) {
        this.__ffpDocumentsMethod = method;
        this.__ffpDocumentsUrl = url;
        return originalOpen.apply(this, arguments);
    };

    XMLHttpRequest.prototype.send = function () {
        var xhr = this;
        xhr.addEventListener('load', function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                successfulResponse(xhr, xhr.__ffpDocumentsUrl || '');
            }
            syncUploadButton();
        });
        return originalSend.apply(this, arguments);
    };

    syncUploadButton();
})();
