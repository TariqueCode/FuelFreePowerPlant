@extends('layouts.portal')
@section('title','Footer Manager')
@section('content')
<section class="footer-manager-head">
    <div class="footer-manager-title">
        <span class="eyebrow">WEBSITE · FOOTER</span>
        <h1>Footer Manager</h1>
        <p>Control the public website footer without mixing website content controls into System Settings.</p>
    </div>

    <div class="footer-manager-actions">
        <div class="footer-manager-scope">
            <i class="fa-solid fa-window-restore"></i>
            <span>
                <strong>Website content</strong>
                <small>Changes apply to the public footer.</small>
            </span>
        </div>
    </div>
</section>
@if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
@if($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('admin.settings.update',['section'=>'footer']) }}" class="footer-stack">
@csrf
<section class="footer-card"><header><div><span class="eyebrow">BRAND</span><h2>Footer identity</h2><p>Keep the footer message concise and consistent with the company identity.</p></div><span class="footer-icon"><i class="fa-solid fa-building"></i></span></header><div class="footer-grid"><label class="full"><span>Tagline</span><input name="footer[tagline]" value="{{ old('footer.tagline',$settings['footer.tagline']) }}" maxlength="255"></label><label class="full"><span>Technology line</span><input name="footer[technology]" value="{{ old('footer.technology',$settings['footer.technology']) }}" maxlength="255"></label></div></section>
<section class="footer-card"><header><div><span class="eyebrow">OFFICE</span><h2>Office information</h2></div><span class="footer-icon"><i class="fa-solid fa-location-dot"></i></span></header><div class="footer-grid"><label><span>Heading</span><input name="footer[office_heading]" value="{{ old('footer.office_heading',$settings['footer.office_heading']) }}" required maxlength="100"></label><label class="full"><span>Address</span><textarea name="footer[address]" rows="4" required maxlength="1000">{{ old('footer.address',$settings['footer.address']) }}</textarea></label></div></section>
<section class="footer-card"><header><div><span class="eyebrow">CONTACT</span><h2>Contact information</h2></div><span class="footer-icon"><i class="fa-solid fa-address-card"></i></span></header><div class="footer-grid"><label><span>Heading</span><input name="footer[contact_heading]" value="{{ old('footer.contact_heading',$settings['footer.contact_heading']) }}" required maxlength="100"></label><label><span>Email</span><input type="email" name="footer[email]" value="{{ old('footer.email',$settings['footer.email']) }}" maxlength="255"></label><label><span>Phone</span><input name="footer[phone]" value="{{ old('footer.phone',$settings['footer.phone']) }}" maxlength="80"></label><label><span>Website label</span><input name="footer[website]" value="{{ old('footer.website',$settings['footer.website']) }}" maxlength="255"></label><label class="full"><span>Website URL</span><input type="url" name="footer[website_url]" value="{{ old('footer.website_url',$settings['footer.website_url']) }}" maxlength="500"></label><label><span>Get in touch label</span><input name="footer[get_in_touch_label]" value="{{ old('footer.get_in_touch_label',$settings['footer.get_in_touch_label']) }}" maxlength="100"></label><label><span>Get in touch URL</span><input type="url" name="footer[get_in_touch_url]" value="{{ old('footer.get_in_touch_url',$settings['footer.get_in_touch_url']) }}" maxlength="500"></label></div></section>
<section class="footer-card"><header><div><span class="eyebrow">VISIBILITY</span><h2>Footer sections</h2><p>Toggle public footer areas without changing their saved content.</p></div><span class="footer-icon"><i class="fa-solid fa-eye"></i></span></header><div class="toggles"><label><input type="checkbox" name="design[footer][columns_enabled]" value="1" @checked(old('design.footer.columns_enabled',$settings['design.footer.columns_enabled']))><span>Office section</span></label><label><input type="checkbox" name="design[footer][links_enabled]" value="1" @checked(old('design.footer.links_enabled',$settings['design.footer.links_enabled']))><span>Footer links</span></label><label><input type="checkbox" name="design[footer][social_enabled]" value="1" @checked(old('design.footer.social_enabled',$settings['design.footer.social_enabled']))><span>Social links</span></label><label><input type="checkbox" name="design[footer][contact_enabled]" value="1" @checked(old('design.footer.contact_enabled',$settings['design.footer.contact_enabled']))><span>Contact section</span></label><label><input type="checkbox" name="design[footer][copyright_enabled]" value="1" @checked(old('design.footer.copyright_enabled',$settings['design.footer.copyright_enabled']))><span>Copyright</span></label></div><label class="full copyright"><span>Copyright text</span><input name="footer[copyright_text]" value="{{ old('footer.copyright_text',$settings['footer.copyright_text']) }}" maxlength="255"></label></section>
<div class="footer-save"><div><strong>Footer Manager</strong><span>Save only footer-specific settings.</span></div><button type="submit"><i class="fa-solid fa-floppy-disk"></i> Save footer</button></div>
</form>
@endsection
@push('styles')
<style>
.footer-stack{
    width:100%;
    max-width:1120px;
    display:grid;
    gap:16px;
}

.footer-manager-head{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap:28px;
    padding:0 0 20px;
    margin-bottom:22px;
    border-bottom:1px solid rgba(103,208,234,.14);
}

.footer-manager-title{
    min-width:0;
}

.footer-manager-title h1{
    margin:7px 0 7px;
    color:#eaf8fb;
    font-size:clamp(30px,3.2vw,42px);
    font-weight:800;
    line-height:1.08;
    letter-spacing:-.035em;
}

.footer-manager-title p{
    margin:0;
    max-width:760px;
    color:#7899a5;
    font-size:10px;
    line-height:1.65;
}

.eyebrow{
    display:inline-block;
    color:#4dcde8;
    font-size:8px;
    font-weight:800;
    letter-spacing:.16em;
}

.footer-manager-actions{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    flex:0 0 auto;
}

.footer-manager-scope{
    display:flex;
    align-items:center;
    gap:9px;
    min-height:38px;
    padding:0 11px;
    border:1px solid rgba(103,208,234,.12);
    border-radius:10px;
    background:rgba(67,194,229,.025);
    color:#6f909b;
    font-size:8px;
}

.footer-manager-scope i{
    color:#54cfe9;
    font-size:11px;
}

.footer-manager-scope strong,
.footer-manager-scope small{
    display:block;
}

.footer-manager-scope strong{
    color:#b8d8df;
    font-size:9px;
}

.footer-manager-scope small{
    margin-top:2px;
    color:#668591;
    font-size:7px;
}

.footer-card{
    min-width:0;
    padding:20px;
    border:1px solid var(--line);
    border-radius:15px;
    background:#061923;
    box-shadow:none;
}

.footer-card header{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:18px;
    margin-bottom:18px;
    padding-bottom:15px;
    border-bottom:1px solid rgba(103,208,234,.08);
}

.footer-card header > div{
    min-width:0;
}

.footer-card h2{
    margin:5px 0;
    color:#eaf8fb;
    font-size:19px;
    font-weight:800;
    line-height:1.2;
    letter-spacing:-.025em;
}

.footer-card header p{
    margin:0;
    color:#718f9a;
    font-size:8px;
    line-height:1.55;
}

.footer-icon{
    width:40px;
    height:40px;
    flex:none;
    display:grid;
    place-items:center;
    border:1px solid rgba(72,216,241,.10);
    border-radius:10px;
    background:rgba(72,216,241,.045);
    color:#51cfe9;
    font-size:13px;
}

.footer-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:13px;
}

.footer-grid label{
    display:grid;
    gap:7px;
    min-width:0;
}

.footer-grid label > span,
.copyright > span{
    color:#9eb9c4;
    font-size:9px;
    font-weight:700;
}

.full{
    grid-column:1/-1;
}

.footer-grid input,
.footer-grid textarea,
.copyright input{
    width:100%;
    min-width:0;
    box-sizing:border-box;
    padding:11px 12px;
    border:1px solid rgba(104,204,235,.13);
    border-radius:10px;
    outline:none;
    background:#071b27;
    color:#e9f7fb;
    font:inherit;
    font-size:10px;
    line-height:1.5;
    resize:vertical;
}

.footer-grid input::placeholder,
.footer-grid textarea::placeholder,
.copyright input::placeholder{
    color:#577681;
}

.footer-grid input:hover,
.footer-grid textarea:hover,
.copyright input:hover{
    border-color:rgba(104,204,235,.20);
}

.footer-grid input:focus,
.footer-grid textarea:focus,
.copyright input:focus{
    border-color:rgba(81,216,240,.42);
    box-shadow:0 0 0 3px rgba(81,216,240,.055);
}

.toggles{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:9px;
    margin-bottom:15px;
}

.toggles label{
    display:flex;
    align-items:center;
    gap:9px;
    min-height:40px;
    box-sizing:border-box;
    padding:9px 11px;
    border:1px solid rgba(104,204,235,.10);
    border-radius:10px;
    background:rgba(72,216,241,.025);
    color:#9eb9c4;
    font-size:9px;
    cursor:pointer;
    transition:border-color .16s ease,background .16s ease;
}

.toggles label:hover{
    border-color:rgba(104,204,235,.22);
    background:rgba(72,216,241,.045);
}

.toggles input{
    width:15px;
    height:15px;
    margin:0;
    accent-color:#38c5e4;
    flex:none;
}

.copyright{
    display:grid;
    gap:7px;
}

.footer-save{
    position:sticky;
    bottom:10px;
    z-index:10;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    padding:11px 13px;
    border:1px solid rgba(76,205,233,.16);
    border-radius:12px;
    background:rgba(3,20,29,.96);
    box-shadow:0 10px 28px rgba(0,0,0,.18);
    backdrop-filter:blur(12px);
    -webkit-backdrop-filter:blur(12px);
}

.footer-save strong,
.footer-save span{
    display:block;
}

.footer-save strong{
    color:#dff5f8;
    font-size:10px;
}

.footer-save span{
    margin-top:3px;
    color:#668591;
    font-size:8px;
}

.footer-save button{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    min-height:38px;
    padding:0 16px;
    border:1px solid rgba(82,216,240,.25);
    border-radius:10px;
    background:#168eaa;
    color:#fff;
    font-size:9px;
    font-weight:800;
    cursor:pointer;
    white-space:nowrap;
    box-shadow:none;
}

.footer-save button:hover{
    background:#1aa0bd;
}

.notice,
.errors{
    max-width:1120px;
    margin:0 0 15px;
    padding:10px 12px;
    border-radius:10px;
    font-size:9px;
}

.notice{
    color:#bfeaf2;
    background:rgba(57,200,225,.07);
    border:1px solid rgba(57,200,225,.15);
}

.errors{
    color:#ffd0d0;
    background:rgba(220,70,70,.07);
    border:1px solid rgba(220,70,70,.15);
}

@media(max-width:900px){
    .footer-manager-head{
        gap:18px;
    }

    .footer-grid{
        grid-template-columns:1fr 1fr;
    }

    .toggles{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }
}

@media(max-width:650px){
    .footer-stack{
        gap:13px;
    }

    .footer-manager-head{
        display:block;
        margin-bottom:15px;
        padding-bottom:15px;
    }

    .footer-manager-title h1{
        font-size:30px;
    }

    .footer-manager-title p{
        font-size:9px;
        line-height:1.6;
    }

    .footer-manager-actions{
        justify-content:flex-start;
        margin-top:12px;
    }

    .footer-manager-scope{
        width:100%;
        box-sizing:border-box;
    }

    .footer-card{
        padding:15px;
        border-radius:13px;
    }

    .footer-card header{
        gap:12px;
        margin-bottom:15px;
        padding-bottom:13px;
    }

    .footer-card h2{
        font-size:17px;
    }

    .footer-card header p{
        font-size:8px;
    }

    .footer-icon{
        width:36px;
        height:36px;
        border-radius:9px;
    }

    .footer-grid{
        grid-template-columns:1fr;
        gap:11px;
    }

    .full{
        grid-column:auto;
    }

    .toggles{
        grid-template-columns:1fr;
        gap:8px;
    }

    .footer-save{
        position:static;
        display:block;
        padding:11px;
    }

    .footer-save button{
        width:100%;
        margin-top:9px;
    }
}
</style>
@endpush