@extends('layouts.public')

@section('title','Career — '.($brand['name'] ?? config('fuelfree.company.name')))

@section('content')
<style>
.career-shell{width:min(1120px,calc(100% - 28px));margin:auto}.career-hero{padding:64px 0 30px}.career-eyebrow{color:#43d1f0;font-size:10px;letter-spacing:.22em;text-transform:uppercase}.career-hero h1{font-size:clamp(42px,7vw,72px);line-height:.98;margin:14px 0}.career-hero p{max-width:760px;color:#8eabb7;line-height:1.8}.career-grid{display:grid;grid-template-columns:.8fr 1.2fr;gap:18px;padding-bottom:70px}.career-card{border:1px solid rgba(86,210,238,.16);border-radius:20px;background:linear-gradient(145deg,rgba(8,38,52,.9),rgba(3,21,30,.84));padding:24px}.career-card h2{margin:0 0 10px;font-size:20px}.career-copy{color:#8eabb7;font-size:12px;line-height:1.8}.career-jobs{display:grid;gap:10px;margin-top:18px}.career-job{padding:14px;border:1px solid rgba(86,210,238,.1);border-radius:13px;background:rgba(67,209,240,.035)}.career-job strong{display:block}.career-job span{display:block;color:#7898a5;font-size:10px;margin-top:4px}.career-field{margin-bottom:13px}.career-field label{display:block;color:#9eb9c4;font-size:12px;margin-bottom:7px}.career-field input,.career-field textarea,.career-field select{width:100%;border:1px solid rgba(86,210,238,.16);background:#061a26;color:#effcff;border-radius:11px;padding:12px;font:inherit;outline:none}.career-field textarea{min-height:125px;resize:vertical}.career-file{padding:14px;border:1px dashed rgba(86,210,238,.28);border-radius:13px;background:rgba(67,209,240,.035)}.career-file small{display:block;color:#718f9c;margin-top:5px}.career-consent{display:flex;gap:9px;align-items:flex-start;color:#7898a5;font-size:11px;line-height:1.5;margin:12px 0}.career-consent input{margin-top:3px}.career-btn{border:0;border-radius:11px;padding:13px 18px;background:linear-gradient(135deg,#37c5e6,#168faf);color:#fff;font-weight:800;cursor:pointer}.career-notice{padding:12px 14px;border-radius:12px;margin-bottom:16px;font-size:12px}.career-ok{border:1px solid rgba(67,224,173,.2);background:rgba(67,224,173,.08);color:#a9ead2}.career-errors{border:1px solid rgba(255,112,112,.2);background:rgba(255,112,112,.07);color:#ffc1c1}.career-errors ul{margin:0;padding-left:18px}@media(max-width:760px){.career-hero{padding-top:45px}.career-grid{grid-template-columns:1fr}.career-card{padding:18px}}
</style>
<main class="career-shell">
<section class="career-hero"><span class="career-eyebrow">Join the team</span><h1>Build your career with us.</h1><p>Explore opportunities at FuelFree PowerPlant and send your CV directly to our career team. You can apply even when no specific vacancy is listed.</p></section>
<section class="career-grid">
<article class="career-card"><h2>Current opportunities</h2><div class="career-copy">Share your basic information and a current CV. Our team can keep your profile for suitable future opportunities.</div>
@if($page->isNotEmpty())<div class="career-jobs">@foreach($page as $job)<div class="career-job"><strong>{{ $job->title }}</strong>@if($job->excerpt)<span>{{ $job->excerpt }}</span>@endif</div>@endforeach</div>@endif
</article>
<article class="career-card"><h2>Submit your CV</h2>
@if(session('career_status'))<div class="career-notice career-ok">{{ session('career_status') }}</div>@endif
@if($errors->any())<div class="career-notice career-errors"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('career.store') }}" enctype="multipart/form-data">@csrf
<div class="career-field"><label for="career-name">Full name *</label><input id="career-name" name="name" value="{{ old('name') }}" required maxlength="120"></div>
<div class="career-field"><label for="career-email">Email *</label><input id="career-email" type="email" name="email" value="{{ old('email') }}" required maxlength="190"></div>
<div class="career-field"><label for="career-phone">Phone</label><input id="career-phone" name="phone" value="{{ old('phone') }}" maxlength="40"></div>
<div class="career-field"><label for="career-position">Position / area of interest</label><input id="career-position" name="position" value="{{ old('position') }}" maxlength="180" placeholder="e.g. Electrical Engineer"></div>
<div class="career-field"><label for="career-education">Education</label><input id="career-education" name="education" value="{{ old('education') }}" maxlength="255"></div>
<div class="career-field"><label for="career-experience">Experience</label><input id="career-experience" name="experience" value="{{ old('experience') }}" maxlength="180" placeholder="e.g. 3 years"></div>
<div class="career-field"><label for="career-location">Location</label><input id="career-location" name="location" value="{{ old('location') }}" maxlength="180"></div>
<div class="career-field"><label for="career-message">About you</label><textarea id="career-message" name="message" maxlength="5000" placeholder="Tell us briefly about your skills, experience and career goals.">{{ old('message') }}</textarea></div>
<div class="career-field career-file"><label for="career-cv">CV / Resume *</label><input id="career-cv" type="file" name="cv" accept=".pdf,.doc,.docx" required><small>PDF, DOC or DOCX · maximum 8 MB</small></div>
<input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-10000px;width:1px;height:1px" aria-hidden="true">
<label class="career-consent"><input type="checkbox" name="consent" value="1" required> I confirm that the information provided is accurate and I consent to FuelFree PowerPlant reviewing my application.</label>
<button class="career-btn" type="submit"><i class="fa-solid fa-paper-plane"></i> Submit application</button>
</form></article></section></main>
@endsection