@php
    $editorId = $editorId ?? 'mail-editor';
    $formId = $formId ?? 'mail-compose-form';
    $bodyName = $bodyName ?? 'body';
    $initial = $initialBody ?? '';
    $allowAttachments = $allowAttachments ?? true;
@endphp
<div class="ff-mail-editor" data-editor="{{ $editorId }}">
    <div class="ff-editor-toolbar" role="toolbar" aria-label="Professional email editor">
        <select data-tool="formatBlock" title="Paragraph style"><option value="p">Paragraph</option><option value="h1">Heading 1</option><option value="h2">Heading 2</option><option value="h3">Heading 3</option><option value="h4">Heading 4</option><option value="blockquote">Quote</option><option value="pre">Code</option></select>
        <select data-tool="fontName" title="Font"><option value="Arial">Arial</option><option value="Georgia">Georgia</option><option value="Tahoma">Tahoma</option><option value="Times New Roman">Times New Roman</option><option value="Verdana">Verdana</option></select>
        <select data-tool="fontSize" title="Size"><option value="2">Small</option><option value="3" selected>Normal</option><option value="4">Large</option><option value="5">X-Large</option><option value="6">Huge</option></select>
        <button type="button" data-cmd="bold" title="Bold"><b>B</b></button><button type="button" data-cmd="italic" title="Italic"><i>I</i></button><button type="button" data-cmd="underline" title="Underline"><u>U</u></button><button type="button" data-cmd="strikeThrough" title="Strikethrough"><s>S</s></button>
        <span class="ff-sep"></span>
        <label class="ff-color" title="Text color"><i class="fa-solid fa-font"></i><input type="color" data-tool="foreColor" value="#1f2937"></label>
        <label class="ff-color" title="Highlight"><i class="fa-solid fa-highlighter"></i><input type="color" data-tool="hiliteColor" value="#fff59d"></label>
        <span class="ff-sep"></span>
        <button type="button" data-cmd="justifyLeft" title="Align left"><i class="fa-solid fa-align-left"></i></button><button type="button" data-cmd="justifyCenter" title="Center"><i class="fa-solid fa-align-center"></i></button><button type="button" data-cmd="justifyRight" title="Align right"><i class="fa-solid fa-align-right"></i></button><button type="button" data-cmd="justifyFull" title="Justify"><i class="fa-solid fa-align-justify"></i></button>
        <span class="ff-sep"></span>
        <button type="button" data-cmd="insertUnorderedList" title="Bulleted list"><i class="fa-solid fa-list"></i></button><button type="button" data-cmd="insertOrderedList" title="Numbered list"><i class="fa-solid fa-list-ol"></i></button><button type="button" data-cmd="outdent" title="Decrease indent"><i class="fa-solid fa-outdent"></i></button><button type="button" data-cmd="indent" title="Increase indent"><i class="fa-solid fa-indent"></i></button>
        <span class="ff-sep"></span>
        <button type="button" data-action="link" title="Insert link"><i class="fa-solid fa-link"></i></button><button type="button" data-action="image" title="Insert image URL"><i class="fa-regular fa-image"></i></button><button type="button" data-action="table" title="Insert table"><i class="fa-solid fa-table"></i></button><button type="button" data-cmd="insertHorizontalRule" title="Horizontal line"><i class="fa-solid fa-minus"></i></button>
        <span class="ff-sep"></span>
        <button type="button" data-cmd="undo" title="Undo"><i class="fa-solid fa-rotate-left"></i></button><button type="button" data-cmd="redo" title="Redo"><i class="fa-solid fa-rotate-right"></i></button><button type="button" data-cmd="removeFormat" title="Clear formatting"><i class="fa-solid fa-eraser"></i></button><button type="button" data-action="source" title="HTML source"><i class="fa-solid fa-code"></i></button><button type="button" data-action="fullscreen" title="Fullscreen"><i class="fa-solid fa-expand"></i></button>
    </div>
    <div class="ff-editor-body" contenteditable="true" spellcheck="true" role="textbox" aria-multiline="true"></div>
    <textarea class="ff-editor-source" hidden aria-label="HTML source"></textarea>
    @if($allowAttachments)
        <div class="ff-attachments"><label class="ff-attach-btn"><i class="fa-solid fa-paperclip"></i> Attach files<input type="file" name="attachments[]" multiple hidden></label><span class="ff-attach-help">Up to 30 files · 100 MB each · sent directly without permanent server storage</span><div class="ff-file-list"></div></div>
    @endif
    <div class="ff-editor-status"><span><i class="fa-solid fa-circle-check"></i> Auto-saved locally</span><span class="ff-word-count">0 words · 0 characters</span></div>
    <textarea name="{{ $bodyName }}" class="ff-editor-value" hidden required></textarea>
</div>
@push('styles')
<style>
.ff-mail-editor{border:1px solid var(--line);border-radius:15px;overflow:hidden;background:#fff;color:#1f2937;box-shadow:0 8px 30px rgba(0,0,0,.12);width:100%;max-width:100%;min-width:0}
.ff-editor-toolbar{display:flex;align-items:center;gap:4px;flex-wrap:wrap;padding:8px;border-bottom:1px solid #d8e2e6;background:#f5f8f9;position:sticky;top:0;z-index:2;max-width:100%;min-width:0}
.ff-editor-toolbar button,.ff-editor-toolbar select{height:34px;border:1px solid #d7e1e5;border-radius:7px;background:#fff;color:#334155;cursor:pointer;font-size:12px;padding:0 8px;flex:0 0 auto}
.ff-editor-toolbar button:hover,.ff-editor-toolbar select:focus{border-color:#22b8d5;background:#eefbfe}
.ff-editor-toolbar button{min-width:34px}.ff-editor-toolbar select{max-width:130px}.ff-sep{width:1px;height:24px;background:#d5e0e4;margin:0 3px}
.ff-color{height:34px;display:inline-flex;align-items:center;gap:4px;padding:0 5px;border:1px solid #d7e1e5;border-radius:7px;background:#fff;color:#536873;cursor:pointer}.ff-color input{width:22px;height:22px;padding:0;border:0;background:transparent;cursor:pointer}
.ff-editor-body{min-height:390px;padding:22px;outline:none;line-height:1.7;font-family:Arial,sans-serif;font-size:15px;overflow:auto}.ff-editor-body:empty:before{content:'Write your email here…';color:#94a3b8}.ff-editor-body h1{font-size:2em}.ff-editor-body h2{font-size:1.6em}.ff-editor-body h3{font-size:1.3em}.ff-editor-body blockquote{margin:12px 0;padding:8px 14px;border-left:4px solid #28b9d7;background:#f3fafc;color:#526873}.ff-editor-body pre{padding:12px;border-radius:8px;background:#17232b;color:#e6f7fb;white-space:pre-wrap}.ff-editor-body img{max-width:100%;height:auto;border-radius:6px}.ff-editor-body table{border-collapse:collapse;width:100%;margin:12px 0}.ff-editor-body td,.ff-editor-body th{border:1px solid #cbd5db;padding:8px;min-width:60px}.ff-editor-source{width:100%;min-height:390px;padding:18px;border:0;outline:none;resize:vertical;font:13px/1.6 ui-monospace,SFMono-Regular,Menlo,monospace;background:#07161e;color:#dff7fb}
.ff-attachments{padding:10px 12px;border-top:1px solid #dbe5e8;background:#f8fbfc}.ff-attach-btn{display:inline-flex;align-items:center;gap:7px;padding:7px 10px;border:1px solid #cbd9de;border-radius:8px;background:#fff;color:#334e58;font-size:12px;font-weight:700;cursor:pointer}.ff-attach-help{margin-left:8px;color:#78909a;font-size:11px}.ff-file-list{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}.ff-file{display:inline-flex;align-items:center;gap:6px;padding:5px 8px;border-radius:7px;background:#eaf5f8;color:#31505b;font-size:11px}.ff-file button{border:0;background:none;color:#78909a;cursor:pointer}
.ff-editor-status{display:flex;justify-content:space-between;gap:10px;padding:7px 12px;border-top:1px solid #dbe5e8;color:#718790;background:#fbfdfe;font-size:10px}.ff-editor-status i{color:#1db58b}
.ff-mail-editor.is-fullscreen{position:fixed;inset:12px;z-index:9999;display:flex;flex-direction:column;box-shadow:0 30px 100px rgba(0,0,0,.55)}.ff-mail-editor.is-fullscreen .ff-editor-body,.ff-mail-editor.is-fullscreen .ff-editor-source{flex:1;min-height:0}
@media(max-width:760px){
 .ff-editor-toolbar{flex-wrap:nowrap;overflow-x:auto;overflow-y:hidden;align-content:center;scrollbar-width:none;-webkit-overflow-scrolling:touch;touch-action:pan-x;padding:7px}
 .ff-editor-toolbar::-webkit-scrollbar{display:none}
 .ff-editor-toolbar select{max-width:112px}
 .ff-editor-body{min-height:280px;padding:14px;font-size:14px;overflow:auto;overflow-wrap:break-word;word-break:normal}
 .ff-editor-body img{max-width:100%;height:auto}
 .ff-editor-body table{width:100%;max-width:100%;table-layout:auto}
 .ff-editor-body td,.ff-editor-body th{max-width:100%;overflow-wrap:break-word;word-break:normal}
 .ff-editor-source{min-height:280px;max-width:100%;overflow:auto}
 .ff-attachments{padding:9px}
 .ff-attach-help{display:block;margin:6px 0 0;line-height:1.5}
 .ff-file-list{max-height:90px;overflow:auto}
 .ff-editor-status{font-size:9px;flex-wrap:wrap;line-height:1.5}
}
@media(max-width:420px){
 .ff-editor-toolbar{gap:3px;padding:6px}
 .ff-editor-toolbar button,.ff-editor-toolbar select,.ff-color{height:32px}
 .ff-editor-toolbar button{min-width:32px;padding:0 6px}
 .ff-editor-body{min-height:240px;padding:12px;font-size:13px}
}
</style>
@endpush
@push('scripts')
<script>
(function(){
 const root=document.querySelector('[data-editor="{{ $editorId }}"]'); if(!root)return;
 const editor=root.querySelector('.ff-editor-body'),source=root.querySelector('.ff-editor-source'),value=root.querySelector('.ff-editor-value'),form=document.getElementById('{{ $formId }}'),fileInput=root.querySelector('input[type=file]'),fileList=root.querySelector('.ff-file-list'),wordCount=root.querySelector('.ff-word-count');
 const storageKey='ff-webmail-draft:'+location.pathname+'::{{ $editorId }}';
 let files=[];
 const initial=@json($initial);
 editor.innerHTML=initial||'';
 const focus=()=>{editor.focus();};
 const saveSelection=()=>{const s=getSelection();if(s&&s.rangeCount)return s.getRangeAt(0);};
 function cmd(c,v=null){focus();document.execCommand(c,false,v);update();}
 root.querySelectorAll('[data-cmd]').forEach(b=>b.addEventListener('click',()=>cmd(b.dataset.cmd)));
 root.querySelectorAll('[data-tool]').forEach(el=>el.addEventListener('change',()=>cmd(el.dataset.tool,el.value)));
 root.querySelector('[data-action=link]').addEventListener('click',()=>{const url=prompt('Link URL','https://');if(url)cmd('createLink',url);});
 root.querySelector('[data-action=image]').addEventListener('click',()=>{const url=prompt('Image URL','https://');if(url)cmd('insertImage',url);});
 root.querySelector('[data-action=table]').addEventListener('click',()=>{let rows=parseInt(prompt('Rows','3'),10),cols=parseInt(prompt('Columns','3'),10);if(!rows||!cols||rows>12||cols>12)return;let html='<table><tbody>';for(let r=0;r<rows;r++){html+='<tr>';for(let c=0;c<cols;c++)html+='<td>&nbsp;</td>';html+='</tr>';}html+='</tbody></table><p><br></p>';focus();document.execCommand('insertHTML',false,html);update();});
 root.querySelector('[data-action=source]').addEventListener('click',()=>{if(source.hidden){source.value=editor.innerHTML;editor.hidden=true;source.hidden=false;}else{editor.innerHTML=source.value;source.hidden=true;editor.hidden=false;update();}});
 root.querySelector('[data-action=fullscreen]').addEventListener('click',()=>{root.classList.toggle('is-fullscreen');});
 editor.addEventListener('input',update);
 editor.addEventListener('paste',()=>setTimeout(update,0));
 function renderFiles(){if(!fileList)return;fileList.innerHTML='';files.forEach((f,i)=>{const el=document.createElement('span');el.className='ff-file';el.innerHTML='<i class="fa-solid fa-paperclip"></i>'+f.name+' <button type="button" aria-label="Remove">×</button>';el.querySelector('button').onclick=()=>{files.splice(i,1);syncFiles();renderFiles();};fileList.appendChild(el);});}
 function syncFiles(){if(!fileInput)return;const dt=new DataTransfer();files.forEach(f=>dt.items.add(f));fileInput.files=dt.files;}
 if(fileInput)fileInput.addEventListener('change',()=>{
   const incoming=[...fileInput.files];
   const maxBytes=100*1024*1024;
   const oversized=incoming.find(f=>f.size>maxBytes);
   if(oversized){alert(oversized.name+' is larger than 100 MB.');fileInput.value='';files=[];renderFiles();return;}
   files=incoming.slice(0,30);syncFiles();renderFiles();
 });
 function update(){if(source.hidden===false){value.value=source.value;return;}value.value=editor.innerHTML.trim();const text=editor.innerText.trim();const words=text?text.split(/\s+/).length:0;if(wordCount)wordCount.textContent=words+' words · '+text.length+' characters';try{localStorage.setItem(storageKey,editor.innerHTML);}catch(e){}}
 try{const saved=localStorage.getItem(storageKey);if(saved&&!initial)editor.innerHTML=saved;}catch(e){}
 if(form)form.addEventListener('submit',()=>{if(!source.hidden)editor.innerHTML=source.value;update();try{localStorage.removeItem(storageKey);}catch(e){}});
 update();
})();
</script>
@endpush
