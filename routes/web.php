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

    // User auth
    Route::get('/login',    [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login',   [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register',[RegisteredUserController::class, 'store']);

});

// Vendor registration (also available to guests and logged-in users)
Route::get('/vendor/register',  [RegisteredUserController::class, 'createVendor'])->name('vendor.register');
Route::post('/vendor/register', [RegisteredUserController::class, 'storeVendor']);

// ---------------------------------------------------------------------------
// 3. AUTHENTICATED — all logged-in users (any role)
// ---------------------------------------------------------------------------

Route::middleware('auth')->group(function () {

     Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

     // General dashboard for all authenticated users
     Route::get('/dashboard', function () {
          return view('dashboard');
     })->name('dashboard');

     // User profile (Norman)
     Route::get('/profile',      [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
     Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
     Route::put('/profile',      [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

     // Ownership applications
     Route::get('/my-applications', [\App\Http\Controllers\Vendor\OwnershipController::class, 'myApplications'])->name('my-applications');
     Route::get('/applications/{application}', [\App\Http\Controllers\Vendor\OwnershipController::class, 'showApplication'])->name('application.show');
     Route::delete('/applications/{application}', [\App\Http\Controllers\Vendor\OwnershipController::class, 'withdrawApplication'])->name('application.withdraw');

     // Claim ownership of existing establishments (authenticated vendors)
     Route::post('/place/{vendor:slug}/claim', [\App\Http\Controllers\Vendor\OwnershipController::class, 'submitClaim'])->name('place.claim.submit');

     // ===== NEW VENDOR SETUP (accessible right after registration) =====
     Route::get('/vendor/setup', function () {
          // For new vendors immediately after registration
          if (!auth()->check()) {
               return redirect('/login');
          }

          if (auth()->user()->role !== 'vendor') {
               return redirect('/');
          }

          $vendor = \App\Models\Vendor::where('user_id', auth()->id())->first();
          if (!$vendor) {
               return redirect('/vendor-dashboard')->with('error', 'Vendor establishment not found.');
          }

          return view('vendor.setup', compact('vendor'));
     })->middleware('auth')->name('vendor.setup.direct');

     // =======================================================================
     // Vendor: Establishment list + per-establishment management
     // =======================================================================

     // List all establishments owned by this vendor account
     Route::get('/vendor-dashboard', function () {
          if (!auth()->user()->isVendor()) {
               abort(403, 'Access Denied. You must be a registered vendor.');
          }
          return view('pages.vendor-establishments');
     });

     // Manage a specific establishment
     Route::get('/vendor-dashboard/{vendor}', function (\App\Models\Vendor $vendor) {
          if (!auth()->user()->isVendor() || $vendor->user_id !== auth()->id()) {
               abort(403, 'Access Denied.');
          }
          return view('pages.vendordashboard', compact('vendor'));
     })->where('vendor', '[0-9]+');

});

// ---------------------------------------------------------------------------
// 4. VENDOR-ONLY
// ---------------------------------------------------------------------------

Route::middleware(['auth', 'role:vendor'])->prefix('vendor')->name('vendor.')->group(function () {

    Route::get('/setup', function () {
        // For new vendors, this is the first landing page after registration
        $user = auth()->user();
        $vendor = \App\Models\Vendor::where('user_id', $user->id)->first();
        
        if (!$vendor) {
            // If no vendor found but user is authenticated as vendor role,
            // something went wrong. Log it and redirect safely.
            \Log::error('Vendor setup: No vendor record for vendor user', [
                'user_id' => $user->id,
                'user_role' => $user->role,
            ]);
            return redirect('/vendor-dashboard')->with('error', 'Vendor establishment not found. Please contact support.');
        }
        
        return view('vendor.setup', compact('vendor'));
    })->name('setup');

    Route::get('/dashboard', [\App\Http\Controllers\Vendor\DashboardController::class, 'index'])
         ->name('dashboard');

    Route::get('/menu',      [\App\Http\Controllers\Vendor\MenuController::class, 'index'])
         ->name('menu');

    Route::get('/media',     [\App\Http\Controllers\Vendor\MediaController::class, 'index'])
         ->name('media');

    Route::get('/photos',         function () {
         $vendor = \App\Models\Vendor::where('user_id', auth()->id())->firstOrFail();
         return view('vendor.upload-photos', ['vendor' => $vendor]);
    })->name('photos');

    Route::post('/photos/cover',   [\App\Http\Controllers\Vendor\PhotosController::class, 'uploadCover'])
         ->name('photos.cover');

    Route::post('/photos/profile', [\App\Http\Controllers\Vendor\PhotosController::class, 'uploadProfile'])
         ->name('photos.profile');

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

    // Ownership applications review
    Route::get('/ownership-applications', [\App\Http\Controllers\Admin\OwnershipApplicationController::class, 'index'])
         ->name('ownership-applications');
    Route::get('/ownership-applications/{application}', [\App\Http\Controllers\Admin\OwnershipApplicationController::class, 'show'])
         ->name('ownership-application.show');
    Route::post('/ownership-applications/{application}/approve', [\App\Http\Controllers\Admin\OwnershipApplicationController::class, 'approve'])
         ->name('ownership-application.approve');
    Route::post('/ownership-applications/{application}/reject', [\App\Http\Controllers\Admin\OwnershipApplicationController::class, 'reject'])
         ->name('ownership-application.reject');
    Route::post('/ownership-applications/{application}/revoke', [\App\Http\Controllers\Admin\OwnershipApplicationController::class, 'revoke'])
         ->name('ownership-application.revoke');

});

//Route::get('/bitespot/create', [\App\Http\Controllers\BiteSpotController::class, 'create'])->name('bitespot.create');

// ---------------------------------------------------------------------------
// ADD ESTABLISHMENT (any authenticated user, unowned)
// ---------------------------------------------------------------------------

Route::get('/bitespot/create', function () {
    $categories = \App\Models\Category::orderBy('name')->get();
    return view('bitespot.create', compact('categories'));
})->middleware('auth')->name('bitespot.create');

Route::post('/bitespot/store', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'business_name' => 'required|string|max:255',
        'category_id'   => 'required|exists:categories,id',
        'description'   => 'nullable|string|max:1000',
        'lat'           => 'required|numeric',
        'lng'           => 'required|numeric',
        'address'       => 'nullable|string|max:500',
        'city'          => 'nullable|string|max:100',
        'province'      => 'nullable|string|max:100',
        'district'      => 'nullable|string|max:100',
        'price_tier'    => 'required|in:$,$$,$$$',
    ]);

    $vendor = \App\Models\Vendor::create([
        ...$validated,
        'user_id' => null,
        'slug'    => \Illuminate\Support\Str::slug($validated['business_name']) . '-' . \Illuminate\Support\Str::random(6),
        'status'  => 'approved',
    ]);

    return redirect()->route('place.show', $vendor->slug)
        ->with('success', $vendor->business_name . ' has been added! Be the first to claim ownership.');
})->middleware('auth')->name('bitespot.store');