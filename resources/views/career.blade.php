@extends('layouts.public')

@section('title','Careers — '.($brand['name'] ?? config('fuelfree.company.name')))

@section('content')
<style>
.career-page{--career-accent:#42c9e8;--career-muted:#7895a1;width:min(1180px,calc(100% - 32px));margin:0 auto;padding:clamp(34px,6vw,76px) 0 80px}
.career-hero{position:relative;overflow:hidden;border:1px solid rgba(74,207,235,.14);border-radius:28px;padding:clamp(28px,6vw,64px);background:radial-gradient(circle at 85% 20%,rgba(52,192,225,.12),transparent 34%),linear-gradient(135deg,rgba(8,38,52,.96),rgba(3,20,29,.94));margin-bottom:20px}
.career-hero:after{content:"";position:absolute;width:180px;height:180px;border:1px solid rgba(74,207,235,.1);border-radius:50%;right:-70px;bottom:-90px}
.career-kicker{display:inline-flex;align-items:center;gap:8px;color:var(--career-accent);font-size:10px;font-weight:800;letter-spacing:.18em;text-transform:uppercase}
.career-kicker i{font-size:9px}.career-hero h1{max-width:780px;font-size:clamp(38px,6.8vw,76px);line-height:.98;letter-spacing:-.045em;margin:15px 0}.career-hero p{max-width:730px;color:#91adb7;font-size:clamp(12px,1.4vw,14px);line-height:1.85;margin:0}.career-stats{display:flex;flex-wrap:wrap;gap:8px;margin-top:25px}.career-stat{padding:8px 11px;border-radius:999px;border:1px solid rgba(74,207,235,.12);background:rgba(74,207,235,.04);color:#8eabb5;font-size:9px}.career-layout{display:grid;grid-template-columns:minmax(280px,.72fr) minmax(0,1.28fr);gap:20px;align-items:start}.career-card{border:1px solid rgba(74,207,235,.12);border-radius:22px;background:linear-gradient(145deg,rgba(8,32,45,.9),rgba(3,20,29,.94));padding:clamp(18px,3vw,26px);box-shadow:0 16px 50px rgba(0,0,0,.12)}.card-heading{display:flex;justify-content:space-between;gap:14px;align-items:flex-start;margin-bottom:18px}.card-heading h2{font-size:20px;margin:3px 0 5px;color:#e8f8fb}.card-heading p{font-size:10px;line-height:1.6;color:var(--career-muted);margin:0}.heading-icon{width:38px;height:38px;display:grid;place-items:center;border-radius:11px;color:var(--career-accent);background:rgba(66,201,232,.07);flex:none}.career-jobs{display:grid;gap:9px}.career-job{padding:14px;border:1px solid rgba(74,207,235,.09);border-radius:14px;background:rgba(66,201,232,.025)}.career-job-top{display:flex;justify-content:space-between;gap:12px;align-items:center}.career-job strong{font-size:11px;color:#dceff3}.career-job .job-tag{font-size:7px;color:#6f8e99;padding:4px 7px;border:1px solid rgba(255,255,255,.07);border-radius:999px;white-space:nowrap}.career-job p{color:#7895a1;font-size:9px;line-height:1.55;margin:7px 0 0}.career-general{margin-top:11px;padding:12px;border-radius:13px;background:rgba(255,255,255,.018);color:#7895a1;font-size:9px;line-height:1.6}.career-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:13px}.career-field{min-width:0}.career-field.full{grid-column:1/-1}.career-field label{display:block;color:#9bb5be;font-size:10px;margin:0 0 6px}.career-field input,.career-field textarea,.career-field select{width:100%;box-sizing:border-box;border:1px solid rgba(74,207,235,.13);background:#051722;color:#eaf8fb;border-radius:11px;padding:12px;font:inherit;outline:none;transition:.18s}.career-field input:focus,.career-field textarea:focus,.career-field select:focus{border-color:rgba(66,201,232,.42);box-shadow:0 0 0 3px rgba(66,201,232,.06)}.career-field textarea{min-height:125px;resize:vertical}.career-file{padding:13px;border:1px dashed rgba(74,207,235,.25);border-radius:13px;background:rgba(66,201,232,.025)}.career-file input{padding:9px;background:transparent;border:0}.career-file small{display:block;color:#688692;font-size:8px;line-height:1.5;margin-top:4px}.career-consent{display:flex;gap:9px;align-items:flex-start;color:#718f9a;font-size:8px;line-height:1.55;margin:13px 0}.career-consent input{margin-top:2px;accent-color:#35bddc}.career-btn{width:100%;border:0;border-radius:11px;padding:13px 18px;background:linear-gradient(135deg,#3bc9e8,#168eae);color:#fff;font-weight:800;font-size:10px;cursor:pointer;box-shadow:0 10px 28px rgba(22,142,174,.18)}.career-btn i{margin-right:6px}.career-notice{padding:11px 13px;border-radius:11px;margin-bottom:14px;font-size:9px;line-height:1.55}.career-ok{border:1px solid rgba(67,224,173,.18);background:rgba(67,224,173,.07);color:#a9ead2}.career-errors{border:1px solid rgba(255,112,112,.18);background:rgba(255,112,112,.07);color:#ffc1c1}.career-errors ul{margin:0;padding-left:17px}.career-note{display:flex;gap:8px;margin-top:12px;color:#668692;font-size:8px;line-height:1.55}.career-note i{color:#55cde8;margin-top:2px}
.career-upload-status{margin-top:10px;padding:10px;border:1px solid rgba(74,207,235,.12);border-radius:10px;background:rgba(66,201,232,.035)}
.career-upload-top{display:flex;justify-content:space-between;gap:10px;color:#7895a1;font-size:8px}.career-upload-top strong{color:#42c9e8}
.career-upload-track{height:6px;margin-top:7px;border-radius:99px;overflow:hidden;background:rgba(255,255,255,.07)}.career-upload-track span{display:block;width:0;height:100%;border-radius:99px;background:linear-gradient(90deg,#3bc9e8,#168eae);transition:width .15s ease}
@media(max-width:980px){.career-page{width:min(100% - 28px,1180px)}.career-layout{grid-template-columns:1fr}.career-card{min-width:0}.career-hero{border-radius:24px}}
@media(max-width:700px){.career-page{width:min(100% - 20px,1180px);padding:28px 0 56px}.career-hero{padding:28px 20px;border-radius:20px;margin-bottom:14px}.career-hero h1{font-size:clamp(34px,11vw,52px);line-height:1.02;margin:12px 0}.career-hero p{font-size:12px;line-height:1.7}.career-stats{margin-top:18px}.career-stat{font-size:8px;padding:7px 9px}.career-card{padding:18px;border-radius:17px}.card-heading{gap:10px;margin-bottom:15px}.card-heading h2{font-size:18px}.card-heading p{font-size:9px}.heading-icon{width:34px;height:34px;border-radius:10px;font-size:12px}.career-form-grid{grid-template-columns:1fr;gap:11px}.career-field.full{grid-column:auto}.career-field input,.career-field textarea,.career-field select{font-size:16px;padding:11px}.career-field textarea{min-height:115px}.career-file{padding:11px}.career-file input[type=file]{width:100%;font-size:13px;padding:8px 0}.career-consent{font-size:9px;gap:8px}.career-btn{font-size:11px;padding:12px}.career-job-top{align-items:flex-start;flex-wrap:wrap}.career-job strong{font-size:10px;min-width:0;overflow-wrap:anywhere}.career-job .job-tag{font-size:7px}.career-note{font-size:8px}}
@media(max-width:380px){.career-page{width:calc(100% - 14px)}.career-hero{padding:23px 16px}.career-card{padding:15px}.career-stat{width:100%;text-align:center}.card-heading{align-items:flex-start}.heading-icon{display:none}}
</style>

<main class="career-page">
    <section class="career-hero">
        <span class="career-kicker"><i class="fa-solid fa-briefcase"></i> Careers at {{ $brand['name'] ?? 'FuelFree PowerPlant' }}</span>
        <h1>Build your next chapter with us.</h1>
        <p>We welcome talented people who want to contribute, learn and grow. Explore current opportunities or send us your CV for consideration when a suitable role becomes available.</p>
        <div class="career-stats"><span class="career-stat"><i class="fa-solid fa-file-arrow-up"></i> Secure CV submission</span><span class="career-stat"><i class="fa-solid fa-mobile-screen"></i> Mobile friendly</span><span class="career-stat"><i class="fa-solid fa-user-check"></i> Reviewed by our team</span></div>
    </section>

    <section class="career-layout">
        <article class="career-card">
            <div class="card-heading"><div><span class="career-kicker">Opportunities</span><h2>Current openings</h2><p>See roles currently published by our team.</p></div><span class="heading-icon"><i class="fa-solid fa-layer-group"></i></span></div>
            @if($page->isNotEmpty())
                <div class="career-jobs">
                    @foreach($page as $job)
                        <div class="career-job">
                            <div class="career-job-top"><strong>{{ $job->title }}</strong><span class="job-tag">OPEN POSITION</span></div>
                            @if($job->excerpt)<p>{{ $job->excerpt }}</p>@endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="career-general"><i class="fa-regular fa-circle-dot"></i> No specific vacancy is published right now. You can still submit your CV and profile for future opportunities.</div>
            @endif
            <div class="career-note"><i class="fa-solid fa-shield-halved"></i><span>Your submitted information is used for recruitment review and handled through the administration panel.</span></div>
        </article>

        <article class="career-card">
            <div class="card-heading"><div><span class="career-kicker">Apply</span><h2>Submit your CV</h2><p>Share your basic information and let our career team review your profile.</p></div><span class="heading-icon"><i class="fa-solid fa-user-tie"></i></span></div>

            @if(session('career_status'))<div class="career-notice career-ok"><i class="fa-solid fa-circle-check"></i> {{ session('career_status') }}</div>@endif
            @if($errors->any())<div class="career-notice career-errors"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

            <form id="career-application-form" method="POST" action="{{ route('career.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="career-form-grid">
                    <div class="career-field"><label for="career-name">Full name *</label><input id="career-name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name"></div>
                    <div class="career-field"><label for="career-email">Email *</label><input id="career-email" type="email" name="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email"></div>
                    <div class="career-field"><label for="career-phone">Phone</label><input id="career-phone" name="phone" value="{{ old('phone') }}" maxlength="40" autocomplete="tel"></div>
                    <div class="career-field"><label for="career-position">Position / area of interest</label><input id="career-position" name="position" value="{{ old('position') }}" maxlength="180" placeholder="e.g. Electrical Engineer"></div>
                    <div class="career-field"><label for="career-education">Education</label><input id="career-education" name="education" value="{{ old('education') }}" maxlength="255" placeholder="Highest qualification"></div>
                    <div class="career-field"><label for="career-experience">Experience</label><input id="career-experience" name="experience" value="{{ old('experience') }}" maxlength="180" placeholder="e.g. 3 years"></div>
                    <div class="career-field full"><label for="career-location">Location</label><input id="career-location" name="location" value="{{ old('location') }}" maxlength="180" autocomplete="address-level2" placeholder="City / district"></div>
                    <div class="career-field full"><label for="career-message">About you</label><textarea id="career-message" name="message" maxlength="5000" placeholder="Briefly tell us about your skills, experience and career goals.">{{ old('message') }}</textarea></div>
                    <div class="career-field full career-file">
    <label for="career-cv">CV / Resume *</label>
    <input id="career-cv" type="file" name="cv" accept=".pdf,.doc,.docx" required>
    <small>Accepted: PDF, DOC, DOCX · Maximum 50 MB. Large files upload in secure 256 KB chunks to avoid server request-size limits.</small>
    <div id="career-upload-status" class="career-upload-status" hidden>
        <div class="career-upload-top"><span id="career-upload-label">Preparing upload…</span><strong id="career-upload-percent">0%</strong></div>
        <div class="career-upload-track"><span id="career-upload-progress"></span></div>
    </div>
</div>
                </div>
                <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-10000px;width:1px;height:1px" aria-hidden="true">
                <label class="career-consent"><input type="checkbox" name="consent" value="1" required> <span>I confirm that the information provided is accurate and consent to {{ $brand['name'] ?? 'FuelFree PowerPlant' }} reviewing my application for current or future opportunities.</span></label>
                <button id="career-submit" class="career-btn" type="submit"><i class="fa-solid fa-paper-plane"></i> Submit application</button>
            </form>
        </article>
    </section>
</main>
@push('scripts')
<script>
(function(){
    const form=document.getElementById('career-application-form');
    const fileInput=document.getElementById('career-cv');
    const submit=document.getElementById('career-submit');
    const status=document.getElementById('career-upload-status');
    const label=document.getElementById('career-upload-label');
    const percent=document.getElementById('career-upload-percent');
    const progress=document.getElementById('career-upload-progress');
    if(!form||!fileInput||!submit)return;

    const MAX_SIZE=50*1024*1024;
    const FALLBACK_CHUNK_SIZE=256*1024;
    const endpoint='{{ route('career.chunks') }}';

    const setProgress=(done,total,message)=>{
        const value=total?Math.min(100,Math.round((done/total)*100)):0;
        status.hidden=false;
        label.textContent=message;
        percent.textContent=value+'%';
        progress.style.width=value+'%';
    };

    const readJson=async(response)=>{
        const raw=await response.text();
        let data={};
        try{data=raw?JSON.parse(raw):{};}catch(e){}
        if(!response.ok){
            throw new Error(data.message||'Upload failed. Please try again.');
        }
        return data;
    };

    form.addEventListener('submit',async function(event){
        event.preventDefault();

        if(!fileInput.files.length){
            fileInput.reportValidity();
            return;
        }

        const file=fileInput.files[0];
        if(file.size>MAX_SIZE){
            setProgress(0,file.size,'File is larger than 50 MB.');
            return;
        }

        submit.disabled=true;
        fileInput.disabled=true;
        submit.innerHTML='<i class="fa-solid fa-spinner fa-spin"></i> Uploading…';

        try{
            const csrf=document.querySelector('meta[name="csrf-token"]')?.content||'{{ csrf_token() }}';

            const start=await fetch(endpoint,{
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':csrf,
                    'X-Requested-With':'XMLHttpRequest',
                    'Accept':'application/json',
                    'Content-Type':'application/x-www-form-urlencoded;charset=UTF-8'
                },
                body:new URLSearchParams({
                    filename:file.name,
                    size:String(file.size),
                    mime_type:file.type||'application/octet-stream'
                })
            });
            const session=await readJson(start);
            const uploadId=session.upload_id;
            const chunkSize=Number(session.chunk_size)||FALLBACK_CHUNK_SIZE;
            const totalChunks=Math.max(1,Math.ceil(file.size/chunkSize));

            for(let index=0;index<totalChunks;index++){
                const offset=index*chunkSize;
                const chunk=file.slice(offset,Math.min(offset+chunkSize,file.size));
                setProgress(offset,file.size,'Uploading CV…');

                const response=await fetch(endpoint,{
                    method:'POST',
                    headers:{
                        'X-CSRF-TOKEN':csrf,
                        'X-Requested-With':'XMLHttpRequest',
                        'Accept':'application/json',
                        'X-Upload-Id':uploadId,
                        'X-Chunk-Index':String(index),
                        'X-Chunk-Offset':String(offset),
                        'Content-Type':'application/octet-stream'
                    },
                    body:chunk
                });
                await readJson(response);
                setProgress(offset+chunk.size,file.size,'Uploading CV…');
            }

            setProgress(file.size,file.size,'Finalizing application…');

            const formData=new FormData(form);
            formData.delete('cv');
            formData.set('finalize','1');

            const finalize=await fetch(endpoint,{
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':csrf,
                    'X-Requested-With':'XMLHttpRequest',
                    'Accept':'application/json',
                    'X-Upload-Id':uploadId
                },
                body:formData
            });
            const result=await readJson(finalize);

            window.location.assign(result.redirect||'{{ route('site.career') }}');
        }catch(error){
            status.hidden=false;
            label.textContent=error.message||'Upload failed. Please try again.';
            percent.textContent='!';
            progress.style.width='0%';
            submit.disabled=false;
            fileInput.disabled=false;
            submit.innerHTML='<i class="fa-solid fa-paper-plane"></i> Submit application';
        }
    });
})();
</script>
@endpush

@endsection