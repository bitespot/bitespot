<x-app-layout>
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        
        <!-- Success Header -->
        <div class="mb-8 p-6 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-green-900">
                        Establishment Registered Successfully!
                    </h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>Your establishment <strong>{{ $vendor->business_name }}</strong> has been registered and is pending admin approval.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left: Setup Steps -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- What's Next Section -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">What's Next?</h2>
                    
                    <!-- Step 1 -->
                    <div class="mb-8 pb-8 border-b">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Add Photos</h3>
                                <p class="mt-2 text-gray-600">Upload cover and profile photos of your establishment. Great photos help attract customers!</p>
                                <a href="{{ route('vendor.photos') }}" class="inline-block mt-3 px-4 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition">
                                    Upload Photos
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="mb-8 pb-8 border-b">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-500 text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Add Menu Items</h3>
                                <p class="mt-2 text-gray-600">Create and organize your menu. Let customers know what delicious offerings you have!</p>
                                <a href="{{ route('vendor.menu') }}" class="inline-block mt-3 px-4 py-2 bg-green-500 text-white font-medium rounded-lg hover:bg-green-600 transition">
                                    Add Menu
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="mb-8 pb-8">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-orange-500 text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M12 8c3.866 0 7-1.343 7-3s-3.134-3-7-3-7 1.343-7 3 3.134 3 7 3zm0 5c3.866 0 7-1.343 7-3s-3.134-3-7-3-7 1.343-7 3 3.134 3 7 3zm0 5c3.866 0 7-1.343 7-3s-3.134-3-7-3-7 1.343-7 3 3.134 3 7 3z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Configure Settings</h3>
                                <p class="mt-2 text-gray-600">Update your business hours, contact information, and other details.</p>
                                <a href="{{ route('vendor.settings') }}" class="inline-block mt-3 px-4 py-2 bg-orange-500 text-white font-medium rounded-lg hover:bg-orange-600 transition">
                                    Edit Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Approval Info -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="font-semibold text-yellow-900">Pending Admin Approval</h3>
                            <p class="mt-1 text-sm text-yellow-800">
                                Your establishment is currently pending approval. Once an admin reviews and approves your listing, you'll be able to fully manage your establishment and accept customer reviews.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Status Card -->
            <div class="space-y-4">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Establishment Info</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs font-semibold text-gray-600 uppercase">Business Name</p>
                            <p class="text-sm text-gray-900">{{ $vendor->business_name }}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-semibold text-gray-600 uppercase">Category</p>
                            <p class="text-sm text-gray-900">{{ $vendor->category?->name ?? 'Uncategorized' }}</p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-gray-600 uppercase">Location</p>
                            <p class="text-sm text-gray-900">
                                {{ $vendor->address }}<br>
                                {{ $vendor->city }}, {{ $vendor->province }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-gray-600 uppercase">Price Tier</p>
                            <p class="text-sm text-gray-900">{{ $vendor->price_tier_label }}</p>
                        </div>

                        <div class="pt-3 border-t">
                            <p class="text-xs font-semibold text-gray-600 uppercase">Status</p>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 mt-1">
                                ⏳ Pending Approval
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('vendor.settings') }}" class="block mt-6 px-4 py-2 text-center border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                        Edit Establishment
                    </a>
                </div>

                <!-- Quick Links -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h3>
                    <div class="space-y-2">
                        <a href="{{ route('vendor.dashboard') }}" class="block px-4 py-2 text-center bg-gray-100 text-gray-900 font-medium rounded-lg hover:bg-gray-200 transition">
                            Go to Dashboard
                        </a>
                        <a href="{{ route('vendor.menu') }}" class="block px-4 py-2 text-center bg-gray-100 text-gray-900 font-medium rounded-lg hover:bg-gray-200 transition">
                            Manage Menu
                        </a>
                        <a href="{{ route('vendor.photos') }}" class="block px-4 py-2 text-center bg-gray-100 text-gray-900 font-medium rounded-lg hover:bg-gray-200 transition">
                            Upload Photos
                        </a>
                        <a href="{{ route('vendor.reviews') }}" class="block px-4 py-2 text-center bg-gray-100 text-gray-900 font-medium rounded-lg hover:bg-gray-200 transition">
                            View Reviews
                        </a>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <h4 class="font-semibold text-blue-900 mb-2">Need Help?</h4>
                    <p class="text-sm text-blue-800 mb-3">
                        Check out our vendor guide for tips on managing your establishment and attracting customers.
                    </p>
                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                        View Vendor Guide →
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
