// public/js/ui.js — shared UI helpers: showToast(), renderSkeleton()

// ---------------------------------------------------------------------------
// Toast
// ---------------------------------------------------------------------------

const _TOAST_ICONS = {
    success: `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>`,
    error:   `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
              </svg>`,
    warning: `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>`,
    info:    `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
              </svg>`,
};

const _TOAST_COLORS = {
    success: 'bg-green-600  text-white',
    error:   'bg-red-600    text-white',
    warning: 'bg-amber-500  text-white',
    info:    'bg-blue-600   text-white',
};

function _getToastContainer() {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.setAttribute('aria-live', 'polite');
        container.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-2 items-end pointer-events-none';
        document.body.appendChild(container);
    }
    return container;
}

/**
 * showToast(message, type, duration)
 *
 * Renders a dismissible toast notification in the bottom-right corner.
 *
 * @param {string} message
 * @param {'success'|'error'|'warning'|'info'} [type='info']
 * @param {number} [duration=4000]  ms before auto-dismiss (0 = no auto-dismiss)
 */
function showToast(message, type = 'info', duration = 4000) {
    const container = _getToastContainer();

    const toast = document.createElement('div');
    toast.setAttribute('data-toast', type);
    toast.className = [
        'pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg max-w-sm w-full',
        'transition-all duration-300 translate-x-0 opacity-100',
        _TOAST_COLORS[type] ?? _TOAST_COLORS.info,
    ].join(' ');

    toast.innerHTML = `
        ${_TOAST_ICONS[type] ?? ''}
        <span class="flex-1 text-sm font-medium">${message}</span>
        <button data-action="toast-close" class="ml-2 opacity-70 hover:opacity-100 transition-opacity" aria-label="Dismiss">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    `;

    function dismiss() {
        toast.classList.add('opacity-0', 'translate-x-4');
        toast.addEventListener('transitionend', () => toast.remove(), { once: true });
    }

    toast.querySelector('[data-action="toast-close"]').addEventListener('click', dismiss);
    container.appendChild(toast);

    if (duration > 0) {
        setTimeout(dismiss, duration);
    }
}

// ---------------------------------------------------------------------------
// Skeleton loader
// ---------------------------------------------------------------------------

/**
 * renderSkeleton(container, count)
 *
 * Fills container with animated skeleton placeholder cards.
 * Call again with count=0 (or replace innerHTML) to remove them.
 *
 * @param {HTMLElement} container   the grid/list element to fill
 * @param {number}      [count=4]   number of skeleton cards to render
 */
function renderSkeleton(container, count = 4) {
    const card = `
        <div data-skeleton="card" class="bg-white rounded-xl overflow-hidden shadow animate-pulse">
            <div class="h-40 bg-gray-200"></div>
            <div class="p-4 space-y-2">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                <div class="flex items-center gap-2 pt-1">
                    <div class="h-3 w-3 bg-gray-200 rounded-full"></div>
                    <div class="h-3 bg-gray-200 rounded w-8"></div>
                </div>
            </div>
        </div>
    `;
    container.innerHTML = Array(count).fill(card).join('');
}
