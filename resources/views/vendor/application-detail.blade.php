<x-app-layout>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        
        <!-- Back Button -->
        <a href="{{ route('my-applications') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-6">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Applications
        </a>

        <div class="bg-white rounded-lg shadow-md p-8">
            
            <!-- Header -->
            <div class="mb-8 pb-8 border-b">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            {{ $application->vendor->business_name }}
                        </h1>
                        <p class="text-gray-600 mt-2">
                            {{ $application->vendor->category?->name ?? 'Uncategorized' }} • {{ $application->vendor->address }}
                        </p>
                    </div>
                    
                    <!-- Status Badge -->
                    <div>
                        @if($application->isPending())
                            <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                                ⏳ Pending Review
                            </span>
                        @elseif($application->isApproved())
                            <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                ✓ Approved
                            </span>
                        @elseif($application->isRejected())
                            <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                ✗ Rejected
                            </span>
                        @elseif($application->isWithdrawn())
                            <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">
                                ⊘ Withdrawn
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="mb-8 pb-8 border-b">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Application Timeline</h2>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex flex-col items-center mr-6">
                            <div class="w-3 h-3 bg-orange-500 rounded-full mt-2"></div>
                            <div class="w-0.5 h-12 bg-gray-300 mt-2"></div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Application Submitted</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $application->created_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>

                    @if($application->reviewed_at)
                        <div class="flex items-start">
                            <div class="flex flex-col items-center mr-6">
                                <div class="w-3 h-3 @if($application->isApproved()) bg-green-500 @elseif($application->isRejected()) bg-red-500 @else bg-gray-300 @endif rounded-full mt-2"></div>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    @if($application->isApproved())
                                        Approved by Admin
                                    @elseif($application->isRejected())
                                        Rejected by Admin
                                    @endif
                                </p>
                                <p class="text-xs text-gray-600 mt-1">{{ $application->reviewed_at->format('F j, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Application Details -->
            <div class="mb-8 pb-8 border-b">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Your Statement</h2>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $application->reason }}</p>
            </div>

            <!-- Documents -->
            <div class="mb-8 pb-8 border-b">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Supporting Documents</h2>
                @if($application->documents && count($application->documents) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($application->documents as $document)
                            <div class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-orange-500 transition">
                                <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0015.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                                </svg>
                                <div class="ml-3 flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ basename($document) }}
                                    </p>
                                    <p class="text-xs text-gray-500">Document</p>
                                </div>
                                <a href="{{ \Illuminate\Support\Facades\Storage::disk('s3')->url($document) }}" target="_blank" class="ml-2 text-orange-500 hover:text-orange-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4m4-6h-8m0 0l3 3m-3-3l-3 3" />
                                    </svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600">No documents uploaded</p>
                @endif
            </div>

            <!-- Admin Notes (if reviewed) -->
            @if($application->admin_notes)
                <div class="mb-8 pb-8 border-b">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Admin Review Notes</h2>
                    <div class="p-4 @if($application->isApproved()) bg-green-50 border border-green-200 @elseif($application->isRejected()) bg-red-50 border border-red-200 @else bg-gray-50 border border-gray-200 @endif rounded-lg">
                        <p class="text-sm text-gray-700">{{ $application->admin_notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            @if($application->isPending())
                <div class="flex gap-4">
                    <form method="POST" action="{{ route('application.withdraw', $application) }}" onsubmit="return confirm('Are you sure you want to withdraw this application?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-3 border border-red-300 text-red-700 font-semibold rounded-lg hover:bg-red-50 transition">
                            Withdraw Application
                        </button>
                    </form>
                    <a href="{{ route('place.show', $application->vendor->slug) }}" class="px-6 py-3 bg-orange-500 text-white font-semibold rounded-lg hover:bg-orange-600 transition">
                        View Establishment
                    </a>
                </div>
            @else
                <a href="{{ route('place.show', $application->vendor->slug) }}" class="inline-block px-6 py-3 bg-orange-500 text-white font-semibold rounded-lg hover:bg-orange-600 transition">
                    View Establishment
                </a>
            @endif
        </div>
    </div>
</x-app-layout>
