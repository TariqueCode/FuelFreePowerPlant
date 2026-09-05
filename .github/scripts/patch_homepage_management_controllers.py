from pathlib import Path

p = Path('app/Http/Controllers/Admin/HomepageBuilderController.php')
s = p.read_text()
if 'use App\\Models\\ManagementProfileFolder;' not in s:
    s = s.replace('use App\\Models\\SiteContentItem;', 'use App\\Models\\SiteContentItem;\nuse App\\Models\\ManagementProfileFolder;')
if '$managementFolders = ManagementProfileFolder::query()' not in s:
    old = """$choices = [
            'management' => SiteContentItem::query()->where('type','management')->published()->orderBy('sort_order')->orderBy('title')->get(['id','title']),"""
    new = """$managementFolders = ManagementProfileFolder::query()
            ->where('status', 'published')
            ->with(['profiles' => fn ($query) => $query->where('status', 'published')->orderBy('sort_order')->orderBy('title')->select(['id','management_profile_folder_id','title'])])
            ->orderBy('sort_order')->orderBy('id')->get();

        $choices = ["""
    if old not in s: raise SystemExit('choices marker missing')
    s = s.replace(old, new)
    s = s.replace("return view('admin.homepage-builder.index', compact('sections', 'counts', 'choices'));", "return view('admin.homepage-builder.index', compact('sections', 'counts', 'choices', 'managementFolders'));")
if "'settings.management.folder_id'" not in s:
    marker = "            'settings.*.ids.*' => ['integer', 'distinct'],"
    additions = """            'settings.management.folder_id' => ['required', 'integer', 'exists:management_profile_folders,id'],
            'settings.management.ids' => ['required', 'array', 'min:1', 'max:100'],
            'settings.management.ids.*' => ['required', 'integer', 'distinct'],"""
    if marker not in s: raise SystemExit('validation marker missing')
    s = s.replace(marker, marker+'\n'+additions, 1)
if '$managementFolderId = (int) $request->input' not in s:
    marker = """        $validIds = [
            'management' => SiteContentItem::query()->where('type', 'management')->published()->whereIn('id', $selectedIds['management'])->pluck('id')->map(fn ($id) => (int) $id)->all(),"""
    replacement = """        $managementFolderId = (int) $request->input('settings.management.folder_id');
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
    if marker not in s: raise SystemExit('valid ids marker missing')
    s = s.replace(marker, replacement, 1)
if "if ($key === 'management')" not in s:
    marker = """            if (in_array($key, ['management','news','gallery'], true) && $request->has(\"settings.{$key}.limit\")) {"""
    replacement = """            if ($key === 'management') {
                $settings['folder_id'] = $managementFolderId;
                $settings['mode'] = 'selected';
                $settings['ids'] = array_values(array_slice($validIds['management'], 0, 100));
                unset($settings['limit']);
            } elseif (in_array($key, ['news','gallery'], true) && $request->has(\"settings.{$key}.limit\")) {"""
    if marker not in s: raise SystemExit('settings marker missing')
    s = s.replace(marker, replacement, 1)
p.write_text(s)

p = Path('app/Http/Controllers/HomeController.php')
s = p.read_text()
if '$managementFolderId' not in s:
    marker = """        $managementLimit = max(1, min(100, (int) data_get($sectionSettings->get('management', []), 'limit', 4)));
        $newsLimit=max(1,min(100,(int)($sectionSettings['news']['limit']??3)));"""
    replacement = """        $managementSettings = $sectionSettings['management'] ?? [];
        $managementFolderId = (int) ($managementSettings['folder_id'] ?? 0);
        $managementSelectedIds = array_values(array_unique(array_filter(array_map('intval', $managementSettings['ids'] ?? []))));
        $managementLimit = max(1, min(100, (int) data_get($managementSettings, 'limit', count($managementSelectedIds) ?: 4)));
        $newsLimit=max(1,min(100,(int)($sectionSettings['news']['limit']??3)));"""
    if marker not in s: raise SystemExit('home limit marker missing')
    s = s.replace(marker, replacement, 1)
    s = s.replace("        $managementSettings = $sectionSettings['management'] ?? [];\n        $gallerySettings", "        $gallerySettings")
    marker = """        if (($managementSettings['mode'] ?? 'latest') === 'selected') {
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
    replacement = """        if ($managementFolderId > 0 && $managementSelectedIds) {
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
    if marker not in s: raise SystemExit('home query marker missing')
    s = s.replace(marker, replacement, 1)
p.write_text(s)
print('backend patched')
