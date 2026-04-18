<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\IndikatorJPTController;
use App\Http\Controllers\KalenderDLController;
use App\Http\Controllers\KalenderKegiatanController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\MasterKegiatanController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PengirimanController;
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\RencanaJPTController;
use App\Http\Controllers\SubKegiatanController;
use App\Http\Controllers\PegawaiRoleController;
use App\Http\Controllers\AgendaPimpinanController;
use App\Http\Controllers\CkpPegawaiController;
use App\Http\Controllers\JenisKegiatanController;
use App\Http\Controllers\AnnouncementController;
use Illuminate\Support\Facades\Auth;

// ROUTE DASHBOARD VISUALISASI DATA

Route::middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile', function () {
        return view('pages.profile', ['title' => 'Profile']);
    })->name('profile');


    // Switching Role
    Route::get('/role-pegawai', [PegawaiRoleController::class, 'index'])
        ->name('pegawai-role.index');

    Route::post('/role-pegawai', [PegawaiRoleController::class, 'store'])
        ->name('pegawai-role.store');

    Route::post('/role-pegawai/{pegawais:id_pegawai}', [PegawaiRoleController::class, 'update'])
        ->name('pegawai-role.update');

    Route::post('/switch-role/{role}', [PegawaiRoleController::class, 'switchRolePegawai'])
        ->name('pegawai-role.switchRolePegawai');

    // CRUD RK IKI JPT BY PIMPINAN
    Route::prefix('rencana-indikator-jpt')->name('rencana-indikator-jpt.')->group(function () {
        // ROUTE UNTUK RENCANA JPT
        Route::prefix('rencana')->name('rencana.')->group(function () {
            Route::get('/', [RencanaJPTController::class, 'index'])->name('index')->middleware('can:kelola-master-data');
            Route::post('/', [RencanaJPTController::class, 'store'])->name('store')->middleware('can:kelola-master-data');
            Route::put('/{rencanaJpt}', [RencanaJPTController::class, 'update'])->name('update')->middleware('can:kelola-master-data');
            Route::delete('/{rencanaJpt}', [RencanaJPTController::class, 'delete'])->name('delete')->middleware('can:kelola-master-data');
        });

        // ROUTE UNTUK INDIKATOR JPT
        Route::prefix('{rencanaJpt}/indikator')->name('indikator.')->group(function () {
            // Select data IKI By RK
            Route::get('/', [RencanaJPTController::class, 'indikator'])->name('rencana-jpt.indikator');
            Route::post('/', [IndikatorJPTController::class, 'store'])->name('store')->middleware('can:kelola-master-data');
            Route::put('/{indikatorJpt}', [IndikatorJPTController::class, 'update'])->name('update')->middleware('can:kelola-master-data');
            Route::delete('/{indikatorJpt}', [IndikatorJPTController::class, 'delete'])->name('delete')->middleware('can:kelola-master-data');
        });
    });
    // END RK IKI JPT BY PIMPINAN

    // CRUD AGENDA PIMPINAN BY PIMPINAN
    Route::prefix('agenda-pimpinan')->middleware('can:kelola-master-data')->group(function () {
        Route::get('/', [AgendaPimpinanController::class, 'index'])->name('agenda.index');
        Route::post('/', [AgendaPimpinanController::class, 'store'])->name('agenda.store');
        Route::put('/{agenda}', [AgendaPimpinanController::class, 'update'])->name('agenda.update');
        Route::delete('/{agenda}', [AgendaPimpinanController::class, 'delete'])->name('agenda.delete');
    });
    // END AGENDA PIMPINAN BY PIMPINAN

    // CRUD JENIS KEGIATAN BY ADMIN
    Route::prefix('jenis-kegiatan')->middleware('can:kelola-master-data')->group(function () {
        Route::get('/', [JenisKegiatanController::class, 'index'])->name('jenis-kegiatan.index');
        Route::post('/', [JenisKegiatanController::class, 'store'])->name('jenis-kegiatan.store');
        Route::get('/{jenisKegiatan}/detail', [JenisKegiatanController::class, 'detail'])->name('jenis-kegiatan.detail');
        Route::put('/{jenisKegiatan}', [JenisKegiatanController::class, 'update'])->name('jenis-kegiatan.update');
        Route::delete('/{jenisKegiatan}', [JenisKegiatanController::class, 'delete'])->name('jenis-kegiatan.delete');
    });
    // END JENIS KEGIATAN BY ADMIN

    // CRUD BIDANG KERJA BY ADMIN
    Route::prefix('bidang-kerja')->middleware('can:kelola-master-data')->group(function () {
        Route::get('/', [BidangController::class, 'index'])->name('bidang.index');
        Route::get('/create', [BidangController::class, 'create'])->name('bidang.create');
        Route::post('/', [BidangController::class, 'store'])->name('bidang.store');
        Route::put('/{bidang:slug}', [BidangController::class, 'update'])->name('bidang.update');
        Route::delete('/{bidang:slug}', [BidangController::class, 'delete'])->name('bidang.delete');
    });
    // END BIDANG KERJA BY ADMIN

    // CRUD ANNOUNCEMENT BY ADMIN
    Route::prefix('announcements')->middleware('can:kelola-master-data')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])
            ->name('announcements.index');
        Route::post('/', [AnnouncementController::class, 'store'])
            ->name('announcements.store');
        Route::put('/{announcement}', [AnnouncementController::class, 'update'])
            ->name('announcements.update');
        Route::delete('/{announcement}', [AnnouncementController::class, 'destroy'])
            ->name('announcements.destroy');
        Route::post('/{announcement}/toggle', [AnnouncementController::class, 'toggleActive'])
            ->name('announcements.toggle');
    });

    // Halaman pengumuman untuk pegawai
    Route::get('/pengumuman', [AnnouncementController::class, 'pegawaiIndex'])->name('announcements.pegawai');

    // END CRUD ANNOUNCEMENT BY ADMIN

    // CRUD KEGIATAN & SUB KEGIATAN BY KETUA TIM
    Route::prefix('kegiatan')->group(function () {
        // Kegiatan
        Route::get('/bidang/{bidang:slug}', [KegiatanController::class, 'index'])->name('kegiatan.index')->middleware('can:viewAny,App\Models\Kegiatan');
        Route::post('/bidang/{bidang:slug}', [KegiatanController::class, 'store'])->name('kegiatan.store')->middleware('can:create,App\Models\Kegiatan');
        Route::put('/{kegiatan:id_kegiatan}', [KegiatanController::class, 'update'])->name('kegiatan.update')->middleware('can:update,kegiatan');
        Route::delete('/{kegiatan:id_kegiatan}', [KegiatanController::class, 'delete'])->name('kegiatan.delete')->middleware('can:delete,kegiatan');

        // Sub Kegiatan
        Route::prefix('{kegiatan:id_kegiatan}/sub-kegiatan')->group(function () {
            Route::post('/', [SubKegiatanController::class, 'store'])->name('sub.kegiatan.store')->middleware('can:create,kegiatan'); // create
            Route::get('/{subKegiatan:id_sub_kegiatan}', [SubKegiatanController::class, 'show'])->name('sub.kegiatan.show')->middleware('can:view,subKegiatan'); // show detail
            Route::put('/{subKegiatan:id_sub_kegiatan}', [SubKegiatanController::class, 'update'])->name('sub.kegiatan.update')->middleware('can:update,subKegiatan'); // edit
            Route::delete('/{subKegiatan:id_sub_kegiatan}', [SubKegiatanController::class, 'delete'])->name('sub.kegiatan.delete')->middleware('can:delete,subKegiatan'); // delete
        });
    });
    // END KEGIATAN & SUB KEGIATAN BY KETUA TIM

    // HAPUS PENGIRIMAN BY ANGGOTA TIM (DELETE KELUAR DARI SCOPE AGAR CLEAN)
    Route::delete('pengirimans/{pengiriman:id_pengiriman}', [PengirimanController::class, 'delete'])->name('pengiriman.delete');

    Route::post('/penugasan/check-duplicate-dates', [PenugasanController::class, 'checkDuplicateDates'])->name('penugasan.check-duplicate-dates');

    // CRUD PENUGASAN  BY KETUA TIM
    Route::prefix('sub-kegiatan/{subKegiatan:id_sub_kegiatan}')->group(function () {
        // CRUD PENUGASAN BY KETUA TIM
        Route::prefix('penugasan')->group(function () {
            Route::post('/', [PenugasanController::class, 'store'])->name('penugasan.store')->middleware('can:create,App\Models\Penugasan,subKegiatan'); // create
            Route::put('/{penugasan:id_penugasan}', [PenugasanController::class, 'update'])->name('penugasan.update')->middleware('can:update,penugasan'); // edit
            Route::delete('/{penugasan}', [PenugasanController::class, 'delete'])->name('penugasan.delete')->middleware('can:delete,penugasan'); // delete

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
    Route::prefix('/master-kegiatan')->group(function () {
        Route::get('/', [MasterKegiatanController::class, 'index'])->name('master-kegiatan.index');
        Route::post('/', [MasterKegiatanController::class, 'store'])->name('master-kegiatan.store')->middleware('can:create,App\Models\Kegiatan');
    });

    Route::get('/rencana-kerja-dl', [MasterKegiatanController::class, 'index_rk_dl'])->name('master-kegiatan.index_rk_dl');
    Route::put('/penugasan/{penugasan:id_penugasan}/rencana-kerja-dl', [PenugasanController::class, 'update_rk_dl'])->name('penugasan.update_rk_dl')->middleware('can:acceptDL,App\Models\Penugasan,penugasan');
    Route::put('/penugasan/{penugasan:id_penugasan}/rencana-kerja-translok', [PenugasanController::class, 'update_rk_translok'])->name('penugasan.update_rk_translok')->middleware('can:acceptTranslok,App\Models\Penugasan,penugasan');
    // END ROUTE MASTER KEGIATAN

    // ROUTE KALENDER DL
    Route::prefix('/kalender-dl')->group(function () {
        Route::get('/', [KalenderDLController::class, 'index'])->name('kalenderDL.index');
        Route::post('/', [KalenderDLController::class, 'store'])->name('kalenderDL.store');
    });
    // END ROUTE KALENDER DL

    // ROUTE KALENDER KEGIATAN
    Route::prefix('/kalender-kegiatan')->group(function () {
        Route::get('/', [KalenderKegiatanController::class, 'index'])->name('kalenderKegiatan.index');
        // Route::post('/', [KalenderKegiatanController::class, 'store'])->name('kalenderKegiatan.store');
    });
    // END ROUTE KALENDER KEGIATAN

    Route::get(
        '/bidang/{bidang:slug}/kegiatan/export-mph',
        [KegiatanController::class, 'exportMph']
    )->name('kegiatan.export-mph');

    Route::get('/kegiatan/export-mph-all', [MasterKegiatanController::class, 'exportMphAll'])
        ->name('kegiatan.export-mph-all');

    // route CKP
    Route::get('ckp-pegawai/export', [CkpPegawaiController::class, 'exportExcel'])->name('ckp.pegawai.export');

    // Route untuk CKP dari Penugasan (Anggota Tim) - SUDAH ADA
    Route::post('/ckp/from-penugasan/{id}', [CkpPegawaiController::class, 'storeFromPenugasan'])
        ->name('ckp.from.penugasan');

    // Route untuk CKP dari Sub Kegiatan (Ketua Tim) - BARU DITAMBAHKAN
    Route::post('/ckp/from-sub-kegiatan/{subKegiatan}', [CkpPegawaiController::class, 'storeFromSubKegiatan'])
        ->name('ckp.from.sub-kegiatan');

    // Route untuk halaman CKP pegawai
    Route::get('/ckp-pegawai', [CkpPegawaiController::class, 'index'])->name('ckp.pegawai.index');
    Route::put('/ckp-pegawai/{id}', [CkpPegawaiController::class, 'update'])->name('ckp.pegawai.update');
});


Route::get('/api/active-announcements', [AnnouncementController::class, 'getActiveAnnouncements']);


require __DIR__ . '/auth.php';
