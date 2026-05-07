<x-guest-layout>
    <div class="min-h-screen flex bg-gray-50">
        <!-- Branding Panel (Desktop Only) -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-green-500 to-green-400 justify-center items-center p-12 text-center text-white">
            <div class="max-w-lg">
                <img src="{{ asset('logo.png') }}" alt="BiteSpot Logo" class="w-32 h-32 mx-auto mb-8 bg-white p-2 rounded-full shadow-md">
                <h1 class="text-4xl font-extrabold mb-4">Join as a Vendor</h1>
                <p class="text-lg text-green-50">Register your food establishment and start reaching more customers today. List your menu, manage reviews, and grow your business.</p>
            </div>
        </div>

        <!-- Form Panel (Mobile + Desktop) -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl lg:shadow-none p-8 lg:p-2 lg:bg-transparent">
                
                <!-- Mobile Logo (Hidden on Desktop) -->
                <div class="flex flex-col items-center lg:items-start mb-8">
                    <img src="{{ asset('logo.png') }}" alt="BiteSpot Logo" class="w-16 h-16 mb-4 lg:hidden">
                    <h2 class="text-3xl font-extrabold text-gray-900">Register Your Establishment</h2>
                    <p class="text-sm text-gray-500 mt-2">Create an account and add your first establishment.</p>
                </div>

                <form method="POST" action="{{ route('vendor.register') }}" class="space-y-5">
                    @csrf
                    
                    @guest
                    <!-- Account Information Section -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Your Name</label>
                                <input id="name" name="name" type="text" autocomplete="name" required autofocus value="{{ old('name') }}" class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                                <input id="email" name="email" type="email" autocomplete="username" required value="{{ old('email') }}" class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                                <input id="password" name="password" type="password" autocomplete="new-password" required class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                    @endguest
                    
                    <!-- Establishment Information Section -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Establishment Information</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="business_name" class="block text-sm font-semibold text-gray-700 mb-1">Business Name *</label>
                                <input id="business_name" name="business_name" type="text" required value="{{ old('business_name') }}" class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                                <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1">Category *</label>
                                <select id="category_id" name="category_id" required class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" rows="3" class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="owner_name" class="block text-sm font-semibold text-gray-700 mb-1">Owner Name</label>
                                <input id="owner_name" name="owner_name" type="text" value="{{ old('owner_name') }}" class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                                <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
                                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="email_business" class="block text-sm font-semibold text-gray-700 mb-1">Business Email</label>
                                <input id="email_business" name="email_business" type="email" value="{{ old('email_business') }}" class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                                <x-input-error :messages="$errors->get('email_business')" class="mt-2" />
                            </div>

                            <div>
                                <label for="website" class="block text-sm font-semibold text-gray-700 mb-1">Website</label>
                                <input id="website" name="website" type="url" value="{{ old('website') }}" placeholder="https://example.com" class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                                <x-input-error :messages="$errors->get('website')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Location Information Section -->
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Information</h3>
                        
                        <div>
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-1">Address *</label>
                            <input id="address" name="address" type="text" required value="{{ old('address') }}" class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label for="district" class="block text-sm font-semibold text-gray-700 mb-1">District</label>
                                <input id="district" name="district" type="text" value="{{ old('district') }}" class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                                <x-input-error :messages="$errors->get('district')" class="mt-2" />
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-semibold text-gray-700 mb-1">City *</label>
                                <input id="city" name="city" type="text" required value="{{ old('city', 'Tacloban City') }}" class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                                <x-input-error :messages="$errors->get('city')" class="mt-2" />
                            </div>

                            <div>
                                <label for="province" class="block text-sm font-semibold text-gray-700 mb-1">Province *</label>
                                <input id="province" name="province" type="text" required value="{{ old('province', 'Leyte') }}" class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-green-500 focus:ring-green-500 shadow-sm transition">
                                <x-input-error :messages="$errors->get('province')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Business Details Section -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Business Details</h3>
                        
                        <div>
                            <label for="price_tier" class="block text-sm font-semibold text-gray-700 mb-1">Price Tier *</label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="radio" name="price_tier" value="$" required {{ old('price_tier') === '$' ? 'checked' : '' }} class="rounded border-gray-300 text-green-500 focus:ring-green-500">
                                    <span class="ml-3">$ (Budget-friendly)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="price_tier" value="$$" {{ old('price_tier') === '$$' ? 'checked' : '' }} class="rounded border-gray-300 text-green-500 focus:ring-green-500">
                                    <span class="ml-3">$$ (Moderate)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="price_tier" value="$$$" {{ old('price_tier') === '$$$' ? 'checked' : '' }} class="rounded border-gray-300 text-green-500 focus:ring-green-500">
                                    <span class="ml-3">$$$ (Premium)</span>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('price_tier')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Submit and Links -->
                    <div class="flex items-center justify-between pt-6">
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Already have an account?</a>
                        <button type="submit" class="px-6 py-3 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition shadow">
                            Register Establishment
                        </button>
                    </div>
                    
                </form>

                <p class="text-xs text-gray-500 text-center mt-6">By registering, your establishment will be pending admin approval. Once approved, you'll have full access to manage your profile.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
