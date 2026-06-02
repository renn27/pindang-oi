<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PegawaiRoleController extends Controller
{
    public function index() {
        $this->authorize('kelola-master-data');
        return view('pages.main.admin.role-pegawai.index', [
            'title'    => 'Manajemen Role Pegawai',
            'pegawais' => Pegawai::with('roles')->orderBy('nama_pegawai')->get(),
            'roles'    => Role::orderBy('nama_role')->get(),
        ]);
    }

    public function storePegawai(Request $request)
    {
        $this->authorize('kelola-master-data');

        $validated = $request->validate([
            'nama_pegawai' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:pegawais,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:pegawais,email'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $roles = Role::whereIn('id', $validated['roles'])->get();

        DB::transaction(function () use ($validated, $roles) {
            $pegawai = Pegawai::create([
                'nama_pegawai' => $validated['nama_pegawai'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? null,
                'jabatan' => $validated['jabatan'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'password' => Hash::make($validated['password']),
                'active_role' => $roles->first()?->nama_role,
                'is_active' => true,
                'inactive_from_month' => null,
            ]);

            $pegawai->roles()->sync($roles->pluck('id'));
        });

        return back()->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function toggleActive(Request $request, Pegawai $pegawai)
    {
        $this->authorize('kelola-master-data');

        if ($pegawai->id_pegawai === $request->user()->id_pegawai && $pegawai->is_active) {
            return back()->with('error', 'Akun yang sedang digunakan tidak dapat dinonaktifkan.');
        }

        if ($pegawai->is_active) {
            // Cek jika pegawai masih memiliki kegiatan aktif yang belum ditransfer
            $kegiatanCount = \App\Models\Kegiatan::where('id_penanggung_jawab', $pegawai->id_pegawai)
                ->whereDoesntHave('transfer')
                ->count();

            if ($kegiatanCount > 0) {
                return back()->with('error', "Pegawai ini masih memimpin {$kegiatanCount} kegiatan aktif. Silakan lakukan transfer kepemilikan kegiatan terlebih dahulu.");
            }
        }

        $validated = $request->validate([
            'inactive_from_month' => [
                $pegawai->is_active ? 'required' : 'nullable',
                'date_format:Y-m',
            ],
        ], [
            'inactive_from_month.required' => 'Bulan mulai nonaktif wajib dipilih.',
            'inactive_from_month.date_format' => 'Format bulan mulai nonaktif tidak valid.',
        ]);

        $pegawai->update($pegawai->is_active
            ? [
                'is_active' => false,
                'inactive_from_month' => Carbon::createFromFormat('Y-m', $validated['inactive_from_month'])->startOfMonth(),
            ]
            : [
                'is_active' => true,
                'inactive_from_month' => null,
            ]
        );

        $status = $pegawai->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

        return back()->with('success', "Pegawai berhasil {$status}.");
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

        if ($previousActiveRole && in_array($previousActiveRole, $currentRoleNames)) {
            // 1️⃣ Jika active_role lama masih ada → BIARKAN, tapi tetap redirect
            return back()->with('success', 'Role pegawai berhasil diperbarui');
        } elseif (! empty($currentRoleNames)) {
            // 2️⃣ Jika masih ada role struktural lain → pakai itu
            $pegawai->update([
                'active_role' => $currentRoleNames[0],
            ]);
        } elseif ($pegawai->penugasanSebagaiAnggota()->exists()) {
            // 3️⃣ Tidak ada role struktural → fallback ke role kontekstual
            $pegawai->update([
                'active_role' => 'Anggota Tim',
            ]);
        } else {
            // 4️⃣ Benar-benar kosong
            $pegawai->update([
                'active_role' => null,
            ]);
        }

        return back()->with('success', 'Role pegawai berhasil diperbarui');
    }

    public function switchRolePegawai(Request $request, string $role) {
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
