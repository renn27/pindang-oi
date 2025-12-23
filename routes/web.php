<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TimKerjaController;

// dashboard pages
Route::get('/', function () {
    return view('pages.dashboard', ['title' => 'Pindang Dashboard']);
})->name('dashboard');

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







// tim kerja
Route::get('/tim-kerja', function () {
    return view('pages.rencana-kerja.tim-kerja', ['title' => 'Tim Kerja']);
})->name('tim-kerja');

// tim kerja
Route::get('/rk-ketua', function () {
    return view('pages.rencana-kerja.rk-ketua', ['title' => 'Rencana Kerja Ketua']);
})->name('rk-ketua');

// daftar kegiatan
Route::get('/daftar-kegiatan', function () {
    return view('pages.rencana-kerja.daftar-kegiatan', ['title' => 'Daftar Kegiatan']);
})->name('daftar-kegiatan');










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
Route::get('/bidang-kerja', function () {
    return view('pages.bidang-kerja', ['title' => 'Bidang Kerja']);
})->name('bidang-kerja');

//halaman bidang kerja 2
Route::get('/bidang-kerja2', function () {
    return view('pages.bidang-kerja2', ['title' => 'Bidang Kerja 2']);
})->name('bidang-kerja2');

//halaman detail kegiatan
Route::get('/detail-kegiatan', function () {
    return view('pages.detail-kegiatan', ['title' => 'Bidang Kerja']);
})->name('detail-kegiatan');