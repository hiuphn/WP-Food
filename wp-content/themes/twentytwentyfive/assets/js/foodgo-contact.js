document.addEventListener('DOMContentLoaded', function() {
    console.log('📞 FoodGo Contact JS: Đang tải...');
    
    // Tìm form bằng ID, nếu không có thì tìm bằng Class
    const contactForm = document.getElementById('foodgoContactForm') || document.querySelector('.foodgo-contact-form');
    
    if (contactForm) {
        console.log('🎯 FoodGo Contact Form: Đã tìm thấy form!', contactForm);
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = contactForm.querySelector('.contact-submit-btn');
            const originalBtnText = submitBtn.innerHTML;
            
            // Thu thập dữ liệu (Dùng querySelector để không bị phụ thuộc vào ID)
            const name = contactForm.querySelector('input[type="text"]').value;
            const email = contactForm.querySelector('input[type="email"]').value;
            const phone = contactForm.querySelector('input[type="tel"]').value;
            const message = contactForm.querySelector('textarea').value;
            
            // Vô hiệu hóa nút để tránh bấm nhiều lần
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Đang gửi... ⏳';
            
            // Tạo FormData để gửi lên server
            const formData = new FormData();
            formData.append('action', 'foodgo_submit_contact');
            formData.append('name', name);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('message', message);
            
            // Gửi AJAX bằng Fetch API
            fetch(foodgo_vars.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Cảm ơn bạn! Tin nhắn của bạn đã được gửi thành công. Chúng tôi sẽ phản hồi sớm nhất.');
                    contactForm.reset();
                } else {
                    alert('Lỗi: ' + data.data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại sau.');
            })
            .finally(() => {
                // Kích hoạt lại nút
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }
});
