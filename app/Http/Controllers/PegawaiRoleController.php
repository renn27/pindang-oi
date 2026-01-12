<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Role;
use Illuminate\Http\Request;

class PegawaiRoleController extends Controller
{
    public function index()
    {
        $pegawais = Pegawai::with('roles')->get();
        $roles    = Role::all();

        return view('pages.main.admin.role-pegawai.index', [
            'title'    => 'Manajemen Role Pegawai',
            'pegawais' => $pegawais,
            'roles'    => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id_pegawai',
            'roles'      => 'required|array',
            'roles.*'    => 'exists:roles,id',
        ]);

        $pegawai = Pegawai::findOrFail($request->pegawai_id);

        // role yang sudah dimiliki
        $existingRoleIds = $pegawai->roles->pluck('id')->toArray();

        // role baru yang benar-benar belum dimiliki
        $newRoleIds = array_diff($request->roles, $existingRoleIds);

        // ❗ kalau tidak ada role baru
        if (empty($newRoleIds)) {
            return back()->with('info', 'Role tersebut sudah dimiliki pegawai');
        }

        // attach hanya role baru
        $pegawai->roles()->attach($newRoleIds);

        // set active role jika belum ada
        if (! $pegawai->active_role) {
            $pegawai->update([
                'active_role' => $pegawai->roles()->first()?->nama_role
            ]);
        }

        return back()->with('success', 'Role baru berhasil ditambahkan');
    }

    public function switchRolePegawai(Request $request, string $role) {
        $user = $request->user();

        abort_if(! $user, 401);

        // daftar role kontekstual yang diperbolehkan
        $contextualRoles = [
            'Anggota Tim',
        ];

        // jika BUKAN role kontekstual, wajib role struktural
        if (! in_array($role, $contextualRoles)) {
            abort_if(! $user->hasRole($role), 403);
        }

        // simpan role aktif
        $user->update([
            'active_role' => $role
        ]);

        return redirect()->route('dashboard')
        ->with('success', 'Berhasil switching role');
    }

}
