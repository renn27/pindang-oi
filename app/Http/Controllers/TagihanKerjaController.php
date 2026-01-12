<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use Illuminate\Http\Request;

class TagihanKerjaController extends Controller
{
    // Menampilkan halaman bidang berdasarkan slug
    public function show($slug)
    {
        // Cari bidang di database
        $bidang = Bidang::where('slug', $slug)
            ->whereNull('deleted_at') // hanya yang aktif
            ->first();
        
        // Jika tidak ditemukan, error 404
        if (!$bidang) {
            abort(404, 'Bidang tidak ditemukan');
        }
        
        // Tampilkan view dengan data
        return view('pages.bidang.detail', [
            'title' => $bidang->nama_bidang,
            'bidang' => $bidang,
            'pageTitle' => $bidang->nama_bidang,
            'detailBidang' => $bidang->detail_bidang
        ]);
    }
}