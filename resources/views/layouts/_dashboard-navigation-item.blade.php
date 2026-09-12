<style>
@media (min-width: 901px) {
    /* Accessibility/readability: keep mobile sizing unchanged while giving
       desktop administrators a larger, easier-to-read interface. */
    .sidebar .nav > a,
    .sidebar .nav-parent {
        font-size: 15px !important;
    }

    .sidebar .nav-sub a {
        font-size: 14px !important;
    }

    .sidebar .nav-label {
        font-size: 10px !important;
    }

    .content {
        font-size: 15px !important;
    }

    .content p,
    .content label,
    .content li,
    .content td,
    .content th,
    .content input,
    .content select,
    .content textarea,
    .content button,
    .content a,
    .content span,
    .content strong {
        font-size: 15px !important;
    }

    .content h2 {
        font-size: 22px !important;
    }

    .content h3 {
        font-size: 19px !important;
    }

    .content small {
        font-size: 13px !important;
    }

    .content .profile-info strong {
        font-size: 15px !important;
    }

    .content .profile-info span {
        font-size: 13px !important;
    }

    .content .folder-title h2 {
        font-size: 22px !important;
    }

    .content .folder-title span,
    .content .folder-meta,
    .content .profile-status,
    .content .folder-status {
        font-size: 13px !important;
    }

    .content .hero p,
    .content .builder-note,
    .content .notice,
    .content .errors {
        font-size: 14px !important;
    }

    .content .primary,
    .content .secondary,
    .content .add-profile {
        font-size: 14px !important;
    }
}
</style>

@php($hasChildren = $item->children->isNotEmpty())
@if($hasChildren)
<div class="nav-group {{ $item->children->contains(fn($child) => request()->url() === $child->url) ? 'open' : '' }}">
    <button type="button" class="nav-parent" aria-expanded="{{ $item->children->contains(fn($child) => request()->url() === $child->url) ? 'true' : 'false' }}">
        <span class="nav-icon"><i class="fa-solid fa-folder-tree"></i></span><span>{{ $item->displayLabel() }}</span><i class="fa-solid fa-chevron-down nav-chevron"></i>
    </button>
    <div class="nav-sub">
        @foreach($item->children as $child)
            @include('layouts._dashboard-navigation-item',['item'=>$child])
        @endforeach
    </div>
</div>
@else
<a class="{{ request()->url() === $item->url ? 'active' : '' }}" href="{{ $item->url }}" @if($item->target === '_blank') target="_blank" rel="noopener noreferrer" @endif>
    <span class="nav-icon"><i class="fa-solid fa-circle-dot"></i></span><span>{{ $item->displayLabel() }}</span>
</a>
@endif

@if(request()->routeIs('admin.documents'))
<script>
(() => {
    if (window.__fuelFreeChunkUploader) return;
    window.__fuelFreeChunkUploader = true;

    const init = () => {
        const form = document.querySelector('#upload-modal form[action*="/admin/documents"]');
        const input = document.getElementById('file-upload-input');
        if (!form || !input || form.dataset.chunkUploaderReady === '1') return;
        form.dataset.chunkUploaderReady = '1';
        input.removeAttribute('accept');

        const uploadButton = form.querySelector('button[type="submit"]');
        const modalActions = form.querySelector('.modal-actions');
        const maxBytes = {{ ((int) ($maxUploadMb ?? 50)) * 1024 * 1024 }};
        // Keep every HTTP request comfortably below common cPanel/LiteSpeed body limits.
        const chunkSize = 262144;

        let status = form.querySelector('.chunk-upload-status');
        if (!status) {
            status = document.createElement('div');
            status.className = 'chunk-upload-status';
            status.setAttribute('role', 'status');
            status.hidden = true;
            if (modalActions) form.insertBefore(status, modalActions);
        }

        input.addEventListener('change', () => {
            const file = input.files?.[0];
            if (file && file.size > maxBytes) {
                input.value = '';
                alert('The selected file is larger than the configured '+{{ (int) ($maxUploadMb ?? 50) }}+' MB limit.');
            }
        });

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const endpoint = @json(route('admin.documents.chunks'));

        const jsonResponse = async response => {
            const text = await response.text();
            let data = {};
            try { data = text ? JSON.parse(text) : {}; } catch (_) {}
            if (!response.ok) {
                const message = data.message || data.error || ('Upload request failed (' + response.status + ').');
                throw new Error(message);
            }
            return data;
        };

        const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));

        const requestWithRetry = async (url, options, attempts = 3) => {
            let lastError;
            for (let attempt = 1; attempt <= attempts; attempt++) {
                try {
                    const response = await fetch(url, options);
                    if (response.ok || ![408, 413, 429, 500, 502, 503, 504].includes(response.status)) return response;
                    lastError = new Error('Temporary upload error (' + response.status + ').');
                } catch (error) {
                    lastError = error;
                }
                if (attempt < attempts) await sleep(700 * attempt);
            }
            throw lastError || new Error('Upload request failed.');
        };

        form.addEventListener('submit', async event => {
            const file = input.files?.[0];
            if (!file) return;

            event.preventDefault();
            if (file.size > maxBytes) {
                alert('The selected file is larger than the configured '+{{ (int) ($maxUploadMb ?? 50) }}+' MB limit.');
                return;
            }

            const originalText = uploadButton?.textContent || 'Upload securely';
            if (uploadButton) { uploadButton.disabled = true; uploadButton.textContent = 'Preparing…'; }
            input.disabled = true;
            status.hidden = false;
            status.classList.remove('error');
            status.textContent = 'Preparing secure upload…';

            try {
                const params = new URLSearchParams();
                params.set('filename', file.name);
                params.set('size', String(file.size));
                params.set('mime_type', file.type || 'application/octet-stream');
                const folder = form.querySelector('[name="folder_id"]');
                if (folder?.value) params.set('folder_id', folder.value);

                const startResponse = await requestWithRetry(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                    },
                    body: params.toString()
                });
                const start = await jsonResponse(startResponse);
                const uploadId = start.upload_id;
                const actualChunkSize = Number(start.chunk_size) || chunkSize;
                if (!uploadId) throw new Error('The server did not create an upload session.');

                const totalChunks = Math.ceil(file.size / actualChunkSize);
                for (let index = 0; index < totalChunks; index++) {
                    const offset = index * actualChunkSize;
                    const blob = file.slice(offset, Math.min(offset + actualChunkSize, file.size));
                    const percent = Math.min(99, Math.round(((offset + blob.size) / file.size) * 100));
                    status.textContent = 'Uploading ' + percent + '% · part ' + (index + 1) + ' of ' + totalChunks;

                    const chunkResponse = await requestWithRetry(endpoint, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'Content-Type': 'application/octet-stream',
                            'X-Upload-Id': uploadId,
                            'X-Chunk-Index': String(index),
                            'X-Chunk-Offset': String(offset),
                            'X-Chunk-Length': String(blob.size)
                        },
                        body: blob
                    });
                    await jsonResponse(chunkResponse);
                }

                status.textContent = 'Finalizing file…';
                const finalResponse = await requestWithRetry(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-Upload-Id': uploadId
                    },
                    body: 'finalize=1'
                });
                await jsonResponse(finalResponse);
                status.textContent = 'Upload complete. Refreshing…';
                window.location.reload();
            } catch (error) {
                status.textContent = error?.message || 'Upload failed. Please try again.';
                status.classList.add('error');
                if (uploadButton) { uploadButton.disabled = false; uploadButton.textContent = originalText; }
                input.disabled = false;
            }
        });
    };

    const boot = () => { init(); setTimeout(init, 150); };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot, { once: true });
    else boot();
})();
</script>
<style>
.chunk-upload-status{margin-top:4px;padding:10px 12px;border:1px solid rgba(67,194,229,.16);border-radius:10px;background:rgba(67,194,229,.05);color:#8edff0;font-size:12px;line-height:1.5}.chunk-upload-status.error{border-color:rgba(220,70,70,.25);background:rgba(220,70,70,.08);color:#ffb7b7}
</style>
@endif