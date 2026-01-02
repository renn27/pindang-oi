<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TimKerjaController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\IndikatorJPTController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\MasterKegiatanController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PengirimanController;
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\RencanaJPTController;
use App\Http\Controllers\SubKegiatanController;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;

// UNTUK SIMULASI ROLE (ADMIN & PIMPINAN)
Route::get('/login-as/{username}', function ($username) {
    $pegawai = Pegawai::where('username', $username)->firstOrFail();
    // dd($pegawai);
    Auth::login($pegawai);

    // langsung dd
    // dd(Auth::user());
    return redirect()->route('dashboard');
});

// dashboard pages
Route::get('/', function () {
    $user = Auth::user(); // <-- INI KUNCI NYA
    return view('pages.dashboard', [
        'title' => 'Pindang Dashboard',
        'user'  => $user,
    ]);
})->name('dashboard');

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

// CRUD BIDANG KERJA BY ADMIN
Route::prefix('bidang-kerja')->group(function () {
    Route::get('/', [BidangController::class, 'index'])->name('bidang.index');
    Route::get('/create', [BidangController::class, 'create'])->name('bidang.create');
    Route::post('/', [BidangController::class, 'store'])->name('bidang.store');
    Route::put('/{bidang:slug}', [BidangController::class, 'update'])->name('bidang.update');
    Route::delete('/{bidang:slug}', [BidangController::class, 'delete'])->name('bidang.delete');
});

// CRUD KEGIATAN & SUB KEGIATAN BY KETUA TIM
Route::prefix('kegiatan')->group(function () {
    // Kegiatan
    Route::get('/bidang/{bidang:slug}', [KegiatanController::class, 'index'])->name('kegiatan.index');
    Route::post('/bidang/{bidang:slug}', [KegiatanController::class, 'store'])->name('kegiatan.store');

    // Sub Kegiatan
    Route::prefix('{kegiatan:id_kegiatan}/sub-kegiatan')->group(function () {
        Route::post('/', [SubKegiatanController::class, 'store'])->name('sub.kegiatan.store'); // create
        Route::get('/{subKegiatan}', [SubKegiatanController::class, 'show'])->name('sub.kegiatan.show'); // edit
        Route::put('/{subKegiatan}', [SubKegiatanController::class, 'update'])->name('sub.kegiatan.update'); // edit
        Route::delete('/{subKegiatan}', [SubKegiatanController::class, 'delete'])->name('sub.kegiatan.delete'); // delete
    });
});

// CRUD PENUGASAN  BY KETUA TIM
Route::prefix('sub-kegiatan/{subKegiatan:id_sub_kegiatan}')->group(function () {
    // CRUD PENUGASAN BY KETUA TIM
    Route::prefix('penugasan')->group(function () {
        Route::post('/', [PenugasanController::class, 'store'])->name('penugasan.store'); // create
        Route::put('/{penugasan}', [PenugasanController::class, 'update'])->name('penugasan.update'); // edit
        Route::delete('/{penugasan}', [PenugasanController::class, 'delete'])->name('penugasan.delete'); // delete

        // CRUD PENGIRIMAN BY ANGGOTA TIM
        Route::prefix('{penugasan:id_penugasan}/pengirimans')->group(function () {
            Route::post('/', [PengirimanController::class, 'store'])->name('pengiriman.store'); // create

            Route::prefix('{pengirimans:id_pengiriman}/penerimaan')->group(function () {
                Route::post('/', [PenerimaanController::class, 'store'])->name('penerimaan.store'); // create
            });
        });

    });
});


// CRUD KEGIATAN BY KETUA
Route::prefix('rencana-kerja')->group(function () {
    Route::get('/', [KegiatanController::class, 'index'])->name('rencana.index');
    Route::get('/sub-kegiatan', [KegiatanController::class, 'subKegiatan'])->name('rencana.sub.kegiatan');
    Route::post('/', [KegiatanController::class, 'store'])->name('rencana.store');
    Route::put('/{rencanaKerja}', [KegiatanController::class, 'update'])->name('rencana.update');
    Route::get('/{slug}', [KegiatanController::class, 'show'])->name('rencana.show');
});



// tim kerja
Route::get('/tim-kerja', function () {
    return view('pages.rencana-kerja.tim-kerja', ['title' => 'Tim Kerja']);
})->name('tim-kerja');

// tim kerja
Route::get('/rk-ketua', function () {
    return view('pages.rencana-kerja.rk-ketua', ['title' => 'Rencana Kerja Ketua']);
})->name('rk-ketua');

Route::prefix('/rk-ketua')->group(function () {
    Route::get('/', [MasterKegiatanController::class, 'index'])->name('rencana.index');
    Route::get('/sub-kegiatan', [MasterKegiatanController::class, 'subKegiatan'])->name('rencana.sub.kegiatan');
    Route::post('/', [MasterKegiatanController::class, 'store'])->name('rencana.store');
    Route::put('/{rencanaKerja}', [MasterKegiatanController::class, 'update'])->name('rencana.update');
    Route::get('/{slug}', [MasterKegiatanController::class, 'show'])->name('rencana.show');
});

// daftar kegiatan
Route::get('/daftar-kegiatan', function () {
    return view('pages.rencana-kerja.daftar-kegiatan', ['title' => 'Daftar Kegiatan']);
})->name('daftar-kegiatan');


// dll pages
Route::get('/dll', function () {
    return view('pages.dashboard.ecommerce', ['title' => 'E-commerce Dashboard']);
})->name('dll');

// calender pages
Route::get('/calendar', function () {
    return view('pages.calender', ['title' => 'Calendar']);
})->name('calendar');

// profile pages
Route::get('/profile', function () {
    return view('pages.profile', ['title' => 'Profile']);
})->name('profile');

// form pages
Route::get('/form-elements', function () {
    return view('pages.form.form-elements', ['title' => 'Form Elements']);
})->name('form-elements');

// tables pages
Route::get('/basic-tables', function () {
    return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
})->name('basic-tables');











// pages

Route::get('/blank', function () {
    return view('pages.blank', ['title' => 'Blank']);
})->name('blank');

// error pages
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');

// chart pages
Route::get('/line-chart', function () {
    return view('pages.chart.line-chart', ['title' => 'Line Chart']);
})->name('line-chart');

Route::get('/bar-chart', function () {
    return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
})->name('bar-chart');


// authentication pages
Route::get('/signin', function () {
    return view('pages.auth.signin', ['title' => 'Sign In']);
})->name('signin');

Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Sign Up']);
})->name('signup');

// ui elements pages
Route::get('/alerts', function () {
    return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
})->name('alerts');

Route::get('/avatars', function () {
    return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
})->name('avatars');

Route::get('/badge', function () {
    return view('pages.ui-elements.badges', ['title' => 'Badges']);
})->name('badges');

Route::get('/buttons', function () {
    return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
})->name('buttons');

Route::get('/image', function () {
    return view('pages.ui-elements.images', ['title' => 'Images']);
})->name('images');

Route::get('/videos', function () {
    return view('pages.ui-elements.videos', ['title' => 'Videos']);
})->name('videos');


//crud tabel tim kerja

Route::get('/tim-kerjates', [TimKerjaController::class, 'index']);
Route::get('/tim-kerja/data', [TimKerjaController::class, 'data'])->name('tim-kerja.data');
Route::post('/tim-kerja', [TimKerjaController::class, 'store'])->name('tim-kerja.store');
Route::put('/tim-kerja/{id}', [TimKerjaController::class, 'update'])->name('tim-kerja.update');
Route::delete('/tim-kerja/{id}', [TimKerjaController::class, 'destroy'])->name('tim-kerja.destroy');

//halaman bidang kerja
// Route::get('/bidang-kerja', function () {
//     return view('pages.bidang-kerja', ['title' => 'Bidang Kerja']);
// })->name('bidang-kerja');

//halaman bidang kerja 2
Route::get('/bidang-kerja2', function () {
    return view('pages.bidang-kerja2', ['title' => 'Bidang Kerja 2']);
})->name('bidang-kerja2');

//halaman detail kegiatan
Route::get('/detail-kegiatan', function () {
    return view('pages.detail-kegiatan', ['title' => 'Bidang Kerja']);
})->name('detail-kegiatan');

// Routes untuk Bidang (Dinamis)
Route::prefix('bidang')->group(function () {
    // Halaman utama bidang
    Route::get('/', [BidangController::class, 'index'])->name('bidang.index');

    // Halaman detail bidang
    Route::get('/{id}', [BidangController::class, 'show'])->name('bidang.show');

    // Sub-halaman untuk tiap bidang
    Route::get('/{id}/kegiatan', [BidangController::class, 'kegiatan'])->name('bidang.kegiatan');
    Route::get('/{id}/laporan', [BidangController::class, 'laporan'])->name('bidang.laporan');

    // Route khusus untuk beberapa bidang (contoh)
    Route::get('/spbe/kegiatan', function () {
        $bidang = \App\Models\Bidang::where('nama_bidang', 'like', '%SPBE%')->first();
        if ($bidang) {
            return redirect()->route('bidang.kegiatan', $bidang->id_bidang);
        }
        return redirect()->route('bidang.index');
    })->name('bidang.spbe');
});
