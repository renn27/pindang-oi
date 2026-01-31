<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardAnalyticsService;

class DashboardAnalyticsController extends Controller
{
    public function __construct(
        private DashboardAnalyticsService $analytics
    ) {}

    public function index()
    {
        return view('pages.main.dashboard.pimpinan.rank-pegawai', [
            'rankPegawai' => $this->analytics->rankPegawai()
        ]);
    }
}
