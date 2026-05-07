@extends('layouts.app-no-nav')

@section('content')

{{-- NAVBAR --}}
@include('components.navbar')

@if(auth()->check() && auth()->user()->isVendor())

{{-- DASHBOARD ROOT — toast and modals live inside here so --orange CSS var is in scope --}}
<div class="vd-root" id="vd-root">

{{-- TOAST --}}
<div class="vd-toast" id="vd-toast">
    <span class="vd-toast-dot"></span>
    <span id="vd-toast-msg"></span>
</div>

{{-- MENU ITEM MODAL (SID_27) --}}
<div class="vd-modal-backdrop" id="menu-modal">
    <div class="vd-modal">
        <div class="vd-modal-header">
            <span class="vd-modal-title" id="menu-modal-title">Add Menu Item</span>
            <button class="vd-icon-btn" onclick="closeMenuModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="vd-modal-body">
            <input type="hidden" id="menu-item-id">
            <div class="vd-field">
                <label>Item Photo</label>
                <div class="vd-upload-zone" id="menu-photo-zone" onclick="document.getElementById('menu-photo-file').click()">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 0.4rem;color:#a8a29e"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    <p style="font-size:.82rem;color:#a8a29e;">Click to upload (max 5 MB)</p>
                    <input type="file" id="menu-photo-file" accept="image/*" style="display:none" onchange="handleMenuPhotoPreview(this)">
                </div>
                <img id="menu-photo-preview" src="" alt="" style="display:none;width:80px;height:80px;border-radius:9px;object-fit:cover;margin-top:0.5rem;border:1px solid #ebe9e4;">
            </div>
            <div class="vd-field">
                <label>Item Name</label>
                <input type="text" id="menu-name" placeholder="e.g. Spicy Tonkotsu Ramen">
            </div>
            <div class="vd-field">
                <label>Description</label>
                <textarea id="menu-desc" rows="2" placeholder="Short description…" style="resize:vertical;"></textarea>
            </div>
            <div class="vd-field-row">
                <div class="vd-field">
                    <label>Price (₱)</label>
                    <input type="number" id="menu-price" placeholder="0.00" min="0" step="0.01">
                </div>
                <div class="vd-field">
                    <label>Category</label>
                    <select id="menu-category">
                        <option value="Starters">Starters</option>
                        <option value="Mains" selected>Mains</option>
                        <option value="Desserts">Desserts</option>
                        <option value="Drinks">Drinks</option>
                        <option value="Sides">Sides</option>
                    </select>
                </div>
            </div>
            <div class="vd-field">
                <label>Status</label>
                <select id="menu-status">
                    <option value="Active">Active</option>
                    <option value="Sold Out">Sold Out</option>
                    <option value="Hidden">Hidden</option>
                </select>
            </div>
        </div>
        <div class="vd-modal-footer">
            <button class="vd-btn vd-btn-ghost" onclick="closeMenuModal()">Cancel</button>
            <button class="vd-btn vd-btn-primary" onclick="saveMenuItem()">Save Item</button>
        </div>
    </div>
</div>

{{-- PROMO MODAL (SID_29) --}}
<div class="vd-modal-backdrop" id="promo-modal">
    <div class="vd-modal">
        <div class="vd-modal-header">
            <span class="vd-modal-title">Create Promotion</span>
            <button class="vd-icon-btn" onclick="closePromoModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="vd-modal-body">
            <div class="vd-field">
                <label>Promotion Title</label>
                <input type="text" id="promo-title" placeholder="e.g. Weekend Fiesta Deal">
            </div>
            <div class="vd-field">
                <label>Description</label>
                <textarea id="promo-desc" rows="2" placeholder="Describe the offer…" style="resize:vertical;"></textarea>
            </div>
            <div class="vd-field-row">
                <div class="vd-field">
                    <label>Discount %</label>
                    <input type="number" id="promo-discount" placeholder="20" min="1" max="100">
                </div>
                <div class="vd-field">
                    <label>Valid Until</label>
                    <input type="date" id="promo-valid-until">
                </div>
            </div>
        </div>
        <div class="vd-modal-footer">
            <button class="vd-btn vd-btn-ghost" onclick="closePromoModal()">Cancel</button>
            <button class="vd-btn vd-btn-primary" onclick="savePromo()">Publish Promo</button>
        </div>
    </div>
</div>

{{-- Delete Establishment Confirmation --}}
<div class="vd-modal-backdrop" id="delete-modal">
    <div class="vd-modal" style="max-width:420px;">
        <div class="vd-modal-header" style="border-bottom:1px solid #fee2e2;background:#fff5f5;">
            <span class="vd-modal-title" style="color:#dc2626;">Delete Establishment</span>
            <button class="vd-icon-btn" onclick="closeDeleteModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="vd-modal-body" style="padding:1.5rem;">
            <div style="display:flex;gap:1rem;align-items:flex-start;">
                <div style="flex-shrink:0;width:40px;height:40px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div>
                    <div style="font-weight:600;font-size:0.95rem;color:#1a1612;margin-bottom:0.4rem;">Are you sure?</div>
                    <div style="font-size:0.85rem;color:#78716c;line-height:1.5;">This will permanently delete <strong id="delete-modal-name" style="color:#1a1612;">this establishment</strong> and all its data — menu items, photos, reviews, and promotions. This cannot be undone.</div>
                </div>
            </div>
        </div>
        <div class="vd-modal-footer">
            <button class="vd-btn vd-btn-ghost" onclick="closeDeleteModal()">Cancel</button>
            <button class="vd-btn" id="delete-confirm-btn" onclick="confirmDeleteEstablishment()" style="background:#dc2626;color:#fff;border:none;">
                Delete
            </button>
        </div>
    </div>
</div>

    {{-- ── SIDEBAR ─────────────────────────────────── --}}
    <aside class="vd-sidebar">
        <div class="vd-sidebar-header">
            <div class="vd-sidebar-biz-name">{{ $vendor->business_name }}</div>
            <span class="vd-sidebar-biz-badge">Vendor</span>
        </div>

        <nav class="vd-nav" id="vd-nav">
            <div class="vd-nav-section-label">Dashboard</div>

            <button class="vd-nav-btn active" data-tab="overview" onclick="switchTab('overview', this)">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                <span class="label">Overview</span>
            </button>

            <button class="vd-nav-btn" data-tab="reviews" onclick="switchTab('reviews', this)">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span class="label">Reviews</span>
            </button>

            <div class="vd-nav-section-label">Manage</div>

            <button class="vd-nav-btn" data-tab="menu" onclick="switchTab('menu', this)">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 2-2.3 2.3a3 3 0 0 0 0 4.2l1.8 1.8a3 3 0 0 0 4.2 0L22 8"/><path d="M15 15 3.3 3.3a4.2 4.2 0 0 0 0 6l7.3 7.3c2.7 2.7 6 2.7 6 0L15 15Z"/><path d="m14 14 6 6"/></svg>
                <span class="label">Menu Items</span>
            </button>

            <button class="vd-nav-btn" data-tab="media" onclick="switchTab('media', this)">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                <span class="label">Photos & Promos</span>
            </button>

            <div class="vd-nav-section-label">Account</div>

            <button class="vd-nav-btn" data-tab="settings" onclick="switchTab('settings', this)">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                <span class="label">Settings</span>
            </button>
        </nav>

        <div class="vd-sidebar-footer">
            <a href="/vendor-dashboard"
               style="display:flex;align-items:center;gap:6px;color:#a8a29e;font-size:0.78rem;text-decoration:none;margin-bottom:0.5rem;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                All Establishments
            </a>
            BiteSpot Vendor Portal
        </div>
    </aside>

    {{-- ── MAIN ─────────────────────────────────────── --}}
    <div class="vd-main">

        {{-- Top bar --}}
        <div class="vd-topbar">
            <div class="vd-topbar-title" id="vd-page-title">Overview</div>
            <div class="vd-topbar-actions" id="vd-topbar-actions">
                {{-- dynamically swapped by JS --}}
            </div>
        </div>

        <div class="vd-content">

            {{-- ═══════ OVERVIEW TAB (SID_25 + SID_26 preview) ═══════ --}}
            <div id="tab-overview">

                {{-- KPI Cards --}}
                <div class="vd-kpi-grid">

                    <div class="vd-kpi vd-kpi--views">
                        <div class="vd-kpi-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </div>
                        <div class="vd-kpi-label">Total Views</div>
                        <div class="vd-kpi-value" id="kpi-views">
                            <div class="vd-kpi-skeleton"></div>
                        </div>
                        <span class="vd-kpi-delta flat" id="kpi-views-delta">—</span>
                    </div>

                    <div class="vd-kpi vd-kpi--rating">
                        <div class="vd-kpi-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        </div>
                        <div class="vd-kpi-label">Average Rating</div>
                        <div class="vd-kpi-value" id="kpi-rating">
                            <div class="vd-kpi-skeleton"></div>
                        </div>
                        <span class="vd-kpi-delta flat" id="kpi-rating-delta">—</span>
                    </div>

                    <div class="vd-kpi vd-kpi--menu">
                        <div class="vd-kpi-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 2-2.3 2.3a3 3 0 0 0 0 4.2l1.8 1.8a3 3 0 0 0 4.2 0L22 8"/><path d="M15 15 3.3 3.3a4.2 4.2 0 0 0 0 6l7.3 7.3c2.7 2.7 6 2.7 6 0L15 15Z"/></svg>
                        </div>
                        <div class="vd-kpi-label">Menu Items</div>
                        <div class="vd-kpi-value" id="kpi-menu">
                            <div class="vd-kpi-skeleton"></div>
                        </div>
                        <span class="vd-kpi-delta flat" id="kpi-menu-delta">—</span>
                    </div>
                </div>

                {{-- Recent Reviews preview --}}
                <div class="vd-card">
                    <div class="vd-card-header">
                        <span class="vd-card-title">Recent Reviews</span>
                        <button class="vd-btn vd-btn-ghost" style="font-size:0.8rem;padding:0.35rem 0.8rem;" onclick="switchTab('reviews', document.querySelector('[data-tab=reviews]'))">
                            View all
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                    <div class="vd-review-list" id="overview-reviews">
                        <div class="vd-kpi-skeleton" style="height:70px;border-radius:10px;width:100%;"></div>
                        <div class="vd-kpi-skeleton" style="height:70px;border-radius:10px;width:100%;"></div>
                    </div>
                </div>
            </div>

            {{-- ═══════ REVIEWS TAB (SID_26) ═══════ --}}
            <div id="tab-reviews" style="display:none;">
                <div class="vd-card">
                    <div class="vd-card-header">
                        <span class="vd-card-title">Customer Reviews</span>
                        <span style="font-size:0.78rem;color:#a8a29e;" id="reviews-count">Loading…</span>
                    </div>
                    <div class="vd-review-list" id="all-reviews">
                        <div class="vd-kpi-skeleton" style="height:80px;border-radius:10px;width:100%;"></div>
                        <div class="vd-kpi-skeleton" style="height:80px;border-radius:10px;width:100%;"></div>
                        <div class="vd-kpi-skeleton" style="height:80px;border-radius:10px;width:100%;"></div>
                    </div>
                </div>
            </div>

            {{-- ═══════ MENU TAB (SID_27) ═══════ --}}
            <div id="tab-menu" style="display:none;">
                <div class="vd-card" style="overflow:hidden;">
                    <div class="vd-card-header">
                        <span class="vd-card-title">Menu Items</span>
                        <span style="font-size:0.78rem;color:#a8a29e;" id="menu-count">—</span>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="vd-menu-table" id="menu-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th style="text-align:right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="menu-tbody">
                                <tr><td colspan="5" style="padding:2rem;text-align:center;color:#a8a29e;">Loading…</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ═══════ MEDIA TAB (SID_28 + SID_29) ═══════ --}}
            <div id="tab-media" style="display:none;display:none;">
                <div style="display:flex;flex-direction:column;gap:1.5rem;">

                    {{-- Photos --}}
                    <div class="vd-card">
                        <div class="vd-card-header">
                            <span class="vd-card-title">Establishment Photos</span>
                            <span style="font-size:0.78rem;color:#a8a29e;">Max 5 MB per file</span>
                        </div>
                        <div class="vd-photo-grid" id="photo-grid">
                            <div class="vd-photo-add" onclick="document.getElementById('photo-upload-input').click()">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Add Photo
                            </div>
                        </div>
                        <input type="file" id="photo-upload-input" accept="image/*" multiple style="display:none" onchange="handlePhotoUpload(this)">
                    </div>

                    {{-- Promotions --}}
                    <div class="vd-card">
                        <div class="vd-card-header">
                            <span class="vd-card-title">Promotions</span>
                            <button class="vd-btn vd-btn-primary" style="font-size:0.8rem;padding:0.4rem 0.9rem;" onclick="openPromoModal()">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                New Promo
                            </button>
                        </div>
                        <div class="vd-promo-grid" id="promo-list">
                            <div style="padding:1.5rem;text-align:center;color:#a8a29e;font-size:0.85rem;">Loading…</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════ SETTINGS TAB (SID_30) ═══════ --}}
            <div id="tab-settings" style="display:none;">
                <div class="vd-settings-sections">

                    {{-- Business Info --}}
                    <div class="vd-card">
                        <div class="vd-card-header">
                            <span class="vd-card-title">Business Information</span>
                        </div>
                        <div style="padding:1.4rem;" class="vd-settings-form">
                            <div style="display:flex;flex-direction:column;gap:0.9rem;">
                                <div class="vd-field">
                                    <label>Business Name</label>
                                    <input type="text" id="s-biz-name" placeholder="Your Restaurant Name">
                                </div>
                                <div class="vd-field-row">
                                    <div class="vd-field">
                                        <label>Category</label>
                                        <select id="s-category">
                                            <option>Restaurant</option>
                                            <option>Café</option>
                                            <option>Street Food</option>
                                            <option>Bar & Grill</option>
                                            <option>Bakery</option>
                                            <option>Other</option>
                                        </select>
                                    </div>
                                    <div class="vd-field">
                                        <label>Price Range</label>
                                        <select id="s-price">
                                            <option value="$">$ – Budget</option>
                                            <option value="$$" selected>$$ – Moderate</option>
                                            <option value="$$$">$$$ – Upscale</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="vd-field">
                                    <label>Location / Address</label>
                                    <input type="text" id="s-location" placeholder="e.g. Colon St., Cebu City">
                                </div>
                                <div class="vd-field">
                                    <label>Phone Number</label>
                                    <input type="tel" id="s-phone" placeholder="+63 912 345 6789">
                                </div>
                                <div class="vd-field">
                                    <label>Description</label>
                                    <textarea id="s-desc" rows="3" placeholder="Tell customers about your place…" style="resize:vertical;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Operating Hours --}}
                    <div class="vd-card">
                        <div class="vd-card-header">
                            <span class="vd-card-title">Operating Hours</span>
                        </div>
                        <div style="padding:1.4rem;">
                            <div style="display:flex;flex-direction:column;gap:0.6rem;" id="hours-grid">
                                {{-- rendered by JS --}}
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;justify-content:flex-end;">
                        <button class="vd-btn vd-btn-primary" onclick="saveSettings()" style="padding:0.7rem 1.75rem;font-size:0.9rem;">
                            Save Changes
                        </button>
                    </div>

                    {{-- Danger Zone --}}
                    <div class="vd-card" style="border:1.5px solid #fecaca;">
                        <div class="vd-card-header" style="background:#fff5f5;">
                            <span class="vd-card-title" style="color:#dc2626;">Danger Zone</span>
                        </div>
                        <div style="padding:1.4rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
                            <div>
                                <div style="font-weight:600;font-size:0.9rem;color:#1a1612;margin-bottom:0.2rem;">Delete this establishment</div>
                                <div style="font-size:0.82rem;color:#78716c;">This will permanently remove the establishment and all its data. This action cannot be undone.</div>
                            </div>
                            <button onclick="deleteEstablishment()" style="flex-shrink:0;padding:0.6rem 1.25rem;background:#dc2626;color:#fff;border:none;border-radius:9px;font-weight:600;font-size:0.85rem;cursor:pointer;">
                                Delete Establishment
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /.vd-content --}}
    </div>{{-- /.vd-main --}}
</div>{{-- /.vd-root --}}

<script>
window.VENDOR_API_BASE = '/api/vendor/establishments/{{ $vendor->id }}';
</script>
@vite(['resources/js/vendor-dashboard.js'])

@else
{{-- UNAUTHORIZED --}}
<div style="display:flex;align-items:center;justify-content:center;min-height:calc(100vh - 64px);background:#f5f4f2;padding:1.5rem;">
    <div style="text-align:center;background:#fff;border-radius:18px;padding:3rem 2.5rem;max-width:400px;width:100%;border:1px solid #ebe9e4;box-shadow:0 4px 24px rgba(0,0,0,0.07);">
        <div style="width:56px;height:56px;border-radius:50%;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;color:#dc2626;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <h1 style="font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:#1a1612;margin-bottom:0.5rem;">Access Denied</h1>
        <p style="color:#78716c;font-size:0.9rem;margin-bottom:1.5rem;">You must be a registered vendor to view this dashboard.</p>
        <a href="{{ url('/') }}" style="display:inline-flex;align-items:center;padding:0.65rem 1.5rem;background:linear-gradient(135deg,#ff8800,#ff6a00);color:#fff;border-radius:10px;font-weight:600;text-decoration:none;font-size:0.9rem;">
            Return Home
        </a>
    </div>
</div>
@endif

@endsection