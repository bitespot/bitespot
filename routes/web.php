<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| BiteSpot — Web Routes (routes/web.php)
|--------------------------------------------------------------------------
|
| Organised into four sections:
|   1. Guest-only routes (login, register)
|   2. Authenticated routes (profile, bookmarks)
|   3. Vendor-only routes (dashboard, menu, media, settings)
|   4. Admin-only routes (panel, approvals, moderation)
|
| The 'role' middleware alias is registered in bootstrap/app.php:
|   $middleware->alias(['role' => \App\Http\Middleware\CheckRole::class]);
|
*/

// ---------------------------------------------------------------------------
// 1. PUBLIC (no auth required)
// ---------------------------------------------------------------------------

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])
     ->name('home');

Route::get('/explore', [\App\Http\Controllers\ExploreController::class, 'index'])
     ->name('explore');

Route::get('/place/{vendor:slug}', [\App\Http\Controllers\EstablishmentController::class, 'show'])
     ->name('place.show');

// ---------------------------------------------------------------------------
// 2. GUEST-ONLY (redirect to home if already logged in)
// ---------------------------------------------------------------------------

Route::middleware('guest')->group(function () {

    // User auth
    Route::get('/login',    [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login',   [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register',[RegisteredUserController::class, 'store']);

    // Vendor registration (also available to guests)
    Route::get('/vendor/register',  [RegisteredUserController::class, 'createVendor'])->name('vendor.register');
    Route::post('/vendor/register', [RegisteredUserController::class, 'storeVendor']);

});

// ---------------------------------------------------------------------------
// 3. AUTHENTICATED — all logged-in users (any role)
// ---------------------------------------------------------------------------

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // User profile (Norman)
    Route::get('/profile',      [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',      [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

});

// ---------------------------------------------------------------------------
// 4. VENDOR-ONLY
// ---------------------------------------------------------------------------

Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\Vendor\DashboardController::class, 'index'])
         ->name('dashboard');

    Route::get('/menu',      [\App\Http\Controllers\Vendor\MenuController::class, 'index'])
         ->name('menu');

    Route::get('/media',     [\App\Http\Controllers\Vendor\MediaController::class, 'index'])
         ->name('media');

    Route::get('/reviews',   [\App\Http\Controllers\Vendor\ReviewsController::class, 'index'])
         ->name('reviews');

    Route::get('/settings',  [\App\Http\Controllers\Vendor\SettingsController::class, 'index'])
         ->name('settings');

});

// ---------------------------------------------------------------------------
// 5. ADMIN-ONLY
// ---------------------------------------------------------------------------

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
         ->name('dashboard');

    // Additional admin sub-routes (Norman)
    Route::get('/vendors',          [\App\Http\Controllers\Admin\VendorApprovalController::class, 'index'])
         ->name('vendors');

    Route::get('/moderation',       [\App\Http\Controllers\Admin\ModerationController::class, 'index'])
         ->name('moderation');

    Route::get('/analytics',        [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])
         ->name('analytics');

});
