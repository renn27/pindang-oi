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

    /**
     * Create a new component instance.
     */
    public function __construct(DashboardAnalyticsService $analytics)
    {
        // Tangkap parameter bulan & tahun langsung dari request URL (Sama seperti VisRankPegawai)
        $this->bulan = request('month', now()->month);
        $this->tahun = request('year', now()->year);

        // Tarik data dengan fungsi yang tadi kita buat di Step 1
        $this->rekapAnggota = $analytics->getRekapPenugasanPegawai($this->bulan, $this->tahun);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.dashboard.vis-rekap-penugasan-pegawai');
    }
}
