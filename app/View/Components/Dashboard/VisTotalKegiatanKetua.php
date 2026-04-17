<?php

namespace App\View\Components\Dashboard;

use App\Services\DashboardAnalyticsService;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class VisTotalKegiatanKetua extends Component
{
    public array $totalkegiatanKetua;

    /**
     * Create a new component instance.
     */
    public function __construct(DashboardAnalyticsService $analytics)
    {
        $idPegawai = Auth::user()?->id_pegawai;

        // antisipasi kalau belum login
        $this->totalkegiatanKetua = $idPegawai
            ? $analytics->summaryKegiatanKetua($idPegawai)
            : [
                'total' => 0,
                // 'belum_mulai' => 0,
                // 'sedang_berjalan' => 0,
                // 'sudah_selesai' => 0,
            ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.dashboard.vis-total-kegiatan-ketua');
    }
}
