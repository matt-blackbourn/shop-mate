
console.log('foodItem.js loaded');
document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('quick-add-submit');
    if (!button) {
        return;
    }

    document.getElementById('quick-add-submit')?.addEventListener('click', async () => {

        const nameInput = document.getElementById('quick-add-name');
        const error = document.getElementById('quick-add-error');

        error.classList.add('d-none');

        const response = await fetch("{{ path('ajax_food_item_quick_add') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name: nameInput.value })
        });

        if (!response.ok) {
            error.textContent = 'Could not create item';
            error.classList.remove('d-none');
            return;
        }

        const item = await response.json();

        // Create checkbox compatible with Symfony form naming
        const list = document.querySelector('.list-group');

        const index = document.querySelectorAll('[name^="shopping_list[foodItems]"]').length;

        const label = document.createElement('label');
        label.className = 'list-group-item d-flex align-items-center gap-3 py-3';

        label.innerHTML = `
            <input type="checkbox"
                name="shopping_list[foodItems][]"
                value="${item.id}"
                class="form-check-input flex-shrink-0"
                checked>
            <span class="flex-grow-1">${item.name}</span>
        `;

        list.prepend(label);

        nameInput.value = '';

        bootstrap.Modal.getInstance(
            document.getElementById('quickAddFoodItemModal')
        ).hide();
    });

});
