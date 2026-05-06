@extends('layouts.app-no-nav')

@section('content')

@include('components.navbar')

<div class="min-h-screen bg-gray-50 pb-24">

    {{-- ── HEADER ──────────────────────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-br from-orange-500 to-orange-400 pt-12 pb-20 px-4">
        <div class="max-w-3xl mx-auto flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}"
               class="text-white/70 hover:text-white transition shrink-0" aria-label="Back to admin dashboard">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </a>
            <div>
                <p class="text-orange-100 text-xs font-medium uppercase tracking-widest mb-0.5">Admin</p>
                <h1 class="text-white text-2xl font-bold">Vendor Approvals</h1>
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto -mt-10 px-4 space-y-4">

        {{-- ── SID_31: Pending vendor list ─────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700">Pending Applications</h2>
                <span class="text-xs text-gray-400">Approve or reject new vendor registrations</span>
            </div>

            <div id="admin-vendor-list" class="divide-y divide-gray-100">
                <p class="text-sm text-gray-400 py-8 text-center animate-pulse">Loading pending vendors…</p>
            </div>

            <div id="admin-vendor-empty" class="hidden px-5 py-12 text-center">
                <svg class="mx-auto mb-3 text-gray-300" width="40" height="40" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <p class="text-sm font-medium text-gray-500">All caught up!</p>
                <p class="text-xs text-gray-400 mt-1">No pending vendor applications.</p>
            </div>
        </div>

    </div>
</div>

{{-- ── Reject reason modal ──────────────────────────────────────────────────── --}}
<div id="admin-reject-modal"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 space-y-4">
        <h3 class="text-base font-semibold text-gray-800">Reject Vendor Application</h3>
        <form id="admin-reject-form" class="space-y-3">
            <input type="hidden" id="admin-reject-vendor-id">
            <div>
                <label for="admin-reject-reason"
                       class="block text-xs font-medium text-gray-600 mb-1">
                    Reason (optional)
                </label>
                <textarea id="admin-reject-reason" rows="4" maxlength="500"
                          placeholder="Explain why this application was rejected…"
                          class="w-full border border-gray-200 rounded-lg p-3 text-sm resize-none
                                 focus:outline-none focus:ring-2 focus:ring-red-300"></textarea>
                <p id="admin-reject-error" class="text-xs text-red-500 min-h-[1rem] mt-1"></p>
            </div>
            <div class="flex gap-2 pt-1">
                <button id="admin-reject-submit" type="submit"
                        class="px-5 py-2 bg-red-500 text-white text-sm font-medium rounded-full
                               hover:bg-red-600 transition disabled:opacity-50">
                    Reject Vendor
                </button>
                <button id="admin-reject-cancel" type="button"
                        class="px-5 py-2 text-sm text-gray-500 hover:text-gray-700 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/api.js') }}"></script>
<script src="{{ asset('js/ui.js') }}"></script>
<script src="{{ asset('js/admin-vendors.js') }}"></script>

@endsection
