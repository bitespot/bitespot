<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorOwnershipApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OwnershipController extends Controller
{
    public function myApplications(): View
    {
        $applications = VendorOwnershipApplication::where('user_id', Auth::id())
            ->with('vendor')
            ->orderByDesc('created_at')
            ->get();

        return view('vendor.my-applications', compact('applications'));
    }

    public function showApplication(VendorOwnershipApplication $application): View
    {
        abort_unless($application->user_id === Auth::id(), 403);

        $application->load(['vendor', 'vendor.category']);

        return view('vendor.application-detail', compact('application'));
    }

    public function withdrawApplication(VendorOwnershipApplication $application): RedirectResponse
    {
        abort_unless($application->user_id === Auth::id(), 403);

        if (!$application->isPending()) {
            return back()->with('error', 'Only pending applications can be withdrawn.');
        }

        $application->update(['status' => 'withdrawn']);

        return redirect()->route('my-applications')->with('success', 'Application withdrawn.');
    }

    public function submitClaim(Vendor $vendor): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Prevent claiming your own establishment
        if ($vendor->user_id === $user->id) {
            return back()->with('error', 'You already own this establishment.');
        }

        // Prevent duplicate active claims
        $existing = VendorOwnershipApplication::where('user_id', $user->id)
            ->where('vendor_id', $vendor->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return back()->with('error', 'You already have an active claim for this establishment.');
        }

        // Directly transfer ownership
        $vendor->update(['user_id' => $user->id]);
        $user->update(['role' => 'vendor']);

        VendorOwnershipApplication::create([
            'user_id'     => $user->id,
            'vendor_id'   => $vendor->id,
            'status'      => 'approved',
            'reviewed_at' => now(),
        ]);

        return redirect('/vendor-dashboard')
            ->with('success', 'You are now the owner of ' . $vendor->name . '!');
    }
}
