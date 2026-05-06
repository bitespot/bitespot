// public/js/vendor-settings.js — SID_30: Vendor settings form fetch

// ── DOM refs ──────────────────────────────────────────────────────────────────

const settingsForm    = document.getElementById('vendor-settings-form');
const businessNameEl  = document.getElementById('settings-business-name');
const descriptionEl   = document.getElementById('settings-description');
const phoneEl         = document.getElementById('settings-phone');
const emailEl         = document.getElementById('settings-email');
const addressEl       = document.getElementById('settings-address');
const cityEl          = document.getElementById('settings-city');
const provinceEl      = document.getElementById('settings-province');
const websiteEl       = document.getElementById('settings-website');
const nameErrEl       = document.getElementById('settings-business-name-error');
const formErrEl       = document.getElementById('settings-form-error');
const submitBtn       = document.getElementById('settings-submit-btn');

// ── Load profile ──────────────────────────────────────────────────────────────

function _populate(vendor) {
    businessNameEl.value = vendor.business_name ?? '';
    descriptionEl.value  = vendor.description   ?? '';
    phoneEl.value        = vendor.phone         ?? '';
    emailEl.value        = vendor.email         ?? '';
    addressEl.value      = vendor.address       ?? '';
    cityEl.value         = vendor.city          ?? '';
    provinceEl.value     = vendor.province      ?? '';
    websiteEl.value      = vendor.website       ?? '';
}

apiFetch('/api/vendor/profile')
    .then(res => {
        const vendor = (!res.data || Array.isArray(res.data)) ? {} : res.data;
        _populate(vendor);
    })
    .catch(err => {
        console.error('[SID_30] Profile load failed:', err);
        showToast('Could not load profile data.', 'error');
    });

// ── Validation ────────────────────────────────────────────────────────────────

function _validate() {
    nameErrEl.textContent = '';
    formErrEl.textContent = '';

    if (!businessNameEl.value.trim()) {
        nameErrEl.textContent = 'Business name is required.';
        return false;
    }
    return true;
}

// ── Submit ────────────────────────────────────────────────────────────────────

settingsForm.addEventListener('submit', async e => {
    e.preventDefault();
    if (!_validate()) return;

    submitBtn.disabled    = true;
    submitBtn.textContent = 'Saving…';

    const body = {
        business_name: businessNameEl.value.trim(),
        description:   descriptionEl.value.trim()  || null,
        phone:         phoneEl.value.trim()         || null,
        email:         emailEl.value.trim()         || null,
        address:       addressEl.value.trim()       || null,
        city:          cityEl.value.trim()          || null,
        province:      provinceEl.value.trim()      || null,
        website:       websiteEl.value.trim()       || null,
    };

    try {
        await apiFetch('/api/vendor/profile', { method: 'PUT', body });
        showToast('Settings saved.', 'success');
    } catch (err) {
        const errs  = err.data?.errors ?? {};
        const first = Object.values(errs)[0]?.[0];
        const msg   = first ?? err.data?.message ?? 'Could not save settings.';
        if (errs.business_name) nameErrEl.textContent = errs.business_name[0];
        else formErrEl.textContent = msg;
        console.error('[SID_30] Settings save failed:', err);
    } finally {
        submitBtn.disabled    = false;
        submitBtn.textContent = 'Save Changes';
    }
});
