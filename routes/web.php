<?php

use App\Http\Controllers\WeatherController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// 1. Ilm
Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');
Route::get('/weather/search', [WeatherController::class, 'search'])->name('weather.search');

// 2. Kaart
Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::get('/map/markers', [MapController::class, 'markers'])->name('map.markers');
Route::post('/map/markers', [MapController::class, 'store'])->name('map.store');
Route::put('/map/markers/{marker}', [MapController::class, 'update'])->name('map.update');
Route::delete('/map/markers/{marker}', [MapController::class, 'destroy'])->name('map.destroy');

// 3. Blogi
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');

Route::middleware('auth')->group(function () {
    Route::get('/blog/create', [BlogController::class, 'create'])->name('blog.create');
    Route::post('/blog', [BlogController::class, 'store'])->name('blog.store');
    Route::get('/blog/{post}/edit', [BlogController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{post}', [BlogController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{post}', [BlogController::class, 'destroy'])->name('blog.destroy');
    Route::post('/blog/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

Route::get('/blog/{post}', [BlogController::class, 'show'])->name('blog.show');

// 4. E-pood
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/{productId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{productId}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/session', [CheckoutController::class, 'createSession'])->name('checkout.session');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
Route::post('/stripe/webhook', [CheckoutController::class, 'webhook'])->name('stripe.webhook')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// 5. Raamatud (Vue SPA page — API on /api/books)
Route::get('/books', function () {
    return Inertia::render('Books/Index');
})->name('books.index');