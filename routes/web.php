<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\PdfController;


/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

Route::get('/produk', [
    ObatController::class,
    'index'
])->name('produk');

Route::get('/produk/search', [
    ObatController::class,
    'search'
])->name('produk.search');

Route::get('/produk/filter', [
    ObatController::class,
    'filterByCategory'
])->name('produk.filter');

// Server-Sent Events stream for real-time new products
Route::get('/produk/stream', [ObatController::class, 'stream'])->name('produk.stream');
Route::get('/produk/stats', [ObatController::class, 'stats'])->name('produk.stats');

Route::get('/obat/{nama}', [
    ObatController::class,
    'detail'
])->name('obat.detail');

Route::get('/rekomendasi', [RecommendationController::class, 'index'])
    ->name('rekomendasi');
// Ajax search for keluhan suggestions
Route::get('/api/keluhan', [RecommendationController::class, 'searchKeluhan'])->name('api.keluhan.search');
// AJAX endpoints for async recommendations
Route::post('/rekomendasi/async', [RecommendationController::class, 'asyncStart'])
    ->name('rekomendasi.async.start');

Route::get('/rekomendasi/async/{jobId}', [RecommendationController::class, 'asyncStatus'])
    ->name('rekomendasi.async.status');

Route::post('/rekomendasi/proses', [RecommendationController::class, 'index'])
    ->name('rekomendasi.proses');

Route::post('/rekomendasi/pilih-obat', [UserController::class, 'selectObat'])
    ->name('rekomendasi.select');

// Finalize and submit validation (keeps existing view form using 'validasi.submit')
Route::post('/validasi/submit', [UserController::class, 'submitSelectedObat'])
    ->name('validasi.submit');

Route::get('/riwayat', [UserController::class, 'riwayat'])
    ->name('riwayat');

Route::get('/riwayat/{id}', [UserController::class, 'detailRiwayat'])
    ->name('riwayat.detail');

Route::get('/riwayat/preview/{id}', [PdfController::class, 'preview'])
    ->name('riwayat.preview');

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {

    // Admin auth
    Route::get('/login', [AdminController::class, 'loginForm'])
        ->name('admin.login');

    Route::post('/login', [AdminController::class, 'login'])
        ->name('admin.login.post');

    Route::get('/', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

    // Logout
    Route::post('/logout', [AdminController::class, 'logout'])
        ->name('admin.logout');

    Route::get('/produk', [AdminController::class, 'produk'])
        ->name('admin.produk');

    Route::post('/produk/import', [AdminController::class, 'importProduk'])
        ->name('admin.produk.import');

    Route::get('/produk/list', [AdminController::class, 'produkList'])
        ->name('admin.produk.list');

    Route::get('/produk/{id}', [AdminController::class, 'detailProduk'])
        ->name('admin.produk.detail');

    // Produk management
    Route::get('/produk/tambah', [AdminController::class, 'tambahProduk'])
        ->name('admin.produk.tambah');

    Route::post('/produk/store', [AdminController::class, 'storeProduk'])
        ->name('admin.produk.store');

    Route::get('/produk/{id}/edit', [AdminController::class, 'editProduk'])
        ->name('admin.produk.edit');

    Route::post('/produk/{id}/update', [AdminController::class, 'updateProduk'])
        ->name('admin.produk.update');

    Route::post('/produk/{id}/hapus', [AdminController::class, 'hapusProduk'])
        ->name('admin.produk.hapus');

    Route::get('/laporan', [AdminController::class, 'laporan'])
        ->name('admin.laporan');

    Route::get('/laporan/{key}', [AdminController::class, 'detailLaporan'])
        ->name('admin.laporan.detail');

    Route::post('/laporan/{id}/delete', [AdminController::class, 'deleteLaporan'])
        ->name('admin.laporan.delete');

    Route::post('/laporan/bulk-delete', [AdminController::class, 'bulkDelete'])
        ->name('admin.laporan.bulk_delete');

    // Delete backup file (admin removed DB record earlier)
    Route::post('/laporan/backup/{key}/delete', [AdminController::class, 'deleteBackup'])
        ->name('admin.laporan.delete_backup');

    Route::get('/validasi', [ValidationController::class, 'index'])
        ->name('admin.validasi');

    Route::get('/validasi/{id}', [ValidationController::class, 'detail'])
        ->name('admin.detail');

    Route::get('/validasi/{id}/edit', [ValidationController::class, 'edit'])
        ->name('admin.validasi.edit');

    Route::post('/validasi/{id}/update', [ValidationController::class, 'update'])
        ->name('admin.validasi.update');

    // Pengaturan admin
    Route::get('/pengaturan', [AdminController::class, 'pengaturan'])
        ->name('admin.pengaturan');

    Route::post('/pengaturan', [AdminController::class, 'updatePengaturan'])
        ->name('admin.pengaturan.update');

    Route::post('/approve/{id}', [ValidationController::class, 'approve'])
        ->name('admin.approve');

    Route::post('/reject/{id}', [ValidationController::class, 'reject'])
        ->name('admin.reject');

    Route::post(
        '/validasi/{id}/delete',
        [ValidationController::class, 'destroy']
    )
        ->name('admin.validasi.delete');
});

/*
|--------------------------------------------------------------------------
| PDF
|--------------------------------------------------------------------------
*/

Route::prefix('pdf')->group(function () {

    Route::get('/preview/{id}', [
        PdfController::class,
        'preview'
    ])->name('pdf.preview');

    Route::get('/download/{id}', [
        PdfController::class,
        'download'
    ])->name('pdf.download');
});

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/', [UserController::class, 'home'])->name('home');

/*
|--------------------------------------------------------------------------
| TENTANG
|--------------------------------------------------------------------------
*/

Route::get('/tentang', function () {
    return view('tentang');
})->name('tentang');

/*
|--------------------------------------------------------------------------
| KONTAK
|--------------------------------------------------------------------------
*/

Route::get('/kontak', function () {
    return view('kontak');
})->name('kontak');

/*
|--------------------------------------------------------------------------
| LAPORAN
|--------------------------------------------------------------------------
*/

Route::get('/laporan', [UserController::class, 'laporan'])
    ->name('laporan');

Route::get('/laporan/{key}', [UserController::class, 'detailLaporan'])
    ->name('laporan.detail');

Route::post('/laporan/{key}/delete', [UserController::class, 'deleteLaporan'])
    ->name('laporan.delete');

Route::get(
    '/rekomendasi/detail/{nama}',
    [RecommendationController::class, 'detail']
)->name('rekomendasi.detail');






Route::fallback(function () {
    abort(404);
});



Route::get('/admin/riwayat', [ValidationController::class, 'riwayat'])
    ->name('admin.riwayat');
