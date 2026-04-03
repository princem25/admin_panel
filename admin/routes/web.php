<?php

use App\Http\Controllers\Admin\LogViewerController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\ProductController as UserProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;

Route::get('/', function () {
    return view('welcome');
});



// --------------------------------------AUTH BREEZ----------------------------------//

// ● Named routes
// ● Route groups
// ● Prefix groups
// ● Middleware groups
// ● Fallback route

Route::get('/admin/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard');

Route::get('/dashboard', [UserDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:user'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::prefix('admin')->middleware(['role:admin', 'throttle:100,1'])->group(function () {
    // These must be placed BEFORE Route::resource('products') 
    Route::get('/products/export', [AdminProductController::class, 'export'])->name('admin.products.export');
    Route::get('/products/{product}/download', [AdminProductController::class, 'download'])->name('admin.products.download');

    Route::resource('products', AdminProductController::class);
    
    Route::get('/logs', [LogViewerController::class, 'index'])->name('admin.logs');
    
    // Order Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('admin.orders.update');
});


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

    // Generate Invoice from Order
    Route::get('/orders/{order}/invoice', [InvoiceController::class, 'generate'])->name('orders.invoice');

    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Order History Routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});



Route::fallback(function () {
    return view('404');
});

