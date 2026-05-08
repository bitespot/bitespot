<x-guest-layout>
    <div class="min-h-screen flex bg-gray-50">
        <!-- Branding Panel (Desktop Only) -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-orange-500 to-orange-400 justify-center items-center p-12 text-center text-white">
            <div class="max-w-lg">
                <img src="{{ asset('logo.png') }}" alt="BiteSpot Logo" class="w-32 h-32 mx-auto mb-8 bg-white p-2 rounded-full shadow-md">
                <h1 class="text-4xl font-extrabold mb-4">Join BiteSpot</h1>
                <p class="text-lg text-orange-50">Discover and connect with the best local food vendors in your area. Set up your account to get started.</p>
            </div>
        </div>

        <!-- Form Panel (Mobile + Desktop) -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md bg-white rounded-2xl shadow-xl lg:shadow-none p-8 lg:p-2 lg:bg-transparent">
                
                <!-- Mobile Logo (Hidden on Desktop) -->
                <div class="flex flex-col items-center lg:items-start mb-8">
                    <img src="{{ asset('logo.png') }}" alt="BiteSpot Logo" class="w-16 h-16 mb-4 lg:hidden">
                    <h2 class="text-3xl font-extrabold text-gray-900">Create an account</h2>
                    <p class="text-sm text-gray-500 mt-2">Enter your details to register.</p>
                </div>

                <!-- Choose Registration Type -->
                <div class="mb-8">
                    <p class="text-sm font-semibold text-gray-700 mb-3">I want to register as:</p>
                    <div class="grid grid-cols-2 gap-3">
                        
                        <button type="button" onclick="selectRole('user')" class="role-btn-user flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl border-2 border-orange-500 bg-orange-50 text-orange-700 font-bold text-sm transition-all active">
                            <img src="/images/who_uses_bitespot/diners.png" alt="Food Customers" class="w-5 h-5 object-contain" loading="lazy">
                            <span>Customer</span>
                        </button>
                        
                        <button type="button" onclick="selectRole('vendor')" class="role-btn-vendor flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl border-2 border-gray-300 bg-white text-gray-700 font-bold text-sm transition-all hover:border-green-500 hover:bg-green-50 hover:text-green-700">
                            <img src="/images/who_uses_bitespot/vendors.png" alt="Food Vendors" class="w-5 h-5 object-contain opacity-80" loading="lazy">
                            <span>Vendor</span>
                        </button>

                    </div>
                </div> 

                <form method="POST" action="{{ route('register') }}" class="space-y-5" id="register-form">
                    @csrf
                    
                    <input type="hidden" name="role" id="role-input" value="user">

                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                        <input id="name" name="name" type="text" autocomplete="name" required autofocus value="{{ old('name') }}" class="block w-full rounded-lg border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500 shadow-sm transition">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                        <input id="email" name="email" type="email" autocomplete="username" required value="{{ old('email') }}" class="block w-full rounded-lg border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500 shadow-sm transition">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required class="block w-full rounded-lg border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500 shadow-sm transition">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="block w-full rounded-lg border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500 shadow-sm transition">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600 hover:to-orange-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all transform hover:-translate-y-0.5">
                            Create Account
                        </button>
                    </div>

                    <div class="text-center mt-6">
                        <span class="text-sm text-gray-600">Already registered?</span>
                        <a href="{{ route('login') }}" class="text-orange-500 font-semibold hover:text-orange-600 hover:underline ml-1 transition">Sign in instead</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function selectRole(role) {
            document.getElementById('role-input').value = role;
            
            const userBtn = document.querySelector('.role-btn-user');
            const vendorBtn = document.querySelector('.role-btn-vendor');

            if (role === 'user') {
                // Highlight User (Orange) & Reset Vendor (Gray)
                userBtn.className = "role-btn-user flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl border-2 border-orange-500 bg-orange-50 text-orange-700 font-bold text-sm transition-all";
                
                vendorBtn.className = "role-btn-vendor flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl border-2 border-gray-300 bg-white text-gray-700 font-bold text-sm transition-all hover:border-green-500 hover:bg-green-50 hover:text-green-700";
            } else if (role === 'vendor') {
                // Highlight Vendor (Green) & Reset User (Gray)
                vendorBtn.className = "role-btn-vendor flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl border-2 border-green-500 bg-green-50 text-green-700 font-bold text-sm transition-all";
                
                userBtn.className = "role-btn-user flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl border-2 border-gray-300 bg-white text-gray-700 font-bold text-sm transition-all hover:border-orange-500 hover:bg-orange-50 hover:text-orange-700";
            }
        }
    </script>
</x-guest-layout>
