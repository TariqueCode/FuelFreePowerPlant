@php
    $editorId = $editorId ?? 'mail-editor';
    $formId = $formId ?? 'mail-compose-form';
    $bodyName = $bodyName ?? 'body';
    $initial = $initialBody ?? '';
    $allowAttachments = $allowAttachments ?? true;
    $editorMode = $editorMode ?? 'mail';
@endphp
<div class="ff-mail-editor" data-editor="{{ $editorId }}" data-mode="{{ $editorMode }}">
    <div class="ff-editor-toolbar" role="toolbar" aria-label="Professional email editor">
        <select data-tool="formatBlock" title="Paragraph style"><option value="p">Paragraph</option><option value="h1">Heading 1</option><option value="h2">Heading 2</option><option value="h3">Heading 3</option><option value="h4">Heading 4</option><option value="blockquote">Quote</option><option value="pre">Code</option></select>
        <select data-tool="fontName" title="Font"><option value="Arial">Arial</option><option value="Georgia">Georgia</option><option value="Tahoma">Tahoma</option><option value="Times New Roman">Times New Roman</option><option value="Verdana">Verdana</option></select>
        <select data-tool="fontSize" title="Size"><option value="2">Small</option><option value="3" selected>Normal</option><option value="4">Large</option><option value="5">X-Large</option><option value="6">Huge</option></select>
        <button type="button" data-cmd="bold" title="Bold"><b>B</b></button><button type="button" data-cmd="italic" title="Italic"><i>I</i></button><button type="button" data-cmd="underline" title="Underline"><u>U</u></button><button type="button" data-cmd="strikeThrough" title="Strikethrough"><s>S</s></button>
        <span class="ff-sep"></span>
        <button type="button" class="ff-color-control" data-color="foreColor" title="Text color"><i class="fa-solid fa-font"></i><span class="ff-color-swatch" data-swatch="foreColor"></span><input type="color" data-tool="foreColor" value="#1f2937" aria-label="Text color"></button>
        <button type="button" class="ff-color-control" data-color="hiliteColor" title="Highlight color"><i class="fa-solid fa-highlighter"></i><span class="ff-color-swatch" data-swatch="hiliteColor"></span><input type="color" data-tool="hiliteColor" value="#fff59d" aria-label="Highlight color"></button>
        <span class="ff-sep"></span>
        <button type="button" data-cmd="justifyLeft" title="Align left"><i class="fa-solid fa-align-left"></i></button><button type="button" data-cmd="justifyCenter" title="Center"><i class="fa-solid fa-align-center"></i></button><button type="button" data-cmd="justifyRight" title="Align right"><i class="fa-solid fa-align-right"></i></button><button type="button" data-cmd="justifyFull" title="Justify"><i class="fa-solid fa-align-justify"></i></button>
        <span class="ff-sep"></span>
        <button type="button" data-cmd="insertUnorderedList" title="Bulleted list"><i class="fa-solid fa-list"></i></button><button type="button" data-cmd="insertOrderedList" title="Numbered list"><i class="fa-solid fa-list-ol"></i></button><button type="button" data-cmd="outdent" title="Decrease indent"><i class="fa-solid fa-outdent"></i></button><button type="button" data-cmd="indent" title="Increase indent"><i class="fa-solid fa-indent"></i></button>
        <span class="ff-sep"></span>
        <button type="button" data-action="link" title="Insert link"><i class="fa-solid fa-link"></i></button><button type="button" data-action="image" title="Insert image URL"><i class="fa-regular fa-image"></i></button><button type="button" data-action="table" title="Insert table"><i class="fa-solid fa-table"></i></button>
@if($editorMode === 'cms')
<button type="button" data-action="image-settings" title="Image settings"><i class="fa-solid fa-expand"></i></button>
<button type="button" data-action="table-row" title="Add table row"><i class="fa-solid fa-table-rows"></i></button>
<button type="button" data-action="table-column" title="Add table column"><i class="fa-solid fa-table-columns"></i></button>
<button type="button" data-action="table-cell" title="Cell size"><i class="fa-solid fa-ruler-combined"></i></button><button type="button" data-action="table-delete-row" title="Delete table row"><i class="fa-solid fa-trash-can"></i><span class="ff-tool-label"> Row</span></button><button type="button" data-action="table-delete-column" title="Delete table column"><i class="fa-solid fa-trash-can"></i><span class="ff-tool-label"> Col</span></button>
<button type="button" data-action="chart" title="Insert chart"><i class="fa-solid fa-chart-pie"></i></button><button type="button" data-action="delete-element" title="Remove selected image or chart"><i class="fa-solid fa-trash"></i></button>
@endif<button type="button" data-cmd="insertHorizontalRule" title="Horizontal line"><i class="fa-solid fa-minus"></i></button>
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
.ff-mail-editor{border:1px solid var(--line);border-radius:15px;overflow:visible;background:#fff;color:#1f2937;box-shadow:0 8px 30px rgba(0,0,0,.12);width:100%;max-width:100%;min-width:0}
.ff-editor-toolbar{display:flex;align-items:center;gap:4px;flex-wrap:wrap;padding:8px;border-bottom:1px solid #d8e2e6;background:#f5f8f9;position:sticky;top:70px;z-index:900;align-self:flex-start;max-width:100%;min-width:0;border-radius:15px 15px 0 0;box-shadow:0 2px 8px rgba(15,55,70,.08)}
.ff-editor-toolbar button,.ff-editor-toolbar select{height:34px;border:1px solid #d7e1e5;border-radius:7px;background:#fff;color:#334155;cursor:pointer;font-size:12px;padding:0 8px;flex:0 0 auto}
.ff-editor-toolbar button:hover,.ff-editor-toolbar select:focus{border-color:#22b8d5;background:#eefbfe}
.ff-editor-toolbar button{min-width:34px}.ff-tool-label{display:inline}@media(max-width:760px){.ff-tool-label{display:none}}.ff-editor-toolbar select{max-width:130px}.ff-sep{width:1px;height:24px;background:#d5e0e4;margin:0 3px;flex:0 0 1px}
.ff-color-control{height:34px!important;display:inline-flex;align-items:center;gap:6px;padding:0 8px!important;border:1px solid #d7e1e5;border-radius:7px;background:#fff;color:#536873;cursor:pointer;position:relative}
.ff-color-control input{position:absolute;width:1px;height:1px;opacity:0;pointer-events:none}
.ff-color-swatch{width:17px;height:17px;border-radius:4px;border:1px solid #b9c8ce;background:#1f2937;box-shadow:inset 0 -3px 0 rgba(0,0,0,.14)}
.ff-editor-body{min-height:390px;padding:22px;outline:none;line-height:1.7;font-family:Arial,sans-serif;font-size:15px;overflow:auto}.ff-editor-body:empty:before{content:'Write your email here…';color:#94a3b8}.ff-editor-body h1{font-size:2em}.ff-editor-body h2{font-size:1.6em}.ff-editor-body h3{font-size:1.3em}.ff-editor-body blockquote{margin:12px 0;padding:8px 14px;border-left:4px solid #28b9d7;background:#f3fafc;color:#526873}.ff-editor-body pre{padding:12px;border-radius:8px;background:#17232b;color:#e6f7fb;white-space:pre-wrap}.ff-editor-body img{max-width:100%;height:auto;border-radius:6px}.ff-editor-body table{border-collapse:collapse;width:100%;margin:12px 0}.ff-editor-body .ff-chart{margin:18px 0;padding:14px;border:1px solid #d8e2e6;border-radius:10px;overflow:auto;color:#246b7a}.ff-editor-body .ff-chart figcaption{font-weight:700;margin-bottom:8px}.ff-editor-body .ff-chart svg{display:block;width:100%;min-width:520px;height:auto}.ff-editor-body img{cursor:default;max-width:100%;height:auto}.ff-editor-body img.ff-selected{outline:2px solid #22b8d5;outline-offset:3px}.ff-editor-body table{table-layout:fixed}.ff-editor-body td,.ff-editor-body th{overflow-wrap:anywhere}.ff-editor-body td,.ff-editor-body th{border:1px solid #cbd5db;padding:8px;min-width:60px}.ff-editor-source{width:100%;min-height:390px;padding:18px;border:0;outline:none;resize:vertical;font:13px/1.6 ui-monospace,SFMono-Regular,Menlo,monospace;background:#07161e;color:#dff7fb}
.ff-attachments{padding:10px 12px;border-top:1px solid #dbe5e8;background:#f8fbfc}.ff-attach-btn{display:inline-flex;align-items:center;gap:7px;padding:7px 10px;border:1px solid #cbd9de;border-radius:8px;background:#fff;color:#334e58;font-size:12px;font-weight:700;cursor:pointer}.ff-attach-help{margin-left:8px;color:#78909a;font-size:11px}.ff-file-list{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}.ff-file{display:inline-flex;align-items:center;gap:6px;padding:5px 8px;border-radius:7px;background:#eaf5f8;color:#31505b;font-size:11px}.ff-file button{border:0;background:none;color:#78909a;cursor:pointer}
.ff-editor-status{display:flex;justify-content:space-between;gap:10px;padding:7px 12px;border-top:1px solid #dbe5e8;color:#718790;background:#fbfdfe;font-size:10px}.ff-editor-status i{color:#1db58b}
.ff-mail-editor.is-fullscreen{position:fixed;inset:12px;z-index:9999;display:flex;flex-direction:column;box-shadow:0 30px 100px rgba(0,0,0,.55)}.ff-mail-editor.is-fullscreen .ff-editor-toolbar{top:0}.ff-mail-editor.is-fullscreen .ff-editor-body,.ff-mail-editor.is-fullscreen .ff-editor-source{flex:1;min-height:0}
@media(max-width:760px){
 .ff-editor-toolbar{top:70px;display:flex;flex-wrap:nowrap;flex-direction:row;justify-content:flex-start;align-items:center;align-content:center;gap:4px;overflow-x:auto;overflow-y:hidden;width:100%;max-width:100%;min-width:0;box-sizing:border-box;scrollbar-width:none;-ms-overflow-style:none;-webkit-overflow-scrolling:touch;overscroll-behavior-x:contain;scroll-behavior:smooth;scroll-padding-inline:10px;touch-action:pan-x;padding:7px 10px}
 .ff-editor-toolbar::-webkit-scrollbar{display:none}
 .ff-editor-toolbar>*{flex:0 0 auto;white-space:nowrap}
 .ff-editor-toolbar button,.ff-editor-toolbar select,.ff-editor-toolbar .ff-color-control{flex:0 0 auto}
 .ff-editor-toolbar button{min-width:34px}
 .ff-editor-toolbar select{max-width:112px;min-width:max-content}
 .ff-editor-toolbar .ff-sep{display:block;flex:0 0 1px;width:1px;height:24px}
 .ff-editor-body{width:100%;max-width:100%;min-width:0;box-sizing:border-box;min-height:280px;padding:14px;font-size:14px;overflow:auto;overflow-wrap:anywhere;word-break:normal;-webkit-overflow-scrolling:touch;overscroll-behavior-x:contain}
 .ff-editor-body img{max-width:100%;height:auto}
 .ff-editor-body table{width:100%;max-width:100%;table-layout:auto}
 .ff-editor-body td,.ff-editor-body th{max-width:100%;overflow-wrap:anywhere;word-break:normal}
 .ff-editor-body .ff-chart{max-width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}
 .ff-editor-source{width:100%;min-height:280px;max-width:100%;box-sizing:border-box;overflow:auto}
 .ff-attachments{padding:9px}
 .ff-attach-help{display:block;margin:6px 0 0;line-height:1.5}
 .ff-file-list{max-height:90px;overflow:auto}
 .ff-editor-status{font-size:9px;flex-wrap:wrap;line-height:1.5}
 .ff-mail-editor.is-fullscreen{inset:0;border-radius:0}
 .ff-mail-editor.is-fullscreen .ff-editor-toolbar{top:0}
}
@media(max-width:420px){
 .ff-editor-toolbar{gap:3px;padding:6px 8px}
 .ff-editor-toolbar button,.ff-editor-toolbar select,.ff-editor-toolbar .ff-color-control{height:32px!important}
 .ff-editor-toolbar button{min-width:32px;padding:0 6px}
 .ff-editor-toolbar select{max-width:108px}
 .ff-editor-toolbar .ff-color-control{min-width:58px!important;padding:0 7px!important}
 .ff-editor-body{min-height:240px;padding:12px;font-size:13px}
}
</style>
@endpush
@push('scripts')
<script>
(function(){
 const root=document.querySelector('[data-editor="{{ $editorId }}"]'); if(!root)return;
 const editor=root.querySelector('.ff-editor-body'),source=root.querySelector('.ff-editor-source'),value=root.querySelector('.ff-editor-value'),form=document.getElementById('{{ $formId }}'),fileInput=root.querySelector('input[type=file]'),fileList=root.querySelector('.ff-file-list'),wordCount=root.querySelector('.ff-word-count');
 const storageKey='ff-editor-draft:'+location.pathname+'::{{ $editorId }}';
 let files=[];
 const initial=@json($initial);
 const cmsMode={{ $editorMode === 'cms' ? 'true' : 'false' }};
 const escapeHtml=(s)=>String(s??'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
 const escapeAttr=(s)=>escapeHtml(s);
 editor.innerHTML=initial||'';
 let savedRange=null;
 const focus=()=>{editor.focus();if(savedRange){const s=getSelection();s.removeAllRanges();s.addRange(savedRange);}};
 const rememberSelection=()=>{const s=getSelection();if(s&&s.rangeCount&&editor.contains(s.anchorNode)){savedRange=s.getRangeAt(0).cloneRange();}};
 function cmd(c,v=null){focus();document.execCommand(c,false,v);rememberSelection();updateToolbarState();update();}
 function updateToolbarState(){
   const s=getSelection(); if(!s||!s.rangeCount||!editor.contains(s.anchorNode)) return;
   let node=s.anchorNode.nodeType===3?s.anchorNode.parentElement:s.anchorNode;
   const block=node?.closest?.('h1,h2,h3,h4,h5,h6,p,blockquote,pre');
   const format=root.querySelector('[data-tool="formatBlock"]');
   if(format) format.value=block?(block.tagName.toLowerCase()==='blockquote'?'blockquote':block.tagName.toLowerCase()):'p';
   const font=root.querySelector('[data-tool="fontName"]');
   if(font){const family=(node?getComputedStyle(node).fontFamily:'').replaceAll('"','');const match=[...font.options].find(o=>family.toLowerCase().includes(o.value.toLowerCase()));font.value=match?match.value:'Arial';}
   const size=root.querySelector('[data-tool="fontSize"]');
   if(size){const px=parseFloat(node?getComputedStyle(node).fontSize:'15');size.value=px<=13?'2':px<=16?'3':px<=20?'4':px<=26?'5':'6';}
   [['foreColor','#1f2937'],['hiliteColor','#fff59d']].forEach(([tool,fallback])=>{
      const input=root.querySelector('[data-tool="'+tool+'"]'), swatch=root.querySelector('[data-swatch="'+tool+'"]');
      if(input&&swatch){let color=getComputedStyle(node||editor)[tool==='foreColor'?'color':'backgroundColor']; if(!color||color==='rgba(0, 0, 0, 0)')color=fallback; swatch.style.background=color;}
   });
 }
 root.querySelectorAll('[data-cmd]').forEach(b=>b.addEventListener('mousedown',e=>e.preventDefault()));
 root.querySelectorAll('[data-cmd]').forEach(b=>b.addEventListener('click',()=>cmd(b.dataset.cmd)));
 root.querySelectorAll('[data-tool]').forEach(el=>{
   if(el.type==='color'){
     el.addEventListener('click',e=>e.stopPropagation());
     el.addEventListener('input',()=>{cmd(el.dataset.tool,el.value);});
   } else el.addEventListener('change',()=>cmd(el.dataset.tool,el.value));
 });
 root.querySelectorAll('[data-color]').forEach(control=>{
   control.addEventListener('mousedown',e=>e.preventDefault());
   control.addEventListener('click',()=>control.querySelector('input[type=color]')?.click());
 });
 root.querySelector('[data-action=link]').addEventListener('click',()=>{const url=prompt('Link URL','https://');if(url)cmd('createLink',url);});
 root.querySelector('[data-action=image]').addEventListener('click',()=>{const url=prompt('Image URL','https://');if(url)cmd('insertImage',url);});
 @if($editorMode === 'cms')
 const selected=(selector)=>{const s=getSelection();if(!s||!s.rangeCount||!editor.contains(s.anchorNode))return null;let n=s.anchorNode.nodeType===3?s.anchorNode.parentElement:s.anchorNode;return n?.closest?.(selector)||null;};
 const validSize=v=>/^\\d+(?:\\.\\d+)?(?:px|%)$/.test(String(v).trim());
 root.querySelector('[data-action=image-settings]').addEventListener('click',()=>{
   const img=selected('img'); if(!img){alert('Select an image first.');return;}
   const width=prompt('Image width (example: 420px or 75%)',img.style.width||'100%'); if(width===null)return;
   if(!validSize(width)){alert('Please enter a size such as 420px or 75%.');return;}
   img.style.width=width.trim();img.style.maxWidth='100%';img.style.height='auto';
   const align=(prompt('Alignment: left, center, or right',img.dataset.align||'left')||'left').trim().toLowerCase();
   if(['left','center','right'].includes(align)){img.dataset.align=align;img.style.display='block';img.style.marginLeft=align==='center'?'auto':align==='right'?'auto':'0';img.style.marginRight=align==='center'?'auto':align==='left'?'auto':'0';}
   const alt=prompt('Alternative text',img.alt||'');if(alt!==null)img.alt=alt;update();
 });
 const cell=()=>selected('td,th');
 root.querySelector('[data-action=table-row]').addEventListener('click',()=>{
   const td=cell();if(!td){alert('Place the cursor inside a table cell first.');return;}
   const tr=td.parentElement,copy=tr.cloneNode(true);[...copy.cells].forEach(x=>x.innerHTML='&nbsp;');tr.parentElement.insertBefore(copy,tr.nextSibling);update();
 });
 root.querySelector('[data-action=table-column]').addEventListener('click',()=>{
   const td=cell();if(!td){alert('Place the cursor inside a table cell first.');return;}
   const table=td.closest('table'),index=td.cellIndex;
   [...table.rows].forEach(row=>{const source=row.cells[index],el=document.createElement(source?.tagName||'td');el.innerHTML='&nbsp;';el.style.width=source?.style.width||'';row.insertBefore(el,row.cells[index+1]||null);});update();
 });
 root.querySelector('[data-action=table-delete-row]').addEventListener('click',()=>{
   const td=cell();if(!td){alert('Place the cursor inside a table row first.');return;}
   const tr=td.parentElement, table=tr.closest('table'); if(table.rows.length<=1){alert('A table must keep at least one row.');return;} tr.remove();update();
 });
 root.querySelector('[data-action=table-delete-column]').addEventListener('click',()=>{
   const td=cell();if(!td){alert('Place the cursor inside a table column first.');return;}
   const table=td.closest('table'),index=td.cellIndex;
   const maxCols=Math.max(...[...table.rows].map(row=>row.cells.length));
   if(maxCols<=1){alert('A table must keep at least one column.');return;}
   [...table.rows].forEach(row=>{if(row.cells[index])row.deleteCell(index);});update();
 });
 root.querySelector('[data-action=delete-element]').addEventListener('click',()=>{
   const target=selected('img,.ff-chart');if(!target){alert('Select an image or chart first.');return;}
   target.remove();update();
 });
 root.querySelector('[data-action=table-cell]').addEventListener('click',()=>{
   const td=cell();if(!td){alert('Place the cursor inside a table cell first.');return;}
   const width=prompt('Cell width (example: 180px or 25%)',td.style.width||'');if(width===null)return;
   if(width && !validSize(width)){alert('Please enter a size such as 180px or 25%.');return;}
   if(width)td.style.width=width.trim();else td.style.removeProperty('width');
   const padding=prompt('Cell padding in px',parseInt(getComputedStyle(td).padding,10)||8);if(padding!==null&&/^\\d+$/.test(padding.trim()))td.style.padding=Math.min(60,parseInt(padding,10))+'px';update();
 });
 root.querySelector('[data-action=chart]').addEventListener('click',()=>{
   const title=prompt('Chart title','Power generation');if(title===null)return;
   const raw=prompt('Data: Label:Value, Label:Value','Plant A:40, Plant B:30, Plant C:20');if(!raw)return;
   const items=raw.split(',').map(x=>x.split(':')).map(x=>({label:x[0]?.trim(),value:Number(x.slice(1).join(':'))})).filter(x=>x.label&&Number.isFinite(x.value)&&x.value>=0).slice(0,10);
   if(!items.length){alert('Use Label:Value pairs, for example Plant A:40, Plant B:30.');return;}
   const max=Math.max(1,...items.map(x=>x.value)),barW=620/items.length;
   const bars=items.map((x,i)=>{const h=220*x.value/max,y=285-h,xx=90+i*barW+barW*.16;return '<rect x="'+xx.toFixed(1)+'" y="'+y.toFixed(1)+'" width="'+(barW*.68).toFixed(1)+'" height="'+h.toFixed(1)+'" rx="5" fill="currentColor" opacity=".72"/><text x="'+(xx+barW*.34).toFixed(1)+'" y="312" text-anchor="middle" font-size="11" fill="currentColor">'+escapeHtml(x.label)+'</text><text x="'+(xx+barW*.34).toFixed(1)+'" y="'+Math.max(16,y-6).toFixed(1)+'" text-anchor="middle" font-size="11" fill="currentColor">'+x.value+'</text>';}).join('');
   const html='<figure class="ff-chart" contenteditable="false" data-chart="bar"><figcaption>'+escapeHtml(title)+'</figcaption><svg viewBox="0 0 760 340" role="img" aria-label="'+escapeAttr(title)+'" xmlns="http://www.w3.org/2000/svg"><line x1="75" y1="285" x2="720" y2="285" stroke="currentColor" opacity=".25"/>'+bars+'</svg></figure><p><br></p>';
   focus();document.execCommand('insertHTML',false,html);update();
 });
 @endif
 root.querySelector('[data-action=table]').addEventListener('click',()=>{let rows=parseInt(prompt('Rows','3'),10),cols=parseInt(prompt('Columns','3'),10);if(!rows||!cols||rows>12||cols>12)return;let html='<table><tbody>';for(let r=0;r<rows;r++){html+='<tr>';for(let c=0;c<cols;c++)html+='<td>&nbsp;</td>';html+='</tr>';}html+='</tbody></table><p><br></p>';focus();document.execCommand('insertHTML',false,html);update();});
 root.querySelector('[data-action=source]').addEventListener('click',()=>{
 if(source.hidden){source.value=editor.innerHTML;editor.hidden=true;source.hidden=false;}
 else{editor.innerHTML=source.value;source.hidden=true;editor.hidden=false;update();}
});
 root.querySelector('[data-action=fullscreen]').addEventListener('click',()=>{root.classList.toggle('is-fullscreen');});
 editor.addEventListener('click',e=>{editor.querySelectorAll('img.ff-selected').forEach(i=>i.classList.remove('ff-selected'));if(e.target.closest('img'))e.target.closest('img').classList.add('ff-selected');});
 editor.addEventListener('input',()=>{rememberSelection();updateToolbarState();update();});
 editor.addEventListener('keyup',()=>{rememberSelection();updateToolbarState();});
 editor.addEventListener('mouseup',()=>{rememberSelection();updateToolbarState();});
 document.addEventListener('selectionchange',()=>{rememberSelection();updateToolbarState();});
 editor.addEventListener('paste',()=>setTimeout(()=>{rememberSelection();updateToolbarState();update();},0));
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
 updateToolbarState();
 update();
})();
</script>
@endpush
