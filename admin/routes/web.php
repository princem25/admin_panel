<?php

use App\Http\Controllers\Admin\LogViewerController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\ProductController as UserProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// --------------------------------------AUTH BREEZ----------------------------------//

// ● Named routes
// ● Route groups
// ● Prefix groups
// ● Middleware groups
// ● Fallback route

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('admin.dashboard');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'role:user'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::prefix('admin')->middleware(['role:admin', 'throttle:100,1'])->group(function () {
    Route::resource('products', AdminProductController::class);
    Route::get('/logs', [LogViewerController::class, 'index'])->name('admin.logs');
});

Route::get('/products/{product}/download', [AdminProductController::class, 'download'])->name('products.download');

// ------------------------------CART ROUTES------------------------------//

Route::prefix('user')->middleware(['role:user', 'auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::patch('/cart/increase/{id}', [CartController::class, 'increase'])->name('cart.increase');
    Route::patch('/cart/decrease/{id}', [CartController::class, 'decrease'])->name('cart.decrease');

    // for all products listing
    Route::get('/products', [UserProductController::class, 'index'])->name('user.products');
    Route::get('/products/{product}', [UserProductController::class, 'show'])->name('user.products.show');

    // Generate Invoice from Cart → direct PDF download
    Route::get('/cart/invoice', [InvoiceController::class, 'generate'])->name('cart.invoice');
});

Route::get('/products/export', [AdminProductController::class, 'export']);

Route::fallback(function () {
    return view('404');
});

