<!-- Floating Add BiteSpot Button and Modal -->
<style>
    .add-bitespot-fab {
        position: fixed;
        right: 2rem;
        bottom: 2rem;
        z-index: 9999;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #ff8800 70%, #ffb347 100%);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        box-shadow: 0 4px 16px rgba(0,0,0,0.18);
        cursor: pointer;
        border: none;
        transition: box-shadow 0.2s;
    }
    .add-bitespot-fab:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,0.22);
        background: linear-gradient(135deg, #ff8800 90%, #ffb347 100%);
    }
    .add-bitespot-modal-backdrop {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.32);
        z-index: 9998;
        display: none;
    }
    .add-bitespot-modal {
        position: fixed;
        right: 2rem;
        bottom: 5.5rem;
        z-index: 10000;
        background: #fff;
        border-radius: 1.2rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        padding: 2rem 1.5rem 1.5rem 1.5rem;
        min-width: 340px;
        max-width: 95vw;
        display: none;
        flex-direction: column;
        gap: 1rem;
        animation: fadeInUp 0.22s cubic-bezier(.4,1.6,.4,1);
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .add-bitespot-modal-header {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .add-bitespot-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #888;
        cursor: pointer;
        margin-left: 1rem;
    }
    .add-bitespot-modal input, .add-bitespot-modal textarea {
        width: 100%;
        padding: 0.6rem 0.9rem;
        border-radius: 0.7rem;
        border: 1.5px solid #ddd;
        font-size: 1rem;
        margin-bottom: 0.7rem;
        background: #fafafa;
    }
    .add-bitespot-modal label {
        font-weight: 500;
        margin-bottom: 0.2rem;
        display: block;
    }
    .add-bitespot-foods-list {
        margin-bottom: 0.7rem;
    }
    .add-bitespot-add-food-btn {
        background: #ff8800;
        color: #fff;
        border: none;
        border-radius: 0.7rem;
        padding: 0.4rem 1rem;
        font-size: 0.98rem;
        cursor: pointer;
        margin-top: 0.2rem;
        margin-bottom: 0.7rem;
    }
    .add-bitespot-remove-food-btn {
        background: #eee;
        color: #c00;
        border: none;
        border-radius: 0.7rem;
        padding: 0.2rem 0.7rem;
        font-size: 0.95rem;
        cursor: pointer;
        margin-left: 0.5rem;
    }
    .add-bitespot-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.7rem;
    }
    .add-bitespot-modal-actions button[type="submit"] {
        background: linear-gradient(135deg, #ff8800 70%, #ffb347 100%);
        color: #fff;
        border: none;
        border-radius: 0.7rem;
        padding: 0.6rem 1.5rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(255,136,0,0.08);
    }
</style>

<div class="add-bitespot-fab" id="add-bitespot-fab" title="Add BiteSpot">
    +
</div>
<div class="add-bitespot-modal-backdrop" id="add-bitespot-backdrop"></div>
<div class="add-bitespot-modal" id="add-bitespot-modal">
    <div class="add-bitespot-modal-header">
        Add a New BiteSpot
        <button class="add-bitespot-close" id="add-bitespot-close" title="Close">&times;</button>
    </div>
    <form id="add-bitespot-form" autocomplete="off">
        <label for="bitespot-name">Name</label>
        <input type="text" id="bitespot-name" name="name" required placeholder="e.g. Jepoy's Grill & Resto">
        <label for="bitespot-location">Location</label>
        <input type="text" id="bitespot-location" name="location" required placeholder="e.g. 123 Main St, City">
        <div class="add-bitespot-foods-list" id="add-bitespot-foods-list">
            <label>Foods/Drinks</label>
            <div class="add-bitespot-food-row">
                <input type="text" name="foods[]" placeholder="e.g. Pork BBQ" required>
                <button type="button" class="add-bitespot-remove-food-btn" style="display:none;">&minus;</button>
            </div>
        </div>
        <button type="button" class="add-bitespot-add-food-btn" id="add-bitespot-add-food">+ Add another</button>
        <div class="add-bitespot-modal-actions">
            <button type="button" id="add-bitespot-cancel">Cancel</button>
            <button type="submit">Add</button>
        </div>
    </form>
</div>
<script>
(function() {
    const fab = document.getElementById('add-bitespot-fab');
    const modal = document.getElementById('add-bitespot-modal');
    const backdrop = document.getElementById('add-bitespot-backdrop');
    const closeBtn = document.getElementById('add-bitespot-close');
    const cancelBtn = document.getElementById('add-bitespot-cancel');
    const addFoodBtn = document.getElementById('add-bitespot-add-food');
    const foodsList = document.getElementById('add-bitespot-foods-list');

    function showModal() {
        modal.style.display = 'flex';
        backdrop.style.display = 'block';
    }
    function hideModal() {
        modal.style.display = 'none';
        backdrop.style.display = 'none';
    }
    fab.addEventListener('click', showModal);
    closeBtn.addEventListener('click', hideModal);
    cancelBtn.addEventListener('click', hideModal);
    backdrop.addEventListener('click', hideModal);

    addFoodBtn.addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'add-bitespot-food-row';
        row.innerHTML = `<input type="text" name="foods[]" placeholder="e.g. Pork BBQ" required>
            <button type="button" class="add-bitespot-remove-food-btn">&minus;</button>`;
        foodsList.appendChild(row);
        updateRemoveButtons();
    });
    function updateRemoveButtons() {
        const rows = foodsList.querySelectorAll('.add-bitespot-food-row');
        rows.forEach((row, idx) => {
            const btn = row.querySelector('.add-bitespot-remove-food-btn');
            btn.style.display = rows.length > 1 ? '' : 'none';
            btn.onclick = function() {
                if (rows.length > 1) row.remove();
                updateRemoveButtons();
            };
        });
    }
    updateRemoveButtons();

    // Optional: handle form submit (AJAX or normal)
    document.getElementById('add-bitespot-form').addEventListener('submit', function(e) {
        e.preventDefault();
        // TODO: Implement AJAX or form submission logic here
        alert('BiteSpot added! (Demo only)');
        hideModal();
        this.reset();
        // Reset foods list to one row
        foodsList.innerHTML = `<label>Foods/Drinks</label>
            <div class="add-bitespot-food-row">
                <input type="text" name="foods[]" placeholder="e.g. Pork BBQ" required>
                <button type="button" class="add-bitespot-remove-food-btn" style="display:none;">&minus;</button>
            </div>`;
        updateRemoveButtons();
    });
})();
</script>
