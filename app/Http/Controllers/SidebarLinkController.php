<?php

namespace App\Http\Controllers;

use App\Models\SidebarLink;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SidebarLinkController extends Controller
{
    public function index()
    {
        $this->authorize('kelola-master-data');

        // Mengambil semua link utama (root) beserta relasi sub-linknya (children)
        $links = SidebarLink::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order', 'asc')
            ->get();

        // Mengambil link yang berpotensi menjadi parent (parent_id null dan url null) untuk dropdown form
        $parentLinks = SidebarLink::whereNull('parent_id')
            ->whereNull('url')
            ->orderBy('name', 'asc')
            ->get();

        return view('pages.main.admin.sidebar-links.index', [
            'title' => 'Kelola Link Sidebar',
            'links' => $links,
            'parentLinks' => $parentLinks,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('kelola-master-data');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:direct,group,sub'],
            'parent_id' => [
                'nullable',
                Rule::requiredIf($request->type === 'sub'),
                'exists:sidebar_links,id',
            ],
            'url' => [
                'nullable',
                Rule::requiredIf($request->type !== 'group'),
                'string',
                'max:255',
            ],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:100'],
            'background_color' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_special' => ['nullable', 'boolean'],

            // Validation for inline children (specifically when creating a group)
            'children' => ['nullable', 'array'],
            'children.*.name' => ['required_with:children', 'string', 'max:255'],
            'children.*.url' => ['required_with:children', 'string', 'max:255'],
            'children.*.icon' => ['nullable', 'string', 'max:255'],
            'children.*.sort_order' => ['required_with:children', 'integer', 'min:0'],
            'children.*.color' => ['nullable', 'string', 'max:100'],
            'children.*.background_color' => ['nullable', 'string', 'max:100'],
        ]);

        $isSpecial = $request->has('is_special') ? (bool) $request->is_special : false;

        DB::transaction(function () use ($validated, $isSpecial) {
            // Tentukan parent_id untuk pergeseran urutan
            $parentId = null;
            if ($validated['type'] === 'sub') {
                $parentId = $validated['parent_id'];
            }

            // Geser urutan link lain yang nilainya >= sort_order baru (+1)
            SidebarLink::where('parent_id', $parentId)
                ->where('sort_order', '>=', $validated['sort_order'])
                ->increment('sort_order');

            // Tentukan parameter parent berdasarkan tipe link
            $parentData = [
                'name' => $validated['name'],
                'icon' => $validated['icon'] ?? null,
                'color' => $validated['color'] ?? null,
                'background_color' => $validated['background_color'] ?? null,
                'sort_order' => $validated['sort_order'],
                'is_special' => $isSpecial,
                'is_external' => true,
            ];

            if ($validated['type'] === 'group') {
                $parentData['url'] = null;
                $parentData['parent_id'] = null;
            } elseif ($validated['type'] === 'direct') {
                $parentData['url'] = $validated['url'];
                $parentData['parent_id'] = null;
            } elseif ($validated['type'] === 'sub') {
                $parentData['url'] = $validated['url'];
                $parentData['parent_id'] = $validated['parent_id'];
            }

            $parentLink = SidebarLink::create($parentData);

            // Jika bertipe group dan ada child inline, simpan
            if ($validated['type'] === 'group' && !empty($validated['children'])) {
                foreach ($validated['children'] as $childInput) {
                    SidebarLink::create([
                        'parent_id' => $parentLink->id,
                        'name' => $childInput['name'],
                        'url' => $childInput['url'],
                        'icon' => $childInput['icon'] ?? null,
                        'color' => $childInput['color'] ?? null,
                        'background_color' => $childInput['background_color'] ?? null,
                        'sort_order' => $childInput['sort_order'],
                        'is_external' => true,
                        'is_special' => false,
                    ]);
                }
            }
        });

        return redirect()
            ->route('sidebar-links.index')
            ->with('success', 'Link sidebar berhasil ditambahkan');
    }

    public function update(Request $request, SidebarLink $sidebarLink)
    {
        $this->authorize('kelola-master-data');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:direct,group,sub'],
            'parent_id' => [
                'nullable',
                Rule::requiredIf($request->type === 'sub'),
                'exists:sidebar_links,id',
            ],
            'url' => [
                'nullable',
                Rule::requiredIf($request->type !== 'group'),
                'string',
                'max:255',
            ],
            'icon' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:100'],
            'background_color' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_special' => ['nullable', 'boolean'],
        ]);

        $isSpecial = $request->has('is_special') ? (bool) $request->is_special : false;

        $oldOrder = $sidebarLink->sort_order;
        $oldParentId = $sidebarLink->parent_id;

        $newOrder = $validated['sort_order'];
        $newParentId = null;
        if ($validated['type'] === 'sub') {
            $newParentId = $validated['parent_id'];
        }

        DB::transaction(function () use ($sidebarLink, $oldOrder, $oldParentId, $newOrder, $newParentId, $validated, $isSpecial) {
            $updateData = [
                'name' => $validated['name'],
                'icon' => $validated['icon'] ?? null,
                'color' => $validated['color'] ?? null,
                'background_color' => $validated['background_color'] ?? null,
                'sort_order' => $newOrder,
                'is_special' => $isSpecial,
                'is_external' => true,
            ];

            // Sesuaikan parameter berdasarkan tipe link
            if ($validated['type'] === 'group') {
                $updateData['url'] = null;
                $updateData['parent_id'] = null;
            } elseif ($validated['type'] === 'direct') {
                $updateData['url'] = $validated['url'];
                $updateData['parent_id'] = null;
            } elseif ($validated['type'] === 'sub') {
                $updateData['url'] = $validated['url'];
                $updateData['parent_id'] = $newParentId;
            }

            // Pergeseran urutan jika ada perubahan parent atau urutan
            if ($oldParentId !== $newParentId) {
                // Skenario C: Berubah parent/grup
                // 1. Rapatkan barisan pada parent lama
                SidebarLink::where('parent_id', $oldParentId)
                    ->where('sort_order', '>', $oldOrder)
                    ->decrement('sort_order');

                // 2. Buat celah pada parent baru
                SidebarLink::where('parent_id', $newParentId)
                    ->where('sort_order', '>=', $newOrder)
                    ->increment('sort_order');
            } else {
                // Sibling reordering (parent tetap sama)
                if ($newOrder < $oldOrder) {
                    // Skenario A: Pindah urutan ke atas
                    SidebarLink::where('parent_id', $oldParentId)
                        ->whereBetween('sort_order', [$newOrder, $oldOrder - 1])
                        ->increment('sort_order');
                } elseif ($newOrder > $oldOrder) {
                    // Skenario B: Pindah urutan ke bawah
                    SidebarLink::where('parent_id', $oldParentId)
                        ->whereBetween('sort_order', [$oldOrder + 1, $newOrder])
                        ->decrement('sort_order');
                }
            }

            $sidebarLink->update($updateData);
        });

        return redirect()
            ->route('sidebar-links.index')
            ->with('success', 'Link sidebar berhasil diperbarui');
    }

    public function delete(SidebarLink $sidebarLink)
    {
        $this->authorize('kelola-master-data');

        try {
            $parentId = $sidebarLink->parent_id;
            $deletedOrder = $sidebarLink->sort_order;

            DB::transaction(function () use ($sidebarLink, $parentId, $deletedOrder) {
                $sidebarLink->delete();

                // Rapatkan barisan link tersisa di parent/grup yang sama
                SidebarLink::where('parent_id', $parentId)
                    ->where('sort_order', '>', $deletedOrder)
                    ->decrement('sort_order');
            });

            return redirect()
                ->route('sidebar-links.index')
                ->with('success', 'Link sidebar berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus link sidebar');
        }
    }
}
