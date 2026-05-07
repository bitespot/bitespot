<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-orange-50 to-orange-100 py-8 px-4">
        <div class="w-full max-w-3xl bg-white rounded-3xl shadow-2xl p-12 flex flex-col md:flex-row gap-8 md:gap-0 md:items-center">
            <div class="hidden md:flex flex-col items-center justify-center w-1/2 pr-8 border-r border-gray-100">
                <img src="{{ asset('logo.png') }}" alt="BiteSpot Logo" class="w-24 h-24 mb-4">
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Welcome Back!</h2>
                <p class="text-gray-500 text-center">Sign in to discover and share the best food spots in your city.</p>
            </div>
            <div class="w-full md:w-1/2">
                <div class="flex flex-col items-center mb-6 md:hidden">
                    <img src="{{ asset('logo.png') }}" alt="BiteSpot Logo" class="w-16 h-16 mb-2">
                    <h2 class="text-2xl font-bold text-gray-900">Sign in to BiteSpot</h2>
                </div>
                <x-auth-session-status class="mb-4" :status="session('status')" />
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" autocomplete="username" required autofocus value="{{ old('email') }}" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-orange-400 focus:ring-orange-400 shadow-sm">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="mt-1 block w-full rounded-xl border-gray-300 focus:border-orange-400 focus:ring-orange-400 shadow-sm">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-orange-500 shadow-sm focus:ring-orange-400" name="remember">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-sm text-orange-500 hover:underline" href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div>
                        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-base font-bold text-white bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600 hover:to-orange-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-400 transition">Log in</button>
                    </div>
                </form>
                <div class="mt-8 text-center">
                    <span class="text-sm text-gray-600">Don't have an account?</span>
                    <a href="{{ route('register') }}" class="text-orange-500 font-semibold hover:underline ml-1">Sign up</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
