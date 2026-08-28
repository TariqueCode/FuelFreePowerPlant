@extends('layouts.portal')

@section('title', $slider->exists ? 'Edit Slider' : 'Add Slider')

@section('content')
<section class="hero">
    <div>
        <span class="eyebrow">HOMEPAGE SLIDER</span>
        <h1>{{ $slider->exists ? 'Edit' : 'Add' }} slider image</h1>
        <p>Use a high-quality company image. Published images rotate automatically above the homepage welcome message.</p>
    </div>
    <a class="back" href="{{ route('admin.sliders.index') }}">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>
</section>

@if(session('error'))
    <div class="errors" role="alert">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="errors" role="alert">{{ $errors->first() }}</div>
@endif

<div class="card">
    <form id="slider-form" method="POST"
          enctype="multipart/form-data"
          action="{{ $slider->exists ? route('admin.sliders.update', $slider) : route('admin.sliders.store') }}">
        @csrf
        @if($slider->exists)
            @method('PATCH')
        @endif

        <div class="grid">
            <div class="full">
                <label for="image">
                    Slider image {{ $slider->exists ? '(leave empty to keep current)' : '' }}
                </label>
                <input id="image"
                       type="file"
                       name="image"
                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                       {{ $slider->exists ? '' : 'required' }}>
                <div id="image-feedback" class="field-feedback" aria-live="polite"></div>

                <div class="upload-guide" role="note">
                    <i class="fa-solid fa-ruler-combined"></i>
                    <div>
                        <strong>Recommended slider image</strong>
                        <span>Use <b>1600 × 700 px</b> (2.29:1) for the best desktop, tablet and mobile crop. JPG or WebP is recommended.</span>
                    </div>
                </div>
            </div>

            <div class="full">
                @if($slider->image_path)
                    <img class="preview"
                         src="{{ asset('storage/'.$slider->image_path) }}"
                         alt="Current slider image">
                @endif
            </div>

            <div class="full">
                <label for="title">Title (optional)</label>
                <input id="title" name="title"
                       value="{{ old('title', $slider->title) }}"
                       maxlength="255" placeholder="e.g. Our power project">
            </div>

            <div class="full">
                <label for="link_url">Destination URL (optional)</label>
                <input id="link_url" type="url" name="link_url"
                       value="{{ old('link_url', $slider->link_url) }}"
                       placeholder="https://example.com">
            </div>

            <div>
                <label for="starts_at">Start time (optional)</label>
                <input id="starts_at" type="datetime-local" name="starts_at"
                       value="{{ old('starts_at', $slider->starts_at?->format('Y-m-d\\TH:i')) }}">
            </div>

            <div>
                <label for="ends_at">End time (optional)</label>
                <input id="ends_at" type="datetime-local" name="ends_at"
                       value="{{ old('ends_at', $slider->ends_at?->format('Y-m-d\\TH:i')) }}">
            </div>

            <div class="full check">
                <label for="is_published">
                    <input id="is_published" type="checkbox" name="is_published" value="1"
                           @checked(old('is_published', $slider->is_published))>
                    Show this image on the homepage slider
                </label>
            </div>
        </div>

        <div class="actions">
            <a class="back" href="{{ route('admin.sliders.index') }}">Cancel</a>
            <button id="save-slider" class="save" type="submit">
                <i class="fa-solid fa-floppy-disk"></i> <span>Save slider</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.card{max-width:900px;padding:20px;border:1px solid var(--line);border-radius:18px;background:rgba(255,255,255,.02)}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:15px}.full{grid-column:1/-1}
label{display:block;color:#89a7b2;font-size:10px;margin-bottom:7px}
input{width:100%;box-sizing:border-box;border:1px solid var(--line);border-radius:10px;background:#061923;color:#e4f3f7;padding:11px;font:inherit;font-size:11px}
.check label{display:flex;align-items:center;gap:8px;color:#b5cbd4}.check input{width:auto}
.preview{max-width:100%;max-height:320px;border-radius:14px;border:1px solid var(--line);display:block}
.actions{display:flex;justify-content:flex-end;gap:9px;margin-top:20px}
.back{display:inline-flex;align-items:center;gap:7px;padding:10px 13px;border:1px solid var(--line);border-radius:11px;color:#9db9c2;text-decoration:none;font-size:10px}
.save{border:0;border-radius:11px;padding:12px 16px;background:#29aaca;color:#fff;font-weight:700;cursor:pointer}
.save:disabled{opacity:.65;cursor:wait}
.errors{margin:0 0 14px;padding:11px 13px;border:1px solid rgba(255,93,113,.3);border-radius:12px;background:rgba(255,93,113,.08);color:#ffb1ba;font-size:10px;line-height:1.5}
.field-feedback{display:none;margin-top:7px;color:#ffb1ba;font-size:9px;line-height:1.4}
.upload-guide{display:flex;align-items:flex-start;gap:11px;margin:8px 0 14px;padding:11px 13px;border:1px solid rgba(72,216,241,.18);border-radius:12px;background:rgba(72,216,241,.045);color:#8aa7af;font-size:10px;line-height:1.5}
.upload-guide i{color:#58cfe7;margin-top:2px}.upload-guide div{display:flex;flex-direction:column;gap:2px}
.upload-guide strong{color:#dff8fc;font-size:11px}.upload-guide b{color:#7ee4f4;font-weight:800}
@media(max-width:650px){.grid{grid-template-columns:1fr}.full{grid-column:auto}.actions{justify-content:stretch}.actions>*{flex:1;justify-content:center;text-align:center}.save{width:100%}}
</style>
@endpush

@push('scripts')
<script>
(() => {
    const form = document.getElementById('slider-form');
    const input = document.getElementById('image');
    const feedback = document.getElementById('image-feedback');
    const button = document.getElementById('save-slider');

    if (!form || !input) return;

    input.addEventListener('change', () => {
        feedback.style.display = 'none';
        feedback.textContent = '';

        const file = input.files && input.files[0];
        if (!file) return;

        const allowed = ['image/jpeg', 'image/png', 'image/webp'];
        const maxBytes = 10 * 1024 * 1024;

        if (!allowed.includes(file.type)) {
            input.value = '';
            feedback.textContent = 'Please choose a JPG, PNG or WebP image.';
            feedback.style.display = 'block';
            return;
        }

        if (file.size > maxBytes) {
            input.value = '';
            feedback.textContent = 'The image is larger than 10 MB. Please choose a smaller image.';
            feedback.style.display = 'block';
            return;
        }

        feedback.textContent = file.name + ' — ' + (file.size / 1024 / 1024).toFixed(2) + ' MB';
        feedback.style.display = 'block';
        feedback.style.color = '#73dcbf';
    });

    form.addEventListener('submit', () => {
        const file = input.files && input.files[0];

        if (!{{ $slider->exists ? 'true' : 'false' }} && !file) {
            feedback.textContent = 'Please choose a slider image before saving.';
            feedback.style.display = 'block';
            feedback.style.color = '#ffb1ba';
            return;
        }

        if (file && file.size > 10 * 1024 * 1024) {
            feedback.textContent = 'The image is larger than 10 MB. Please choose a smaller image.';
            feedback.style.display = 'block';
            feedback.style.color = '#ffb1ba';
            return;
        }

        button.disabled = true;
        button.querySelector('span').textContent = 'Saving…';
    });
})();
</script>
@endpush
