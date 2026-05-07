{{-- C:\Software Projects\bitespot\resources\views\components\add-bitespot.blade.php --}}
<style>
    .add-bitespot-fab {
        position: fixed;
        right: 2rem;
        bottom: 2rem;
        z-index: 90;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--color-primary) 70%, #ffb347 100%);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        box-shadow: 0 4px 16px rgba(0,0,0,0.18);
        text-decoration: none;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .add-bitespot-fab:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,0.22);
        transform: translateY(-2px);
        color: #fff;
    }
</style>

<a href="{{ route('bitespot.create') }}" class="add-bitespot-fab" title="Post a new BiteSpot">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
</a>