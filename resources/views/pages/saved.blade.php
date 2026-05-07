{{-- C:\Software Projects\bitespot\resources\views\pages\saved.blade.php --}}
@extends('layouts.app-no-nav')

@section('content')
@include('components.navbar')

<style>
    .btn-active svg { fill: currentColor; }
</style>

<div class="bg-gray-50 min-h-screen text-gray-900 pt-8 pb-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        
        <div class="mb-6 flex items-center gap-3">
            <a href="/dashboard" class="text-gray-400 hover:text-gray-900 transition-colors">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            </a>
            <h1 class="text-2xl font-bold tracking-tight">Saved Spots</h1>
        </div>

        <div class="space-y-6">
            @forelse ($posts as $post)
                <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" data-post-id="{{ $post->id }}">
                    {{-- Post Header --}}
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

                    {{-- Post Content --}}
                    @if($post->spot_review)
                        <div class="px-4 pb-3">
                            <p class="text-sm">{{ $post->spot_review }}</p>
                        </div>
                    @endif

                    {{-- Post Image --}}
                    @if($post->general_photo)
                        <div class="bg-gray-100 aspect-video w-full overflow-hidden">
                            <img src="{{ Storage::url($post->general_photo) }}" alt="{{ $post->spot_name }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    {{-- Post Actions --}}
                    <div class="p-4 flex items-center gap-6 border-t border-gray-50">
                        @php
                            $isLiked = $post->likes->contains('id', auth()->id());
                            $isSaved = $post->saves->contains('id', auth()->id());
                        @endphp

                        <button class="action-like flex items-center gap-1.5 transition-colors {{ $isLiked ? 'text-orange-500 btn-active' : 'text-gray-400 hover:text-orange-500' }}" data-liked="{{ $isLiked ? 'true' : 'false' }}">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform active:scale-75"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                            <span class="text-sm font-medium like-count">{{ $post->likes->count() }}</span>
                        </button>
                        
                        <button class="action-save flex items-center gap-1.5 transition-colors ml-auto {{ $isSaved ? 'text-gray-900 btn-active' : 'text-gray-400 hover:text-gray-800' }}" data-saved="{{ $isSaved ? 'true' : 'false' }}">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform active:scale-75"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                        </button>
                    </div>
                </article>
            @empty
                {{-- Empty State Placeholder --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">No saved spots yet</h3>
                    <p class="text-sm text-gray-500 mb-6">When you see a post or a vendor you like, tap the bookmark icon to save it here for later.</p>
                    <a href="/dashboard" class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-medium px-6 py-2 rounded-lg transition-colors">
                        Back to Feed
                    </a>
                </div>
            @endforelse
        </div>

    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.action-like').forEach(button => {
        button.addEventListener('click', async function() {
            const article = this.closest('article');
            const postId = article.getAttribute('data-post-id');
            let isLiked = this.getAttribute('data-liked') === 'true';
            let countSpan = this.querySelector('.like-count');
            let currentCount = parseInt(countSpan.innerText);

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
                await fetch(`/bitespots/${postId}/toggle-like`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
            } catch (error) { console.error('Error toggling like:', error); }
        });
    });

    document.querySelectorAll('.action-save').forEach(button => {
        button.addEventListener('click', async function() {
            const article = this.closest('article');
            const postId = article.getAttribute('data-post-id');
            let isSaved = this.getAttribute('data-saved') === 'true';

            if (isSaved) {
                this.setAttribute('data-saved', 'false');
                this.classList.remove('text-gray-900', 'btn-active');
                this.classList.add('text-gray-400');
                article.style.opacity = '0.5'; 
            } else {
                this.setAttribute('data-saved', 'true');
                this.classList.remove('text-gray-400');
                this.classList.add('text-gray-900', 'btn-active');
                article.style.opacity = '1';
            }

            try {
                await fetch(`/bitespots/${postId}/toggle-save`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
            } catch (error) { console.error('Error toggling save:', error); }
        });
    });
</script>
@endsection