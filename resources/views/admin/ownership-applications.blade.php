<x-admin-layout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Ownership Applications</h1>
            <p class="text-gray-600 mt-2">Review and manage establishment ownership claims</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Pending Applications</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingApplications->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Approved</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $approvedApplications->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-600 text-sm">Rejected</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $rejectedApplications->total() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button onclick="switchTab('pending')" class="tab-btn active py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                    Pending Applications ({{ $pendingApplications->total() }})
                </button>
                <button onclick="switchTab('approved')" class="tab-btn py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-600 hover:text-gray-900 hover:border-gray-300">
                    Approved ({{ $approvedApplications->total() }})
                </button>
                <button onclick="switchTab('rejected')" class="tab-btn py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-600 hover:text-gray-900 hover:border-gray-300">
                    Rejected ({{ $rejectedApplications->total() }})
                </button>
            </nav>
        </div>

        <!-- Pending Tab -->
        <div id="pending-tab" class="tab-content">
            @if($pendingApplications->isEmpty())
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No pending applications</h3>
                    <p class="text-gray-600 mt-2">All ownership applications have been reviewed.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($pendingApplications as $application)
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <!-- Applicant Info -->
                                    <div class="flex items-center mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $application->user->name }}
                                        </h3>
                                        <span class="ml-3 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            Applicant
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $application->user->email }}</p>

                                    <!-- Establishment Info -->
                                    <div class="mt-4 pt-4 border-t">
                                        <p class="text-sm font-medium text-gray-700">Claiming Ownership Of:</p>
                                        <p class="text-lg font-semibold text-gray-900 mt-1">
                                            {{ $application->vendor->business_name }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{ $application->vendor->category?->name }} • {{ $application->vendor->address }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Current Owner: {{ $application->vendor->user->name }}
                                        </p>
                                    </div>

                                    <!-- Application Reason -->
                                    <div class="mt-4 pt-4 border-t">
                                        <p class="text-sm font-medium text-gray-700">Applicant's Statement:</p>
                                        <p class="text-sm text-gray-700 mt-2">{{ Str::limit($application->reason, 200) }}</p>
                                    </div>

                                    <!-- Documents -->
                                    <div class="mt-4 pt-4 border-t">
                                        <p class="text-sm font-medium text-gray-700 mb-2">
                                            Supporting Documents ({{ count($application->documents ?? []) }})
                                        </p>
                                        @if($application->documents && count($application->documents) > 0)
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($application->documents as $doc)
                                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('s3')->url($doc) }}" target="_blank" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0015.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                                                        </svg>
                                                        {{ basename($doc) }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-sm text-gray-600">No documents provided</p>
                                        @endif
                                    </div>

                                    <!-- Time Info -->
                                    <p class="text-xs text-gray-500 mt-4">
                                        Submitted {{ $application->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="ml-4 flex flex-col gap-2 min-w-max">
                                    <a href="{{ route('admin.ownership-application.show', $application) }}" class="px-4 py-2 text-sm bg-blue-100 text-blue-900 font-semibold rounded-lg hover:bg-blue-200 transition text-center">
                                        Review Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($pendingApplications->hasPages())
                    <div class="mt-8">
                        {{ $pendingApplications->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Approved Tab -->
        <div id="approved-tab" class="tab-content hidden">
            @if($approvedApplications->isEmpty())
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <p class="text-gray-600">No approved applications yet.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($approvedApplications as $application)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $application->user->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600">{{ $application->user->email }}</p>
                                    <p class="text-sm text-gray-700 mt-2">
                                        <strong>Established:</strong> {{ $application->vendor->business_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Approved by {{ $application->reviewedBy->name }} on {{ $application->reviewed_at->format('F j, Y') }}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.ownership-application.show', $application) }}" class="px-4 py-2 text-sm bg-gray-100 text-gray-900 font-semibold rounded-lg hover:bg-gray-200 transition">
                                        View
                                    </a>
                                    <form method="POST" action="{{ route('admin.ownership-application.revoke', $application) }}" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('POST')
                                        <input type="hidden" name="admin_notes" value="Ownership revoked by admin">
                                        <button type="submit" class="px-4 py-2 text-sm bg-red-100 text-red-700 font-semibold rounded-lg hover:bg-red-200 transition">
                                            Revoke
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($approvedApplications->hasPages())
                    <div class="mt-8">
                        {{ $approvedApplications->links('pagination::tailwind', ['paginator' => $approvedApplications, 'path' => request()->url(), 'query' => array_merge(request()->query(), ['approved_page' => '{page}'])]) }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Rejected Tab -->
        <div id="rejected-tab" class="tab-content hidden">
            @if($rejectedApplications->isEmpty())
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <p class="text-gray-600">No rejected applications yet.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($rejectedApplications as $application)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $application->user->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600">{{ $application->user->email }}</p>
                                    <p class="text-sm text-gray-700 mt-2">
                                        <strong>Attempted to Claim:</strong> {{ $application->vendor->business_name }}
                                    </p>
                                    @if($application->admin_notes)
                                        <p class="text-sm text-red-700 mt-2"><strong>Reason:</strong> {{ $application->admin_notes }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">
                                        Rejected by {{ $application->reviewedBy->name }} on {{ $application->reviewed_at->format('F j, Y') }}
                                    </p>
                                </div>
                                <a href="{{ route('admin.ownership-application.show', $application) }}" class="px-4 py-2 text-sm bg-gray-100 text-gray-900 font-semibold rounded-lg hover:bg-gray-200 transition">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($rejectedApplications->hasPages())
                    <div class="mt-8">
                        {{ $rejectedApplications->links('pagination::tailwind', ['paginator' => $rejectedApplications]) }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <script>
        function switchTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('active', 'border-blue-500', 'text-blue-600');
                el.classList.add('border-transparent', 'text-gray-600');
            });

            // Show selected tab
            document.getElementById(tab + '-tab').classList.remove('hidden');
            event.target.classList.add('active', 'border-blue-500', 'text-blue-600');
            event.target.classList.remove('border-transparent', 'text-gray-600');
        }
    </script>
</x-admin-layout>
