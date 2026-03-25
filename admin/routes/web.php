<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\productController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// --------------------------------------AUTH BREEZ----------------------------------//

//● Named routes 
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

require __DIR__ . '/auth.php';

Route::prefix('admin')->middleware(['role:admin', 'throttle:10,1'])->group(function () {
    Route::resource('products', productController::class);
});

Route::get('/products/{product}/download', [productController::class, 'download']);

//------------------------------CART ROUTES------------------------------//

Route::prefix('user')->middleware(['role:user','auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    //for all products listing
    Route::get('/products', [productController::class, 'userProducts'])->name('user.products');
   
});

 
Route::fallback(function () {
    return view('404');
});
