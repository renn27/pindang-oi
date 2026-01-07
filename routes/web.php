<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\IndikatorJPTController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\MasterKegiatanController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PengirimanController;
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\RencanaJPTController;
use App\Http\Controllers\SimulasiLoginController;
use App\Http\Controllers\SubKegiatanController;
use Illuminate\Support\Facades\Auth;

Route::middleware('web')->group(function () {

    // Simulasi Autentikasi dan Autorisasi Untuk Atur Role dan Permission
    Route::get('/login-as/{username}', [SimulasiLoginController::class, 'loginAs'])->name('simulasi.login');
    Route::get('/logout-as', [SimulasiLoginController::class, 'logoutAs'])->name('simulasi.logout');

    // Dashboard
    Route::get('/', function () {
        $user = Auth::user(); // otomatis dari Auth
        return view('pages.dashboard', [
            'title' => 'Dashboard',
            'user'  => $user,
        ]);
    })->name('dashboard');

    // Session test
    Route::get('/session-test', function () {
        session(['test' => 'laravel12']);
        return session()->all();
    });
});

// Route fallback login
Route::get('/login', function () {
    return response('Belum login. Gunakan /login-as/{username}', 401);
})->name('login');


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
    Route::get('/bidang/{bidang:slug}', [KegiatanController::class, 'index'])->name('kegiatan.index');
    Route::post('/bidang/{bidang:slug}', [KegiatanController::class, 'store'])->name('kegiatan.store');

    // Sub Kegiatan
    Route::prefix('{kegiatan:id_kegiatan}/sub-kegiatan')->group(function () {
        Route::post('/', [SubKegiatanController::class, 'store'])->name('sub.kegiatan.store'); // create
        Route::get('/{subKegiatan:id_sub_kegiatan}', [SubKegiatanController::class, 'show'])->name('sub.kegiatan.show'); // edit
        Route::put('/{subKegiatan:id_sub_kegiatan}', [SubKegiatanController::class, 'update'])->name('sub.kegiatan.update'); // edit
        Route::delete('/{subKegiatan:id_sub_kegiatan}', [SubKegiatanController::class, 'delete'])->name('sub.kegiatan.delete'); // delete
    });
});
// END KEGIATAN & SUB KEGIATAN BY KETUA TIM

// CRUD PENUGASAN  BY KETUA TIM
Route::prefix('sub-kegiatan/{subKegiatan:id_sub_kegiatan}')->group(function () {
    // CRUD PENUGASAN BY KETUA TIM
    Route::prefix('penugasan')->group(function () {
        Route::post('/', [PenugasanController::class, 'store'])->name('penugasan.store'); // create
        Route::put('/{penugasan:id_penugasan}', [PenugasanController::class, 'update'])->name('penugasan.update'); // edit
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
// CRUD PENUGASAN  BY KETUA TIM

// ROUTE MASTER KEGIATAN
Route::prefix('/master-kegiatan')->group(function () {
    Route::get('/', [MasterKegiatanController::class, 'index'])->name('master-kegiatan.index');
    Route::post('/', [MasterKegiatanController::class, 'store'])->name('master-kegiatan.store');
});
// END ROUTE MASTER KEGIATAN

















































// =====================================================================================================

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

// ====================================================================================================