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

        // 5. Trình xử lý thêm vào giỏ hàng cốt lõi
        function performAddToCart(addBtn, nameOverride = null, priceOverride = null, qtyOverride = null) {
            try {
                const foodCard = addBtn.closest('.food-card') || 
                                 addBtn.closest('.wp-block-column') || 
                                 addBtn.parentElement.parentElement;
                
                const imgEl = foodCard ? foodCard.querySelector('img') : null;
                const image = addBtn.dataset.image || (imgEl ? imgEl.src : '');
                
                const name = nameOverride || addBtn.dataset.name;
                const price = priceOverride !== null ? priceOverride : parseInt(addBtn.dataset.price);
                const quantity = qtyOverride !== null ? qtyOverride : 1;
                
                if (!name || isNaN(price) || price === 0) {
                    console.error('❌ Lỗi: Không lấy được thông tin món ăn');
                    return;
                }
                
                console.log('📦 Đang thêm:', name, price.toLocaleString('vi-VN') + 'đ');
                
                const existingItem = cart.find(item => item.name === name);
                if (existingItem) {
                    existingItem.quantity += quantity;
                } else {
                    cart.push({ name, price, image, quantity: quantity });
                }
                
                // Phản hồi nút bấm
                const originalHTML = addBtn.innerHTML;
                addBtn.innerHTML = 'Thành công! ✓';
                const originalBG = addBtn.style.backgroundColor;
                const originalColor = addBtn.style.color;
                addBtn.style.backgroundColor = '#27ae60';
                addBtn.style.color = '#fff';
                
                setTimeout(() => {
                    addBtn.innerHTML = originalHTML;
                    addBtn.style.backgroundColor = originalBG;
                    addBtn.style.color = originalColor;
                }, 1000);
                
                updateUI();
                
                // Hiển thị thông báo Toast sang trọng
                if (window.Swal) {
                    window.Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Đã thêm ' + name + ' vào giỏ hàng!',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            } catch (err) {
                console.error('❌ Lỗi thêm vào giỏ hàng:', err);
            }
        }

        // Trình lấy và hiển thị Modal Chọn Size AJAX
        function openQuickAddModal(productId, addBtn) {
            if (!productId) return;
            
            const originalHTML = addBtn.innerHTML;
            addBtn.innerHTML = 'Đang tải...';
            
            const formData = new FormData();
            formData.append('action', 'foodgo_get_product_options');
            formData.append('product_id', productId);
            
            fetch(foodgo_vars.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                addBtn.innerHTML = originalHTML;
                
                if (data.success) {
                    if (data.data.has_variants) {
                        const modal = document.getElementById('fg-quick-add-modal');
                        const body = document.getElementById('fg-modal-body');
                        if (modal && body) {
                            body.innerHTML = data.data.html;
                            modal.style.display = 'flex';
                            
                            initModalInteractions(modal, body, addBtn);
                        }
                    } else {
                        // Không có biến thể, thêm thẳng vào giỏ
                        performAddToCart(addBtn);
                    }
                } else {
                    console.error('❌ AJAX Lỗi:', data.data);
                    performAddToCart(addBtn);
                }
            })
            .catch(err => {
                console.error('❌ Lỗi kết nối:', err);
                addBtn.innerHTML = originalHTML;
                performAddToCart(addBtn);
            });
        }

        // Tương tác trên Modal Chọn Size
        function initModalInteractions(modal, body, addBtn) {
            const priceEl = body.querySelector('.fg-modal-price');
            const confirmBtn = body.querySelector('.fg-modal-add-btn');
            const qtyInput = body.querySelector('.fg-qty-input');
            const btnMinus = body.querySelector('.fg-qty-btn-minus');
            const btnPlus = body.querySelector('.fg-qty-btn-plus');
            
            const baseRegular = parseInt(body.querySelector('.fg-modal-content-inner').dataset.baseRegular) || 0;
            const baseSale = parseInt(body.querySelector('.fg-modal-content-inner').dataset.baseSale) || 0;
            const originalName = body.querySelector('h3').textContent.trim();
            
            function updateModalPrice() {
                let variantPriceSum = 0;
                let selectedNames = [];
                
                body.querySelectorAll('.fg-variant-pill-label input:checked').forEach(input => {
                    variantPriceSum += parseInt(input.dataset.price) || 0;
                    selectedNames.push(input.value);
                });
                
                const newRegularPrice = baseRegular + variantPriceSum;
                const newSalePrice = baseSale > 0 ? baseSale + variantPriceSum : 0;
                const activePrice = newSalePrice > 0 ? newSalePrice : newRegularPrice;
                
                priceEl.textContent = activePrice.toLocaleString('vi-VN') + '₫';
                confirmBtn.dataset.price = activePrice;
                
                if (selectedNames.length > 0) {
                    confirmBtn.dataset.name = `${originalName} (${selectedNames.join(', ')})`;
                } else {
                    confirmBtn.dataset.name = originalName;
                }
            }
            
            body.querySelectorAll('.fg-variant-pill-label input').forEach(input => {
                input.addEventListener('change', updateModalPrice);
            });
            
            btnMinus.onclick = function() {
                let val = parseInt(qtyInput.value) || 1;
                if (val > 1) {
                    val--;
                    qtyInput.value = val;
                }
            };
            
            btnPlus.onclick = function() {
                let val = parseInt(qtyInput.value) || 1;
                val++;
                qtyInput.value = val;
            };
            
            confirmBtn.onclick = function() {
                const finalPrice = parseInt(confirmBtn.dataset.price);
                const finalName = confirmBtn.dataset.name;
                const finalQty = parseInt(qtyInput.value) || 1;
                
                performAddToCart(addBtn, finalName, finalPrice, finalQty);
                modal.style.display = 'none';
            };
            
            const closeBtn = modal.querySelector('.fg-modal-close');
            closeBtn.onclick = function() {
                modal.style.display = 'none';
            };
            
            modal.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            };
            
            updateModalPrice();
        }

        // Lắng nghe click toàn cục
        document.addEventListener('click', function(e) {
            let addBtn = e.target.closest('.add-to-cart');
            
            if (!addBtn) {
                const potentialBtn = e.target.closest('.btn') || e.target.closest('button') || e.target.closest('p');
                if (potentialBtn && potentialBtn.textContent.includes('Đặt món')) {
                    addBtn = potentialBtn;
                }
            }

            if (addBtn) {
                e.preventDefault();
                console.log('🎯 Hệ thống: Nhấp chọn Đặt món!');
                
                const isDetailPage = addBtn.closest('.fg-product-layout');
                const productId = addBtn.dataset.id;
                
                if (isDetailPage) {
                    // Tại trang chi tiết: Thêm trực tiếp với các lựa chọn đã tích sẵn
                    const name = addBtn.dataset.name;
                    const price = parseInt(addBtn.dataset.price);
                    
                    const qtyInput = addBtn.closest('.fg-product-layout').querySelector('.fg-quantity input');
                    const qty = qtyInput ? parseInt(qtyInput.value) : 1;
                    
                    performAddToCart(addBtn, name, price, qty);
                } else {
                    // Tại danh sách ngoài: Gọi AJAX kiểm tra và hiển thị modal
                    openQuickAddModal(productId, addBtn);
                }
            }

            // Xử lý nút thanh toán
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
