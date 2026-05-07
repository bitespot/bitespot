<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Review;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    // SID_31 - Vendor Approval Workflow

    public function pendingVendors(Request $request): JsonResponse
    {
        $vendors = Vendor::where('status', 'pending')
            ->with(['user', 'category'])
            ->paginate(15);

        return response()->json($vendors);
    }

    public function approveVendor(Request $request, Vendor $vendor): JsonResponse
    {
        if ($vendor->status === 'approved') {
            return response()->json(['message' => 'Vendor is already approved.'], 422);
        }

        $vendor->update(['status' => 'approved']);
        $this->logAdminAction('approved_vendor', 'vendor', $vendor->id);

        return response()->json([
            'message' => 'Vendor approved successfully.',
            'vendor' => $vendor,
        ]);
    }

    public function rejectVendor(Request $request, Vendor $vendor): JsonResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $vendor->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        $this->logAdminAction('rejected_vendor', 'vendor', $vendor->id, $request->rejection_reason);

        return response()->json([
            'message' => 'Vendor rejected successfully.',
            'vendor' => $vendor,
        ]);
    }

    // SID_32 - Review Moderation

    public function removeReview(Request $request, Review $review): JsonResponse
    {
        $vendorId = $review->vendor_id;
        $review->forceDelete();

        $this->updateVendorStats($vendorId);
        $this->logAdminAction('removed_review', 'review', $review->id);

        return response()->json(['message' => 'Review deleted successfully.']);
    }

    // SID_33 - User Banning

    public function banUser(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'ban_reason' => 'required|string|max:500',
        ]);

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot ban admin users.'], 422);
        }

        $user->update(['role' => 'banned']);
        $this->logAdminAction('banned_user', 'user', $user->id, $request->ban_reason);

        return response()->json([
            'message' => 'User banned successfully.',
            'user' => $user,
        ]);
    }

    // SID_34 - Vendor Suspension

    public function suspendVendor(Request $request, Vendor $vendor): JsonResponse
    {
        $request->validate([
            'suspend_reason' => 'required|string|max:500',
        ]);

        if ($vendor->status === 'suspended') {
            return response()->json(['message' => 'Vendor is already suspended.'], 422);
        }

        $vendor->update(['status' => 'suspended']);
        $this->logAdminAction('suspended_vendor', 'vendor', $vendor->id, $request->suspend_reason);

        return response()->json([
            'message' => 'Vendor suspended successfully.',
            'vendor' => $vendor,
        ]);
    }

    // SID_34 - Toggle Featured

    public function toggleFeatured(Request $request, Vendor $vendor): JsonResponse
    {
        $vendor->update(['is_featured' => !$vendor->is_featured]);
        $action = $vendor->is_featured ? 'featured_vendor' : 'unfeatured_vendor';
        $this->logAdminAction($action, 'vendor', $vendor->id);

        return response()->json([
            'message' => 'Vendor featured status toggled.',
            'vendor' => $vendor,
            'is_featured' => $vendor->is_featured,
        ]);
    }

    // Helper Methods

    protected function logAdminAction(
        string $action,
        string $targetType,
        int $targetId,
        string $notes = null
    ): void {
        AdminLog::create([
            'admin_id' => auth()->id(),
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'notes' => $notes,
            'created_at' => now(),
        ]);
    }

    protected function updateVendorStats(int $vendorId): void
    {
        $vendor = Vendor::find($vendorId);
        if ($vendor) {
            $stats = Review::where('vendor_id', $vendorId)
                ->selectRaw('COUNT(*) as count, AVG(rating) as avg')
                ->first();

            $vendor->update([
                'review_count' => $stats->count,
                'avg_rating' => round($stats->avg, 1),
            ]);
        }
    }
}
