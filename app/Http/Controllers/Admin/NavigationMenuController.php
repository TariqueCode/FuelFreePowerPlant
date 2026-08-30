<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Models\NavigationMenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class NavigationMenuController extends Controller
{
    public function index(Request $request): View
    {
        $menu = $request->string('menu')->toString() ?: 'main';

        $items = NavigationMenuItem::query()
            ->where('menu', $menu)
            ->whereNull('parent_id')
            ->with(['children.children'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $all = NavigationMenuItem::query()
            ->where('menu', $menu)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // Calculate hierarchy depth for a clear parent selector without
        // changing the persisted navigation schema.
        $byId = $all->keyBy('id');
        $all->each(function (NavigationMenuItem $item) use ($byId): void {
            $depth = 0;
            $cursor = $item->parent_id;
            $seen = [];

            while ($cursor !== null && ! isset($seen[$cursor]) && isset($byId[$cursor])) {
                $seen[$cursor] = true;
                $depth++;
                $cursor = $byId[$cursor]->parent_id;
            }

            $item->depth = $depth;
        });

        $pages = CmsPage::query()
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'is_published']);

        return view('admin.navigation.index', compact('items', 'all', 'pages', 'menu'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateItem($request);
        $data['menu'] = $request->string('menu')->toString() ?: 'main';
        $data['parent_id'] = $this->validatedParentId($data['parent_id'] ?? null, $data['menu']);
        $data['sort_order'] = (int) (
            NavigationMenuItem::query()
                ->where('menu', $data['menu'])
                ->where('parent_id', $data['parent_id'])
                ->max('sort_order') ?? -1
        ) + 1;

        NavigationMenuItem::create($data);
        Cache::forget("public.navigation.{$data['menu']}");

        return back()->with('status', 'Menu item added.');
    }

    public function update(Request $request, NavigationMenuItem $item): RedirectResponse
    {
        $data = $this->validateItem($request);
        $data['menu'] = $item->menu;
        $data['parent_id'] = $this->validatedParentId($data['parent_id'] ?? null, $item->menu, $item->id);
        $data['sort_order'] = $item->sort_order;

        $item->update($data);
        Cache::forget("public.navigation.{$item->menu}");

        return back()->with('status', 'Menu item updated.');
    }

    public function destroy(NavigationMenuItem $item): RedirectResponse
    {
        DB::transaction(function () use ($item): void {
            NavigationMenuItem::where('parent_id', $item->id)
                ->update(['parent_id' => $item->parent_id]);

            $item->delete();
        });
        Cache::forget("public.navigation.{$item->menu}");

        return back()->with('status', 'Menu item deleted.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'menu' => ['required', 'string', 'max:60'],
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

        if ($parentId !== null) {
            $parent = NavigationMenuItem::query()
                ->where('menu', $data['menu'])
                ->findOrFail($parentId);

            abort_if($items->has($parent->id), 422, 'A menu item cannot be its own parent.');

            foreach ($items as $item) {
                abort_if(
                    $item->id !== $parent->id && $this->isDescendantOf($parent->id, $item->id),
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
        Cache::forget("public.navigation.{$data['menu']}");

        return response()->json(['ok' => true]);
    }

    private function validateItem(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:160'],
            'url' => ['nullable', 'string', 'max:500'],
            'route_name' => ['nullable', 'string', 'max:160'],
            'group' => ['nullable', 'string', 'max:100'],
            'parent_id' => ['nullable', 'integer'],
            'target' => ['required', 'in:_self,_blank'],
            'icon' => ['nullable', 'string', 'max:100'],
            'is_visible' => ['nullable', 'boolean'],
        ]) + [
            'is_visible' => $request->boolean('is_visible'),
        ];
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

        if ($ignoreId !== null && $this->isDescendantOf($parentId, $ignoreId)) {
            abort(422, 'A menu item cannot be placed inside its own descendant.');
        }

        return $parentId;
    }

    private function isDescendantOf(int $candidateId, int $ancestorId): bool
    {
        $seen = [];

        while ($candidateId) {
            if (isset($seen[$candidateId])) {
                return false;
            }

            $seen[$candidateId] = true;
            $parentId = NavigationMenuItem::query()->whereKey($candidateId)->value('parent_id');

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
