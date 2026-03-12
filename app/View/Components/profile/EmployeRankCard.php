<?php

namespace App\View\Components\Profile;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\DashboardAnalyticsService;

class EmployeRankCard extends Component
{

    public object|null $bestEmployee;

    /**
     * Create a new component instance.
     */
    public function __construct(DashboardAnalyticsService $analytics)
    {
        $month = request('month', now()->month);
        $year = request('year', now()->year);

        // ambil ranking, cukup 1 teratas
        $this->bestEmployee = $analytics
            ->rankPegawai(1, $month, $year)
            ->first();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.profile.employe-rank-card');
    }
}
