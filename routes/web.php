<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ShippingAddressController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root by auth state / role to avoid / <-> /login loop
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    return Auth::user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('customer.home');
});

//auth
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

//customer
Route::middleware(['auth', 'role:customer'])->prefix('shop')->name('customer.')->group(function () {
    Route::get('/',          [CustomerController::class, 'home'])->name('home');
    Route::get('/cart',      [CustomerController::class, 'cart'])->name('cart');
    Route::post('/cart/{product}',          [CustomerController::class, 'addToCart'])->name('cart.add');
    Route::patch('/cart/{product}',         [CustomerController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/{product}',        [CustomerController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/order',                   [CustomerController::class, 'placeOrder'])->name('order.place');
    Route::get('/order-placed',             [CustomerController::class, 'orderPlaced'])->name('order.placed');
    Route::get('/orders',                   [CustomerController::class, 'orders'])->name('orders');
    Route::get('/profile',                  [CustomerController::class, 'profile'])->name('profile');
    Route::patch('/profile',                [CustomerController::class, 'updateProfile'])->name('profile.update');

    Route::get('/addresses', [ShippingAddressController::class, 'index'])->name('addresses');
    Route::post('/addresses', [ShippingAddressController::class, 'store'])->name('addresses.store');
    Route::patch('/addresses/{address}', [ShippingAddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [ShippingAddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/default', [ShippingAddressController::class, 'makeDefault'])->name('addresses.default');
});

//admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',                        [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders',                           [AdminController::class, 'orders'])->name('orders');
    Route::patch('/orders/{order}/status',          [AdminController::class, 'updateOrderStatus'])->name('orders.status');

    Route::get('/products',                         [AdminController::class, 'products'])->name('products');
    Route::post('/products',                        [AdminController::class, 'storeProduct'])->name('products.store');
    Route::patch('/products/{product}',             [AdminController::class, 'updateProduct'])->name('products.update');
    Route::patch('/products/{product}/archive',     [AdminController::class, 'archiveProduct'])->name('products.archive');
    Route::patch('/products/{product}/unarchive',   [AdminController::class, 'unarchiveProduct'])->name('products.unarchive');
    Route::get('/archived',                         [AdminController::class, 'archived'])->name('archived');
    Route::get('/customers',                        [AdminController::class, 'customers'])->name('customers');
    Route::get('/reports',                          [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/pdf',                      [AdminController::class, 'reportsPdf'])->name('reports.pdf');
});

Route::middleware(['auth', 'role:admin'])->get('/fix-images', function () {
    $map = [
        'BB Cream'          => 'images/products/bb-cream.jpg',
        'Brow Gel'          => 'images/products/brow-gel.jpg',
        'Contour Stick'     => 'images/products/contour-stick.jpg',
        'Cream Blush'       => 'images/products/cream-blush.jpg',
        'Eyeliner Pencil'   => 'images/products/eyeliner-pencil.jpg',
        'Eyeshadow Palette' => 'images/products/eyeshadow-palette.jpg',
        'Highlighter'       => 'images/products/highlighter.jpg',
        'Lip Gloss'         => 'images/products/lip-gloss.jpg',
        'Lip Liner'         => 'images/products/lip-liner.jpg',
        'Lip Tint'          => 'images/products/lip-tint.jpg',
        'Liquid Foundation' => 'images/products/liquid-foundation.png',
        'Mascara'           => 'images/products/mascara.jpg',
        'Matte Lipstick'    => 'images/products/matte-lipstick.jpg',
        'Setting Powder'    => 'images/products/setting-powder.png',
    ];

    foreach ($map as $name => $path) {
        \App\Models\Product::where('pName', $name)->update(['image' => $path]);
    }

    return redirect()->route('admin.products')->with('success', 'Product image paths updated.');
});

Route::get('/check-images', function () {
    return \App\Models\Product::select('pName', 'image')->get();
});