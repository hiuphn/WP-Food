(function () {
    // Chạy ngay khi file được load (thường ở cuối trang nên DOM đã sẵn sàng)
    const initFilter = () => {
        const foodGrid = document.getElementById('food-grid');
        const categoryButtons = document.querySelectorAll('.category-item');
        const searchInput = document.getElementById('food-search-input');

        if (categoryButtons && foodGrid) {
            categoryButtons.forEach(button => {
                button.addEventListener('click', () => {
                    categoryButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    
                    const category = button.dataset.category.toLowerCase();
                    foodGrid.style.opacity = '0.5'; // Hiệu ứng loading
                    
                    const formData = new FormData();
                    formData.append('action', 'foodgo_filter_products');
                    formData.append('category', category);
                    
                    fetch('/wp-admin/admin-ajax.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(html => {
                        foodGrid.innerHTML = html;
                        foodGrid.style.opacity = '1';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        foodGrid.style.opacity = '1';
                    });
                });
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase();
                const cards = document.querySelectorAll('.food-card');
                cards.forEach(card => {
                    const title = card.querySelector('h3').innerText.toLowerCase();
                    if (title.includes(keyword)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    };

    // Đợi 500ms để chắc chắn Shortcode đã render xong HTML
    setTimeout(initFilter, 500);
})();
