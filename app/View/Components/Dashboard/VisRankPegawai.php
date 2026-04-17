<?php

namespace App\View\Components\Dashboard;

use App\Services\DashboardAnalyticsService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class VisRankPegawai extends Component
{
    public LengthAwarePaginator $rankPegawai;
    public int $perPage;

    /**
     * Create a new component instance.
     */
    public function __construct(DashboardAnalyticsService $analytics, int $perPage = 5)
    {
        $month = request('month', now()->month);
        $year = request('year', now()->year);

        $this->perPage = $perPage;
        // ambil data ranking dari service dengan pagination
        $this->rankPegawai = $analytics->rankPegawai($perPage, $month, $year);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.dashboard.vis-rank-pegawai');
    }
}