from pathlib import Path

p = Path('resources/views/admin/homepage-builder/index.blade.php')
s = p.read_text()
start_marker = "@elseif(in_array($section->key,['management','news','gallery'],true))"
end_marker = "@elseif(in_array($section->key,['hero','highlight'],true))"
start = s.index(start_marker)
end = s.index(end_marker, start)
replacement = """@elseif($section->key==='management')
                    <div class=\"management-config\">
                        <div class=\"display-grid\">
                            <label><span>Profile folder</span><select name=\"settings[management][folder_id]\" class=\"management-folder\" required><option value=\"\">Select a profile folder</option>@foreach($managementFolders as $folder)<option value=\"{{ $folder->id }}\" @selected((int)($settings['folder_id'] ?? 0)===(int)$folder->id)>{{ $folder->name }} ({{ $folder->profiles->count() }} profiles)</option>@endforeach</select><small>Folders are managed in Profile Builder and reflected in the Menu Builder.</small></label>
                            <label><span>Section alignment</span><select name=\"settings[management][layout]\"><option value=\"left\" @selected($layout==='left')>Left</option><option value=\"center\" @selected($layout==='center')>Center</option><option value=\"right\" @selected($layout==='right')>Right</option></select></label>
                        </div>
                        <div class=\"selection-panel management-selection-panel\" data-key=\"management\" data-required=\"true\">
                            <div class=\"picker-head\"><div><strong>Select profiles to show</strong><small>Choose one or more profiles from the selected folder. At least one profile is required.</small></div><span data-count>0 selected</span></div>
                            <input class=\"picker-search\" type=\"search\" placeholder=\"Search profiles...\" aria-label=\"Search management profiles\">
                            <div class=\"picker-list\">
                            @foreach($managementFolders as $folder)
                                @foreach($folder->profiles as $profile)
                                    @php($profileId=(int)$profile->id)
                                    <label class=\"picker-item management-profile-choice\" data-folder-id=\"{{ $folder->id }}\" data-search=\"{{ strtolower($profile->title) }}\"><input type=\"checkbox\" name=\"settings[management][ids][]\" value=\"{{ $profileId }}\" data-folder-profile @checked(in_array($profileId,$settings['ids'] ?? [],true))><span>{{ $profile->title }}</span></label>
                                @endforeach
                            @endforeach
                            </div>
                        </div>
                    </div>
                @elseif(in_array($section->key,['news','gallery'],true))
                    <div class=\"display-grid\">
                        <label><span>Items on homepage</span><div class=\"number-field\"><input type=\"number\" name=\"settings[{{ $section->key }}][limit]\" min=\"1\" max=\"100\" value=\"{{ $limit }}\"><em>items</em></div></label>
                        <label><span>Content selection</span><select name=\"settings[{{ $section->key }}][mode]\" class=\"home-mode\"><option value=\"latest\" @selected($mode==='latest')>Latest published</option><option value=\"selected\" @selected($mode==='selected')>Choose specific items</option></select></label>
                        <label><span>Section alignment</span><select name=\"settings[{{ $section->key }}][layout]\"><option value=\"left\" @selected($layout==='left')>Left</option><option value=\"center\" @selected($layout==='center')>Center</option><option value=\"right\" @selected($layout==='right')>Right</option></select></label>
                    </div>
                    <div class=\"selection-panel\" data-key=\"{{ $section->key }}\" hidden>
                        <div class=\"picker-head\"><div><strong>Choose items to feature</strong><small>These records already exist in the source module. Nothing is created or edited here.</small></div><span data-count>0 selected</span></div>
                        <input class=\"picker-search\" type=\"search\" placeholder=\"Search published items...\" aria-label=\"Search {{ $titles[$section->key] }} items\">
                        <div class=\"picker-list\">
                        @foreach(($choices[$section->key] ?? []) as $choice)
                            @php($choiceId=(int)$choice->id)
                            <label class=\"picker-item\" data-search=\"{{ strtolower($choice->title ?? $choice->name) }}\"><input type=\"checkbox\" name=\"settings[{{ $section->key }}][ids][]\" value=\"{{ $choiceId }}\" @checked(in_array($choiceId,$settings['ids'] ?? [],true))><span>{{ $choice->title ?? $choice->name }}</span></label>
                        @endforeach
                        </div>
                    </div>
"""
s = s[:start] + replacement + s[end:]
css_marker = ".selection-panel{margin-top:11px;padding:11px;border:1px solid rgba(91,213,237,.1);border-radius:10px;background:#061923}.picker-head"
css_add = ".management-config{display:grid;gap:11px}.management-selection-panel[data-invalid=true]{border-color:rgba(244,122,122,.35);box-shadow:0 0 0 2px rgba(244,122,122,.05)}.management-profile-choice[hidden]{display:none!important}.management-profile-choice input:disabled{opacity:.35}.selection-panel small{line-height:1.45}"
if '.management-config{' not in s:
    if css_marker not in s: raise SystemExit('view css marker missing')
    s = s.replace(css_marker, css_add + css_marker, 1)
oldjs_marker = "    document.querySelectorAll('.home-mode').forEach(select=>{"
js_start = s.index(oldjs_marker)
js_end = s.index("    form.addEventListener('submit',sync);", js_start)
existing = s[js_start:js_end]
# Keep the existing news/gallery mode handler and append the management handler immediately after it.
management_js = """
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
                if(!same&&box.checked)box.checked=false;
                if(item)item.hidden=!same;
                box.disabled=!same;
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
        settings?.querySelector('.control-toggle')?.addEventListener('click',()=>setTimeout(refresh,0));
        refresh();
    });

    form.addEventListener('submit',e=>{
        const panel=document.querySelector('.management-selection-panel');
        if(!panel)return;
        const folder=panel.closest('.section-controls')?.querySelector('.management-folder');
        const selected=[...panel.querySelectorAll('input[data-folder-profile]:checked')];
        if(!folder?.value||selected.length<1){
            e.preventDefault();
            panel.hidden=false;
            panel.dataset.invalid='true';
            panel.scrollIntoView({behavior:'smooth',block:'center'});
            panel.querySelector('input[data-folder-profile]:not(:disabled)')?.focus();
        }
    });

"""
if '.management-selection-panel' not in existing:
    s = s[:js_start] + existing + management_js + s[js_end:]
p.write_text(s)
print('patched homepage management view')
