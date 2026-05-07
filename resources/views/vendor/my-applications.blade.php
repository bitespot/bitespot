<x-app-layout>
    <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Ownership Applications</h1>
            <p class="text-gray-600 mt-2">Track the status of your establishment ownership claims</p>
        </div>

        @if($applications->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No applications yet</h3>
                <p class="text-gray-600 mt-2">You haven't submitted any ownership claims yet.</p>
                <a href="{{ route('explore') }}" class="inline-block mt-6 px-6 py-3 bg-orange-500 text-white font-semibold rounded-lg hover:bg-orange-600 transition">
                    Browse Establishments
                </a>
            </div>
        @else
            <!-- Applications List -->
            <div class="space-y-4">
                @foreach($applications as $application)
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Business Info -->
                                <h3 class="text-lg font-semibold text-gray-900">
                                    <a href="{{ route('place.show', $application->vendor->slug) }}" class="hover:text-orange-500 transition">
                                        {{ $application->vendor->business_name }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $application->vendor->category?->name ?? 'Uncategorized' }} • 
                                    {{ $application->vendor->address }}
                                </p>

                                <!-- Status Badge -->
                                <div class="mt-3 flex items-center gap-2">
                                    @if($application->isPending())
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            ⏳ Pending Review
                                        </span>
                                        <span class="text-xs text-gray-600">
                                            Applied {{ $application->created_at->diffForHumans() }}
                                        </span>
                                    @elseif($application->isApproved())
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            ✓ Approved
                                        </span>
                                        <span class="text-xs text-gray-600">
                                            Approved {{ $application->reviewed_at->diffForHumans() }}
                                        </span>
                                    @elseif($application->isRejected())
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                            ✗ Rejected
                                        </span>
                                        <span class="text-xs text-gray-600">
                                            Rejected {{ $application->reviewed_at->diffForHumans() }}
                                        </span>
                                    @elseif($application->isWithdrawn())
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                            ⊘ Withdrawn
                                        </span>
                                    @endif
                                </div>

                                <!-- Admin Notes (if rejected) -->
                                @if($application->isRejected() && $application->admin_notes)
                                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                        <p class="text-sm font-semibold text-red-900">Admin Notes</p>
                                        <p class="text-sm text-red-800 mt-1">{{ $application->admin_notes }}</p>
                                    </div>
                                @endif

                                <!-- Application Details -->
                                <p class="text-sm text-gray-700 mt-4">
                                    <strong>Your Statement:</strong>
                                </p>
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($application->reason, 150) }}</p>
                            </div>

                            <!-- Actions -->
                            <div class="ml-4 flex flex-col gap-2">
                                <a href="{{ route('application.show', $application) }}" class="px-4 py-2 text-sm bg-gray-100 text-gray-900 font-semibold rounded-lg hover:bg-gray-200 transition text-center">
                                    View Details
                                </a>
                                @if($application->isPending())
                                    <form method="POST" action="{{ route('application.withdraw', $application) }}" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full px-4 py-2 text-sm bg-red-50 text-red-700 font-semibold rounded-lg hover:bg-red-100 transition">
                                            Withdraw
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Continue Exploring -->
            <div class="mt-8 p-6 bg-orange-50 border border-orange-200 rounded-lg">
                <p class="text-gray-700">
                    Want to claim ownership of another establishment? 
                    <a href="{{ route('explore') }}" class="font-semibold text-orange-600 hover:text-orange-700 transition">
                        Browse more establishments
                    </a>
                </p>
            </div>
        @endif
    </div>
</x-app-layout>
