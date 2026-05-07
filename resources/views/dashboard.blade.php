{{-- C:\Software Projects\bitespot\resources\views\dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Feed — {{ config('app.name', 'BiteSpot') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .btn-active svg { fill: currentColor; }
        </style>
    </head>

    <body class="bg-gray-50 min-h-screen text-gray-900 relative">

        @include('components.navbar')

        <main class="max-w-2xl mx-auto pt-8 pb-20 px-4 sm:px-6 lg:px-8">
            {{--   
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight">Recent Bites</h1>
                <a href="/saved" class="text-sm font-medium text-gray-500 hover:text-gray-900 flex items-center gap-1 transition-colors">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                    Saved Spots
                </a>
            </div>
            --}}
            <div class="space-y-6">
                @forelse ($posts as $post)
                    <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" data-post-id="{{ $post->id }}">
                        <div class="p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-orange-400 to-red-500 text-white flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($post->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <h3 class="font-semibold text-sm">{{ $post->user->name }}</h3>
                                    <p class="text-xs text-gray-500">
                                        {{ $post->created_at->diffForHumans() }} at 
                                        <span class="font-medium text-orange-600">{{ $post->spot_name }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($post->spot_review)
                            <div class="px-4 pb-3">
                                {{--<p class="text-sm">{{ $post->spot_review }}</p>--}}
                                {{-- NEW: Dynamic Star Rating --}}
                                @if(isset($post->spot_rating))
                                    <div class="flex items-center gap-0.5 mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="{{ $i <= $post->spot_rating ? '#facc15' : '#e5e7eb' }}" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        @endfor
                                        <span class="text-xs text-gray-500 ml-1.5 font-medium">{{ $post->spot_rating }}/5</span>
                                    </div>
                                @endif

                                {{-- Existing Review Text --}}
                                @if($post->spot_review)
                                    <p class="text-sm text-gray-800">{{ $post->spot_review }}</p>
                                @endif
                            </div>
                        @endif

                        @if($post->general_photo)
                            <div class="bg-gray-100 aspect-video w-full overflow-hidden">
                                <img src="{{ Storage::url($post->general_photo) }}" alt="{{ $post->spot_name }}" class="w-full h-full object-cover">
                            </div>
                        @endif

                        <div class="p-4 flex items-center gap-6 border-t border-gray-50">
                            @php
                                $isLiked = $post->likes->contains('id', auth()->id());
                                $isSaved = $post->saves->contains('id', auth()->id());
                            @endphp

                            <button class="action-like flex items-center gap-1.5 transition-colors {{ $isLiked ? 'text-orange-500 btn-active' : 'text-gray-400 hover:text-orange-500' }}" data-liked="{{ $isLiked ? 'true' : 'false' }}">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform active:scale-75">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                                <span class="text-sm font-medium like-count">{{ $post->likes->count() }}</span>
                            </button>
                            
                            <button class="action-save flex items-center gap-1.5 transition-colors ml-auto {{ $isSaved ? 'text-gray-900 btn-active' : 'text-gray-400 hover:text-gray-800' }}" data-saved="{{ $isSaved ? 'true' : 'false' }}">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform active:scale-75">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="text-center py-12 bg-white rounded-2xl border border-gray-100">
                        <p class="text-gray-500">No bites posted yet. Be the first!</p>
                    </div>
                @endforelse
            </div>
        </main>

        @auth
            @include('components.add-bitespot')
        @endauth

        {{-- Toast Notification Container --}}
        <div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 pointer-events-none"></div>

        <script>
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // --- Toast Logic ---
            function showToast(message, isError = false) {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');
                
                const bgColor = isError ? 'bg-red-600' : 'bg-gray-900';
                const icon = isError 
                    ? `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`
                    : `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`;

                toast.className = `${bgColor} text-white px-5 py-3 rounded-xl shadow-xl text-sm font-medium transition-all duration-300 translate-y-4 opacity-0 flex items-center gap-2`;
                toast.innerHTML = `${icon} <span>${message}</span>`;
                
                container.appendChild(toast);

                // Animate in
                setTimeout(() => toast.classList.remove('translate-y-4', 'opacity-0'), 10);

                // Remove after 3s
                setTimeout(() => {
                    toast.classList.add('opacity-0');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            // --- Like Logic ---
            document.querySelectorAll('.action-like').forEach(button => {
                button.addEventListener('click', async function() {
                    const article = this.closest('article');
                    const postId = article.getAttribute('data-post-id');
                    let isLiked = this.getAttribute('data-liked') === 'true';
                    let countSpan = this.querySelector('.like-count');
                    let currentCount = parseInt(countSpan.innerText);

                    // Optimistic UI Update
                    if (isLiked) {
                        this.setAttribute('data-liked', 'false');
                        this.classList.remove('text-orange-500', 'btn-active');
                        this.classList.add('text-gray-400');
                        countSpan.innerText = currentCount - 1;
                    } else {
                        this.setAttribute('data-liked', 'true');
                        this.classList.remove('text-gray-400');
                        this.classList.add('text-orange-500', 'btn-active');
                        countSpan.innerText = currentCount + 1;
                    }

                    try {
                        const response = await fetch(`/bitespots/${postId}/toggle-like`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                        });
                        if (!response.ok) throw new Error('Database Error');
                    } catch (error) {
                        // Revert UI if it failed
                        showToast('Error saving like. Try again.', true);
                        this.setAttribute('data-liked', isLiked ? 'true' : 'false');
                        if(isLiked) {
                            this.classList.add('text-orange-500', 'btn-active');
                            this.classList.remove('text-gray-400');
                            countSpan.innerText = currentCount;
                        } else {
                            this.classList.remove('text-orange-500', 'btn-active');
                            this.classList.add('text-gray-400');
                            countSpan.innerText = currentCount;
                        }
                    }
                });
            });

            // --- Save Logic ---
            document.querySelectorAll('.action-save').forEach(button => {
                button.addEventListener('click', async function() {
                    const article = this.closest('article');
                    const postId = article.getAttribute('data-post-id');
                    let isSaved = this.getAttribute('data-saved') === 'true';

                    // Optimistic UI Update
                    if (isSaved) {
                        this.setAttribute('data-saved', 'false');
                        this.classList.remove('text-gray-900', 'btn-active');
                        this.classList.add('text-gray-400');
                    } else {
                        this.setAttribute('data-saved', 'true');
                        this.classList.remove('text-gray-400');
                        this.classList.add('text-gray-900', 'btn-active');
                    }

                    try {
                        const response = await fetch(`/bitespots/${postId}/toggle-save`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                        });
                        
                        if (!response.ok) throw new Error('Database Error');
                        
                        // Show Success Toast
                        showToast(isSaved ? 'Removed from saved spots' : 'Saved to your spots!');

                    } catch (error) {
                        // Revert UI if it failed
                        showToast('Error saving post. Try again.', true);
                        this.setAttribute('data-saved', isSaved ? 'true' : 'false');
                        if(isSaved) {
                            this.classList.add('text-gray-900', 'btn-active');
                            this.classList.remove('text-gray-400');
                        } else {
                            this.classList.remove('text-gray-900', 'btn-active');
                            this.classList.add('text-gray-400');
                        }
                    }
                });
            });
        </script>
    </body>
</html>