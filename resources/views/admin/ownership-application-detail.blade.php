<x-admin-layout>
    <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        
        <!-- Back Button -->
        <a href="{{ route('admin.ownership-applications') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-6">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Applications
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Application Review</h2>
                        @if($application->isPending())
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                ⏳ Pending Review
                            </span>
                        @elseif($application->isApproved())
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                ✓ Approved
                            </span>
                        @elseif($application->isRejected())
                            <span class="px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                ✗ Rejected
                            </span>
                        @endif
                    </div>

                    <!-- Timeline -->
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex flex-col items-center mr-4">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mt-1.5"></div>
                                <div class="w-0.5 h-12 bg-gray-300 mt-2"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Application Submitted</p>
                                <p class="text-xs text-gray-600">{{ $application->created_at->format('F j, Y \a\t g:i A') }}</p>
                            </div>
                        </div>

                        @if($application->reviewed_at)
                            <div class="flex items-start">
                                <div class="flex flex-col items-center mr-4">
                                    <div class="w-3 h-3 @if($application->isApproved()) bg-green-500 @elseif($application->isRejected()) bg-red-500 @else bg-gray-300 @endif rounded-full mt-1.5"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        @if($application->isApproved())
                                            Approved by {{ $application->reviewedBy->name }}
                                        @elseif($application->isRejected())
                                            Rejected by {{ $application->reviewedBy->name }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-600">{{ $application->reviewed_at->format('F j, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Applicant Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Applicant Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Name</p>
                            <p class="text-base font-medium text-gray-900">{{ $application->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="text-base font-medium text-gray-900">{{ $application->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Account Role</p>
                            <p class="text-base font-medium text-gray-900">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    {{ ucfirst($application->user->role) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Member Since</p>
                            <p class="text-base font-medium text-gray-900">{{ $application->user->created_at->format('F j, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Establishment Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Establishment Details</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Business Name</p>
                            <p class="text-base font-medium text-gray-900">{{ $application->vendor->business_name }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Category</p>
                                <p class="text-base font-medium text-gray-900">{{ $application->vendor->category?->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Current Owner</p>
                                <p class="text-base font-medium text-gray-900">{{ $application->vendor->user->name }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Address</p>
                            <p class="text-base font-medium text-gray-900">{{ $application->vendor->address }}</p>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">City</p>
                                <p class="text-base font-medium text-gray-900">{{ $application->vendor->city }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">District</p>
                                <p class="text-base font-medium text-gray-900">{{ $application->vendor->district ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Province</p>
                                <p class="text-base font-medium text-gray-900">{{ $application->vendor->province }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <p class="text-base font-medium">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold @if($application->vendor->status === 'approved') bg-green-100 text-green-800 @elseif($application->vendor->status === 'pending') bg-yellow-100 text-yellow-800 @elseif($application->vendor->status === 'rejected') bg-red-100 text-red-800 @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($application->vendor->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Applicant's Statement -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Applicant's Statement</h3>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $application->reason }}</p>
                </div>

                <!-- Supporting Documents -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Supporting Documents</h3>
                    @if($application->documents && count($application->documents) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($application->documents as $document)
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('s3')->url($document) }}" target="_blank" class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-blue-500 transition">
                                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                        <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0015.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                                    </svg>
                                    <div class="ml-3 flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ basename($document) }}
                                        </p>
                                        <p class="text-xs text-gray-500">View Document</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4m4-6h-8m0 0l3 3m-3-3l-3 3" />
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">No documents provided</p>
                    @endif
                </div>

                <!-- Admin Notes (if reviewed) -->
                @if($application->admin_notes)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Review Notes</h3>
                        <div class="p-4 @if($application->isApproved()) bg-green-50 border border-green-200 @elseif($application->isRejected()) bg-red-50 border border-red-200 @else bg-gray-50 border border-gray-200 @endif rounded-lg">
                            <p class="text-sm text-gray-700">{{ $application->admin_notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar - Actions -->
            <div class="lg:col-span-1">
                @if($application->isPending())
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Decision</h3>
                        
                        <!-- Approve Form -->
                        <form method="POST" action="{{ route('admin.ownership-application.approve', $application) }}" class="mb-4">
                            @csrf
                            <div class="mb-4">
                                <label for="approve_notes" class="block text-sm font-semibold text-gray-700 mb-2">Approval Notes</label>
                                <textarea id="approve_notes" name="admin_notes" rows="3" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:ring-green-500 shadow-sm transition" placeholder="Optional notes for the applicant..."></textarea>
                            </div>
                            <button type="submit" class="w-full px-4 py-3 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition">
                                ✓ Approve Application
                            </button>
                        </form>

                        <!-- Reject Form -->
                        <form method="POST" action="{{ route('admin.ownership-application.reject', $application) }}" class="mb-4">
                            @csrf
                            <div class="mb-4">
                                <label for="reject_notes" class="block text-sm font-semibold text-gray-700 mb-2">Rejection Reason *</label>
                                <textarea id="reject_notes" name="admin_notes" rows="3" required class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm transition" placeholder="Why are you rejecting this application?"></textarea>
                            </div>
                            <button type="submit" class="w-full px-4 py-3 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 transition">
                                ✗ Reject Application
                            </button>
                        </form>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Status</h3>
                        
                        @if($application->isApproved())
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg mb-4">
                                <p class="text-sm font-medium text-green-900">✓ Approved</p>
                                <p class="text-xs text-green-700 mt-1">
                                    by {{ $application->reviewedBy->name }} on {{ $application->reviewed_at->format('F j, Y') }}
                                </p>
                            </div>

                            <form method="POST" action="{{ route('admin.ownership-application.revoke', $application) }}">
                                @csrf
                                @method('POST')
                                <div class="mb-4">
                                    <label for="revoke_notes" class="block text-sm font-semibold text-gray-700 mb-2">Revocation Reason</label>
                                    <textarea id="revoke_notes" name="admin_notes" rows="3" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm transition" placeholder="Why are you revoking this approval?"></textarea>
                                </div>
                                <button type="submit" onclick="return confirm('Are you sure you want to revoke this approval?')" class="w-full px-4 py-3 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 transition">
                                    Revoke Approval
                                </button>
                            </form>
                        @elseif($application->isRejected())
                            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm font-medium text-red-900">✗ Rejected</p>
                                <p class="text-xs text-red-700 mt-1">
                                    by {{ $application->reviewedBy->name }} on {{ $application->reviewed_at->format('F j, Y') }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
