// public/js/vendor-media.js — SID_28: Photo upload fetch + preview

// ── DOM refs ──────────────────────────────────────────────────────────────────

const fileInput    = document.getElementById('photo-file-input');
const previewWrap  = document.getElementById('photo-preview-wrap');
const previewImg   = document.getElementById('photo-preview');
const previewName  = document.getElementById('photo-preview-name');
const uploadBtn    = document.getElementById('photo-upload-btn');
const uploadErr    = document.getElementById('photo-upload-error');
const photosGrid   = document.getElementById('photos-container');

// ── Helpers ───────────────────────────────────────────────────────────────────

function _esc(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// ── State ─────────────────────────────────────────────────────────────────────

let _previewObjectUrl = null;
const _cache = new Map(); // id → photo

// ── Render ────────────────────────────────────────────────────────────────────

function _renderPhoto(photo) {
    return `
        <div data-photo-id="${photo.id}" class="relative group rounded-xl overflow-hidden aspect-square bg-gray-100">
            <img src="${_esc(photo.url ?? photo.path ?? photo.photo_url ?? '')}"
                 alt="Establishment photo"
                 class="w-full h-full object-cover">
            <button type="button" data-action="delete-photo" data-id="${photo.id}"
                    class="absolute top-2 right-2 w-7 h-7 flex items-center justify-center
                           bg-black/60 text-white rounded-full opacity-0 group-hover:opacity-100
                           transition hover:bg-red-600" aria-label="Delete photo">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>`;
}

function _renderGrid(photos) {
    if (!photos.length) {
        photosGrid.innerHTML =
            '<p class="col-span-full text-sm text-gray-400 py-6 text-center">No photos yet. Upload your first one!</p>';
        return;
    }
    photosGrid.innerHTML = photos.map(_renderPhoto).join('');
}

// ── Load photos ───────────────────────────────────────────────────────────────

function loadPhotos() {
    photosGrid.innerHTML =
        '<p class="col-span-full text-sm text-gray-400 py-6 text-center animate-pulse">Loading photos…</p>';

    apiFetch('/api/vendor/photos')
        .then(res => {
            const photos = Array.isArray(res) ? res : (Array.isArray(res.data) ? res.data : []);
            _cache.clear();
            photos.forEach(p => _cache.set(p.id, p));
            _renderGrid(photos);
        })
        .catch(err => {
            console.error('[SID_28] Photos load failed:', err);
            photosGrid.innerHTML =
                '<p class="col-span-full text-sm text-red-400 py-6 text-center">Could not load photos.</p>';
        });
}

// ── File input → instant preview ──────────────────────────────────────────────

fileInput.addEventListener('change', () => {
    const file = fileInput.files[0];
    uploadErr.textContent = '';

    if (!file) {
        previewWrap.classList.add('hidden');
        uploadBtn.disabled = true;
        return;
    }

    if (file.size > 5 * 1024 * 1024) {
        uploadErr.textContent = 'File is too large. Max size is 5 MB.';
        fileInput.value = '';
        previewWrap.classList.add('hidden');
        uploadBtn.disabled = true;
        return;
    }

    if (_previewObjectUrl) URL.revokeObjectURL(_previewObjectUrl);
    _previewObjectUrl = URL.createObjectURL(file);

    previewImg.src          = _previewObjectUrl;
    previewName.textContent = file.name;
    previewWrap.classList.remove('hidden');
    uploadBtn.disabled = false;
});

// ── Upload ────────────────────────────────────────────────────────────────────

uploadBtn.addEventListener('click', async () => {
    const file = fileInput.files[0];
    if (!file) return;

    uploadErr.textContent   = '';
    uploadBtn.disabled      = true;
    uploadBtn.textContent   = 'Uploading…';

    const formData = new FormData();
    formData.append('photo', file);

    try {
        const res  = await apiFetch('/api/vendor/photos', { method: 'POST', body: formData });
        const saved = (!res.data || Array.isArray(res.data)) ? null : res.data;

        // Optimistic: use the object URL if the API didn't return a real URL
        const optimistic = {
            id:  saved?.id ?? Date.now(),
            url: saved?.url ?? saved?.path ?? _previewObjectUrl,
        };
        _cache.set(optimistic.id, optimistic);
        _renderGrid([..._cache.values()]);

        // Reset upload area
        fileInput.value       = '';
        previewWrap.classList.add('hidden');
        previewImg.src        = '';
        previewName.textContent = '';
        showToast('Photo uploaded.', 'success');
    } catch (err) {
        uploadErr.textContent = err.data?.message ?? 'Upload failed. Please try again.';
        console.error('[SID_28] Photo upload failed:', err);
    } finally {
        uploadBtn.disabled    = false;
        uploadBtn.textContent = 'Upload Photo';
    }
});

// ── Delete ────────────────────────────────────────────────────────────────────

async function _deletePhoto(id) {
    if (!confirm('Remove this photo?')) return;

    try {
        await apiFetch(`/api/vendor/photos/${id}`, { method: 'DELETE' });
        _cache.delete(Number(id));
        _renderGrid([..._cache.values()]);
        showToast('Photo removed.', 'success');
    } catch (err) {
        showToast(err.data?.message ?? 'Could not remove photo.', 'error');
        console.error('[SID_28] Photo delete failed:', err);
    }
}

// ── Event delegation ──────────────────────────────────────────────────────────

photosGrid.addEventListener('click', e => {
    const btn = e.target.closest('[data-action="delete-photo"]');
    if (btn) _deletePhoto(btn.dataset.id);
});

// ── Init ──────────────────────────────────────────────────────────────────────

loadPhotos();
