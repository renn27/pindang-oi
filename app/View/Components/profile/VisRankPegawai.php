<?php

namespace App\View\Components\Profile;

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
        $this->perPage = $perPage;
        // ambil data ranking dari service dengan pagination
        $this->rankPegawai = $analytics->rankPegawai($perPage);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.profile.vis-rank-pegawai');
    }
}