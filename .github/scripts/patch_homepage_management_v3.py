from pathlib import Path

p = Path('resources/views/admin/homepage-builder/index.blade.php')
s = p.read_text()
if '/* FF_HOMEPAGE_MANAGEMENT_SELECTION_V3 */' in s:
    raise SystemExit(0)
marker = "    form.addEventListener('submit',sync);"
if marker not in s:
    raise SystemExit('form submit marker missing')
js = r'''    /* FF_HOMEPAGE_MANAGEMENT_SELECTION_V3 */
    document.querySelectorAll('.management-selection-panel').forEach(panel=>{
        const settings=panel.closest('.section-controls');
        const folder=settings?.querySelector('.management-folder');
        const boxes=()=>[...panel.querySelectorAll('input[data-folder-profile]')];
        const refresh=()=>{
            const folderId=folder?.value||'';
            const selected=boxes().filter(x=>x.checked);
            const validSelected=selected.filter(x=>x.closest('.picker-item')?.dataset.folderId===folderId);
            panel.querySelector('[data-count]').textContent=validSelected.length+' selected';
            panel.dataset.invalid=(!folderId||validSelected.length<1)?'true':'false';
            boxes().forEach(box=>{
                const item=box.closest('.picker-item');
                const same=item?.dataset.folderId===folderId;
                if(!same)box.checked=false;
                box.disabled=!same;
                if(item)item.hidden=!same;
            });
            const q=panel.querySelector('.picker-search')?.value.toLowerCase().trim()||'';
            boxes().forEach(box=>{
                const item=box.closest('.picker-item');
                if(item&&!item.hidden)item.hidden=!!q&&!item.dataset.search.includes(q);
            });
        };
        folder?.addEventListener('change',refresh);
        panel.querySelector('.picker-search')?.addEventListener('input',refresh);
        panel.addEventListener('change',e=>{if(e.target.matches('input[data-folder-profile]'))refresh();});
        refresh();
    });

    const managementPanel=document.querySelector('.management-selection-panel');
    if(managementPanel){
        form.addEventListener('submit',e=>{
            const settings=managementPanel.closest('.section-controls');
            const folder=settings?.querySelector('.management-folder');
            const selected=[...managementPanel.querySelectorAll('input[data-folder-profile]:checked')];
            if(!folder?.value||selected.length<1){
                e.preventDefault();
                managementPanel.hidden=false;
                managementPanel.dataset.invalid='true';
                managementPanel.scrollIntoView({behavior:'smooth',block:'center'});
                folder?.focus();
            }
        });
    }

'''
s=s.replace(marker,js+marker,1)
p.write_text(s)
print('homepage management JS added')
