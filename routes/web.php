<?php

use App\Http\Controllers\Web\TestPaymentController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\AddressController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\WishlistController;
use App\Http\Controllers\Webhooks\PaymentCallbackController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\RobotsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

Route::post('/webhooks/payment/callback', [PaymentCallbackController::class, 'handleCallback'])
    ->name('webhooks.payment.callback')
    ->middleware('throttle:60,1')
    ->withoutMiddleware(['web', 'csrf']);

// Language switcher
Route::post('/locale/{lang}', function (string $lang) {
    $available = config('app.available_locales', ['tr', 'en']);
    if (!in_array($lang, $available, true)) {
        abort(400);
    }
    $cookie = cookie('locale', $lang, 60 * 24 * 365); // 1 year
    return redirect()->back()->withCookie($cookie);
})->name('locale.switch');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('product.show');

if (app()->environment(['local', 'testing'])) {
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/test-payment', [TestPaymentController::class, 'index'])->name('test-payment.index');
        Route::post('/test-payment/create', [TestPaymentController::class, 'createTestOrder'])->name('test-payment.create');
    });
}

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::post('/wishlist/check', [WishlistController::class, 'check'])->name('wishlist.check');
Route::get('/wishlist/count', [WishlistController::class, 'count'])->name('wishlist.count');

Route::middleware(['auth', 'throttle.checkout'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{orderId}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel/{orderId}', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', fn() => redirect()->route('profile.index'))->name('profile.edit');
    Route::post('/profile/update-profile', [ProfileController::class, 'updateProfile'])->name('profile.update-profile');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::delete('/profile/account', [ProfileController::class, 'destroyAccount'])->name('profile.destroy-account');
    
    Route::get('/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/set-default', [ProfileController::class, 'setDefaultAddress'])->name('addresses.set-default');

    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
