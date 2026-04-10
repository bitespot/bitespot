<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| BiteSpot — API Routes (routes/api.php)
|--------------------------------------------------------------------------
|
| Naming conventions:
|   GET    /api/{resource}          → index  (list)
|   POST   /api/{resource}          → store  (create)
|   GET    /api/{resource}/{id}     → show   (single item)
|   PUT    /api/{resource}/{id}     → update
|   DELETE /api/{resource}/{id}     → destroy
|
*/

// ---------------------------------------------------------------------------
// PUBLIC ROUTES — no auth required
// ---------------------------------------------------------------------------

// Vendor / Establishment listings (Gian)
Route::prefix('vendors')->group(function () {

    // GET /api/vendors?category=cafes&city=Tacloban&price=$&rating=4&q=searchterm
    Route::get('/', [\App\Http\Controllers\Api\VendorController::class, 'index'])
         ->name('api.vendors.index');

    // GET /api/vendors/{id}  — establishment detail page data
    Route::get('/{vendor}', [\App\Http\Controllers\Api\VendorController::class, 'show'])
         ->name('api.vendors.show');

    // GET /api/vendors/{id}/menu
    Route::get('/{vendor}/menu', [\App\Http\Controllers\Api\MenuItemController::class, 'publicIndex'])
         ->name('api.vendors.menu');

    // GET /api/vendors/{id}/reviews
    Route::get('/{vendor}/reviews', [\App\Http\Controllers\Api\ReviewController::class, 'publicIndex'])
         ->name('api.vendors.reviews');

    // GET /api/vendors/{id}/photos
    Route::get('/{vendor}/photos', [\App\Http\Controllers\Api\PhotoController::class, 'publicIndex'])
         ->name('api.vendors.photos');

    // GET /api/vendors/{id}/promotions  — active promos only
    Route::get('/{vendor}/promotions', [\App\Http\Controllers\Api\PromotionController::class, 'publicIndex'])
         ->name('api.vendors.promotions');
});

// Category list (Gian)
Route::get('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index'])
     ->name('api.categories.index');

// Trending spots (Norman)
Route::get('/trending', [\App\Http\Controllers\Api\VendorController::class, 'trending'])
     ->name('api.vendors.trending');

// ---------------------------------------------------------------------------
// AUTHENTICATED ROUTES — require session auth (auth:sanctum or auth middleware)
// ---------------------------------------------------------------------------

Route::middleware('auth:sanctum')->group(function () {

    // -----------------------------------------------------------------------
    // Auth / Current User (Norman)
    // -----------------------------------------------------------------------
    Route::prefix('auth')->group(function () {
        Route::get('/me',     [\App\Http\Controllers\Api\AuthController::class, 'me']);
        Route::post('/logout',[\App\Http\Controllers\Api\AuthController::class, 'logout']);
    });

    // -----------------------------------------------------------------------
    // User profile & activity (Norman)
    // -----------------------------------------------------------------------
    Route::prefix('user')->group(function () {
        Route::get('/profile',           [\App\Http\Controllers\Api\UserController::class, 'show']);
        Route::put('/profile',           [\App\Http\Controllers\Api\UserController::class, 'update']);
        Route::get('/bookmarks',         [\App\Http\Controllers\Api\BookmarkController::class, 'index']);
        Route::post('/bookmarks/{vendor}',   [\App\Http\Controllers\Api\BookmarkController::class, 'store']);
        Route::delete('/bookmarks/{vendor}', [\App\Http\Controllers\Api\BookmarkController::class, 'destroy']);
        Route::get('/reviews',           [\App\Http\Controllers\Api\ReviewController::class, 'userIndex']);
    });

    // -----------------------------------------------------------------------
    // Reviews (Rolf)
    // -----------------------------------------------------------------------
    Route::prefix('reviews')->group(function () {
        Route::post('/',           [\App\Http\Controllers\Api\ReviewController::class, 'store']);
        Route::put('/{review}',    [\App\Http\Controllers\Api\ReviewController::class, 'update']);
        Route::delete('/{review}', [\App\Http\Controllers\Api\ReviewController::class, 'destroy']);
    });

    // -----------------------------------------------------------------------
    // Vendor self-management (Rolf)
    // role:vendor middleware applied per route group
    // -----------------------------------------------------------------------
    Route::middleware('role:vendor')->prefix('vendor')->group(function () {

        // Dashboard KPIs
        Route::get('/dashboard', [\App\Http\Controllers\Api\VendorDashboardController::class, 'index']);

        // Listing info
        Route::get('/profile',  [\App\Http\Controllers\Api\VendorDashboardController::class, 'show']);
        Route::put('/profile',  [\App\Http\Controllers\Api\VendorDashboardController::class, 'update']);

        // Menu management
        Route::get('/menu',          [\App\Http\Controllers\Api\MenuItemController::class, 'index']);
        Route::post('/menu',         [\App\Http\Controllers\Api\MenuItemController::class, 'store']);
        Route::put('/menu/{item}',   [\App\Http\Controllers\Api\MenuItemController::class, 'update']);
        Route::delete('/menu/{item}',[\App\Http\Controllers\Api\MenuItemController::class, 'destroy']);

        // Photo management
        Route::get('/photos',            [\App\Http\Controllers\Api\PhotoController::class, 'index']);
        Route::post('/photos',           [\App\Http\Controllers\Api\PhotoController::class, 'store']);
        Route::delete('/photos/{photo}', [\App\Http\Controllers\Api\PhotoController::class, 'destroy']);

        // Promotions
        Route::get('/promotions',              [\App\Http\Controllers\Api\PromotionController::class, 'index']);
        Route::post('/promotions',             [\App\Http\Controllers\Api\PromotionController::class, 'store']);
        Route::put('/promotions/{promotion}',  [\App\Http\Controllers\Api\PromotionController::class, 'update']);
        Route::delete('/promotions/{promotion}',[\App\Http\Controllers\Api\PromotionController::class, 'destroy']);

        // Review replies
        Route::get('/reviews',                    [\App\Http\Controllers\Api\ReviewController::class, 'vendorIndex']);
        Route::post('/reviews/{review}/reply',    [\App\Http\Controllers\Api\VendorReplyController::class, 'store']);
        Route::put('/reviews/{review}/reply',     [\App\Http\Controllers\Api\VendorReplyController::class, 'update']);
    });

    // -----------------------------------------------------------------------
    // Admin panel (Norman)
    // -----------------------------------------------------------------------
    Route::middleware('role:admin')->prefix('admin')->group(function () {

        // Vendor approval workflow
        Route::get('/vendors/pending',          [\App\Http\Controllers\Api\AdminController::class, 'pendingVendors']);
        Route::post('/vendors/{vendor}/approve',[\App\Http\Controllers\Api\AdminController::class, 'approveVendor']);
        Route::post('/vendors/{vendor}/reject', [\App\Http\Controllers\Api\AdminController::class, 'rejectVendor']);

        // Content moderation
        Route::delete('/reviews/{review}',      [\App\Http\Controllers\Api\AdminController::class, 'removeReview']);
        Route::post('/users/{user}/ban',        [\App\Http\Controllers\Api\AdminController::class, 'banUser']);
        Route::post('/vendors/{vendor}/suspend',[\App\Http\Controllers\Api\AdminController::class, 'suspendVendor']);

        // Platform analytics
        Route::get('/analytics',                [\App\Http\Controllers\Api\AdminController::class, 'analytics']);
    });

    // -----------------------------------------------------------------------
    // Vendor Registration (anyone authenticated can start a vendor application)
    // -----------------------------------------------------------------------
    Route::post('/vendor/register', [\App\Http\Controllers\Api\VendorController::class, 'register'])
         ->name('api.vendor.register');

});
