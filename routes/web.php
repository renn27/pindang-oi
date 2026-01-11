<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\IndikatorJPTController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\MasterKegiatanController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PengirimanController;
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\RencanaJPTController;
use App\Http\Controllers\SubKegiatanController;
use App\Http\Controllers\PegawaiRoleController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('pages.dashboard', [
        'title' => 'Dashboard',
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile', function () {
        return view('pages.profile', ['title' => 'Profile']);
    })->name('profile');


    // Switching Role
    Route::get('/role-pegawai', [PegawaiRoleController::class, 'index'])
        ->name('pegawai-role.index');

    Route::post('/role-pegawai', [PegawaiRoleController::class, 'store'])
        ->name('pegawai-role.store');

    Route::post('/switch-role/{role}', function (Request $request, string $role) {
        $user = $request->user();

        abort_if(! $user, 401);
        abort_if(! $user->hasRole($role), 403);

        $user->update([
            'active_role' => $role
        ]);

        return back();
    })->middleware('auth')->name('switch.role');

    // CRUD RK IKI JPT BY PIMPINAN
    Route::prefix('rencana-indikator-jpt')->name('rencana-indikator-jpt.')->group(function () {
        // ROUTE UNTUK RENCANA JPT
        Route::prefix('rencana')->name('rencana.')->group(function () {
            Route::get('/', [RencanaJPTController::class, 'index'])->name('index');
            Route::post('/', [RencanaJPTController::class, 'store'])->name('store');
            Route::put('/{rencanaJpt}', [RencanaJPTController::class, 'update'])->name('update');
            Route::delete('/{rencanaJpt}', [RencanaJPTController::class, 'delete'])->name('delete');
        });

        // ROUTE UNTUK INDIKATOR JPT
        Route::prefix('{rencanaJpt}/indikator')->name('indikator.')->group(function () {
            // Select data IKI By RK
            Route::get('/', [RencanaJPTController::class, 'indikator'])->name('rencana-jpt.indikator');
            Route::post('/', [IndikatorJPTController::class, 'store'])->name('store');
            Route::put('/{indikatorJpt}', [IndikatorJPTController::class, 'update'])->name('update');
            Route::delete('/{indikatorJpt}', [IndikatorJPTController::class, 'delete'])->name('delete');
        });
    });
    // END RK IKI JPT BY PIMPINAN

    // CRUD BIDANG KERJA BY ADMIN
    Route::prefix('bidang-kerja')->group(function () {
        Route::get('/', [BidangController::class, 'index'])->name('bidang.index');
        Route::get('/create', [BidangController::class, 'create'])->name('bidang.create');
        Route::post('/', [BidangController::class, 'store'])->name('bidang.store');
        Route::put('/{bidang:slug}', [BidangController::class, 'update'])->name('bidang.update');
        Route::delete('/{bidang:slug}', [BidangController::class, 'delete'])->name('bidang.delete');
    });
    // END BIDANG KERJA BY ADMIN

    // CRUD KEGIATAN & SUB KEGIATAN BY KETUA TIM
    Route::prefix('kegiatan')->group(function () {
        // Kegiatan
        Route::get('/bidang/{bidang:slug}', [KegiatanController::class, 'index'])->name('kegiatan.index')->middleware('can:viewAny,App\Models\Kegiatan');
        Route::post('/bidang/{bidang:slug}', [KegiatanController::class, 'store'])->name('kegiatan.store')->middleware('can:create,App\Models\Kegiatan');

        // Sub Kegiatan
        Route::prefix('{kegiatan:id_kegiatan}/sub-kegiatan')->group(function () {
            Route::post('/', [SubKegiatanController::class, 'store'])->name('sub.kegiatan.store')->middleware('can:create,App\Models\SubKegiatan,kegiatan');; // create
            Route::get('/{subKegiatan:id_sub_kegiatan}', [SubKegiatanController::class, 'show'])->name('sub.kegiatan.show')->middleware('can:view,subKegiatan');; // show detail
            Route::put('/{subKegiatan:id_sub_kegiatan}', [SubKegiatanController::class, 'update'])->name('sub.kegiatan.update')->middleware('can:update,subKegiatan');; // edit
            Route::delete('/{subKegiatan:id_sub_kegiatan}', [SubKegiatanController::class, 'delete'])->name('sub.kegiatan.delete')->middleware('can:delete,subKegiatan');; // delete
        });
    });
    // END KEGIATAN & SUB KEGIATAN BY KETUA TIM

    // CRUD PENUGASAN  BY KETUA TIM
    Route::prefix('sub-kegiatan/{subKegiatan:id_sub_kegiatan}')->group(function () {
        // CRUD PENUGASAN BY KETUA TIM
        Route::prefix('penugasan')->group(function () {
            Route::post('/', [PenugasanController::class, 'store'])->name('penugasan.store')->middleware('can:create,App\Models\Penugasan,subKegiatan');; // create
            Route::put('/{penugasan:id_penugasan}', [PenugasanController::class, 'update'])->name('penugasan.update') ->middleware('can:update,penugasan');; // edit
            Route::delete('/{penugasan}', [PenugasanController::class, 'delete'])->name('penugasan.delete')->middleware('can:delete,penugasan');; // delete

            // CRUD PENGIRIMAN BY ANGGOTA TIM
            Route::prefix('{penugasan:id_penugasan}/pengirimans')->group(function () {
                Route::post('/', [PengirimanController::class, 'store'])->name('pengiriman.store')->middleware('can:send,penugasan'); // create pengiriman

                Route::prefix('{pengirimans:id_pengiriman}/penerimaan')->middleware('can:receive,penugasan')->group(function () {
                    Route::post('/', [PenerimaanController::class, 'store'])->name('penerimaan.store'); // create penerimaan
                });
            });
        });
    });
    // CRUD PENUGASAN  BY KETUA TIM

    // ROUTE MASTER KEGIATAN
    Route::prefix('/master-kegiatan')->middleware('can:create,App\Models\Kegiatan')->group(function () {
        Route::get('/', [MasterKegiatanController::class, 'index'])->name('master-kegiatan.index');
        Route::post('/', [MasterKegiatanController::class, 'store'])->name('master-kegiatan.store');
    });
    // END ROUTE MASTER KEGIATAN
});

require __DIR__.'/auth.php';
