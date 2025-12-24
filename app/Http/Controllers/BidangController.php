<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bidang;
use Illuminate\Support\Str;

class BidangController extends Controller
{

    public function index()
    {
        $bidangs = Bidang::all();
        return view('pages.bidang-kerja.index', ['title' => 'Seluruh Bidang Kerja', 'bidangs' => $bidangs]);
    }

    public function show($slug)
    {
        $bidang = Bidang::with('kegiatans.pegawai')->where('slug', $slug)->firstOrFail();
        return view('pages.bidang-kerja.show', ['title' => $bidang->nama_bidang, 'bidang' => $bidang, 'kegiatans' => $bidang->kegiatans]);
    }

    public function create() {
        return view('pages.bidang-kerja.create', ['title' => 'Tambah Bidang Kerja']);
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'nama_bidang' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:bidangs,slug',
            'detail_bidang' => 'required|string|max:255',
        ]);

            // Kalau slug kosong (misal user hapus)
        if (empty($validatedData['slug'])) {
            $validatedData['slug'] = Str::slug($validatedData['nama_bidang']);
        }

        $bidang= Bidang::create($validatedData);

        return redirect()
        ->route('bidang.index', $bidang->slug)
        ->with('success', 'Bidang berhasil ditambahkan');
    }
}
