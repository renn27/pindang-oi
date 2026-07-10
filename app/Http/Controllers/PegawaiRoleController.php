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

            // Inisialisasi periode aktif pertama pegawai baru
            $pegawai->statusPeriods()->create([
                'status' => 'Aktif',
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => null,
            ]);
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

        $prevStatusActive = $pegawai->is_active;

        DB::transaction(function () use ($pegawai, $validated, $prevStatusActive) {
            if ($prevStatusActive) {
                // MENONAKTIFKAN PEGAWAI
                $deactiveMonth = Carbon::createFromFormat('Y-m', $validated['inactive_from_month'])->startOfMonth();
                $activeEnd = $deactiveMonth->copy()->subDay()->toDateString();

                $ongoingActive = $pegawai->statusPeriods()
                    ->where('status', 'Aktif')
                    ->whereNull('end_date')
                    ->first();

                if ($ongoingActive) {
                    if ($ongoingActive->start_date->greaterThanOrEqualTo($deactiveMonth)) {
                        // Jika aktif dimulai pada/setelah bulan nonaktif, hapus periode aktif ini
                        $ongoingActive->delete();
                        // Cari periode nonaktif sebelumnya dan buka kembali
                        $prevInactive = $pegawai->statusPeriods()
                            ->where('status', 'Nonaktif')
                            ->orderBy('start_date', 'desc')
                            ->first();
                        if ($prevInactive) {
                            $prevInactive->update(['end_date' => null]);
                        }
                    } else {
                        // Tutup periode aktif dan buat periode nonaktif baru
                        $ongoingActive->update(['end_date' => $activeEnd]);
                        $pegawai->statusPeriods()->create([
                            'status' => 'Nonaktif',
                            'start_date' => $deactiveMonth->toDateString(),
                            'end_date' => null,
                        ]);
                    }
                }

                // Hitung is_active legacy untuk hari ini
                $isCurrentlyActive = now()->startOfMonth()->lt($deactiveMonth);
                $pegawai->update([
                    'is_active' => $isCurrentlyActive,
                    'inactive_from_month' => $deactiveMonth->toDateString(),
                ]);
            } else {
                // MENGAKTIFKAN KEMBALI PEGAWAI
                $reactivateMonth = now()->startOfMonth();
                $inactiveEnd = $reactivateMonth->copy()->subDay()->toDateString();

                $ongoingInactive = $pegawai->statusPeriods()
                    ->where('status', 'Nonaktif')
                    ->whereNull('end_date')
                    ->first();

                if ($ongoingInactive) {
                    if ($ongoingInactive->start_date->greaterThanOrEqualTo($reactivateMonth)) {
                        // Diaktifkan kembali di bulan yang sama dengan penonaktifan
                        $ongoingInactive->delete();
                        $prevActive = $pegawai->statusPeriods()
                            ->where('status', 'Aktif')
                            ->orderBy('start_date', 'desc')
                            ->first();
                        if ($prevActive) {
                            $prevActive->update(['end_date' => null]);
                        }
                    } else {
                        // Tutup periode nonaktif dan buat periode aktif baru
                        $ongoingInactive->update(['end_date' => $inactiveEnd]);
                        $pegawai->statusPeriods()->create([
                            'status' => 'Aktif',
                            'start_date' => $reactivateMonth->toDateString(),
                            'end_date' => null,
                        ]);
                    }
                }

                $pegawai->update([
                    'is_active' => true,
                    'inactive_from_month' => null,
                ]);
            }
        });

        $status = $prevStatusActive ? 'dinonaktifkan' : 'diaktifkan kembali';

        return back()->with('success', "Pegawai berhasil {$status}.");
    }

    public function checkKegiatanBelumTransfer(Pegawai $pegawai)
    {
        $this->authorize('kelola-master-data');

        $kegiatans = \App\Models\Kegiatan::where('id_penanggung_jawab', $pegawai->id_pegawai)
            ->whereDoesntHave('transfer')
            ->with('bidang:id_bidang,slug,detail_bidang')
            ->get(['id_kegiatan', 'nama_rk_kegiatan', 'id_bidang']);

        return response()->json([
            'total'     => $kegiatans->count(),
            'kegiatans' => $kegiatans->map(fn($k) => [
                'id'           => $k->id_kegiatan,
                'nama'         => $k->nama_rk_kegiatan,
                'bidang'       => $k->bidang?->detail_bidang ?? '-',
                'tagihan_url'  => $k->bidang ? route('kegiatan.index', ['bidang' => $k->bidang->slug]) : null,
            ])->values(),
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

        // Cek halaman sebelumnya (previous URL)
        $previousUrl = url()->previous();

        // Pastikan URL sebelumnya berasal dari domain/host aplikasi ini
        $refererHost = parse_url($previousUrl, PHP_URL_HOST);
        $currentHost = parse_url(config('app.url'), PHP_URL_HOST) ?? $request->getHost();

        if ($refererHost === $currentHost && $previousUrl !== $request->url()) {
            try {
                // Buat request tiruan untuk memeriksa apakah halaman sebelumnya masih bisa diakses
                $subRequest = Request::create($previousUrl, 'GET');
                
                // Gunakan resolver user dengan role yang baru di-update
                $subRequest->setUserResolver(fn() => $user);

                // Coba cocokkan rute untuk URL tersebut
                $route = app('router')->getRoutes()->match($subRequest);

                // Bind parameters and substitute bindings to resolve model instances (safe, no session side-effects)
                $route->bind($subRequest);
                app(\Illuminate\Routing\Middleware\SubstituteBindings::class)->handle($subRequest, function () {});
                $routeParams = $route->parameters();

                // Dapatkan middleware rute tersebut
                $middlewares = app('router')->gatherRouteMiddleware($route);

                // Cek otorisasi berdasarkan gate/policy di middleware 'can:...'
                $isAuthorized = true;
                foreach ($middlewares as $mw) {
                    if (str_starts_with($mw, 'can:')) {
                        $ability = substr($mw, 4);
                        $parts = explode(',', $ability);
                        $abilityName = $parts[0];
                        $parameters = array_slice($parts, 1);

                        $resolvedParams = [];
                        foreach ($parameters as $param) {
                            if (isset($routeParams[$param])) {
                                $resolvedParams[] = $routeParams[$param];
                            } else {
                                if (class_exists($param)) {
                                    $resolvedParams[] = $param;
                                } else {
                                    $resolvedParams[] = $param;
                                }
                            }
                        }

                        if (\Illuminate\Support\Facades\Gate::forUser($user)->denies($abilityName, $resolvedParams)) {
                            $isAuthorized = false;
                            break;
                        }
                    }
                }

                // Cek otorisasi Policy tingkat Controller secara in-memory
                if ($isAuthorized) {
                    $actionName = $route->getActionName();
                    if ($actionName && strpos($actionName, '@') !== false) {
                        [$controllerClass, $method] = explode('@', $actionName);
                        
                        $abilityMap = [
                            'index' => 'viewAny',
                            'show' => 'view',
                            'create' => 'create',
                            'store' => 'create',
                            'edit' => 'update',
                            'update' => 'update',
                            'destroy' => 'delete',
                            'delete' => 'delete',
                        ];
                        
                        if (isset($abilityMap[$method])) {
                            $abilityName = $abilityMap[$method];
                            
                            $modelInstance = null;
                            foreach ($routeParams as $param) {
                                if (is_object($param)) {
                                    $modelInstance = $param;
                                    break;
                                }
                            }
                            
                            $modelClass = null;
                            if (!$modelInstance) {
                                $controllerBaseName = class_basename($controllerClass);
                                $modelName = str_replace('Controller', '', $controllerBaseName);
                                $guessedClass = 'App\\Models\\' . $modelName;
                                if (class_exists($guessedClass)) {
                                    $modelClass = $guessedClass;
                                }
                            }
                            
                            if ($modelInstance) {
                                if (\Illuminate\Support\Facades\Gate::forUser($user)->denies($abilityName, $modelInstance)) {
                                    $isAuthorized = false;
                                }
                            } elseif ($modelClass) {
                                if (\Illuminate\Support\Facades\Gate::forUser($user)->denies($abilityName, $modelClass)) {
                                    $isAuthorized = false;
                                }
                            }
                        }
                    }
                }

                if ($isAuthorized) {
                    return redirect($previousUrl)->with('success', 'Berhasil switching role');
                }
            } catch (\Exception $e) {
                // Jika terjadi exception (route matching fail dll), diredirect ke dashboard
            }
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Berhasil switching role');
    }
}
