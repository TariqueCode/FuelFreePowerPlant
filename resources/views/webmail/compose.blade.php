@extends('webmail.layout')
@section('content')
<div class="hero">
    <div><div class="eyebrow">FuelFree PowerPlant Webmail</div><h1 class="title">{{ $mode === 'reply' ? 'Reply' : ($mode === 'forward' ? 'Forward' : 'Compose') }}</h1><p class="sub">Send an email from {{ $email }}.</p></div>
    <a class="btn" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/inbox">← Inbox</a>
</div>
<form class="card compose" method="POST" action="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/send" id="compose-form">
    @csrf
    <div class="field"><label for="to">To</label><input id="to" name="to" type="email" value="{{ old('to', $initialTo) }}" placeholder="recipient@example.com" required></div>
    <div class="field"><label for="subject">Subject</label><input id="subject" name="subject" type="text" value="{{ old('subject', $initialSubject) }}" placeholder="Subject"></div>
    <div class="field">
        <label>Message</label>
        <div class="editor-shell">
            <div class="editor-toolbar" role="toolbar" aria-label="Email formatting">
                <button type="button" data-cmd="bold" title="Bold"><b>B</b></button><button type="button" data-cmd="italic" title="Italic"><i>I</i></button><button type="button" data-cmd="underline" title="Underline"><u>U</u></button><button type="button" data-cmd="strikeThrough" title="Strikethrough"><s>S</s></button><span class="toolbar-sep"></span>
                <button type="button" data-block="p">P</button><button type="button" data-block="h2">H2</button><button type="button" data-block="h3">H3</button><button type="button" data-cmd="insertUnorderedList">• List</button><button type="button" data-cmd="insertOrderedList">1. List</button><span class="toolbar-sep"></span>
                <button type="button" data-cmd="justifyLeft">Left</button><button type="button" data-cmd="justifyCenter">Center</button><button type="button" data-cmd="justifyRight">Right</button><button type="button" id="link-btn">Link</button><button type="button" id="image-btn">Image</button><button type="button" data-cmd="removeFormat">Clear format</button>
            </div>
            <div id="editor" class="visual-editor" contenteditable="true" role="textbox" aria-multiline="true"></div>
        </div>
        <textarea id="body" name="body" hidden required>{{ old('body', $initialBody) }}</textarea>
        <p class="editor-hint">Use the visual editor to format text, add links/images, lists and headings. The email will be sent as a responsive HTML message.</p>
    </div>
    <div class="compose-actions"><a class="btn" href="{{ config('cpanel.webmail_url','https://mail.fuelfreepowerplant.com') }}/inbox">Cancel</a><button class="btn primary" type="submit">Send message</button></div>
</form>
@push('styles')
<style>.compose{padding:22px}.editor-shell{border:1px solid var(--line);border-radius:15px;overflow:hidden;background:#041a23}.editor-toolbar{display:flex;align-items:center;gap:5px;flex-wrap:wrap;padding:9px;border-bottom:1px solid var(--line);background:#071e28}.editor-toolbar button{border:1px solid transparent;background:transparent;color:#b9d0d7;border-radius:8px;padding:7px 9px;font-size:12px;cursor:pointer}.editor-toolbar button:hover{background:#0b3542;border-color:#1b5968;color:#fff}.toolbar-sep{width:1px;height:24px;background:var(--line);margin:0 3px}.visual-editor{min-height:360px;padding:18px;color:#e9f6fa;outline:none;line-height:1.7;overflow-wrap:anywhere}.visual-editor:focus{box-shadow:inset 0 0 0 1px rgba(34,195,223,.35)}.visual-editor:empty:before{content:'Write your message...';color:#65828e}.visual-editor img{max-width:100%;height:auto;border-radius:8px}.visual-editor a{color:#22c3df}.visual-editor blockquote{border-left:3px solid #287384;padding-left:14px;margin-left:0;color:#9db8c0}.editor-hint{color:var(--muted);font-size:12px;margin:8px 0 0;line-height:1.5}.compose-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:18px}.compose-actions .btn{text-decoration:none}.field input{min-height:46px}@media(max-width:650px){.compose{padding:15px}.visual-editor{min-height:300px;padding:14px}.editor-toolbar{gap:3px}.editor-toolbar button{padding:7px 8px}.toolbar-sep{display:none}.compose-actions{justify-content:stretch}.compose-actions .btn{flex:1}}</style>
@endpush
@push('scripts')
<script>
(function(){
    const editor=document.getElementById('editor');const body=document.getElementById('body');const form=document.getElementById('compose-form');const initialBody=@json(old('body',$initialBody));editor.innerHTML=initialBody||'';
    const focusEditor=()=>editor.focus();
    document.querySelectorAll('[data-cmd]').forEach(button=>button.addEventListener('click',()=>{focusEditor();document.execCommand(button.dataset.cmd,false,null);}));
    document.querySelectorAll('[data-block]').forEach(button=>button.addEventListener('click',()=>{focusEditor();document.execCommand('formatBlock',false,'<'+button.dataset.block+'>');}));
    document.getElementById('link-btn').addEventListener('click',()=>{focusEditor();const url=prompt('Enter the link URL:','https://');if(url)document.execCommand('createLink',false,url);});
    document.getElementById('image-btn').addEventListener('click',()=>{focusEditor();const url=prompt('Enter an image URL:','https://');if(url)document.execCommand('insertImage',false,url);});
    form.addEventListener('submit',function(e){body.value=editor.innerHTML.trim();if(!body.value||body.value==='<br>'){e.preventDefault();editor.focus();}});
})();
</script>
@endpush
@endsection
