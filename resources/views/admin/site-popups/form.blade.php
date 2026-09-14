@extends('layouts.portal')
@section('title',$popup->exists?'Edit Announcement Banner':'New Announcement Banner')
@section('content')
<section class="highlight-form-hero">
    <div class="highlight-form-hero-copy">
        <span class="eyebrow">HOMEPAGE HIGHLIGHT</span>
        <h1>{{ $popup->exists?'Edit':'Create' }} banner</h1>
        <p>Upload the important notice image your visitors should see when they open the website.</p>
    </div>
    <a class="highlight-form-back" href="{{ route('admin.site-popups.index') }}"><i class="fa-solid fa-arrow-left"></i><span>Back</span></a>
</section>

@if($errors->any())<div class="highlight-form-errors">{{ $errors->first() }}</div>@endif

<div class="ff-form-card highlight-form-card">
    <form id="highlight-form" method="POST" enctype="multipart/form-data" action="{{ $popup->exists?route('admin.site-popups.update',$popup):route('admin.site-popups.store') }}">
        @csrf
        @if($popup->exists) @method('PATCH') @endif

        <div class="highlight-form-section">
            <div class="highlight-form-section-head">
                <div>
                    <span class="highlight-form-section-kicker">01 · Banner</span>
                    <h2>Banner content</h2>
                </div>
                <span class="highlight-form-section-note">Public-facing highlight</span>
            </div>

            <div class="highlight-form-grid highlight-form-grid-main">
                <div class="highlight-upload-field">
                    <label for="highlight-image">Banner image <span>{{ $popup->exists?'(leave empty to keep current)':'(required)' }}</span></label>
                    <div class="highlight-upload-layout">
                        <label class="highlight-upload-zone" for="highlight-image">
                            <input id="highlight-image" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/avif" {{ $popup->exists?'':'required' }}>
                            <span class="highlight-upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></span>
                            <strong>Choose banner image</strong>
                            <span class="highlight-upload-help">JPG, PNG, WebP or AVIF · Square preview</span>
                            <span class="highlight-upload-name" id="highlight-file-name">{{ $popup->image_path?'Replace current image':'No image selected' }}</span>
                        </label>
                        <div class="highlight-preview-wrap{{ $popup->image_path?' has-image':'' }}" id="highlight-preview-wrap">
                            @if($popup->image_path)
                                <img id="highlight-preview" class="highlight-preview" src="{{ asset('storage/'.$popup->image_path) }}" alt="Current banner">
                            @else
                                <div class="highlight-preview-empty" id="highlight-preview-empty"><i class="fa-regular fa-image"></i><span>1:1 preview</span></div>
                                <img id="highlight-preview" class="highlight-preview" alt="Selected banner preview" hidden>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="highlight-field">
                    <label for="highlight-title">Internal title <span>(optional)</span></label>
                    <input id="highlight-title" name="title" value="{{ old('title',$popup->title) }}" maxlength="255" placeholder="e.g. Important notice">
                    <small>Used to identify this highlight inside the admin area.</small>
                </div>
            </div>
        </div>

        <div class="highlight-form-divider"></div>

        <div class="highlight-form-section">
            <div class="highlight-form-section-head">
                <div>
                    <span class="highlight-form-section-kicker">02 · Display</span>
                    <h2>Visitor experience</h2>
                </div>
                <span class="highlight-form-section-note">Timing & destination</span>
            </div>

            <div class="highlight-form-grid">
                <div class="highlight-field">
                    <label for="highlight-display-seconds">Auto-close after <span>(seconds)</span></label>
                    <input id="highlight-display-seconds" type="number" name="display_seconds" value="{{ old('display_seconds',$popup->display_seconds) }}" min="1" max="3600" placeholder="Leave empty for close button only">
                    <small>Leave empty when visitors should close the highlight themselves.</small>
                </div>
                <div class="highlight-field">
                    <label for="highlight-link-url">Destination URL <span>(optional)</span></label>
                    <input id="highlight-link-url" type="url" name="link_url" value="{{ old('link_url',$popup->link_url) }}" placeholder="https://example.com/notice">
                    <small>Optional page visitors open when the highlight is linked.</small>
                </div>
                <div class="highlight-field">
                    <label for="highlight-starts-at">Start time <span>(optional)</span></label>
                    <input id="highlight-starts-at" type="datetime-local" name="starts_at" value="{{ old('starts_at',$popup->starts_at?->format('Y-m-d\\TH:i')) }}">
                    <small>Leave empty to make the highlight available immediately.</small>
                </div>
                <div class="highlight-field">
                    <label for="highlight-ends-at">End time <span>(optional)</span></label>
                    <input id="highlight-ends-at" type="datetime-local" name="ends_at" value="{{ old('ends_at',$popup->ends_at?->format('Y-m-d\\TH:i')) }}">
                    <small>Leave empty when the highlight has no expiry.</small>
                </div>
            </div>
        </div>

        <div class="highlight-form-divider"></div>

        <div class="highlight-form-section highlight-publish-section">
            <div class="highlight-form-section-head">
                <div>
                    <span class="highlight-form-section-kicker">03 · Publishing</span>
                    <h2>Public visibility</h2>
                </div>
            </div>
            <label class="highlight-publish-toggle" for="highlight-published">
                <input id="highlight-published" type="checkbox" name="is_published" value="1" @checked(old('is_published',$popup->is_published))>
                <span class="highlight-switch"><span></span></span>
                <span class="highlight-publish-copy"><strong>Publish on the public homepage</strong><small>When enabled, this highlight can be shown to website visitors according to its timing.</small></span>
            </label>
        </div>

        <div class="highlight-form-actions">
            <a class="highlight-form-cancel" href="{{ route('admin.site-popups.index') }}">Cancel</a>
            <button class="highlight-form-save" type="submit"><i class="fa-solid fa-floppy-disk"></i><span>{{ $popup->exists?'Save changes':'Save banner' }}</span></button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.highlight-form-hero{width:100%;margin:0 0 20px!important;padding:0!important;display:flex;align-items:flex-end;justify-content:space-between;gap:20px;background:transparent!important;border:0!important;box-shadow:none!important;border-radius:0!important;min-height:0!important}
.highlight-form-hero-copy{min-width:0;max-width:920px}.highlight-form-hero .eyebrow{display:inline-block;margin:0 0 8px;color:#61e1aa;font-size:12px;font-weight:800;letter-spacing:.13em;text-transform:uppercase}.highlight-form-hero h1{margin:0;color:var(--text);font-size:clamp(30px,3.2vw,48px);line-height:1.1}.highlight-form-hero p{margin:12px 0 0;color:#8eabb5;font-size:16px;line-height:1.6}.highlight-form-back,.highlight-form-cancel{display:inline-flex;align-items:center;justify-content:center;gap:7px;border:1px solid var(--line);border-radius:11px;color:#9db9c2;text-decoration:none;font-size:10px;font-weight:700;transition:border-color .18s,background .18s,transform .18s}.highlight-form-back{flex:0 0 auto;padding:10px 13px}.highlight-form-back:hover,.highlight-form-cancel:hover{border-color:var(--ff-card-border-hover,var(--line));background:rgba(72,216,241,.05);transform:translateY(-1px)}
.highlight-form-errors{margin:0 0 14px;padding:12px 14px;border:1px solid rgba(255,93,113,.2);border-radius:12px;background:rgba(255,93,113,.06);color:#ffadb6;font-size:11px}.ff-form-card.highlight-form-card{width:100%;max-width:none;padding:0;overflow:hidden}
.highlight-form-section{padding:24px}.highlight-form-section-head{display:flex;align-items:flex-start;justify-content:space-between;gap:15px;margin-bottom:18px}.highlight-form-section-kicker{display:block;margin-bottom:5px;color:#55d9ef;font-size:9px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}.highlight-form-section-head h2{margin:0;color:var(--text);font-size:18px;line-height:1.3}.highlight-form-section-note{padding:6px 9px;border:1px solid rgba(104,224,192,.1);border-radius:999px;background:rgba(67,194,137,.035);color:#73949e;font-size:8px;font-weight:700;white-space:nowrap}.highlight-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px}.highlight-form-grid-main{grid-template-columns:minmax(0,1.45fr) minmax(260px,.75fr)}.highlight-form-divider{height:1px;background:rgba(104,224,192,.09);margin:0 24px}.highlight-field,.highlight-upload-field{min-width:0}.highlight-field label,.highlight-upload-field>label{display:block;margin:0 0 7px;color:#a6bec5;font-size:10px;font-weight:800}.highlight-field label span,.highlight-upload-field>label span{color:#66858f;font-weight:600}.highlight-field input{width:100%;height:44px;box-sizing:border-box;border:1px solid rgba(104,204,235,.14);border-radius:10px;background:#061923;color:#e4f3f7;padding:11px 12px;font:inherit;font-size:11px;outline:none;transition:border-color .18s,box-shadow .18s}.highlight-field input:focus{border-color:rgba(86,210,238,.5);box-shadow:0 0 0 3px rgba(67,194,229,.07)}.highlight-field small{display:block;margin-top:6px;color:#66858f;font-size:8px;line-height:1.45}.highlight-upload-layout{display:grid;grid-template-columns:minmax(0,1fr) 150px;gap:14px;align-items:stretch}.highlight-upload-zone{position:relative;min-width:0;min-height:150px;padding:18px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;border:1px dashed rgba(104,204,235,.24);border-radius:13px;background:rgba(4,23,31,.7);cursor:pointer;transition:border-color .18s,background .18s,transform .18s}.highlight-upload-zone:hover{border-color:rgba(86,210,238,.48);background:rgba(72,216,241,.045);transform:translateY(-1px)}.highlight-upload-zone input{position:absolute;width:1px;height:1px;opacity:0;pointer-events:none}.highlight-upload-icon{width:38px;height:38px;display:grid;place-items:center;margin-bottom:9px;border-radius:11px;background:rgba(72,216,241,.08);color:#58cfe7;font-size:15px}.highlight-upload-zone strong{color:#dff5f4;font-size:12px}.highlight-upload-help{margin-top:5px;color:#6d9099;font-size:8px}.highlight-upload-name{max-width:100%;margin-top:10px;padding:5px 8px;border-radius:7px;background:rgba(67,194,137,.06);color:#79c8ad;font-size:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.highlight-preview-wrap{width:150px;height:150px;min-width:150px;overflow:hidden;border:1px solid var(--line);border-radius:13px;background:#061923;aspect-ratio:1/1;display:grid;place-items:center}.highlight-preview{width:100%!important;height:100%!important;display:block;object-fit:cover!important;object-position:center!important}.highlight-preview-empty{width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:7px;color:#5f818a;font-size:8px}.highlight-preview-empty i{font-size:22px;color:#397582}.highlight-publish-section{padding-bottom:20px}.highlight-publish-toggle{display:flex;align-items:center;gap:12px;min-height:64px;padding:13px 15px;border:1px solid rgba(104,204,235,.1);border-radius:13px;background:rgba(67,194,229,.025);cursor:pointer}.highlight-publish-toggle>input{position:absolute;opacity:0;pointer-events:none}.highlight-switch{position:relative;flex:0 0 auto;width:38px;height:22px;border-radius:999px;background:#405962;transition:background .18s}.highlight-switch span{position:absolute;top:3px;left:3px;width:16px;height:16px;border-radius:50%;background:#b3c4c8;box-shadow:0 1px 3px rgba(0,0,0,.3);transition:left .18s,background .18s}.highlight-publish-toggle>input:checked+.highlight-switch{background:#32b985}.highlight-publish-toggle>input:checked+.highlight-switch span{left:19px;background:#effff8}.highlight-publish-copy{min-width:0;display:flex;flex-direction:column;gap:3px}.highlight-publish-copy strong{color:#bcd0d6;font-size:11px}.highlight-publish-copy small{color:#698992;font-size:8px;line-height:1.45}.highlight-form-actions{display:flex;justify-content:flex-end;align-items:center;gap:9px;padding:16px 24px;border-top:1px solid rgba(104,224,192,.09);background:rgba(2,12,17,.24)}.highlight-form-cancel{min-width:80px;padding:11px 14px}.highlight-form-save{min-height:44px;border:1px solid rgba(104,221,239,.18);border-radius:11px;padding:11px 18px;display:inline-flex;align-items:center;justify-content:center;gap:8px;background:linear-gradient(135deg,#25abc9,#1687a4);color:#fff;font-size:11px;font-weight:800;cursor:pointer;transition:transform .18s,filter .18s}.highlight-form-save:hover{transform:translateY(-1px);filter:brightness(1.06)}
@media(max-width:900px){.highlight-form-hero{align-items:flex-start;flex-direction:column;gap:13px}.highlight-form-back{align-self:flex-start}.highlight-form-grid-main,.highlight-form-grid{grid-template-columns:1fr}.highlight-upload-layout{grid-template-columns:minmax(0,1fr) 150px}}
@media(max-width:620px){.highlight-form-hero{margin-bottom:14px!important;gap:11px}.highlight-form-hero .eyebrow{font-size:10px}.highlight-form-hero h1{font-size:32px}.highlight-form-hero p{font-size:14px;line-height:1.5;margin-top:9px}.highlight-form-section{padding:17px 15px}.highlight-form-section-head{margin-bottom:14px}.highlight-form-section-note{display:none}.highlight-form-divider{margin:0 15px}.highlight-upload-layout{grid-template-columns:1fr}.highlight-preview-wrap{width:100%;height:auto;min-width:0;aspect-ratio:1/1;max-width:260px;justify-self:start}.highlight-upload-zone{min-height:140px}.highlight-form-actions{padding:13px 15px;flex-direction:column-reverse;align-items:stretch}.highlight-form-actions>*{width:100%;box-sizing:border-box}.highlight-form-cancel,.highlight-form-save{min-height:44px}.highlight-publish-toggle{align-items:flex-start}.highlight-switch{margin-top:1px}}
@media(max-width:380px){.highlight-form-section{padding:15px 12px}.highlight-form-divider{margin:0 12px}.highlight-upload-zone{padding:14px}.highlight-preview-wrap{max-width:220px}.highlight-field input{height:42px}.highlight-form-actions{padding:12px}.highlight-publish-toggle{padding:12px}.highlight-publish-copy strong{font-size:10px}}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){
    const input=document.getElementById('highlight-image');
    const preview=document.getElementById('highlight-preview');
    const empty=document.getElementById('highlight-preview-empty');
    const wrap=document.getElementById('highlight-preview-wrap');
    const fileName=document.getElementById('highlight-file-name');
    if(!input||!preview)return;
    input.addEventListener('change',function(){
        const file=this.files&&this.files[0];
        if(!file){
            fileName.textContent='{{ $popup->image_path?'Replace current image':'No image selected' }}';
            return;
        }
        fileName.textContent=file.name;
        const url=URL.createObjectURL(file);
        preview.src=url;
        preview.hidden=false;
        if(empty)empty.hidden=true;
        if(wrap)wrap.classList.add('has-image');
        preview.onload=function(){URL.revokeObjectURL(url)};
    });
});
</script>
@endpush
