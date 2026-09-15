@extends('layouts.portal')

@section('title', $slider->exists ? 'Edit Slider' : 'Add Slider')

@section('content')
<section class="slider-page-header">
    <div>
        <span class="eyebrow">HOMEPAGE SLIDER</span>
        <h1>{{ $slider->exists ? 'Edit' : 'Add' }} slider image</h1>
        <p>Use a high-quality company image. Published images rotate automatically above the homepage welcome message.</p>
    </div>
    <a class="slider-back" href="{{ route('admin.sliders.index') }}">
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
            <a class="slider-cancel" href="{{ route('admin.sliders.index') }}"><i class="fa-solid fa-xmark"></i><span>Cancel</span></a>
            <button id="save-slider" class="save" type="submit">
                <i class="fa-solid fa-floppy-disk"></i> <span>Save slider</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
/* FuelFree PowerPlant — PREMIUM SLIDER FORM / RESPONSIVE */

.card{
    width:min(1120px,100%);
    max-width:none;
    box-sizing:border-box;
    padding:28px;
    border:1px solid rgba(91,190,211,.16);
    border-radius:20px;
    background:
        linear-gradient(145deg,rgba(10,31,40,.96),rgba(4,20,28,.96));
    box-shadow:0 14px 40px rgba(0,0,0,.18);
}

.grid{
    display:grid;
    grid-template-columns:minmax(0,1fr) minmax(0,1fr);
    gap:22px;
}

.full{grid-column:1/-1}

label{
    display:block;
    color:#a5c1ca;
    font-size:12px;
    font-weight:700;
    letter-spacing:.02em;
    margin-bottom:8px;
}

input{
    width:100%;
    min-height:48px;
    box-sizing:border-box;
    border:1px solid rgba(116,188,205,.17);
    border-radius:12px;
    background:#061923;
    color:#e8f7fa;
    padding:12px 14px;
    font:inherit;
    font-size:13px;
}

input::placeholder{color:#58747e}

input:hover{
    border-color:rgba(80,204,224,.28);
}

input:focus{
    outline:none;
    border-color:rgba(76,205,226,.55);
    box-shadow:0 0 0 3px rgba(76,205,226,.09);
}

input[type="file"]{
    padding:8px;
    cursor:pointer;
}

input[type="file"]::file-selector-button{
    border:1px solid rgba(91,208,225,.22);
    border-radius:9px;
    background:#0c3440;
    color:#dff8fb;
    padding:9px 13px;
    margin-right:10px;
    cursor:pointer;
    font-weight:700;
}

.preview{
    width:100%;
    max-width:100%;
    max-height:390px;
    object-fit:cover;
    object-position:center;
    border-radius:16px;
    border:1px solid rgba(105,199,215,.18);
    display:block;
    background:#061923;
}

.upload-guide{
    display:flex;
    align-items:flex-start;
    gap:13px;
    margin-top:12px;
    padding:15px 16px;
    border:1px solid rgba(72,216,241,.16);
    border-radius:14px;
    background:rgba(72,216,241,.035);
    color:#8faeb7;
    font-size:11px;
    line-height:1.55;
}

.upload-guide i{
    color:#58d2e8;
    font-size:16px;
    margin-top:2px;
}

.upload-guide div{
    display:flex;
    flex-direction:column;
    gap:3px;
}

.upload-guide strong{
    color:#e1f8fb;
    font-size:12px;
}

.upload-guide b{
    color:#7ee4f4;
    font-weight:800;
}

.check{
    padding:14px 16px;
    border:1px solid rgba(115,199,211,.12);
    border-radius:13px;
    background:rgba(255,255,255,.018);
}

.check label{
    display:flex;
    align-items:center;
    gap:10px;
    margin:0;
    color:#bdd2d8;
    font-size:12px;
    cursor:pointer;
}

.check input{
    width:18px;
    min-height:18px;
    accent-color:#31c79d;
}

.actions{
    display:flex;
    justify-content:flex-end;
    align-items:center;
    gap:10px;
    margin-top:26px;
    padding-top:20px;
    border-top:1px solid rgba(115,199,211,.10);
}

.back{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:7px;
    min-height:44px;
    box-sizing:border-box;
    padding:10px 16px;
    border:1px solid rgba(116,188,205,.16);
    border-radius:11px;
    color:#a8c2ca;
    text-decoration:none;
    font-size:12px;
    font-weight:700;
}

.back:hover{
    color:#e5f8fb;
    background:rgba(255,255,255,.035);
}

.save{
    min-height:46px;
    border:0;
    border-radius:11px;
    padding:11px 19px;
    background:linear-gradient(135deg,#2ab5d5,#28a9c9);
    color:#fff;
    font-weight:800;
    font-size:12px;
    cursor:pointer;
    box-shadow:0 7px 18px rgba(42,181,213,.13);
}

.save:hover{
    background:linear-gradient(135deg,#35c1df,#2bb4d2);
}

.save:disabled{
    opacity:.65;
    cursor:wait;
}

.errors{
    width:min(1120px,100%);
    box-sizing:border-box;
    margin:0 0 16px;
    padding:13px 15px;
    border:1px solid rgba(255,93,113,.28);
    border-radius:13px;
    background:rgba(255,93,113,.07);
    color:#ffb1ba;
    font-size:11px;
    line-height:1.5;
}

.field-feedback{
    display:none;
    margin-top:8px;
    color:#ffb1ba;
    font-size:10px;
    line-height:1.45;
}

@media(max-width:1000px){
    .card{
        padding:22px;
    }

    .grid{
        gap:18px;
    }

    .preview{
        max-height:330px;
    }
}

@media(max-width:760px){
    .card{
        padding:18px;
        border-radius:16px;
    }

    .grid{
        grid-template-columns:1fr;
        gap:16px;
    }

    .full{
        grid-column:auto;
    }

    .preview{
        max-height:none;
        aspect-ratio:16/7;
        object-fit:cover;
    }

    .actions{
        justify-content:stretch;
    }

    .actions>*{
        flex:1 1 0;
        text-align:center;
    }
}

@media(max-width:520px){
    .card{
        padding:14px;
        border-radius:14px;
    }

    label{
        font-size:11px;
    }

    input{
        min-height:46px;
        font-size:12px;
        padding:11px 12px;
    }

    .upload-guide{
        padding:13px;
        font-size:10px;
    }

    .actions{
        flex-direction:column-reverse;
        gap:8px;
    }

    .actions>*{
        width:100%;
        flex:none;
    }

    .save,
    .back{
        width:100%;
    }
}

@media(max-width:380px){
    .card{
        padding:11px;
    }

    .upload-guide{
        gap:9px;
    }

    .check{
        padding:12px;
    }
}

@media(prefers-reduced-motion:reduce){
    .card *,
    .card *::before,
    .card *::after{
        animation:none!important;
        transition:none!important;
    }
}
/* FuelFree PowerPlant — PREMIUM HEADER BACK BUTTON */
.slider-page-header .slider-back{
display:inline-flex!important;
align-items:center!important;
justify-content:center!important;
gap:9px!important;
height:42px!important;
box-sizing:border-box!important;
margin-top:16px!important;
padding:0 17px!important;
border:1px solid rgba(73,211,226,.24)!important;
border-radius:12px!important;
background:linear-gradient(135deg,rgba(8,43,52,.92),rgba(5,29,38,.96))!important;
color:#b9dce3!important;
text-decoration:none!important;
font-size:11px!important;
font-weight:800!important;
letter-spacing:.01em!important;
box-shadow:0 6px 16px rgba(0,0,0,.12),inset 0 1px 0 rgba(255,255,255,.035)!important;
}
.slider-page-header .slider-back i{
color:#55d9e7!important;
font-size:12px!important;
}
.slider-page-header .slider-back:hover{
color:#ecfcff!important;
border-color:rgba(78,220,235,.52)!important;
background:linear-gradient(135deg,rgba(9,54,64,.98),rgba(5,34,43,.98))!important;
}
.slider-page-header .slider-back:hover i{
color:#63e5d0!important;
}
.slider-page-header .slider-back:focus-visible{
outline:2px solid rgba(79,216,231,.55)!important;
outline-offset:3px!important;
}
@media(max-width:560px){
.slider-page-header .slider-back{
width:100%!important;
height:43px!important;
margin-top:15px!important;
}
}
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
        const maxBytes = {{ ((int) ($maxUploadMb ?? 50)) * 1024 * 1024 }};

        if (!allowed.includes(file.type)) {
            input.value = '';
            feedback.textContent = 'Please choose a JPG, PNG or WebP image.';
            feedback.style.display = 'block';
            return;
        }

        if (file.size > maxBytes) {
            input.value = '';
            feedback.textContent = 'The image is larger than {{ $maxUploadMb ?? 50 }} MB. Please choose a smaller image.';
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

        if (file && file.size > {{ ((int) ($maxUploadMb ?? 50)) * 1024 * 1024 }}) {
            feedback.textContent = 'The image is larger than {{ $maxUploadMb ?? 50 }} MB. Please choose a smaller image.';
            feedback.style.display = 'block';
            feedback.style.color = '#ffb1ba';
            return;
        }

        button.disabled = true;
        button.querySelector('span').textContent = 'Saving…';
    });
})();
</script>

<style>

/* FuelFree PowerPlant — FINAL PREMIUM SLIDER OVERRIDE */

.hero{
    max-width:1120px!important;
    margin:0 auto 20px!important;
    padding:4px 2px 8px!important;
    background:transparent!important;
    border:0!important;
    border-radius:0!important;
    box-shadow:none!important;
}

.hero .eyebrow{
    color:#59dfbd!important;
    font-weight:800!important;
    letter-spacing:.14em!important;
}

.hero h1{
    margin:7px 0 8px!important;
    font-size:clamp(28px,3vw,42px)!important;
    line-height:1.08!important;
    letter-spacing:-.035em!important;
}

.hero p{
    max-width:850px!important;
    margin:0 0 14px!important;
    color:#8faeb7!important;
    font-size:13px!important;
    line-height:1.65!important;
}

.card{
    max-width:1120px!important;
    margin:0 auto!important;
    padding:22px!important;
    border:1px solid rgba(74,205,225,.16)!important;
    border-radius:20px!important;
    background:linear-gradient(145deg,rgba(7,30,39,.97),rgba(3,21,28,.99))!important;
    box-shadow:0 12px 35px rgba(0,0,0,.18)!important;
}

.grid{
    display:grid!important;
    grid-template-columns:minmax(0,1.35fr) minmax(280px,.65fr)!important;
    gap:18px!important;
}

.full{grid-column:1/-1!important}

.card label{
    display:block!important;
    margin:0 0 8px!important;
    color:#a9c5cc!important;
    font-size:11px!important;
    font-weight:800!important;
}

.card input[type="text"],
.card input[type="url"],
.card input[type="datetime-local"]{
    width:100%!important;
    min-height:46px!important;
    box-sizing:border-box!important;
    padding:12px 14px!important;
    border:1px solid rgba(77,201,222,.17)!important;
    border-radius:12px!important;
    outline:0!important;
    background:#041821!important;
    color:#e8f8fb!important;
    font-size:12px!important;
}

.card input:focus{
    border-color:rgba(77,211,231,.62)!important;
    box-shadow:0 0 0 3px rgba(77,211,231,.08)!important;
}

.card input::placeholder{
    color:#526f78!important;
}

.card input[type="file"]{
    width:100%!important;
    min-height:50px!important;
    box-sizing:border-box!important;
    padding:6px!important;
    border:1px dashed rgba(73,211,231,.42)!important;
    border-radius:13px!important;
    background:rgba(5,28,37,.88)!important;
    color:#9bb6be!important;
    font-size:11px!important;
}

.card input[type="file"]::file-selector-button{
    margin-right:10px!important;
    padding:10px 15px!important;
    border:1px solid rgba(73,211,231,.35)!important;
    border-radius:10px!important;
    background:rgba(48,174,201,.16)!important;
    color:#dffaff!important;
    font-weight:800!important;
    cursor:pointer!important;
}

.upload-guide{
    display:flex!important;
    align-items:flex-start!important;
    gap:11px!important;
    margin:10px 0 0!important;
    padding:13px 14px!important;
    border:1px solid rgba(79,207,226,.14)!important;
    border-radius:13px!important;
    background:rgba(54,190,211,.035)!important;
    color:#8daab2!important;
    font-size:10px!important;
    line-height:1.55!important;
}

.upload-guide i{
    color:#55d8e8!important;
    font-size:14px!important;
    margin-top:2px!important;
}

.upload-guide strong{
    color:#dff8fc!important;
    font-size:11px!important;
}

.upload-guide b{
    color:#70e3f0!important;
}

.preview{
    width:100%!important;
    max-width:100%!important;
    max-height:350px!important;
    object-fit:cover!important;
    border-radius:15px!important;
    border:1px solid rgba(76,207,225,.18)!important;
    display:block!important;
    background:#041821!important;
}

.check{
    margin-top:2px!important;
    padding:14px 16px!important;
    border:1px solid rgba(71,209,190,.15)!important;
    border-radius:14px!important;
    background:rgba(55,205,174,.035)!important;
}

.check label{
    display:flex!important;
    align-items:center!important;
    gap:10px!important;
    margin:0!important;
    color:#c2d8dd!important;
}

.check input{
    width:18px!important;
    height:18px!important;
    accent-color:#42d7b0!important;
}

.actions{
    display:flex!important;
    justify-content:flex-end!important;
    align-items:center!important;
    gap:10px!important;
    margin-top:20px!important;
    padding-top:17px!important;
    border-top:1px solid rgba(75,204,222,.10)!important;
}

.back{
    display:inline-flex!important;
    align-items:center!important;
    justify-content:center!important;
    gap:7px!important;
    min-height:42px!important;
    padding:0 15px!important;
    border:1px solid rgba(80,196,215,.17)!important;
    border-radius:11px!important;
    background:rgba(255,255,255,.015)!important;
    color:#a8c1c8!important;
    text-decoration:none!important;
    font-size:11px!important;
    font-weight:700!important;
}

.save{
    min-height:42px!important;
    border:1px solid rgba(92,228,235,.28)!important;
    border-radius:11px!important;
    padding:0 18px!important;
    background:linear-gradient(135deg,#28aebe,#31c7b2)!important;
    color:#fff!important;
    font-size:11px!important;
    font-weight:800!important;
    cursor:pointer!important;
    box-shadow:0 7px 18px rgba(36,183,190,.13)!important;
}

.save:disabled{
    opacity:.65!important;
    cursor:wait!important;
}

.errors{
    max-width:1120px!important;
    margin:0 auto 14px!important;
}

@media(max-width:900px){
    .card{
        padding:18px!important;
    }

    .grid{
        grid-template-columns:1fr!important;
    }

    .full{
        grid-column:auto!important;
    }

    .preview{
        max-height:300px!important;
    }
}

@media(max-width:560px){
    .hero h1{
        font-size:28px!important;
    }

    .hero p{
        font-size:11px!important;
    }

    .card{
        padding:14px!important;
        border-radius:16px!important;
    }

    .actions{
        flex-direction:column-reverse!important;
        align-items:stretch!important;
    }

    .actions>*{
        width:100%!important;
    }

    .back,
    .save{
        width:100%!important;
    }
}

@media(max-width:380px){
    .hero h1{
        font-size:25px!important;
    }

    .card{
        padding:11px!important;
    }
}

@media(prefers-reduced-motion:reduce){
    .hero,
    .card,
    .card *{
        animation:none!important;
        transition:none!important;
    }
}

/* FuelFree PowerPlant — PREMIUM HEADER BACK BUTTON */
.slider-page-header .slider-back{
display:inline-flex!important;
align-items:center!important;
justify-content:center!important;
gap:9px!important;
height:42px!important;
box-sizing:border-box!important;
margin-top:16px!important;
padding:0 17px!important;
border:1px solid rgba(73,211,226,.24)!important;
border-radius:12px!important;
background:linear-gradient(135deg,rgba(8,43,52,.92),rgba(5,29,38,.96))!important;
color:#b9dce3!important;
text-decoration:none!important;
font-size:11px!important;
font-weight:800!important;
letter-spacing:.01em!important;
box-shadow:0 6px 16px rgba(0,0,0,.12),inset 0 1px 0 rgba(255,255,255,.035)!important;
}
.slider-page-header .slider-back i{
color:#55d9e7!important;
font-size:12px!important;
}
.slider-page-header .slider-back:hover{
color:#ecfcff!important;
border-color:rgba(78,220,235,.52)!important;
background:linear-gradient(135deg,rgba(9,54,64,.98),rgba(5,34,43,.98))!important;
}
.slider-page-header .slider-back:hover i{
color:#63e5d0!important;
}
.slider-page-header .slider-back:focus-visible{
outline:2px solid rgba(79,216,231,.55)!important;
outline-offset:3px!important;
}
@media(max-width:560px){
.slider-page-header .slider-back{
width:100%!important;
height:43px!important;
margin-top:15px!important;
}
}
</style>
@endpush

@push('styles')
<style>
/* FuelFree PowerPlant — PREMIUM SLIDER WORKSPACE */

.slider-page-header{
    max-width:1120px!important;
    margin:0 auto 20px!important;
    padding:4px 2px!important;
    background:transparent!important;
    border:0!important;
    box-shadow:none!important;
}

.slider-page-header .eyebrow{
    color:#59dfbd!important;
    font-weight:900!important;
    letter-spacing:.14em!important;
}

.slider-page-header h1{
    margin:7px 0 8px!important;
    color:#effcff!important;
    font-size:clamp(30px,3vw,43px)!important;
    line-height:1.05!important;
}

.slider-page-header p{
    max-width:900px!important;
    color:#88a8b1!important;
    font-size:12px!important;
    line-height:1.65!important;
}

.card{
    max-width:1120px!important;
    margin:0 auto!important;
    padding:0!important;
    overflow:hidden!important;
    border:1px solid rgba(73,204,224,.17)!important;
    border-radius:20px!important;
    background:linear-gradient(145deg,rgba(6,30,39,.97),rgba(3,20,27,.99))!important;
    box-shadow:0 14px 38px rgba(0,0,0,.16)!important;
}

.grid{
    display:grid!important;
    grid-template-columns:minmax(0,1.3fr) minmax(300px,.7fr)!important;
    gap:0!important;
    padding:22px!important;
}

.grid > .full:nth-child(1){
    grid-column:1!important;
}

.grid > .full:nth-child(2){
    grid-column:2!important;
    grid-row:1!important;
    margin-left:18px!important;
}

.grid > .full:nth-child(3),
.grid > .full:nth-child(4){
    grid-column:1/-1!important;
}

.grid > div:not(.full){
    min-width:0!important;
}

.grid label{
    color:#a9c5cc!important;
    font-size:10px!important;
    font-weight:850!important;
}

.grid input{
    min-height:46px!important;
    border-color:rgba(76,201,222,.17)!important;
    border-radius:12px!important;
    background:#041820!important;
}

.grid input:focus{
    border-color:rgba(77,216,231,.58)!important;
    box-shadow:0 0 0 3px rgba(77,216,231,.07)!important;
}

.grid > .full:nth-child(1) > label:first-child{
    display:flex!important;
    align-items:center!important;
    gap:7px!important;
}

.grid > .full:nth-child(1) > label:first-child:before{
    content:"\\f1c5";
    font-family:"Font Awesome 6 Free";
    font-weight:900;
    color:#52d8ca;
}

.drag-drop-zone{
    position:relative!important;
    min-height:145px!important;
    display:flex!important;
    flex-direction:column!important;
    align-items:center!important;
    justify-content:center!important;
    text-align:center!important;
    border:1px dashed rgba(65,211,231,.48)!important;
    border-radius:15px!important;
    background:rgba(4,28,37,.72)!important;
    cursor:pointer!important;
}

.drag-drop-zone:hover,
.drag-drop-zone.is-dragging{
    border-color:#55dce9!important;
    background:rgba(8,40,50,.8)!important;
}

.drag-drop-zone .drop-icon{
    width:44px!important;
    height:44px!important;
    display:flex!important;
    align-items:center!important;
    justify-content:center!important;
    margin-bottom:8px!important;
    border-radius:12px!important;
    background:rgba(55,205,224,.10)!important;
    color:#55d9ed!important;
    font-size:19px!important;
}

.drag-drop-zone strong{
    color:#e4f9fb!important;
    font-size:12px!important;
}

.drag-drop-zone span{
    margin-top:5px!important;
    color:#6f8c95!important;
    font-size:9px!important;
}

.drag-drop-zone input[type="file"]{
    position:absolute!important;
    inset:0!important;
    width:100%!important;
    height:100%!important;
    opacity:0!important;
    cursor:pointer!important;
}

.upload-guide{
    border-color:rgba(76,211,231,.15)!important;
    background:rgba(53,197,216,.035)!important;
}

.preview{
    width:100%!important;
    height:100%!important;
    min-height:230px!important;
    max-height:270px!important;
    object-fit:cover!important;
    border-radius:15px!important;
    border:1px solid rgba(76,207,225,.18)!important;
}

.preview-placeholder{
    min-height:230px!important;
    display:flex!important;
    flex-direction:column!important;
    align-items:center!important;
    justify-content:center!important;
    gap:7px!important;
    border:1px dashed rgba(74,199,220,.23)!important;
    border-radius:15px!important;
    background:#031820!important;
    color:#68858e!important;
    text-align:center!important;
}

.preview-placeholder i{
    color:#3d6873!important;
    font-size:38px!important;
}

.preview-placeholder strong{
    color:#b9d1d7!important;
    font-size:11px!important;
}

.preview-placeholder span{
    font-size:9px!important;
}

.grid > .full:nth-child(3),
.grid > .full:nth-child(4){
    margin-top:3px!important;
}

.check{
    border-color:rgba(70,213,188,.14)!important;
    background:rgba(49,200,174,.035)!important;
}

.actions{
    margin:0!important;
    padding:15px 22px!important;
    border-top:1px solid rgba(74,204,223,.10)!important;
    background:rgba(2,16,22,.45)!important;
}

@media(max-width:850px){
    .grid{
        grid-template-columns:1fr!important;
        gap:16px!important;
    }

    .grid > .full:nth-child(1),
    .grid > .full:nth-child(2){
        grid-column:1!important;
        grid-row:auto!important;
        margin-left:0!important;
    }

    .preview{
        min-height:220px!important;
        max-height:320px!important;
    }
}

@media(max-width:560px){
    .slider-page-header{
        padding:2px 0 8px!important;
    }

    .slider-page-header h1{
        font-size:29px!important;
    }

    .card{
        border-radius:16px!important;
    }

    .grid{
        padding:14px!important;
    }

    .actions{
        padding:14px!important;
        flex-direction:column-reverse!important;
    }

    .actions>*{
        width:100%!important;
    }
}

@media(prefers-reduced-motion:reduce){
    .drag-drop-zone,
    .card *{
        animation:none!important;
        transition:none!important;
    }
}
/* FuelFree PowerPlant — PREMIUM HEADER BACK BUTTON */
.slider-page-header .slider-back{
display:inline-flex!important;
align-items:center!important;
justify-content:center!important;
gap:9px!important;
height:42px!important;
box-sizing:border-box!important;
margin-top:16px!important;
padding:0 17px!important;
border:1px solid rgba(73,211,226,.24)!important;
border-radius:12px!important;
background:linear-gradient(135deg,rgba(8,43,52,.92),rgba(5,29,38,.96))!important;
color:#b9dce3!important;
text-decoration:none!important;
font-size:11px!important;
font-weight:800!important;
letter-spacing:.01em!important;
box-shadow:0 6px 16px rgba(0,0,0,.12),inset 0 1px 0 rgba(255,255,255,.035)!important;
}
.slider-page-header .slider-back i{
color:#55d9e7!important;
font-size:12px!important;
}
.slider-page-header .slider-back:hover{
color:#ecfcff!important;
border-color:rgba(78,220,235,.52)!important;
background:linear-gradient(135deg,rgba(9,54,64,.98),rgba(5,34,43,.98))!important;
}
.slider-page-header .slider-back:hover i{
color:#63e5d0!important;
}
.slider-page-header .slider-back:focus-visible{
outline:2px solid rgba(79,216,231,.55)!important;
outline-offset:3px!important;
}
@media(max-width:560px){
.slider-page-header .slider-back{
width:100%!important;
height:43px!important;
margin-top:15px!important;
}
}
</style>
@endpush

@push('scripts')
<script>
/* FuelFree PowerPlant — REAL DRAG & DROP + LIVE PREVIEW */

(() => {
    const input = document.getElementById('image');
    if (!input) return;

    const originalParent = input.parentElement;

    const zone = document.createElement('label');
    zone.className = 'drag-drop-zone';
    zone.setAttribute('for', 'image');

    zone.innerHTML = `
        <span class="drop-icon">
            <i class="fa-solid fa-cloud-arrow-up"></i>
        </span>
        <strong>Choose an image</strong>
        <span>or drag & drop an image here · JPG, PNG or WebP</span>
    `;

    input.style.position = 'absolute';
    input.style.opacity = '0';
    input.style.pointerEvents = 'none';

    zone.appendChild(input);
    originalParent.appendChild(zone);

    const previewContainer = document.querySelector('.grid > .full:nth-child(2)');

    if (previewContainer && !previewContainer.querySelector('.preview')) {
        previewContainer.innerHTML = `
            <div class="preview-placeholder">
                <i class="fa-regular fa-image"></i>
                <strong>No image selected</strong>
                <span>Choose an image to preview it here.</span>
            </div>
        `;
    }

    const showPreview = file => {
        if (!file || !file.type.startsWith('image/')) return;

        const url = URL.createObjectURL(file);

        if (previewContainer) {
            previewContainer.innerHTML = `
                <img class="preview"
                     src="${url}"
                     alt="Selected slider image preview">
            `;
        }
    };

    input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        if (file) showPreview(file);
    });

    ['dragenter','dragover'].forEach(eventName => {
        zone.addEventListener(eventName, event => {
            event.preventDefault();
            zone.classList.add('is-dragging');
        });
    });

    ['dragleave','drop'].forEach(eventName => {
        zone.addEventListener(eventName, event => {
            event.preventDefault();
            zone.classList.remove('is-dragging');
        });
    });

    zone.addEventListener('drop', event => {
        const files = event.dataTransfer.files;

        if (!files || !files.length) return;

        try {
            const transfer = new DataTransfer();
            transfer.items.add(files[0]);
            input.files = transfer.files;
        } catch (error) {
            return;
        }

        input.dispatchEvent(new Event('change', {bubbles:true}));
    });
})();
</script>
@endpush


@push('styles')
<style>
/* FuelFree PowerPlant — SLIDER FORM FINAL UI POLISH */

/* Remove malformed pseudo-icon text */
.grid > .full:nth-child(1) > label:first-child::before{
    content:none!important;
}

/* Main workspace */
.card{
    width:min(1120px,100%)!important;
    max-width:1120px!important;
    margin:0 auto!important;
    padding:0!important;
    overflow:hidden!important;
    border:1px solid rgba(71,205,224,.18)!important;
    border-radius:20px!important;
    background:
        linear-gradient(145deg,
        rgba(6,29,38,.98),
        rgba(3,19,26,.99))!important;
    box-shadow:
        0 18px 45px rgba(0,0,0,.18),
        inset 0 1px 0 rgba(255,255,255,.025)!important;
}

/* Balanced desktop grid */
.grid{
    grid-template-columns:minmax(0,1.18fr) minmax(320px,.82fr)!important;
    gap:0!important;
    padding:24px!important;
}

/* Upload column */
.grid > .full:nth-child(1){
    grid-column:1!important;
    grid-row:1!important;
    padding-right:10px!important;
}

/* Preview column */
.grid > .full:nth-child(2){
    grid-column:2!important;
    grid-row:1!important;
    margin-left:14px!important;
    padding-left:22px!important;
    border-left:1px solid rgba(74,204,223,.10)!important;
}

/* Preview heading */
.grid > .full:nth-child(2)::before{
    content:"Image preview"!important;
    display:block!important;
    margin:0 0 11px!important;
    color:#dceff2!important;
    font-size:12px!important;
    font-weight:850!important;
    letter-spacing:.01em!important;
}

/* Existing preview */
.preview{
    width:100%!important;
    max-width:100%!important;
    height:260px!important;
    max-height:260px!important;
    object-fit:cover!important;
    object-position:center!important;
    display:block!important;
    border:1px solid rgba(74,207,225,.20)!important;
    border-radius:15px!important;
    background:#031820!important;
    box-shadow:inset 0 0 0 1px rgba(255,255,255,.015)!important;
}

/* File input */
.grid > .full:nth-child(1) input[type="file"]{
    min-height:52px!important;
    border:1px dashed rgba(74,213,232,.42)!important;
    border-radius:14px!important;
    background:rgba(4,27,36,.9)!important;
    padding:7px!important;
}

.grid > .full:nth-child(1) input[type="file"]::file-selector-button{
    min-height:38px!important;
    padding:0 15px!important;
    border:1px solid rgba(78,213,230,.30)!important;
    border-radius:10px!important;
    background:linear-gradient(
        135deg,
        rgba(35,157,181,.25),
        rgba(39,190,180,.14)
    )!important;
    color:#e6fbfd!important;
    font-weight:850!important;
}

/* Recommendation box */
.upload-guide{
    margin-top:11px!important;
    padding:13px 14px!important;
    border:1px solid rgba(75,208,226,.14)!important;
    border-radius:13px!important;
    background:linear-gradient(
        135deg,
        rgba(57,196,215,.055),
        rgba(48,196,176,.025)
    )!important;
}

.upload-guide i{
    color:#53d9e7!important;
}

/* Title + URL side by side */
.grid > .full:nth-child(3){
    grid-column:1!important;
    grid-row:2!important;
    margin-top:20px!important;
    padding-right:7px!important;
}

.grid > .full:nth-child(4){
    grid-column:2!important;
    grid-row:2!important;
    margin-top:20px!important;
    padding-left:7px!important;
}

/* Date fields */
.grid > div:not(.full){
    margin-top:18px!important;
}

.grid > div:not(.full) label{
    display:flex!important;
    align-items:center!important;
    gap:7px!important;
}

.grid > div:not(.full) label::before{
    content:""!important;
    width:5px!important;
    height:5px!important;
    border-radius:50%!important;
    background:#4fd9c2!important;
    box-shadow:0 0 8px rgba(79,217,194,.25)!important;
}

/* Publish block */
.grid > .check{
    grid-column:1/-1!important;
    margin-top:18px!important;
}

/* Inputs */
.grid input[type="text"],
.grid input[type="url"],
.grid input[type="datetime-local"]{
    min-height:46px!important;
    border:1px solid rgba(76,201,222,.16)!important;
    border-radius:11px!important;
    background:#031820!important;
    color:#e6f8fa!important;
    font-size:11px!important;
}

.grid input[type="text"]:focus,
.grid input[type="url"]:focus,
.grid input[type="datetime-local"]:focus{
    border-color:rgba(78,218,232,.58)!important;
    box-shadow:0 0 0 3px rgba(78,218,232,.065)!important;
}

/* Action bar */
.actions{
    margin:0!important;
    padding:15px 24px!important;
    border-top:1px solid rgba(74,204,223,.10)!important;
    background:rgba(2,16,22,.58)!important;
}

.actions .back{
    min-height:42px!important;
    padding:0 16px!important;
    border:1px solid rgba(78,202,220,.20)!important;
    border-radius:11px!important;
    background:rgba(255,255,255,.018)!important;
    color:#a9c4cb!important;
}

.actions .save{
    min-height:42px!important;
    padding:0 18px!important;
    border-radius:11px!important;
    background:linear-gradient(135deg,#27b2ce,#31c7b0)!important;
    box-shadow:0 7px 18px rgba(38,183,190,.12)!important;
}

/* Tablet */
@media(max-width:850px){

    .grid{
        grid-template-columns:1fr!important;
        padding:20px!important;
    }

    .grid > .full:nth-child(1),
    .grid > .full:nth-child(2),
    .grid > .full:nth-child(3),
    .grid > .full:nth-child(4){
        grid-column:1!important;
        grid-row:auto!important;
        margin-left:0!important;
        padding-left:0!important;
        padding-right:0!important;
    }

    .grid > .full:nth-child(2){
        margin-top:18px!important;
        padding-top:18px!important;
        border-left:0!important;
        border-top:1px solid rgba(74,204,223,.10)!important;
    }

    .preview{
        height:auto!important;
        max-height:340px!important;
        aspect-ratio:16/7!important;
    }
}

/* Mobile */
@media(max-width:560px){

    .card{
        border-radius:16px!important;
    }

    .grid{
        padding:14px!important;
    }

    .grid > .full:nth-child(3),
    .grid > .full:nth-child(4){
        margin-top:15px!important;
    }

    .actions{
        flex-direction:column-reverse!important;
        padding:14px!important;
    }

    .actions>*{
        width:100%!important;
        justify-content:center!important;
    }

    .preview{
        aspect-ratio:16/7!important;
        max-height:none!important;
    }
}

/* Small mobile */
@media(max-width:380px){

    .grid{
        padding:11px!important;
    }

    .upload-guide{
        padding:11px!important;
    }

    .slider-page-header h1{
        font-size:27px!important;
    }
}

/* No heavy motion */
@media(prefers-reduced-motion:reduce){
    .card *,
    .card *::before,
    .card *::after{
        animation:none!important;
        transition:none!important;
    }
}
</style>
@endpush


@push('styles')
<style>
/* FuelFree PowerPlant — PREMIUM SLIDER CANCEL BUTTON */

.actions a.back{
    display:inline-flex!important;
    align-items:center!important;
    justify-content:center!important;
    gap:8px!important;
    min-width:108px!important;
    min-height:42px!important;
    box-sizing:border-box!important;
    padding:0 17px!important;

    border:1px solid rgba(91,190,211,.22)!important;
    border-radius:11px!important;

    background:linear-gradient(
        135deg,
        rgba(12,43,53,.88),
        rgba(5,27,36,.96)
    )!important;

    color:#a9c5cc!important;
    text-decoration:none!important;

    font-size:10px!important;
    font-weight:800!important;
    letter-spacing:.01em!important;

    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.035),
        0 5px 15px rgba(0,0,0,.10)!important;
}

.actions a.back:hover{
    color:#e4f9fc!important;
    border-color:rgba(79,215,231,.48)!important;

    background:linear-gradient(
        135deg,
        rgba(13,53,64,.96),
        rgba(6,34,43,.98)
    )!important;

    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.045),
        0 7px 18px rgba(0,0,0,.14)!important;
}

.actions a.back:focus-visible{
    outline:2px solid rgba(78,216,231,.55)!important;
    outline-offset:3px!important;
}

.actions a.back::before{
    content:"\f060";
    font-family:"Font Awesome 6 Free";
    font-weight:900;
    color:#55d9e7;
    font-size:10px;
}

@media(max-width:560px){
    .actions a.back{
        width:100%!important;
        min-height:43px!important;
    }
}

@media(prefers-reduced-motion:reduce){
    .actions a.back{
        transition:none!important;
        animation:none!important;
    }
}
</style>
@endpush


@push('styles')
<style>
/* FuelFree PowerPlant — FINAL PREMIUM CANCEL */

.actions .slider-cancel{
    display:inline-flex!important;
    align-items:center!important;
    justify-content:center!important;
    gap:8px!important;
    width:108px!important;
    height:42px!important;
    box-sizing:border-box!important;

    border:1px solid rgba(92,191,211,.24)!important;
    border-radius:11px!important;

    background:linear-gradient(
        135deg,
        rgba(10,42,52,.95),
        rgba(4,25,34,.98)
    )!important;

    color:#a9c5cc!important;
    text-decoration:none!important;

    font-size:10px!important;
    font-weight:800!important;
    line-height:1!important;

    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.035),
        0 5px 16px rgba(0,0,0,.12)!important;
}

.actions .slider-cancel i{
    color:#55d9e7!important;
    font-size:11px!important;
}

.actions .slider-cancel:hover{
    color:#effcff!important;
    border-color:rgba(80,219,233,.55)!important;

    background:linear-gradient(
        135deg,
        rgba(13,56,67,.98),
        rgba(6,34,44,.99)
    )!important;

    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.05),
        0 8px 20px rgba(0,0,0,.16)!important;
}

.actions .slider-cancel:hover i{
    color:#64e5d1!important;
}

.actions .slider-cancel:focus-visible{
    outline:2px solid rgba(80,216,232,.58)!important;
    outline-offset:3px!important;
}

@media(max-width:560px){
    .actions .slider-cancel{
        width:100%!important;
        height:43px!important;
    }
}

@media(prefers-reduced-motion:reduce){
    .actions .slider-cancel{
        animation:none!important;
        transition:none!important;
    }
}
</style>
@endpush
