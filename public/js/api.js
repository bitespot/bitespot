// public/js/api.js — shared fetch wrapper for all BiteSpot AJAX calls

const _MUTATING = new Set(['POST', 'PUT', 'PATCH', 'DELETE']);

function _csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

/**
 * apiFetch(url, options)
 *
 * Wraps fetch() with BiteSpot conventions:
 *   - credentials: 'same-origin' on every request
 *   - X-CSRF-TOKEN injected automatically for POST/PUT/PATCH/DELETE
 *   - Plain objects in options.body are JSON-encoded; FormData is passed as-is
 *   - Throws an Error (with .status and .data) on non-2xx responses
 *   - Always resolves to the parsed JSON body: { success, data, message, errors }
 *
 * @param {string} url
 * @param {{ method?: string, body?: any, headers?: object } & RequestInit} [options]
 * @returns {Promise<{ success: boolean, data: any, message: string, errors: object }>}
 */
async function apiFetch(url, options = {}) {
    const method  = (options.method ?? 'GET').toUpperCase();
    const headers = { Accept: 'application/json', ...(options.headers ?? {}) };

    if (_MUTATING.has(method)) {
        headers['X-CSRF-TOKEN'] = _csrfToken();
    }

    let body = options.body ?? null;
    let actualMethod = method;

    // Handle Laravel/PHP limitation: multipart/form-data is only parsed on POST requests.
    // If sending FormData with PUT/PATCH, we spoof the method using _method.
    if (body instanceof FormData && (method === 'PUT' || method === 'PATCH')) {
        body.append('_method', method);
        actualMethod = 'POST';
    }

    if (body !== null && !(body instanceof FormData) && typeof body === 'object') {
        headers['Content-Type'] = 'application/json';
        body = JSON.stringify(body);
    }

    const response = await fetch(url, {
        ...options,
        method: actualMethod,
        headers,
        body,
        credentials: 'same-origin',
    });

    const json = await response.json().catch(() => ({
        success: false,
        message: `HTTP ${response.status} — invalid response body`,
        data:    null,
        errors:  {},
    }));

    if (!response.ok) {
        const err     = new Error(json.message ?? `HTTP ${response.status}`);
        err.status    = response.status;
        err.data      = json;
        throw err;
    }

    return json;
}
