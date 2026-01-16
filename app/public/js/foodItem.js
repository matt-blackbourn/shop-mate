const container = document.getElementById('items');
let index = {{ form.items|length }};


document.getElementById('add-item').addEventListener('click', () => {
const prototype = container.dataset.prototype.replace(/__name__/g, index);
index++;


const div = document.createElement('div');
div.classList.add('list-item');
div.innerHTML = prototype + '<button type="button" class="remove-item">Remove</button>';


container.appendChild(div);
});


container.addEventListener('click', e => {
if (e.target.classList.contains('remove-item')) {
e.target.closest('.list-item').remove();
}
});