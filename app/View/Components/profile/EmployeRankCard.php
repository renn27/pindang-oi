<?php

namespace App\View\Components\Profile;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\DashboardAnalyticsService;

class EmployeRankCard extends Component
{

    public object|null $bestEmployee;

    public function __construct(DashboardAnalyticsService $analytics, $bestEmployee = null)
    {
        $month = request('month', now()->month);
        $year = request('year', now()->year);

        if ($bestEmployee) {
            $this->bestEmployee = $bestEmployee;
        } else {
            // Get all rankings, filter out Sukendro, then take the first one
            $this->bestEmployee = $analytics
                ->rankPegawaiAll($month, $year)
                ->reject(function ($item) {
                    return $item->nip_bps === '340017814';
                })
                ->first();
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.profile.employe-rank-card');
    }
}
