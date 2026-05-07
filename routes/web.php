<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/* |--------------------------------------------------------------------------
| BiteSpot — Web Routes (routes/web.php)
|--------------------------------------------------------------------------
*/

// ---------------------------------------------------------------------------
// 1. PUBLIC (no auth required)
// ---------------------------------------------------------------------------

Route::get('/', function () {
     if (auth()->check()) {
          return redirect('/dashboard');
     }
     return app(\App\Http\Controllers\HomeController::class)->index();
})->name('home');

Route::get('/explore', [\App\Http\Controllers\ExploreController::class, 'index'])
     ->name('explore');

Route::get('/place/{vendor:slug}', [\App\Http\Controllers\EstablishmentController::class, 'show'])
     ->name('place.show');

// ---------------------------------------------------------------------------
// 2. GUEST-ONLY (redirect to home if already logged in)
// ---------------------------------------------------------------------------

Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login',   [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register',[RegisteredUserController::class, 'store']);
});

Route::get('/vendor/register',  [RegisteredUserController::class, 'createVendor'])->name('vendor.register');
Route::post('/vendor/register', [RegisteredUserController::class, 'storeVendor']);

// ---------------------------------------------------------------------------
// 3. AUTHENTICATED — all logged-in users (any role)
// ---------------------------------------------------------------------------

Route::middleware('auth')->group(function () {

     Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

     // General dashboard for all authenticated users
     Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

     // ---> NEW: Feed Action Routes <---
     Route::post('/bitespots/{bitespot}/toggle-like', [\App\Http\Controllers\BiteSpotController::class, 'toggleLike'])->name('bitespots.like');
     Route::post('/bitespots/{bitespot}/toggle-save', [\App\Http\Controllers\BiteSpotController::class, 'toggleSave'])->name('bitespots.save');

     // ---> NEW: BiteSpot Creation Routes <---
     Route::get('/bitespot/create', [\App\Http\Controllers\BiteSpotController::class, 'create'])->name('bitespot.create');
     Route::post('/bitespot/store', [\App\Http\Controllers\BiteSpotController::class, 'store'])->name('bitespot.store');

     // User profile
     Route::get('/profile',      [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
     Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
     Route::put('/profile',      [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

     // Ownership applications
     Route::get('/my-applications', [\App\Http\Controllers\Vendor\OwnershipController::class, 'myApplications'])->name('my-applications');
     Route::get('/applications/{application}', [\App\Http\Controllers\Vendor\OwnershipController::class, 'showApplication'])->name('application.show');
     Route::delete('/applications/{application}', [\App\Http\Controllers\Vendor\OwnershipController::class, 'withdrawApplication'])->name('application.withdraw');

     // Claim ownership of existing establishments
     Route::get('/place/{vendor}/claim', [\App\Http\Controllers\Vendor\OwnershipController::class, 'showClaimForm'])->name('place.claim');
     Route::post('/place/{vendor}/claim', [\App\Http\Controllers\Vendor\OwnershipController::class, 'submitClaim'])->name('place.claim.submit');

     // Vendor Setup
     Route::get('/vendor/setup', function () {
          if (!auth()->check()) return redirect('/login');
          if (auth()->user()->role !== 'vendor') return redirect('/');

          $vendor = \App\Models\Vendor::where('user_id', auth()->id())->first();
          if (!$vendor) return redirect('/vendor-dashboard')->with('error', 'Vendor establishment not found.');

          return view('vendor.setup', compact('vendor'));
     })->name('vendor.setup.direct');

     // Vendor Dashboard Access
     Route::get('/vendor-dashboard', function () {
          if (!auth()->user()->isVendor()) abort(403, 'Access Denied. You must be a registered vendor.');
          return view('pages.vendor-establishments');
     });

     Route::get('/vendor-dashboard/{vendor}', function (\App\Models\Vendor $vendor) {
          if (!auth()->user()->isVendor() || $vendor->user_id !== auth()->id()) abort(403, 'Access Denied.');
          return view('pages.vendordashboard', compact('vendor'));
     })->where('vendor', '[0-9]+');

     // General dashboard for all authenticated users
     Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
     Route::get('/saved', [\App\Http\Controllers\DashboardController::class, 'saved'])->name('saved');
});

// ---------------------------------------------------------------------------
// 4. VENDOR-ONLY
// ---------------------------------------------------------------------------

Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {

    Route::get('/setup', function () {
        $user = auth()->user();
        $vendor = \App\Models\Vendor::where('user_id', $user->id)->first();
        
        if (!$vendor) {
            \Log::error('Vendor setup: No vendor record for vendor user', [
                'user_id' => $user->id,
                'user_role' => $user->role,
            ]);
            return redirect('/vendor-dashboard')->with('error', 'Vendor establishment not found. Please contact support.');
        }
        return view('vendor.setup', compact('vendor'));
    })->name('setup');

    Route::get('/dashboard', [\App\Http\Controllers\Vendor\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/menu',      [\App\Http\Controllers\Vendor\MenuController::class, 'index'])->name('menu');
    Route::get('/media',     [\App\Http\Controllers\Vendor\MediaController::class, 'index'])->name('media');
    
    Route::get('/photos', function () {
         $vendor = \App\Models\Vendor::where('user_id', auth()->id())->firstOrFail();
         return view('vendor.upload-photos', ['vendor' => $vendor]);
    })->name('photos');

    Route::post('/photos/cover',   [\App\Http\Controllers\Vendor\PhotosController::class, 'uploadCover'])->name('photos.cover');
    Route::post('/photos/profile', [\App\Http\Controllers\Vendor\PhotosController::class, 'uploadProfile'])->name('photos.profile');
    Route::get('/reviews',   [\App\Http\Controllers\Vendor\ReviewsController::class, 'index'])->name('reviews');
    Route::get('/settings',  [\App\Http\Controllers\Vendor\SettingsController::class, 'index'])->name('settings');
});

// ---------------------------------------------------------------------------
// 5. ADMIN-ONLY
// ---------------------------------------------------------------------------

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/vendors',          [\App\Http\Controllers\Admin\VendorApprovalController::class, 'index'])->name('vendors');
    Route::get('/moderation',       [\App\Http\Controllers\Admin\ModerationController::class, 'index'])->name('moderation');
    Route::get('/analytics',        [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics');

    Route::get('/ownership-applications', [\App\Http\Controllers\Admin\OwnershipApplicationController::class, 'index'])->name('ownership-applications');
    Route::get('/ownership-applications/{application}', [\App\Http\Controllers\Admin\OwnershipApplicationController::class, 'show'])->name('ownership-application.show');
    Route::post('/ownership-applications/{application}/approve', [\App\Http\Controllers\Admin\OwnershipApplicationController::class, 'approve'])->name('ownership-application.approve');
    Route::post('/ownership-applications/{application}/reject', [\App\Http\Controllers\Admin\OwnershipApplicationController::class, 'reject'])->name('ownership-application.reject');
    Route::post('/ownership-applications/{application}/revoke', [\App\Http\Controllers\Admin\OwnershipApplicationController::class, 'revoke'])->name('ownership-application.revoke');
});