<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimKerja;
use Yajra\DataTables\Facades\DataTables;

class TimKerjaController extends Controller
{
    public function index()
    {
        return view('tim_kerja.index');
    }

    public function data()
    {
        return DataTables::of(TimKerja::query())

            ->filter(function ($query) {
                if (request()->has('search') && request('search')['value'] != '') {
                    $search = request('search')['value'];

                    $query->where(function ($q) use ($search) {
                        $q->where('nama_tim', 'like', "%{$search}%")
                            ->orWhere('id_ketua', 'like', "%{$search}%")
                            ->orWhere('created_at', 'like', "%{$search}%");
                    });
                }
            })

            // ===== NAMA TIM =====
            ->addColumn('nama_tim', function ($row) {
                 return $row->nama_tim;
            })

            // ===== ID KETUA =====
            ->editColumn('id_ketua', function ($row) {
                return $row->id_ketua;
            })

            // ===== CREATED AT =====
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M Y');
            })

            // ===== STATUS =====
            ->addColumn('status', function () {
                return '
                <span class="px-2 inline-flex text-xs font-semibold rounded-full
                    bg-green-50 text-green-600">
                    active
                </span>
            ';
            })

            // ===== AKSI ===== (KOSONG - biar frontend render)
            ->addColumn('aksi', function ($row) {
                return null; // Kosongkan, frontend yang akan render
            })

            ->rawColumns(['nama_tim', 'status', 'aksi'])
            ->make(true);
    }

    // ===============================
    // STORE
    // ===============================
    public function store(Request $request)
    {
        $request->validate([
            'nama_tim' => 'required',
            'id_ketua' => 'required'
        ]);

        TimKerja::create([
            'nama_tim' => $request->nama_tim,
            'id_ketua' => $request->id_ketua, // ✅ FIX
        ]);

        return response()->json(['message' => 'berhasil tambah data']);
    }

    // ===============================
    // UPDATE
    // ===============================
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tim' => 'required',
            'id_ketua' => 'required'
        ]);

        TimKerja::findOrFail($id)->update([
            'nama_tim' => $request->nama_tim,
            'id_ketua' => $request->id_ketua, // ✅ FIX
        ]);

        return response()->json(['message' => 'berhasil update data']);
    }

    // ===============================
    // DELETE
    // ===============================
    public function destroy($id)
    {
        TimKerja::findOrFail($id)->delete();

        return response()->json(['message' => 'berhasil hapus data']);
    }
}
