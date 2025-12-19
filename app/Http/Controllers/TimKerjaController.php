<?php

namespace App\Http\Controllers;

use App\Models\TimKerja;
use Yajra\DataTables\DataTables;

class TimKerjaController extends Controller
{
    public function index()
    {
        return view('tim_kerja.index');
    }

    public function data()
    {
        return DataTables::of(TimKerja::query())
            ->addColumn('aksi', function ($row) {
                return '
                    <button class="btn btn-sm btn-warning edit" data-id="'.$row->id_tim_kerja.'">edit</button>
                    <button class="btn btn-sm btn-danger hapus" data-id="'.$row->id_tim_kerja.'">hapus</button>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
