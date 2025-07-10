<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\DaftarProduk;
use App\Filament\Pages\Keranjang;
use App\Http\Controllers\PermintaanController;

Route::get('/', function () {
    return redirect('/bpkp');
});

Route::post('/bpkp/daftar-produk/tambah', [DaftarProduk::class, 'addToCart'])
    ->name('filament.pages.daftar-produk.addToCart');

Route::post('/admin/keranjang/checkout', [Keranjang::class, 'checkout'])->name('filament.pages.keranjang.checkout');

Route::get('/permintaan/{id}/cetak-pdf', [PermintaanController::class, 'downloadPdf'])
    ->middleware(['auth'])
    ->name('permintaan.pdf');
