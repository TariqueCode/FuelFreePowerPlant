from pathlib import Path

p = Path('resources/views/admin/site-content/create.blade.php')
s = p.read_text()

required = [
    '<div class="word-ribbon" role="toolbar" aria-label="Microsoft Word style content editor">',
    'id="upload-image"',
    'id="upload-video"',
    "const editor=document.getElementById('editor')",
    "document.querySelectorAll('[data-editor-tab]')",
    'function alignSelectedImage(cls)',
]
for marker in required:
    if marker not in s:
        raise SystemExit(f'Expected verified Site Content CMS editor marker missing: {marker}')

old_buttons = '''<button type="button" class="word-command" id="upload-image" title="Upload image"><i class="fa-regular fa-image"></i><span>Picture</span></button>
        <button type="button" class="word-command" id="insert-image-url" title="Insert image from URL"><i class="fa-solid fa-image"></i><span>Image URL</span></button>
        <button type="button" class="word-command" id="upload-video" title="Upload video"><i class="fa-solid fa-video"></i><span>Video</span></button>'''
new_buttons = '''<button type="button" class="word-command" id="upload-media" title="Upload images or videos"><i class="fa-solid fa-photo-film"></i><span>Media</span></button>
        <button type="button" class="word-command" id="insert-image-url" title="Insert image from URL"><i class="fa-solid fa-image"></i><span>Image URL</span></button>'''
if s.count(old_buttons) != 1:
    raise SystemExit('Expected separate image/video upload controls were not found exactly once.')
s = s.replace(old_buttons, new_buttons, 1)

old_inputs = '''<input id="media-input" type="file" hidden accept="image/jpeg,image/png,image/webp,image/gif"><input id="gallery-input" type="file" hidden multiple accept="image/jpeg,image/png,image/webp,image/gif"><input id="video-input" type="file" hidden accept="video/mp4,video/webm">'''
new_inputs = '''<input id="media-input" type="file" hidden multiple accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime"><input id="gallery-input" type="file" hidden multiple accept="image/jpeg,image/png,image/webp,image/gif">'''
if s.count(old_inputs) != 1:
    raise SystemExit('Expected Site Content editor media inputs were not found exactly once.')
s = s.replace(old_inputs, new_inputs, 1)

old_const = "const editor=document.getElementById('editor'),source=document.getElementById('content-source'),form=document.getElementById('content-form'),mediaInput=document.getElementById('media-input'),galleryInput=document.getElementById('gallery-input'),videoInput=document.getElementById('video-input'),galleryBatchInput=document.getElementById('gallery-batch-input'),galleryStatus=document.getElementById('gallery-upload-status');"
new_const = "const editor=document.getElementById('editor'),source=document.getElementById('content-source'),form=document.getElementById('content-form'),mediaInput=document.getElementById('media-input'),galleryInput=document.getElementById('gallery-input'),galleryBatchInput=document.getElementById('gallery-batch-input'),galleryStatus=document.getElementById('gallery-upload-status');"
if s.count(old_const) != 1:
    raise SystemExit('Expected Site Content editor JS state declaration was not found exactly once.')
s = s.replace(old_const, new_const, 1)

old_handlers = '''document.getElementById('upload-image').onclick=()=>mediaInput.click();mediaInput.onchange=async()=>{if(!mediaInput.files[0])return;try{const d=await upload(mediaInput.files[0]);exec('insertHTML',`<img src="${d.url}" alt="${safeName(d.name)}" loading="lazy">`)}catch(e){alert(e.message)}mediaInput.value=''};
document.getElementById('upload-video').onclick=()=>videoInput.click();videoInput.onchange=async()=>{if(!videoInput.files[0])return;try{const d=await upload(videoInput.files[0]);exec('insertHTML',`<video controls preload="metadata" src="${d.url}"></video>`)}catch(e){alert(e.message)}videoInput.value=''};'''
new_handlers = '''document.getElementById('upload-media').onclick=()=>mediaInput.click();
mediaInput.onchange=async()=>{
  const files=[...mediaInput.files];
  if(!files.length)return;
  try{
    const uploads=await Promise.all(files.map(upload));
    const html=uploads.map(d=>d.mime?.startsWith('video/')
      ? `<video controls preload="metadata" src="${d.url}"></video>`
      : `<img src="${d.url}" alt="${safeName(d.name)}" loading="lazy">`
    ).join('<p><br></p>');
    exec('insertHTML',html);
  }catch(e){alert(e.message)}
  mediaInput.value='';
};'''
if s.count(old_handlers) != 1:
    raise SystemExit('Expected separate Site Content media handlers were not found exactly once.')
s = s.replace(old_handlers, new_handlers, 1)

old_tabs = '''document.querySelectorAll('[data-editor-tab]').forEach(tab=>tab.addEventListener('click',()=>{
  const name=tab.dataset.editorTab;
  document.querySelectorAll('[data-editor-tab]').forEach(t=>{const active=t.dataset.editorTab===name;t.classList.toggle('active',active);t.setAttribute('aria-selected',active?'true':'false')});
  document.querySelectorAll('[data-editor-panel]').forEach(p=>p.classList.toggle('active',p.dataset.editorPanel===name));
}));'''
new_tabs = '''document.querySelectorAll('[data-editor-tab]').forEach(tab=>tab.addEventListener('click',()=>{
  const name=tab.dataset.editorTab;
  document.querySelectorAll('[data-editor-tab]').forEach(t=>{
    const active=t.dataset.editorTab===name;
    t.classList.toggle('active',active);
    t.setAttribute('aria-selected',active?'true':'false');
  });
  document.querySelectorAll('[data-editor-panel]').forEach(p=>{
    const active=p.dataset.editorPanel===name;
    p.classList.toggle('active',active);
    p.style.display=active?'flex':'none';
    p.setAttribute('aria-hidden',active?'false':'true');
  });
});
document.querySelectorAll('[data-editor-panel]').forEach(p=>{
  const active=p.dataset.editorPanel==='home';
  p.classList.toggle('active',active);
  p.style.display=active?'flex':'none';
  p.setAttribute('aria-hidden',active?'false':'true');
});'''
if s.count(old_tabs) != 1:
    raise SystemExit('Expected Site Content ribbon tab activation code was not found exactly once.')
s = s.replace(old_tabs, new_tabs, 1)

css_anchor = '.editor img,.editor video,.editor iframe{max-width:100%;height:auto;box-sizing:border-box}'
css_add = '''
.ff-image-resize-handle{
  position:fixed;width:14px;height:14px;margin:-7px 0 0 -7px;
  border:2px solid #061923;border-radius:4px;background:#43c2e5;
  box-shadow:0 0 0 1px rgba(67,194,229,.55),0 3px 12px rgba(0,0,0,.35);
  cursor:nwse-resize;z-index:10050;display:none;touch-action:none;
}
.editor img.ff-selected-image{outline:2px solid #43c2e5;outline-offset:2px;cursor:default}
@media(max-width:700px){.ff-image-resize-handle{width:16px;height:16px;margin:-8px 0 0 -8px;border-radius:5px}.editor img.ff-selected-image{outline-width:2px}}
'''
if s.count(css_anchor) < 1:
    raise SystemExit('Expected Site Content editor media CSS anchor was not found.')
s = s.replace(css_anchor, css_anchor+css_add, 1)

old_align = "function alignSelectedImage(cls){const sel=window.getSelection();let node=sel&&sel.anchorNode;while(node&&node!==editor&&node.nodeType===3)node=node.parentElement;while(node&&node!==editor&&node.tagName!=='IMG')node=node.parentElement;if(!node||node===editor||node.tagName!=='IMG'){alert('Select an image first.');return}node.classList.remove('align-left','align-center','align-right');node.classList.add(cls);sync()}"
new_align = "function alignSelectedImage(cls){let node=window.ffGetSelectedEditorImage?.()||null;if(!node){const sel=window.getSelection();node=sel&&sel.anchorNode;while(node&&node!==editor&&node.nodeType===3)node=node.parentElement;while(node&&node!==editor&&node.tagName!=='IMG')node=node.parentElement}if(!node||node===editor||node.tagName!=='IMG'){alert('Select an image first.');return}node.classList.remove('align-left','align-center','align-right');node.classList.add(cls);sync()}"
if s.count(old_align) != 1:
    raise SystemExit('Expected Site Content image alignment helper was not found exactly once.')
s = s.replace(old_align, new_align, 1)

resize_js = r'''

(function(){
  const shell=editor?.closest('.editor-shell');
  if(!editor||!shell)return;
  const handle=document.createElement('div');
  handle.className='ff-image-resize-handle';
  handle.setAttribute('aria-label','Resize image');
  handle.setAttribute('role','button');
  shell.appendChild(handle);
  let selectedImage=null;
  let drag=null;

  function clearImageSelection(){
    if(selectedImage)selectedImage.classList.remove('ff-selected-image');
    selectedImage=null;
    handle.style.display='none';
  }
  function positionHandle(){
    if(!selectedImage||!editor.contains(selectedImage)){clearImageSelection();return;}
    const rect=selectedImage.getBoundingClientRect();
    if(!rect.width||!rect.height){clearImageSelection();return;}
    handle.style.left=rect.right+'px';
    handle.style.top=rect.bottom+'px';
    handle.style.display='block';
  }
  function selectImage(img){
    if(selectedImage&&selectedImage!==img)selectedImage.classList.remove('ff-selected-image');
    selectedImage=img;
    selectedImage.classList.add('ff-selected-image');
    positionHandle();
  }

  editor.addEventListener('click',e=>{
    const img=e.target?.closest?.('img');
    if(img&&editor.contains(img)){selectImage(img);return;}
    if(!handle.contains(e.target))clearImageSelection();
  });
  editor.addEventListener('keydown',e=>{if(e.key==='Escape')clearImageSelection()});

  handle.addEventListener('pointerdown',e=>{
    if(!selectedImage)return;
    e.preventDefault();e.stopPropagation();
    const rect=selectedImage.getBoundingClientRect();
    drag={pointerId:e.pointerId,startX:e.clientX,startWidth:rect.width};
    handle.setPointerCapture?.(e.pointerId);
  });
  handle.addEventListener('pointermove',e=>{
    if(!drag||e.pointerId!==drag.pointerId||!selectedImage)return;
    e.preventDefault();
    const editorRect=editor.getBoundingClientRect();
    const minWidth=Math.max(80,Math.min(120,editorRect.width-24));
    const maxWidth=Math.max(minWidth,editorRect.width-24);
    const next=Math.max(minWidth,Math.min(maxWidth,drag.startWidth+(e.clientX-drag.startX)));
    selectedImage.style.width=Math.round(next)+'px';
    selectedImage.style.maxWidth='100%';
    selectedImage.style.height='auto';
    positionHandle();
  });
  const finishDrag=()=>{
    if(!drag)return;
    drag=null;
    if(selectedImage){sync();positionHandle();}
  };
  handle.addEventListener('pointerup',finishDrag);
  handle.addEventListener('pointercancel',finishDrag);
  window.addEventListener('scroll',positionHandle,{passive:true});
  window.addEventListener('resize',positionHandle,{passive:true});
  editor.addEventListener('input',()=>{if(selectedImage)setTimeout(positionHandle,0)});
  window.ffGetSelectedEditorImage=()=>selectedImage;
})();'''
s = s.replace(new_align, new_align+resize_js, 1)

final_css = '''
/* Final Site Content CMS ribbon contract: one active panel + one horizontal touch rail. */
.word-ribbon .word-panel{display:none!important;width:100%;max-width:100%;min-width:0;overflow-x:auto!important;overflow-y:hidden!important;align-items:stretch;flex-wrap:nowrap!important;-webkit-overflow-scrolling:touch;touch-action:pan-x;overscroll-behavior-x:contain}
.word-ribbon .word-panel.active{display:flex!important}
.word-ribbon .word-panel[aria-hidden="true"]{display:none!important}
.word-ribbon .word-panel[aria-hidden="false"]{display:flex!important}
.word-ribbon .word-panel>*{flex:0 0 auto!important}
.word-ribbon .word-group{flex:0 0 auto!important;width:max-content!important;max-width:none!important;min-width:max-content!important;flex-wrap:nowrap!important}
.word-ribbon .word-group-row{flex:0 0 auto!important;width:max-content!important;max-width:none!important;flex-wrap:nowrap!important}
.word-ribbon .word-command,.word-ribbon .word-icon,.word-ribbon .word-select,.word-ribbon .word-color{flex:0 0 auto!important}
@media(max-width:700px){
  .word-ribbon .word-panel{min-height:68px!important;padding:5px 4px!important;scrollbar-width:thin}
  .word-ribbon .word-group{padding-left:5px!important;padding-right:5px!important}
  .word-ribbon .word-group-row{gap:3px!important}
}
'''
style_close = '</style>@endpush'
if s.count(style_close) != 1:
    raise SystemExit('Unexpected Site Content editor style block structure.')
s = s.replace(style_close, final_css+'\n'+style_close, 1)

assert s.count('<script>') == s.count('</script>')
assert 'id="upload-image"' not in s
assert 'id="upload-video"' not in s
assert 'id="upload-media"' in s
assert 'multiple accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm,video/quicktime"' in s
assert "p.style.display=active?'flex':'none';" in s
assert '.word-ribbon .word-panel[aria-hidden="true"]{display:none!important}' in s
assert '.word-ribbon .word-panel[aria-hidden="false"]{display:flex!important}' in s
assert 'ff-image-resize-handle' in s
assert 'window.ffGetSelectedEditorImage' in s

p.write_text(s)
print('Verified Site Content CMS editor patch applied successfully.')
