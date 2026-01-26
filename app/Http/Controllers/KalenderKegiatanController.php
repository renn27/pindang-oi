<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KalenderKegiatanController extends Controller
{
    public function index(Request $request)
    {
        return view("pages.main.pegawai.kalender-kegiatan.index", [
            'title' => 'Kalender Kegiatan',
        ]);
    }
}
