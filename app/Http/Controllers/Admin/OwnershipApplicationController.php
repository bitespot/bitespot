<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorOwnershipApplication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OwnershipApplicationController extends Controller
{
    /**
     * List all pending ownership applications.
     */
    public function index(): View
    {
        $pendingApplications = VendorOwnershipApplication::where('status', 'pending')
            ->with(['user', 'vendor', 'vendor.category'])
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        $approvedApplications = VendorOwnershipApplication::where('status', 'approved')
            ->with(['user', 'vendor', 'reviewedBy'])
            ->orderByDesc('reviewed_at')
            ->paginate(10, ['*'], 'approved_page');

        $rejectedApplications = VendorOwnershipApplication::where('status', 'rejected')
            ->with(['user', 'vendor', 'reviewedBy'])
            ->orderByDesc('reviewed_at')
            ->paginate(10, ['*'], 'rejected_page');

        return view('admin.ownership-applications', compact(
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications'
        ));
    }

    /**
     * Show application details with documents.
     */
    public function show(VendorOwnershipApplication $application): View
    {
        $application->load(['user', 'vendor', 'vendor.category', 'reviewedBy']);

        return view('admin.ownership-application-detail', compact('application'));
    }

    /**
     * Approve an ownership application.
     */
    public function approve(Request $request, VendorOwnershipApplication $application): RedirectResponse
    {
        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Only pending applications can be approved
        if (!$application->isPending()) {
            return back()->with('error', 'Only pending applications can be approved.');
        }

        // Update application
        $application->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Transfer vendor ownership to the applicant
        $application->vendor->update([
            'user_id' => $application->user_id,
            'status' => 'approved', // Ensure vendor is also approved
        ]);

        // Update user role to vendor if not already
        $application->user->update([
            'role' => 'vendor',
        ]);

        return redirect()->route('admin.ownership-applications')
            ->with('success', 'Ownership approved! Vendor now has full control.');
    }

    /**
     * Reject an ownership application.
     */
    public function reject(Request $request, VendorOwnershipApplication $application): RedirectResponse
    {
        $request->validate([
            'admin_notes' => ['required', 'string', 'max:1000'],
        ]);

        // Only pending applications can be rejected
        if (!$application->isPending()) {
            return back()->with('error', 'Only pending applications can be rejected.');
        }

        $application->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.ownership-applications')
            ->with('success', 'Application rejected.');
    }

    /**
     * Revoke vendor ownership (admin action).
     */
    public function revoke(Request $request, VendorOwnershipApplication $application): RedirectResponse
    {
        $request->validate([
            'admin_notes' => ['required', 'string', 'max:1000'],
        ]);

        // Only approved applications can be revoked
        if (!$application->isApproved()) {
            return back()->with('error', 'Only approved applications can be revoked.');
        }

        $application->update([
            'status' => 'rejected', // Change status to rejected to indicate revocation
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Revert ownership to original owner or suspend the vendor
        $application->vendor->update([
            'status' => 'suspended',
            'rejection_reason' => 'Ownership revoked by admin: ' . $request->admin_notes,
        ]);

        return redirect()->route('admin.ownership-applications')
            ->with('success', 'Ownership revoked and vendor suspended.');
    }
}
