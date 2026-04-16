<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Role;
use Illuminate\Http\Request;

class PegawaiRoleController extends Controller
{
    public function index() {
        $this->authorize('kelola-master-data');
        return view('pages.main.admin.role-pegawai.index', [
            'title'    => 'Manajemen Role Pegawai',
            'pegawais' => Pegawai::with('roles')->get(),
            'roles'    => Role::all(),
        ]);
    }

    public function store(Request $request) {
        $this->authorize('kelola-master-data');
        $validated = $request->validate([
            'id_pegawai' => 'required|exists:pegawais,id_pegawai',
            'roles'      => 'required|array|min:1',
            'roles.*'    => 'exists:roles,id',
        ]);

        $pegawai = Pegawai::with('roles')->findOrFail($validated['id_pegawai']);

        // role existing
        $existingRoleIds = $pegawai->roles->pluck('id')->toArray();

        // hanya role baru
        $rolesToAttach = array_diff($validated['roles'], $existingRoleIds);

        if (empty($rolesToAttach)) {
            return back()->with('info', 'Role tersebut sudah dimiliki pegawai');
        }

        $pegawai->roles()->attach($rolesToAttach);

        // set active_role jika belum ada
        if (! $pegawai->active_role) {
            $pegawai->update([
                'active_role' => $pegawai->roles()->first()?->nama_role
            ]);
        }

        return back()->with('success', 'Role pegawai berhasil ditambahkan');
    }

    public function update(Request $request, Pegawai $pegawais) {
        $this->authorize('kelola-master-data');
        $validated = $request->validate([
            'id_pegawai' => 'required|exists:pegawais,id_pegawai',
            'roles'      => 'nullable|array',
            'roles.*'    => 'exists:roles,id',
        ]);

        // 🛡️ safety: URL & body harus konsisten
        abort_if(
            $validated['id_pegawai'] != $pegawais->id_pegawai,
            422,
            'Pegawai tidak valid'
        );

        $pegawai = Pegawai::with('roles')->findOrFail($validated['id_pegawai']);

        // SIMPAN STATE SEBELUM UPDATE
        $previousActiveRole = $pegawai->active_role;
        $previousRoleNames  = $pegawai->roles->pluck('nama_role')->toArray();

        // PROSES ATTACH / DETACH
        $existingRoleIds  = $pegawai->roles->pluck('id')->toArray();
        $incomingRoleIds  = $validated['roles'] ?? [];

        $rolesToAttach = array_diff($incomingRoleIds, $existingRoleIds);
        $rolesToDetach = array_diff($existingRoleIds, $incomingRoleIds);

        if (empty($rolesToAttach) && empty($rolesToDetach)) {
            return back()->with('info', 'Tidak ada perubahan role');
        }

        if (! empty($rolesToAttach)) {
            $pegawai->roles()->attach($rolesToAttach);
        }

        if (! empty($rolesToDetach)) {
            $pegawai->roles()->detach($rolesToDetach);
        }

        // reload roles terbaru
        $pegawai->load('roles');

        // PENENTUAN ACTIVE ROLE (FIX BUG)
        $currentRoleNames = $pegawai->roles->pluck('nama_role')->toArray();

        // 1️⃣ Jika active_role lama masih ada → BIARKAN
        if (
            $previousActiveRole &&
            in_array($previousActiveRole, $currentRoleNames)
        ) {
            // do nothing
        }

        // 2️⃣ Jika masih ada role struktural lain → pakai itu
        elseif (! empty($currentRoleNames)) {
            $pegawai->update([
                'active_role' => $currentRoleNames[0],
            ]);
        }

        // 3️⃣ Tidak ada role struktural → fallback ke role kontekstual
        elseif ($pegawai->penugasanSebagaiAnggota()->exists()) {
            $pegawai->update([
                'active_role' => 'Anggota Tim',
            ]);
        }

        // 4️⃣ Benar-benar kosong
        else {
            $pegawai->update([
                'active_role' => null,
            ]);
        }

        return back()->with('success', 'Role pegawai berhasil diperbarui');
    }

    public function switchRolePegawai(Request $request, string $role) {
        $this->authorize('kelola-master-data');
        $user = $request->user();
        abort_if(! $user, 401);

        $contextualRoles = ['Anggota Tim'];

        if (! in_array($role, $contextualRoles)) {
            abort_if(! $user->hasRole($role), 403);
        }

        $user->update([
            'active_role' => $role
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Berhasil switching role');
    }
}
