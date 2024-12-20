<?php

use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\UserController;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'login'])->name('login');
Route::get('/register', [UserController::class, 'register'])->name('register');
Route::post('/register', [UserController::class, 'registerStore'])->name('register.store');
Route::post('/login', [UserController::class, 'loginCheck'])->name('login.check');
Route::resource('users', UserController::class);

// Dashboard
Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/logout', [UserController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth']], function () {
    Route::post('produk/cetak/label', [ProdukController::class, 'cetakLabel'])->name('produk.cetakLabel');
    Route::put('produk/edit/{id}/tambahStok', [ProdukController::class, 'tambahStok'])->name('produk.tambahStok');
    Route::get('produk/logproduk', [ProdukController::class, 'logproduk'])->name('produk.logproduk');
    Route::put('produk/{id}/updateStok', [ProdukController::class, 'updateStok'])->name('produk.updateStok');
    Route::resource('produk', ProdukController::class);
    Route::resource('penjualan', PenjualanController::class);
});
