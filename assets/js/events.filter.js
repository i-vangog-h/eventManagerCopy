const filterDropdown = document.getElementById('filterDropdown');
const cards = Array.from(document.getElementsByClassName('card'));

filterDropdown.addEventListener('change', () => {
    const selectedCategory = filterDropdown.value;

    cards.forEach(card => {
        const category = card.getAttribute('data-category');
        if (selectedCategory === 'any' || category === selectedCategory) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});