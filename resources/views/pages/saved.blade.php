@extends('layouts.app-no-nav')

@section('content')

<style>
.saved-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #F3F4F6;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    display: flex;
    flex-direction: column;
    transition: box-shadow .15s, transform .15s;
}
.saved-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.10); transform: translateY(-1px); }

.saved-card__img   { width:100%; aspect-ratio:16/9; object-fit:cover; display:block; flex-shrink:0; }
.saved-card__noimg { width:100%; aspect-ratio:16/9; background:linear-gradient(135deg,#fb923c,#fcd34d);
                     display:flex; align-items:center; justify-content:center; font-size:2.4rem; flex-shrink:0; }
.saved-card__body  { padding:12px 14px; flex:1; display:flex; flex-direction:column; }
.saved-card__name  { font-weight:700; font-size:.9rem; color:#111827;
                     white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.saved-card__sub   { font-size:.75rem; color:#6B7280; margin-top:2px; }
.saved-card__city  { font-size:.72rem; color:#9CA3AF; margin-top:1px;
                     white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.saved-card__stars { display:flex; align-items:center; gap:3px; font-size:.78rem;
                     font-weight:600; color:#F59E0B; margin-top:6px; }
.saved-card__foot  { display:flex; align-items:center; justify-content:space-between;
                     padding:10px 14px 12px; gap:8px; }

.btn-view {
    flex:1; display:block; text-align:center; padding:7px 0;
    background:linear-gradient(90deg,#f97316,#fb923c); color:#fff;
    font-size:.78rem; font-weight:600; border-radius:8px;
    text-decoration:none; transition:opacity .15s;
}
.btn-view:hover { opacity:.88; color:#fff; text-decoration:none; }

.btn-unsave {
    flex-shrink:0; width:34px; height:34px; border-radius:8px;
    border:1.5px solid #FED7AA; background:#FFF7ED;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:background .15s, border-color .15s;
    color:#f97316;
}
.btn-unsave:hover { background:#FEE2C0; border-color:#f97316; }
.btn-unsave.busy  { opacity:.5; pointer-events:none; }
</style>

@include('components.navbar')

<div class="min-h-screen bg-gray-50">

    {{-- ── PAGE HEADER ─────────────────────────────────────────────────────── --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 flex items-center gap-3">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="#f97316" stroke="#f97316"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
        </svg>
        <h1 class="text-lg font-bold text-gray-900">Saved Places</h1>
        <span id="saved-count" class="ml-auto text-sm text-gray-500">
            {{ $bookmarks->count() }} {{ $bookmarks->count() === 1 ? 'place' : 'places' }}
        </span>
    </div>

    {{-- ── GRID ─────────────────────────────────────────────────────────────── --}}
    <div class="p-4 sm:p-6 lg:p-8">

        @if($bookmarks->isEmpty())
            {{-- Empty state --}}
            <div id="empty-state" class="flex flex-col items-center justify-center py-24 text-gray-400">
                <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.3" class="mb-4 opacity-30">
                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                </svg>
                <p class="font-semibold text-base text-gray-500">No saved places yet</p>
                <p class="text-sm mt-1">Find places you love and tap the bookmark icon to save them.</p>
                <a href="{{ route('explore') }}"
                   class="mt-5 px-5 py-2.5 bg-orange-500 text-white text-sm font-semibold rounded-full hover:bg-orange-600 transition">
                    Explore Places
                </a>
            </div>
        @else
            <div id="saved-grid"
                 class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
                @foreach($bookmarks as $bookmark)
                    @php $v = $bookmark->vendor; @endphp
                    @if($v)
                    <div class="saved-card" data-saved-card="{{ $v->id }}">
                        {{-- Image --}}
                        <a href="/place/{{ $v->slug }}" tabindex="-1">
                            @if($v->primary_photo)
                                <img src="{{ $v->primary_photo }}" alt="{{ $v->business_name }}"
                                     class="saved-card__img" loading="lazy">
                            @else
                                <div class="saved-card__noimg">🍽️</div>
                            @endif
                        </a>

                        {{-- Body --}}
                        <div class="saved-card__body">
                            <div class="saved-card__name">{{ $v->business_name }}</div>
                            <div class="saved-card__sub">
                                {{ implode(' • ', array_filter([$v->category?->name, $v->price_tier_label])) }}
                            </div>
                            @if($v->city)
                                <div class="saved-card__city">{{ $v->city }}</div>
                            @endif
                            <div class="saved-card__stars">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                                {{ $v->avg_rating !== null ? number_format($v->avg_rating, 1) : 'New' }}
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="saved-card__foot">
                            <a href="/place/{{ $v->slug }}" class="btn-view">View Place</a>
                            <button class="btn-unsave"
                                    data-unsave-btn
                                    data-vendor-id="{{ $v->id }}"
                                    aria-label="Remove from saved"
                                    title="Remove from saved">
                                <svg width="16" height="16" viewBox="0 0 24 24"
                                     fill="currentColor" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>

            {{-- Dynamically shown when last card is removed --}}
            <div id="empty-state" class="hidden flex-col items-center justify-center py-24 text-gray-400">
                <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.3" class="mb-4 opacity-30">
                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
                </svg>
                <p class="font-semibold text-base text-gray-500">No saved places yet</p>
                <p class="text-sm mt-1">Find places you love and tap the bookmark icon to save them.</p>
                <a href="{{ route('explore') }}"
                   class="mt-5 px-5 py-2.5 bg-orange-500 text-white text-sm font-semibold rounded-full hover:bg-orange-600 transition">
                    Explore Places
                </a>
            </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
(function () {
    let count = {{ $bookmarks->count() }};

    function updateCountEl() {
        const el = document.getElementById('saved-count');
        if (el) el.textContent = count + ' ' + (count === 1 ? 'place' : 'places');
    }

    function showEmptyState() {
        const grid  = document.getElementById('saved-grid');
        const empty = document.getElementById('empty-state');
        if (grid)  grid.classList.add('hidden');
        if (empty) {
            empty.classList.remove('hidden');
            empty.classList.add('flex');
        }
    }

    document.querySelectorAll('[data-unsave-btn]').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const vendorId = this.dataset.vendorId;
            const card     = document.querySelector(`[data-saved-card="${vendorId}"]`);

            this.classList.add('busy');

            try {
                await apiFetch(`/api/user/bookmarks/${vendorId}`, { method: 'DELETE' });

                if (card) {
                    card.style.transition = 'opacity .25s, transform .25s';
                    card.style.opacity    = '0';
                    card.style.transform  = 'scale(0.95)';
                    setTimeout(function () {
                        card.remove();
                        count--;
                        updateCountEl();
                        if (count === 0) showEmptyState();
                    }, 260);
                }

                showToast('Removed from saved places.', 'info');
            } catch (err) {
                this.classList.remove('busy');
                showToast(err.data?.message ?? 'Something went wrong. Try again.', 'error');
            }
        });
    });
}());
</script>
@endpush

@endsection
