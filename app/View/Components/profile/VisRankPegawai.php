<?php

namespace App\View\Components\Profile;

use App\Services\DashboardAnalyticsService;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class VisRankPegawai extends Component
{
    public array|\Illuminate\Support\Collection $rankPegawai;

    /**
     * Create a new component instance.
     */
    public function __construct(DashboardAnalyticsService $analytics)
    {
        // ambil data ranking dari service
        $this->rankPegawai = $analytics->rankPegawai();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.profile.vis-rank-pegawai');
    }
}
