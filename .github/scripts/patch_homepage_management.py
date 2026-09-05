from pathlib import Path

# Homepage builder controller
p = Path('app/Http/Controllers/Admin/HomepageBuilderController.php')
s = p.read_text()
if 'use App\\Models\\ManagementProfileFolder;' not in s:
    s = s.replace('use App\\Models\\SiteContentItem;', 'use App\\Models\\SiteContentItem;\nuse App\\Models\\ManagementProfileFolder;')
s = s.replace("$choices = [\n            'management' => SiteContentItem::query()->where('type','management')->published()->orderBy('sort_order')->orderBy('title')->get(['id','title']),", "$managementFolders = ManagementProfileFolder::query()\n            ->where('status', 'published')\n            ->with(['profiles' => fn ($query) => $query->where('status', 'published')->orderBy('sort_order')->orderBy('title')->select(['id','management_profile_folder_id','title'])])\n            ->orderBy('sort_order')->orderBy('id')->get();\n\n        $choices = [")
s = s.replace("return view('admin.homepage-builder.index', compact('sections', 'counts', 'choices'));", "return view('admin.homepage-builder.index', compact('sections', 'counts', 'choices', 'managementFolders'));")
old = """            'settings.*.mode' => ['nullable', 'in:latest,selected'],
            'settings.*.ids' => ['nullable', 'array', 'max:100'],
            'settings.*.ids.*' => ['integer', 'distinct'],"""
new = """            'settings.*.mode' => ['nullable', 'in:latest,selected'],
            'settings.*.ids' => ['nullable', 'array', 'max:100'],
            'settings.*.ids.*' => ['integer', 'distinct'],
            'settings.management.folder_id' => ['required', 'integer', 'exists:management_profile_folders,id'],
            'settings.management.ids' => ['required', 'array', 'min:1', 'max:100'],
            'settings.management.ids.*' => ['required', 'integer', 'distinct'],"""
if old not in s and "settings.management.folder_id" not in s:
    raise SystemExit('management validation marker missing')
if "settings.management.folder_id" not in s:
    s = s.replace(old, new)
old = """        $selectedIds = [];
        foreach (['management','news','gallery'] as $key) {
            $selectedIds[$key] = array_values(array_unique(array_map('intval', (array) $request->input(\"settings.{$key}.ids\", []))));
        }

        $validIds = [
            'management' => SiteContentItem::query()->where('type', 'management')->published()->whereIn('id', $selectedIds['management'])->pluck('id')->map(fn ($id) => (int) $id)->all(),"""
new = """        $selectedIds = [];
        foreach (['management','news','gallery'] as $key) {
            $selectedIds[$key] = array_values(array_unique(array_map('intval', (array) $request->input(\"settings.{$key}.ids\", []))));
        }
        $managementFolderId = (int) $request->input('settings.management.folder_id');
        $managementFolder = ManagementProfileFolder::query()->where('status', 'published')->find($managementFolderId);
        if (! $managementFolder) {
            return back()->withErrors(['settings.management.folder_id' => 'Choose a valid published profile folder.']);
        }

        $managementValidIds = SiteContentItem::query()
            ->where('type', 'management')
            ->where('management_profile_folder_id', $managementFolderId)
            ->published()
            ->whereIn('id', $selectedIds['management'])
            ->pluck('id')->map(fn ($id) => (int) $id)->all();
        if (count($managementValidIds) < 1) {
            return back()->withErrors(['settings.management.ids' => 'Select at least one published profile from the selected folder.']);
        }

        $validIds = [
            'management' => $managementValidIds,"""
if old not in s and '$managementFolderId' not in s:
    raise SystemExit('management ids marker missing')
if '$managementFolderId' not in s:
    s = s.replace(old, new)
old = """            if (in_array($key, ['management','news','gallery'], true) && $request->has(\"settings.{$key}.limit\")) {
                $settings['limit'] = max(1, min(100, (int) $request->input(\"settings.{$key}.limit\")));
                $mode = $request->input(\"settings.{$key}.mode\", $settings['mode'] ?? 'latest');
                $settings['mode'] = $mode;
                if ($mode === 'selected') {
                    $settings['ids'] = array_values(array_slice($validIds[$key] ?? [], 0, 100));
                } else {
                    unset($settings['ids']);
                }
            }"""
new = """            if ($key === 'management') {
                $settings['folder_id'] = $managementFolderId;
                $settings['mode'] = 'selected';
                $settings['ids'] = array_values(array_slice($validIds['management'], 0, 100));
                unset($settings['limit']);
            } elseif (in_array($key, ['news','gallery'], true) && $request->has(\"settings.{$key}.limit\")) {
                $settings['limit'] = max(1, min(100, (int) $request->input(\"settings.{$key}.limit\")));
                $mode = $request->input(\"settings.{$key}.mode\", $settings['mode'] ?? 'latest');
                $settings['mode'] = $mode;
                if ($mode === 'selected') {
                    $settings['ids'] = array_values(array_slice($validIds[$key] ?? [], 0, 100));
                } else {
                    unset($settings['ids']);
                }
            }"""
if old not in s and "if ($key === 'management')" not in s:
    raise SystemExit('management settings marker missing')
if "if ($key === 'management')" not in s:
    s = s.replace(old, new)
p.write_text(s)

# Public homepage query
p = Path('app/Http/Controllers/HomeController.php')
s = p.read_text()
old = """        $managementLimit = max(1, min(100, (int) data_get($sectionSettings->get('management', []), 'limit', 4)));
        $newsLimit=max(1,min(100,(int)($sectionSettings['news']['limit']??3)));"""
new = """        $managementSettings = $sectionSettings['management'] ?? [];
        $managementFolderId = (int) ($managementSettings['folder_id'] ?? 0);
        $managementSelectedIds = array_values(array_unique(array_filter(array_map('intval', $managementSettings['ids'] ?? []))));
        $managementLimit = max(1, min(100, (int) data_get($managementSettings, 'limit', count($managementSelectedIds) ?: 4)));
        $newsLimit=max(1,min(100,(int)($sectionSettings['news']['limit']??3)));"""
if old not in s and '$managementFolderId' not in s:
    raise SystemExit('homepage management limit marker missing')
if '$managementFolderId' not in s:
    s = s.replace(old, new)
s = s.replace("        $managementSettings = $sectionSettings['management'] ?? [];\n        $gallerySettings", "        $gallerySettings")
old = """        if (($managementSettings['mode'] ?? 'latest') === 'selected') {
            $homeManagement = $applySelection($managementQuery, $managementSettings, $managementLimit);
        } else {
            $homeManagement = SiteContentItem::published()
                ->where('type', 'management')
                ->orderBy('sort_order')
                ->orderBy('title')
                ->limit(100)
                ->get()
                ->take($managementLimit)
                ->values();
        }"""
new = """        if ($managementFolderId > 0 && $managementSelectedIds) {
            $position = array_flip($managementSelectedIds);
            $homeManagement = $managementQuery
                ->where('management_profile_folder_id', $managementFolderId)
                ->whereIn('id', $managementSelectedIds)
                ->get()
                ->sortBy(fn ($item) => $position[(int) $item->id] ?? PHP_INT_MAX)
                ->values();
        } else {
            $homeManagement = collect();
        }"""
if old not in s and '$managementFolderId > 0' not in s:
    raise SystemExit('homepage management query marker missing')
if '$managementFolderId > 0' not in s:
    s = s.replace(old, new)
p.write_text(s)

# Homepage builder view
p = Path('resources/views/admin/homepage-builder/index.blade.php')
s = p.read_text()
start = s.index("                @elseif(in_array($section->key,['management','news','gallery'],true))")
end = s.index("                @elseif(in_array($section->key,['hero','highlight'],true))", start)
replacement = """                @elseif($section->key==='management')
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
oldjs = """    document.querySelectorAll('.home-mode').forEach(select=>{
        const settings=select.closest('.section-controls');
        const panel=settings?.querySelector('.selection-panel');
        const limit=settings?.querySelector('.number-field input');
        const boxes=()=>panel?[...panel.querySelectorAll('input[type=checkbox]')]:[];
        const refresh=()=>{
            if(!panel)return;
            const selected=boxes().filter(x=>x.checked);
            panel.querySelector('[data-count]').textContent=selected.length+' selected';
            const max=Math.max(1,Math.min(100,parseInt(limit?.value||'100',10)));
            boxes().forEach(x=>x.disabled=!x.checked&&selected.length>=max);
        };
        const syncMode=()=>{if(panel)panel.hidden=select.value!=='selected';refresh();};
        select.addEventListener('change',syncMode);
        limit?.addEventListener('input',refresh);
        panel?.querySelector('.picker-search')?.addEventListener('input',e=>{
            const q=e.target.value.toLowerCase().trim();
            panel.querySelectorAll('.picker-item').forEach(item=>item.hidden=!!q&&!item.dataset.search.includes(q));
        });
        panel?.addEventListener('change',e=>{if(e.target.matches('input[type=checkbox]'))refresh();});
        syncMode();
    });
"""
newjs = """    document.querySelectorAll('.home-mode').forEach(select=>{
        const settings=select.closest('.section-controls');
        const panel=settings?.querySelector('.selection-panel');
        const limit=settings?.querySelector('.number-field input');
        const boxes=()=>panel?[...panel.querySelectorAll('input[type=checkbox]')]:[];
        const refresh=()=>{
            if(!panel)return;
            const selected=boxes().filter(x=>x.checked);
            panel.querySelector('[data-count]').textContent=selected.length+' selected';
            const max=Math.max(1,Math.min(100,parseInt(limit?.value||'100',10)));
            boxes().forEach(x=>x.disabled=!x.checked&&selected.length>=max);
        };
        const syncMode=()=>{if(panel)panel.hidden=select.value!=='selected';refresh();};
        select.addEventListener('change',syncMode);
        limit?.addEventListener('input',refresh);
        panel?.querySelector('.picker-search')?.addEventListener('input',e=>{
            const q=e.target.value.toLowerCase().trim();
            panel.querySelectorAll('.picker-item').forEach(item=>item.hidden=!!q&&!item.dataset.search.includes(q));
        });
        panel?.addEventListener('change',e=>{if(e.target.matches('input[type=checkbox]'))refresh();});
        syncMode();
    });

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
if oldjs not in s and '.management-selection-panel' not in s:
    raise SystemExit('view js marker missing')
if '.management-selection-panel' not in s:
    s = s.replace(oldjs, newjs, 1)
p.write_text(s)

print('Homepage management selection patch complete')
