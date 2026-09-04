<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationMenuItem;
use App\Services\NavigationSourceRegistry;
use App\Services\PublicNavigationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NavigationMenuController extends Controller
{
    public function index(Request $request, NavigationSourceRegistry $registry): View
    {
        $menu = $request->string('menu')->toString() ?: 'main';
        $area = $menu === 'dashboard' ? 'dashboard' : 'public';

        $all = NavigationMenuItem::query()
            ->where('menu', $menu)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // The builder must reflect only destinations that are currently real and permitted.
        // Folders are structural and remain available even when their children change.
        $all = $all->filter(function (NavigationMenuItem $item) use ($registry, $area): bool {
            if ($item->source_type === 'folder') {
                return true;
            }

            $sourceKey = $item->source_key ?: ($item->route_name ? 'route:'.$item->route_name : null);
            if (! $sourceKey) {
                return false;
            }

            $source = $registry->resolveAny($sourceKey, $area);

            if (! $source) {
                return false;
            }

            // Live sources keep their destination in sync, but their navigation label
            // is user-owned after the item has been added. Do not silently overwrite a
            // custom menu label every time the builder page is opened.
            if (trim((string) $item->label) === '') {
                $item->label = $source['label'];
            }
            $item->url = $source['url'];
            $item->route_name = $source['route_name'];
            $item->permission_key = $source['permission'] ?? null;
            $item->source_key = $source['key'];
            $item->source_type = $source['type'];

            return true;
        })->values();

        $byParent = $all->groupBy(fn (NavigationMenuItem $item) => $item->parent_id ?? 0);
        $attachChildren = function (NavigationMenuItem $item) use (&$attachChildren, $byParent): NavigationMenuItem {
            $children = $byParent->get($item->id, collect())
                ->sortBy(fn (NavigationMenuItem $child) => [$child->sort_order, $child->id])
                ->values();

            $children->each(fn (NavigationMenuItem $child) => $attachChildren($child));
            $item->setRelation('children', $children);

            return $item;
        };

        $items = $byParent->get(0, collect())
            ->sortBy(fn (NavigationMenuItem $item) => [$item->sort_order, $item->id])
            ->values()
            ->map($attachChildren);

        $all->each(function (NavigationMenuItem $item) use ($all): void {
            $depth = 0;
            $cursor = $item->parent_id;
            $seen = [];

            while ($cursor !== null && ! isset($seen[$cursor])) {
                $seen[$cursor] = true;
                $parent = $all->firstWhere('id', $cursor);
                if (! $parent) {
                    break;
                }
                $depth++;
                $cursor = $parent->parent_id;
            }

            $item->depth = $depth;
        });

        $sources = $registry->available($area, $menu);

        return view('admin.navigation.index', compact('items', 'all', 'sources', 'menu', 'area'));
    }

    public function show(NavigationMenuItem $item): RedirectResponse
    {
        abort_unless(in_array($item->menu, ['main', 'dashboard'], true), 404);

        return redirect(route('admin.menu-builder.index', [
            'menu' => $item->menu,
        ]).'#edit-'.$item->id);
    }

    public function store(Request $request, NavigationSourceRegistry $registry): RedirectResponse
    {
        $data = $request->validate([
            'menu' => ['required', 'in:main,dashboard'],
            'source_key' => ['nullable', 'string', 'max:255'],
            'label' => ['nullable', 'string', 'max:160'],
            'folder_label' => ['nullable', 'string', 'max:160'],
            'parent_id' => ['nullable', 'integer'],
            'target' => ['required', 'in:_self,_blank'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_visible' => ['nullable', 'boolean'],
            'kind' => ['required', 'in:folder,source'],
        ]) + ['is_visible' => $request->boolean('is_visible')];

        $area = $data['menu'] === 'dashboard' ? 'dashboard' : 'public';
        $parentId = $this->validatedParentId($data['parent_id'] ?? null, $data['menu']);

        if ($data['kind'] === 'folder') {
            $data['source_key'] = null;
            $data['source_type'] = 'folder';
            $data['permission_key'] = null;
            $data['url'] = null;
            $data['route_name'] = null;
            $data['label'] = trim((string) ($data['folder_label'] ?? ''));
            abort_if($data['label'] === '', 422, 'Folder name is required.');
        } else {
            abort_if(empty($data['source_key']), 422, 'Choose a live navigation source.');
            $source = $registry->resolve($data['source_key'], $area, $data['menu']);
            abort_unless($source !== null, 422, 'This navigation source is no longer available.');
            abort_if(NavigationMenuItem::query()->where('menu', $data['menu'])->where('source_key', $data['source_key'])->exists(), 422, 'This navigation source is already in the menu.');

            $data['source_type'] = $source['type'];
            $data['permission_key'] = $source['permission'] ?? null;
            $data['url'] = $source['url'];
            $data['route_name'] = $source['route_name'];
            $data['label'] = $source['label'];
        }

        unset($data['folder_label']);
        $data['menu'] = $data['menu'];
        $data['area'] = $area;
        $data['parent_id'] = $parentId;
        $data['sort_order'] = (int) (NavigationMenuItem::query()
            ->where('menu', $data['menu'])
            ->where('parent_id', $parentId)
            ->max('sort_order') ?? -1) + 1;

        unset($data['kind']);

        NavigationMenuItem::create($data);
        app(PublicNavigationService::class)->clear($data['menu']);

        return back()->with('status', 'Navigation item added.');
    }

    public function update(Request $request, NavigationMenuItem $item, NavigationSourceRegistry $registry): RedirectResponse
    {
        abort_unless(in_array($item->menu, ['main', 'dashboard'], true), 404);

        $data = $request->validate([
            'label' => ['required', 'string', 'max:160'],
            'parent_id' => ['nullable', 'integer'],
            'target' => ['required', 'in:_self,_blank'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_visible' => ['nullable', 'boolean'],
        ]) + ['is_visible' => $request->boolean('is_visible')];

        $data['label'] = trim($data['label']);
        abort_if($data['label'] === '', 422, 'Navigation label is required.');
        $data['parent_id'] = $this->validatedParentId($data['parent_id'] ?? null, $item->menu, $item->id);

        if ($item->source_type === 'folder') {
            abort_if($data['label'] === '', 422, 'Folder name is required.');
        } elseif ($item->source_key) {
            $source = $registry->resolveAny($item->source_key, $item->area);
            abort_unless($source !== null, 422, 'This navigation source no longer exists.');
            // Destination remains live and synchronized; the label is intentionally
            // taken from the admin's submitted value so live sources can be renamed.
            $data['url'] = $source['url'];
            $data['route_name'] = $source['route_name'];
            $data['permission_key'] = $source['permission'] ?? null;
            $data['source_type'] = $source['type'];
        }

        $item->update($data);
        app(PublicNavigationService::class)->clear($item->menu);

        return back()->with('status', 'Navigation item updated.');
    }

    public function destroy(NavigationMenuItem $item): RedirectResponse
    {
        abort_unless(in_array($item->menu, ['main', 'dashboard'], true), 404);
        $menu = $item->menu;

        DB::transaction(function () use ($item): void {
            // Detach the item from its parent first, then promote its children.
            // This makes folder deletion safe on MySQL installations that enforce
            // the parent_id foreign key without ON DELETE CASCADE.
            $children = NavigationMenuItem::query()
                ->where('menu', $item->menu)
                ->where('parent_id', $item->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            $nextOrder = (int) (NavigationMenuItem::query()
                ->where('menu', $item->menu)
                ->where('parent_id', $item->parent_id)
                ->where('id', '!=', $item->id)
                ->max('sort_order') ?? -1) + 1;

            foreach ($children as $child) {
                $child->update([
                    'parent_id' => $item->parent_id,
                    'sort_order' => $nextOrder++,
                ]);
            }

            $item->delete();
        });

        app(PublicNavigationService::class)->clear($menu);

        return redirect()
            ->route('admin.menu-builder.index', ['menu' => $menu])
            ->with('status', 'Navigation item deleted successfully.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'menu' => ['required', 'in:main,dashboard'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        $parentId = $data['parent_id'] ?? null;
        $items = NavigationMenuItem::query()
            ->where('menu', $data['menu'])
            ->whereIn('id', $data['ids'])
            ->get()
            ->keyBy('id');

        abort_unless($items->count() === count($data['ids']), 422, 'Invalid menu items.');
        abort_unless(
            $parentId === null || ! NavigationMenuItem::query()
                ->where('menu', $data['menu'])
                ->whereKey($parentId)
                ->exists(),
            422,
            'Invalid reorder parent.'
        );

        if ($parentId !== null) {
            $parent = NavigationMenuItem::query()
                ->where('menu', $data['menu'])
                ->findOrFail($parentId);

            abort_if($parent->source_type !== 'folder', 422, 'Only folders can contain navigation items.');

            foreach ($items as $item) {
                abort_if(
                    $this->isDescendantOf($parent->id, $item->id, $data['menu']),
                    422,
                    'A menu item cannot be placed inside its own descendant.'
                );
            }
        }

        DB::transaction(function () use ($data, $items, $parentId): void {
            foreach ($data['ids'] as $position => $id) {
                $items[$id]->update([
                    'parent_id' => $parentId,
                    'sort_order' => $position,
                ]);
            }
        });

        app(PublicNavigationService::class)->clear($data['menu']);

        return response()->json(['ok' => true]);
    }

    private function validatedParentId(?int $parentId, string $menu, ?int $ignoreId = null): ?int
    {
        if ($parentId === null) {
            return null;
        }

        $query = NavigationMenuItem::query()
            ->where('menu', $menu)
            ->whereKey($parentId);

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        abort_unless($query->exists(), 422, 'Invalid parent menu item.');

        $parent = $query->firstOrFail();
        abort_unless($parent->source_type === 'folder', 422, 'Only folders can contain navigation items.');

        if ($ignoreId !== null && $this->isDescendantOf($parentId, $ignoreId, $menu)) {
            abort(422, 'A menu item cannot be placed inside its own descendant.');
        }

        $parentDepth = 0;
        $cursor = $parentId;
        $seen = [];

        while ($cursor !== null && ! isset($seen[$cursor])) {
            $seen[$cursor] = true;
            $cursor = NavigationMenuItem::query()
                ->where('menu', $menu)
                ->whereKey($cursor)
                ->value('parent_id');
            $parentDepth++;
            if ($parentDepth > 5) {
                abort(422, 'Navigation can have up to five nested levels.');
            }
        }

        if ($ignoreId !== null) {
            $subtreeDepth = $this->maxDescendantDepth($ignoreId, $menu);
            abort_if($parentDepth + $subtreeDepth > 5, 422, 'This move would exceed the five-level navigation limit.');
        } else {
            abort_if($parentDepth + 1 > 5, 422, 'Navigation can have up to five nested levels.');
        }

        return $parentId;
    }

    private function maxDescendantDepth(int $rootId, string $menu): int
    {
        $children = NavigationMenuItem::query()
            ->where('menu', $menu)
            ->where('parent_id', $rootId)
            ->pluck('id');

        if ($children->isEmpty()) {
            return 1;
        }

        return 1 + $children->map(
            fn ($id): int => $this->maxDescendantDepth((int) $id, $menu)
        )->max();
    }

    private function isDescendantOf(int $candidateId, int $ancestorId, string $menu): bool
    {
        $seen = [];

        while ($candidateId) {
            if (isset($seen[$candidateId])) {
                return false;
            }

            $seen[$candidateId] = true;
            $parentId = NavigationMenuItem::query()
                ->where('menu', $menu)
                ->whereKey($candidateId)
                ->value('parent_id');

            if ($parentId === null) {
                return false;
            }

            if ((int) $parentId === $ancestorId) {
                return true;
            }

            $candidateId = (int) $parentId;
        }

        return false;
    }
}
