<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Vendor;
use App\Models\Review;

// Create admin user
$admin = User::firstOrCreate(
    ['email' => 'admin@test.com'],
    [
        'name' => 'Admin User',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]
);
echo "Admin: {$admin->id} - {$admin->email}\n";

// Create test vendor with pending status
$pendingVendor = Vendor::create([
    'user_id' => 1,
    'category_id' => 1,
    'business_name' => 'Test Pending Vendor',
    'slug' => 'test-pending-' . uniqid(),
    'address' => '123 Test St',
    'city' => 'Tacloban City',
    'status' => 'pending',
]);
echo "Pending Vendor: {$pendingVendor->id}\n";

// Create test user for banning
$testUser = User::firstOrCreate(
    ['email' => 'testuser@test.com'],
    [
        'name' => 'Test User',
        'password' => bcrypt('password'),
        'role' => 'user',
    ]
);
echo "Test User: {$testUser->id} - {$testUser->email}\n";

// Create approved vendor for suspension/featured
$approvedVendor = Vendor::where('status', 'approved')->first();
echo "Approved Vendor: {$approvedVendor->id} - {$approvedVendor->business_name}\n";

// Create review for deletion test
if ($approvedVendor) {
    $review = Review::firstOrCreate(
        ['vendor_id' => $approvedVendor->id, 'user_id' => $testUser->id],
        [
            'rating' => 5,
            'body' => 'Test review for deletion',
        ]
    );
    echo "Test Review: {$review->id}\n";
}
