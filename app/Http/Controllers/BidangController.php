<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bidang;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BidangController extends Controller
{

    public function index()
    {
        $bidangs = Bidang::all();
        return view('pages.main.admin.bidang-kerja.index', ['title' => 'Seluruh Bidang Kerja', 'bidangs' => $bidangs]);
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

    public function update(Request $request, Bidang $bidang)
    {
        $validatedData = $request->validate([
            'nama_bidang' => 'required|string|max:255',
            'slug' => ['nullable','string','max:255',
                Rule::unique('bidangs', 'slug')
                    ->ignore($bidang->id_bidang, 'id_bidang'),
            ],
            'detail_bidang' => 'required|string|max:255',
        ]);

        try {
            $bidang->update($validatedData);

            // Redirect dengan flash message
            return redirect()->route('bidang.index')
                ->with('success', 'Bidang berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Bidang. Silakan coba lagi.')
                ->withInput();
        }
    }

    public function delete(Bidang $bidang)
    {
        try {
            $bidang->delete();

            return redirect()
                ->route('bidang.index')
                ->with('success', 'Bidang berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus Bidang');
        }
    }
}
