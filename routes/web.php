<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;

// Homepage
Route::get('/', [ProdukController::class, 'home'])->name('home');

// Shop
Route::get('/shop', [ProdukController::class, 'index'])->name('shop.index');
Route::get('/api/search', [ProdukController::class, 'search'])->name('produk.search');
Route::get('/shop/{slug}', [ProdukController::class, 'show'])->name('produk.detail');

// Keranjang (API-style, JSON responses)
Route::prefix('keranjang')->group(function () {
    Route::get('/', [KeranjangController::class, 'index'])->name('keranjang.index');
    Route::post('/tambah', [KeranjangController::class, 'tambah'])->name('keranjang.tambah');
    Route::put('/{id}', [KeranjangController::class, 'update'])->name('keranjang.update');
    Route::delete('/{id}', [KeranjangController::class, 'hapus'])->name('keranjang.hapus');
    Route::get('/jumlah', [KeranjangController::class, 'jumlah'])->name('keranjang.jumlah');
});

// Checkout
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout/buy-now', [CheckoutController::class, 'buyNow'])->name('checkout.buy-now');
Route::post('/checkout/from-cart', [CheckoutController::class, 'fromCart'])->name('checkout.from-cart');
Route::post('/checkout/voucher', [CheckoutController::class, 'applyVoucher'])->name('checkout.voucher');
Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.place-order');

// Pesanan (Order)
Route::get('/pesanan/{kode}', [CheckoutController::class, 'showOrder'])->name('pesanan.show');
Route::get('/pesanan/{kode}/invoice', [CheckoutController::class, 'showInvoice'])->name('pesanan.invoice');
Route::post('/pesanan/{kode}/cancel', [CheckoutController::class, 'cancelOrder'])->name('pesanan.cancel');
Route::post('/pesanan/{kode}/confirm-received', [CheckoutController::class, 'confirmReceived'])->name('pesanan.confirm-received');

// Trust & information pages
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'attempt'])
        ->middleware('throttle:6,1')
        ->name('login.attempt');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:3,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->middleware('throttle:3,1')
        ->name('password.update');

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('register.store');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'show'])->name('account.show');
    Route::get('/account/delivery', [AccountController::class, 'delivery'])->name('account.delivery');
    Route::get('/account/information', [AccountController::class, 'information'])->name('account.information');
    Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::patch('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::patch('/account/delivery', [AccountController::class, 'updateDelivery'])->name('account.delivery.update');
    Route::post('/shop/{slug}/reviews', [ProdukController::class, 'storeReview'])->name('produk.reviews.store');
});

Route::post('/pesanan/{kode}/after-sales', [CheckoutController::class, 'requestAfterSales'])->name('pesanan.after-sales');

// Redirect lama
Route::redirect('/products', '/shop');
