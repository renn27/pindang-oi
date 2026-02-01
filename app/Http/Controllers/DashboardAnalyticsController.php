<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardAnalyticsService;
use Illuminate\Support\Facades\Auth;

class DashboardAnalyticsController extends Controller
{
    public function __construct(
        private DashboardAnalyticsService $analytics
    ) {}

    public function index()
    {
        return view('pages.main.dashboard.pimpinan.vis-rank-pegawai', [
            'rankPegawai' => $this->analytics->rankPegawai()
        ]);
    }

    public function index2()
    {
        return view('pages.main.dashboard.pimpinan.vis-total-penugasan', [
            'totalpenugasanPegawai' => $this->analytics->summaryPenugasanAnggota(Auth::user()->id_pegawai)
        ]);
    }
}
