<?php

namespace App\Http\Controllers;

use App\Models\PanduanFitur;
use App\Models\Bidang;
use Illuminate\Http\Request;

class PanduanPenggunaController extends Controller
{
    /**
     * Display the dynamic user guide documentation.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $activeRole = $user->active_role ?? 'Anggota Tim';

        // Define which tabs are visible based on active role
        if ($activeRole === 'Admin' || $activeRole === 'Pimpinan') {
            $allowedTabs = ['Pimpinan', 'Admin', 'Ketua Tim', 'Anggota Tim'];
        } else {
            $allowedTabs = ['Ketua Tim', 'Anggota Tim'];
        }

        // Fetch user guides for allowed tabs, ordered by sort_order
        $panduans = PanduanFitur::query()
            ->where('type', 'user')
            ->whereIn('role_tab', $allowedTabs)
            ->orderBy('sort_order')
            ->get();

        // Get first bidang to resolve route requirements for kegiatan.index
        $firstBidang = Bidang::query()->orderBy('urutan')->first();

        // Generate dynamic URL mapping for target routes
        $routeMap = [];
        foreach ($panduans as $p) {
            if ($p->route_target) {
                try {
                    if ($p->route_target === 'kegiatan.index') {
                        if ($firstBidang) {
                            $routeMap[$p->slug] = route('kegiatan.index', ['bidang' => $firstBidang->slug]);
                        } else {
                            $routeMap[$p->slug] = '#';
                        }
                    } else {
                        $routeMap[$p->slug] = route($p->route_target);
                    }
                } catch (\Exception $e) {
                    $routeMap[$p->slug] = '#';
                }
            }
        }

        // Group the guides by role_tab for easier rendering
        $groupedPanduans = $panduans->groupBy('role_tab');

        // Order the tabs list deterministically
        $availableTabs = array_values(array_intersect(
            ['Pimpinan', 'Admin', 'Ketua Tim', 'Anggota Tim'],
            array_keys($groupedPanduans->toArray())
        ));

        return view('pages.main.panduan-pengguna.index', [
            'title' => 'Panduan Pengguna',
            'groupedPanduans' => $groupedPanduans,
            'availableTabs' => $availableTabs,
            'activeRole' => $activeRole,
            'routeMap' => $routeMap,
        ]);
    }
}
