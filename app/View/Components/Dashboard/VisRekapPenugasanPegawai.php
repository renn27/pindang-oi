<?php

namespace App\View\Components\Dashboard;

use App\Services\DashboardAnalyticsService;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class VisRekapPenugasanPegawai extends Component
{
    public $rekapAnggota;
    public $bulan;
    public $tahun;

    public function __construct(DashboardAnalyticsService $analytics, $rekapAnggota = null)
    {
        // Tangkap parameter bulan & tahun langsung dari request URL (Sama seperti VisRankPegawai)
        $this->bulan = request('month', now()->month);
        $this->tahun = request('year', now()->year);

        $data = $rekapAnggota ?? $analytics->getRekapPenugasanPegawai($this->bulan, $this->tahun);

        // Filter out Sukendro for the overall table
        $this->rekapAnggota = $data->reject(function ($item) {
            return $item->nip_bps === '340017814';
        })->values();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.dashboard.vis-rekap-penugasan-pegawai');
    }
}
