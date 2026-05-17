// FoodGo Real-time Cart System
console.log("🚀 FoodGo Cart Engine: Loading...");

(function() {
    function initFoodGo() {
        console.log("✅ FoodGo Cart Engine: Ready and Active!");
        // 1. Khởi tạo dữ liệu
        let cart = JSON.parse(localStorage.getItem('foodgo_cart')) || [];
        
        // Xóa giỏ hàng khi người dùng bấm Đăng xuất
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href.includes('action=logout')) {
                localStorage.removeItem('foodgo_cart');
            }
        });

        const cartCountElements = document.querySelectorAll('.cart-count');
        const cartItemsContainer = document.getElementById('cart-items');
        const subtotalElement = document.getElementById('cart-subtotal');
        const totalElement = document.getElementById('cart-total');

        // 2. Hàm cập nhật giao diện toàn hệ thống
        function updateUI() {
            // Lưu dữ liệu
            localStorage.setItem('foodgo_cart', JSON.stringify(cart));

            // Cập nhật số lượng trên Header
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            cartCountElements.forEach(el => {
                el.textContent = totalItems;
                el.style.display = totalItems > 0 ? 'inline-block' : 'none';
            });

            // Cập nhật trang Giỏ hàng
            if (cartItemsContainer) {
                renderCartPage();
            }

            // Cập nhật Floating Cart (nếu có)
            updateFloatingCart(totalItems, totalPrice);
        }

        function updateFloatingCart(totalItems, totalPrice) {
            const floatingCart = document.getElementById('floating-cart');
            const floatingCount = document.getElementById('cart-count-float'); // Tránh trùng ID
            const floatingTotal = document.getElementById('cart-total-price');

            if (floatingCart) {
                if (totalItems > 0) {
                    floatingCart.style.display = 'flex';
                    if (floatingCount) floatingCount.textContent = totalItems;
                    if (floatingTotal) floatingTotal.textContent = totalPrice.toLocaleString('vi-VN') + 'đ';
                } else {
                    floatingCart.style.display = 'none';
                }
            }
        }

        // 3. Hàm Render trang Giỏ hàng
        function renderCartPage() {
            if (cart.length === 0) {
                cartItemsContainer.innerHTML = `
                    <div style="text-align:center; padding: 40px 0;">
                        <p style="font-size: 18px; color: #888;">Giỏ hàng của bạn đang trống.</p>
                        <a href="/#foods" class="btn" style="margin-top: 20px; display: inline-block;">Quay lại chọn món</a>
                    </div>
                `;
                if (subtotalElement) subtotalElement.textContent = '0đ';
                if (totalElement) totalElement.textContent = '0đ';
                return;
            }

            let html = '';
            let subtotal = 0;

            cart.forEach((item, index) => {
                subtotal += item.price * item.quantity;
                html += `
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <img src="${item.image}" alt="${item.name}">
                        </div>
                        <div class="cart-item-content">
                            <div class="cart-item-top">
                                <h4>${item.name}</h4>
                                <p class="cart-price">${(item.price * item.quantity).toLocaleString('vi-VN')}đ</p>
                            </div>
                            <div class="cart-item-actions">
                                <div class="quantity-box">
                                    <button class="qty-minus" data-index="${index}" style="background:none; border:none; cursor:pointer; padding: 0 10px; font-size: 20px;">−</button>
                                    <span class="quantity-number">${item.quantity}</span>
                                    <button class="qty-plus" data-index="${index}" style="background:none; border:none; cursor:pointer; padding: 0 10px; font-size: 20px;">+</button>
                                </div>
                                <button class="remove-btn-real" data-index="${index}" style="background:none; border:1px solid #ddd; border-radius:50px; padding: 5px 15px; cursor:pointer; font-size: 14px;">Xóa</button>
                            </div>
                        </div>
                    </div>
                `;
            });

            cartItemsContainer.innerHTML = html;
            
            const totalStr = subtotal.toLocaleString('vi-VN') + 'đ';
            if (subtotalElement) subtotalElement.textContent = totalStr;
            if (totalElement) totalElement.textContent = totalStr;

            // Gán lại sự kiện cho các nút mới tạo
            attachCartEvents();
        }

        // 4. Hàm lắng nghe sự kiện trong trang Giỏ hàng
        function attachCartEvents() {
            document.querySelectorAll('.qty-plus').forEach(btn => {
                btn.onclick = () => {
                    const index = btn.dataset.index;
                    cart[index].quantity++;
                    updateUI();
                };
            });

            document.querySelectorAll('.qty-minus').forEach(btn => {
                btn.onclick = () => {
                    const index = btn.dataset.index;
                    if (cart[index].quantity > 1) {
                        cart[index].quantity--;
                    } else {
                        cart.splice(index, 1);
                    }
                    updateUI();
                };
            });

            document.querySelectorAll('.remove-btn-real').forEach(btn => {
                btn.onclick = () => {
                    const index = btn.dataset.index;
                    cart.splice(index, 1);
                    updateUI();
                };
            });
        }

        // 5. Lắng nghe sự kiện "Thêm vào giỏ" ở trang chủ
        document.addEventListener('click', function(e) {
            // Tìm nút theo CLASS hoặc theo CHỮ bên trong nút
            let addBtn = e.target.closest('.add-to-cart');
            
            // Nếu không tìm thấy class, thử tìm theo nội dung chữ
            if (!addBtn) {
                const potentialBtn = e.target.closest('.btn') || e.target.closest('button') || e.target.closest('p');
                if (potentialBtn && potentialBtn.textContent.includes('Đặt món')) {
                    addBtn = potentialBtn;
                }
            }

            if (addBtn) {
                e.preventDefault();
                console.log('🎯 Hệ thống: Đã nhận diện lệnh Đặt món!');
                
                // Tìm khung chứa món ăn (linh hoạt hơn)
                const foodCard = addBtn.closest('.food-card') || 
                                 addBtn.closest('.wp-block-column') || 
                                 addBtn.parentElement.parentElement;
                
                if (!foodCard) {
                    console.error('❌ Lỗi: Không tìm thấy khung chứa món ăn (food-card)');
                    return;
                }

                try {
                    // Tìm tên và giá (thử nhiều kiểu selector khác nhau)
                    const nameEl = foodCard.querySelector('h1, h2, h3, h4, .food-title');
                    const priceEl = foodCard.querySelector('.price, .cart-price, span[style*="color"]');
                    const imgEl = foodCard.querySelector('img');

                    // Ưu tiên lấy dữ liệu từ data attributes của nút bấm
                    const name = addBtn.dataset.name || (nameEl ? nameEl.textContent.trim() : '');
                    let price = 0;
                    
                    if (addBtn.dataset.price) {
                        price = parseInt(addBtn.dataset.price);
                    } else if (priceEl) {
                        let priceText = priceEl.textContent.toUpperCase();
                        if (priceText.includes('K')) {
                            price = parseInt(priceText.replace(/[^0-9]/g, '')) * 1000;
                        } else {
                            price = parseInt(priceText.replace(/[^0-9]/g, '')) || 0;
                        }
                    }

                    const image = addBtn.dataset.image || (imgEl ? imgEl.src : '');

                    if (!name || isNaN(price) || price === 0) {
                        console.error('❌ Lỗi: Không lấy được thông tin món ăn từ:', foodCard);
                        return;
                    }

                    console.log('📦 Đang thêm:', name, price.toLocaleString('vi-VN') + 'đ');

                    // Tìm số lượng (nếu có input)
                    const qtyInput = foodCard.querySelector('input[type="text"]') || foodCard.querySelector('input[type="number"]');
                    const quantity = qtyInput ? parseInt(qtyInput.value) : 1;

                    // Kiểm tra xem món đã có trong giỏ chưa
                    const existingItem = cart.find(item => item.name === name);
                    if (existingItem) {
                        existingItem.quantity += quantity;
                    } else {
                        cart.push({ name, price, image, quantity: quantity });
                    }

                    // Hiệu ứng phản hồi cho người dùng
                    const originalHTML = addBtn.innerHTML;
                    addBtn.innerHTML = 'Thành công! ✓';
                    addBtn.style.backgroundColor = '#27ae60';
                    addBtn.style.color = '#fff';
                    
                    setTimeout(() => {
                        addBtn.innerHTML = originalHTML;
                        addBtn.style.backgroundColor = '';
                        addBtn.style.color = '';
                    }, 1000);

                    updateUI();
                } catch (err) {
                    console.error('❌ Lỗi hệ thống:', err);
                }
            }

            // Xử lý nút thanh toán (Chuyển hướng sang trang checkout)
            if (e.target.closest('#checkout-btn')) {
                if (cart.length === 0) {
                    alert('Giỏ hàng của bạn đang trống!');
                    return;
                }
                window.location.href = '/thanh-toan';
            }
        });

        // 6. Tự động định dạng giá tiền trên toàn trang (Fix lỗi "149K" thành "149.000đ")
        function formatGlobalPrices() {
            document.querySelectorAll('.price, .cart-price').forEach(el => {
                let text = el.textContent.toUpperCase();
                if (text.includes('K') && !text.includes('.000')) {
                    let val = parseInt(text.replace(/[^0-9]/g, '')) * 1000;
                    el.textContent = val.toLocaleString('vi-VN') + 'đ';
                    el.style.color = '#ff4d4f'; // Đảm bảo màu đỏ đẹp
                    el.style.fontWeight = 'bold';
                }
            });
        }

        // 7. Lắng nghe sự kiện cộng trừ số lượng ở trang chi tiết
        document.addEventListener('click', function(e) {
            const btn = e.target;
            if (btn.closest('.fg-quantity')) {
                const input = btn.closest('.fg-quantity').querySelector('input');
                if (!input) return;
                
                let val = parseInt(input.value) || 1;
                
                if (btn.textContent === '+') {
                    val++;
                } else if (btn.textContent === '-') {
                    if (val > 1) val--;
                }
                
                input.value = val;
            }
        });

        // Chạy lần đầu
        updateUI();
        formatGlobalPrices();
    }

    // Đợi DOM sẵn sàng
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initFoodGo);
    } else {
        initFoodGo();
    }
})();
