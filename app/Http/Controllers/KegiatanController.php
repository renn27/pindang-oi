<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bidang;
use App\Models\Kegiatan;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatans = Kegiatan::all();
        return view('pages.rencana-kerja.index', ['title' => 'Seluruh Rencana Kerja', 'kegiatans' => $kegiatans]);
    }

    public function subKegiatan()
    {
        $kegiatans = Kegiatan::all();
        return view('pages.rencana-kerja.detail-kegiatan', ['title' => 'Sub Kegiatan', 'kegiatans' => $kegiatans]);
    }

    public function show($slug)
    {
        $bidang = Bidang::where('slug', $slug)->firstOrFail();
        return view('pages.rencana-kerja.show', ['title' => $bidang->nama_bidang, 'bidang' => $bidang]);
    }

    public function create() {
        return view('pages.rencana-kerja.create', ['title' => 'Tambah Rencana Kerja']);
    }
}
