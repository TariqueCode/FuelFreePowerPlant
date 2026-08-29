@extends('webmail.layout')
@section('content')
<div class="hero"><div><div class="eyebrow">Mailbox</div><h1 class="title">{{ $mode === 'reply' ? 'Reply' : ($mode === 'forward' ? 'Forward' : 'Compose') }}</h1><p class="sub">Professional HTML email editor · sending from {{ $email }}.</p></div><a class="btn" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/inbox?folder={{ urlencode($folder ?? 'INBOX') }}"><i class="fa-solid fa-arrow-left"></i> Inbox</a></div>
<form class="card compose" method="POST" enctype="multipart/form-data" action="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/send" id="compose-form">
 @csrf
 <div class="recipient-grid">
  <div class="field"><label for="to">To</label><input id="to" name="to" value="{{ old('to',$initialTo) }}" placeholder="recipient@example.com, another@example.com" required></div>
  <div class="field"><label for="cc">Cc</label><input id="cc" name="cc" value="{{ old('cc',$initialCc ?? '') }}" placeholder="Optional"></div>
  <div class="field"><label for="bcc">Bcc</label><input id="bcc" name="bcc" value="{{ old('bcc','') }}" placeholder="Optional"></div>
 </div>
 <div class="field"><label for="subject">Subject</label><input id="subject" name="subject" value="{{ old('subject',$initialSubject) }}" placeholder="Subject"></div>
 <div class="field"><label>Message</label>@include('partials.mail-editor',['editorId'=>'webmail-editor','formId'=>'compose-form','bodyName'=>'body','initialBody'=>old('body',$initialBody),'allowAttachments'=>true])</div>
 <input type="hidden" name="draft_uid" id="draft_uid" value="{{ (int)($draftUid ?? 0) }}">
 <div class="compose-actions"><a class="btn" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/inbox?folder={{ urlencode($folder ?? 'INBOX') }}" id="compose-cancel">Cancel</a><button class="btn primary" type="submit"><i class="fa-solid fa-paper-plane"></i> Send message</button></div>
</form>
@push('styles')<style>
.compose{padding:22px;min-width:0;max-width:100%;overflow:visible}
.recipient-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.recipient-grid .field:first-child{grid-column:1/-1}
.field{margin-bottom:14px}
.field label{display:block;margin-bottom:7px;color:#a9c2ca;font-weight:700;font-size:14px}
.field input{width:100%;min-height:46px;border:1px solid var(--line);background:#041a23;color:var(--text);border-radius:13px;padding:13px 14px;outline:none}
.field input:focus{border-color:var(--accent)}
.compose-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:18px}
.compose-actions .btn{text-decoration:none}
@media(max-width:900px){.recipient-grid{grid-template-columns:1fr}.recipient-grid .field:first-child{grid-column:auto}}
@media(max-width:760px){
 .compose{padding:12px;border-radius:16px}
 .recipient-grid{gap:8px}
 .compose-actions{justify-content:stretch;flex-direction:column-reverse}
 .compose-actions .btn{width:100%}
 .hero{flex-direction:column;align-items:stretch}
 .hero>.btn{width:100%}
}
</style>@endpush
@push('scripts')
<script>
(function(){
 const form=document.getElementById('compose-form');
 if(!form)return;
 const draftField=document.getElementById('draft_uid');
 const status=document.querySelector('.ff-editor-status span:first-child');
 const csrf=document.querySelector('meta[name="csrf-token"]')?.content || '';
 let timer=null, saving=false, dirty=false, sending=false;

 function payload(){
   const editor=document.querySelector('[data-editor="webmail-editor"]');
   const body=editor?.querySelector('.ff-editor-value')?.value || '';
   return {
     _token:csrf,
     to:document.getElementById('to')?.value || '',
     cc:document.getElementById('cc')?.value || '',
     bcc:document.getElementById('bcc')?.value || '',
     subject:document.getElementById('subject')?.value || '',
     body,
     draft_uid:parseInt(draftField?.value || '0',10) || 0
   };
 }

 async function saveDraft(keepalive=false){
   if(sending || saving)return;
   const data=payload();
   if(!data.to && !data.cc && !data.bcc && !data.subject && !data.body.trim())return;
   saving=true;
   try{
     const response=await fetch('{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/draft',{
       method:'POST',
       credentials:'same-origin',
       keepalive,
       headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},
       body:JSON.stringify(data)
     });
     const result=await response.json().catch(()=>({}));
     if(result.ok && result.uid && draftField) {
       draftField.value=result.uid;
       dirty=false;
       if(status)status.innerHTML='<i class="fa-solid fa-cloud-arrow-up"></i> Saved to Drafts';
     } else if(status) {
       status.innerHTML='<i class="fa-solid fa-circle-exclamation"></i> Draft save pending';
     }
   }catch(e){
     if(status)status.innerHTML='<i class="fa-solid fa-hard-drive"></i> Saved locally';
   }finally{saving=false;}
 }

 function queue(){
   dirty=true;
   clearTimeout(timer);
   timer=setTimeout(()=>saveDraft(false),1200);
 }
 ['to','cc','bcc','subject'].forEach(id=>document.getElementById(id)?.addEventListener('input',queue));
 document.querySelector('[data-editor="webmail-editor"] .ff-editor-body')?.addEventListener('input',queue);
 document.querySelector('[data-editor="webmail-editor"] .ff-editor-source')?.addEventListener('input',queue);
 form.addEventListener('submit',()=>{sending=true;clearTimeout(timer);});
 document.getElementById('compose-cancel')?.addEventListener('click',()=>{if(dirty)saveDraft(true);});
 document.addEventListener('visibilitychange',()=>{if(document.visibilityState==='hidden'&&dirty)saveDraft(true);});
 window.addEventListener('beforeunload',()=>{if(dirty)saveDraft(true);});
 setTimeout(()=>saveDraft(false),700);
})();
</script>
@endpush
@endsection