<?php
/**
 * FoodGo Theme Core Setup and Configurations
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// 1. Đăng ký Custom Post Type "Đơn hàng"
function foodgo_register_order_cpt()
{
    register_post_type('foodgo_order', array(
        'labels' => array(
            'name' => 'Đơn hàng FoodGo',
            'singular_name' => 'Đơn hàng',
            'menu_name' => 'Đơn hàng FoodGo',
            'add_new' => 'Thêm đơn hàng mới',
            'all_items' => 'Tất cả đơn hàng',
        ),
        'public' => false,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array('title', 'editor', 'custom-fields'),
        'menu_icon' => 'dashicons-cart',
    ));
}
add_action('init', 'foodgo_register_order_cpt');

// 2. Đăng ký Custom Post Type "Tin nhắn Liên hệ"
function foodgo_register_contact_cpt()
{
    register_post_type('foodgo_contact', array(
        'labels' => array(
            'name' => 'Tin nhắn Liên hệ',
            'singular_name' => 'Tin nhắn',
            'menu_name' => 'Tin nhắn Liên hệ',
            'all_items' => 'Tất cả tin nhắn',
        ),
        'public' => false,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array('title', 'editor'),
        'menu_icon' => 'dashicons-email',
    ));
}
add_action('init', 'foodgo_register_contact_cpt');

// 3. AJAX login handler (fallback to show errors without full reload)
function foodgo_ajax_login_handler() {
    // Expect POST: username, password, login_nonce
    if (!isset($_POST['login_nonce']) || !wp_verify_nonce($_POST['login_nonce'], 'foodgo_login_nonce')) {
        wp_send_json_error('Phiên đăng nhập không hợp lệ.');
    }

    $creds = array(
        'user_login'    => isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '',
        'user_password' => isset($_POST['password']) ? $_POST['password'] : '',
        'remember'      => true,
    );

    $user = wp_signon($creds, false);
    if (is_wp_error($user)) {
        wp_send_json_error(wp_strip_all_tags($user->get_error_message()));
    }

    wp_send_json_success();
}
add_action('wp_ajax_foodgo_ajax_login', 'foodgo_ajax_login_handler');
add_action('wp_ajax_nopriv_foodgo_ajax_login', 'foodgo_ajax_login_handler');

// 4. ẨN THANH ADMIN BAR Ở GIAO DIỆN NGOÀI (FRONT-END)
add_filter('show_admin_bar', '__return_false');

// 5. CẤU HÌNH TIỀN TỆ VNĐ CHO PLUGIN WP FOOD MANAGER
add_filter('wpfm_currency', function () {
    return 'VND';
});
add_filter('wpfm_get_price_decimals', function () {
    return 0;
});
add_filter('wpfm_get_price_thousand_separator', function () {
    return '.';
});

// 6. TRÌNH LẤY URL HÌNH ẢNH AN TOÀN (TRÁNH LỖI HÌNH ẢNH LÀ MẢNG KHI LƯU POST)
function foodgo_get_product_image_url($post_id) {
    $image_meta = get_post_meta($post_id, '_food_banner', true);
    if (empty($image_meta)) {
        return '';
    }
    
    while (is_array($image_meta)) {
        if (isset($image_meta['url'])) {
            $image_meta = $image_meta['url'];
            break;
        }
        
        $first = reset($image_meta);
        if ($first === false) {
            return '';
        }
        $image_meta = $first;
    }
    
    return is_string($image_meta) ? $image_meta : '';
}

// 7. HIỂN THỊ CONTAINER MODAL CHỌN SIZE Ở FOOTER TOÀN TRANG
add_action('wp_footer', 'foodgo_render_quick_add_modal_container');
function foodgo_render_quick_add_modal_container() {
    ?>
    <!-- QUICK ADD VARIATION MODAL (Option 1: Premium Glassmorphism) -->
    <div id="fg-quick-add-modal" class="fg-modal">
        <div class="fg-modal-content">
            <span class="fg-modal-close">&times;</span>
            <div id="fg-modal-body">
                <!-- Loaded Dynamically via AJAX -->
                <p style="text-align: center; color: #666; padding: 20px;">Đang tải tùy chọn món ăn...</p>
            </div>
        </div>
    </div>
    
    <style>
        @keyframes fgModalFadeIn {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        
        /* Modal Styles */
        #fg-quick-add-modal {
            display: none;
            position: fixed;
            z-index: 99999; /* Ensure it is always on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }
        
        #fg-quick-add-modal .fg-modal-content {
            background-color: #fff !important;
            padding: 30px !important;
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            width: 90% !important;
            max-width: 460px !important;
            border-radius: 28px !important;
            box-shadow: 0 25px 60px rgba(0,0,0,0.18) !important;
            position: relative !important;
            animation: fgModalFadeIn 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-sizing: border-box !important;
        }
        
        #fg-quick-add-modal .fg-modal-close {
            color: #8e8e93 !important;
            font-size: 26px !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            position: absolute !important;
            right: 24px !important;
            top: 20px !important;
            line-height: 1 !important;
            transition: all 0.2s ease !important;
            z-index: 10 !important;
        }
        
        #fg-quick-add-modal .fg-modal-close:hover {
            color: #ff4d4f !important;
            transform: scale(1.15);
        }
        
        /* Premium custom pill selectors inside Modal */
        #fg-quick-add-modal .fg-variant-pill-label {
            cursor: pointer !important;
            position: relative !important;
            user-select: none !important;
            display: inline-block !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        #fg-quick-add-modal .fg-variant-pill-label input {
            position: absolute !important;
            opacity: 0 !important;
            width: 100% !important;
            height: 100% !important;
            top: 0 !important;
            left: 0 !important;
            cursor: pointer !important;
            margin: 0 !important;
            padding: 0 !important;
            z-index: 2 !important;
        }
        
        #fg-quick-add-modal .fg-variant-pill-span {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 8px 18px !important;
            border-radius: 999px !important;
            background: #f5f5f7 !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            color: #1d1d1f !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            width: auto !important;
            height: auto !important;
            line-height: 1.2 !important;
            min-width: unset !important;
            min-height: unset !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            position: relative !important;
            z-index: 1 !important;
            white-space: nowrap !important;
        }
        
        #fg-quick-add-modal .fg-variant-pill-label:hover .fg-variant-pill-span {
            background: #e8e8ed !important;
            color: #1d1d1f !important;
        }
        
        #fg-quick-add-modal .fg-variant-pill-label input:checked + .fg-variant-pill-span {
            background: linear-gradient(135deg, #ff7875, #ff4d4f) !important;
            border-color: #ff4d4f !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(255, 77, 79, 0.25) !important;
        }
        
        /* Modal Quantity buttons hover feedback */
        #fg-quick-add-modal .fg-quantity button {
            transition: background 0.2s, color 0.2s;
        }
        
        #fg-quick-add-modal .fg-quantity button:hover {
            background-color: #e8e8ed !important;
            color: #1d1d1f !important;
        }
        
        /* SweetAlert2 Toast Custom Premium Override */
        body .swal2-container.swal2-top-end,
        body .swal2-container.swal2-backdrop-show {
            z-index: 999999 !important; /* Stand above everything, including sticky header */
        }
        body .swal2-popup.swal2-toast {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
            border-radius: 16px !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            padding: 12px 20px !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }
        body .swal2-popup.swal2-toast .swal2-icon {
            margin: 0 !important;
            transform: scale(0.6) !important;
            transform-origin: center center !important;
            min-width: 38px !important;
            min-height: 38px !important;
            width: 38px !important;
            height: 38px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: visible !important;
        }
        body .swal2-popup.swal2-toast .swal2-title {
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #1d1d1f !important;
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1.4 !important;
            text-align: left !important;
        }
        body .swal2-popup.swal2-toast .swal2-timer-progress-bar {
            background: linear-gradient(90deg, #ff7875, #ff4d4f) !important;
            height: 3px !important;
        }
    </style>
    <?php
}

/**
 * 7. QUẢN LÝ PHẢN HỒI LIÊN HỆ TRONG WP ADMIN CMS (Metabox & Reply Email Sender)
 */

// Đăng ký Metabox phản hồi tin nhắn trong trang chi tiết liên hệ
add_action('add_meta_boxes', 'foodgo_contact_register_reply_metabox');
function foodgo_contact_register_reply_metabox() {
    add_meta_box(
        'foodgo_contact_reply_box',
        'Phản hồi liên hệ khách hàng',
        'foodgo_contact_reply_metabox_callback',
        'foodgo_contact',
        'normal',
        'high'
    );
}

// Nội dung hiển thị Metabox trong Admin CMS
function foodgo_contact_reply_metabox_callback($post) {
    // Tạo mã bảo mật Nonce
    wp_nonce_field('foodgo_contact_reply_action', 'foodgo_contact_reply_nonce');
    
    // Lấy thông tin khách hàng và phản hồi trước đó
    $email = get_post_meta($post->ID, '_contact_email', true);
    $phone = get_post_meta($post->ID, '_contact_phone', true);
    $status = get_post_meta($post->ID, '_contact_status', true) ?: 'pending';
    $replies = get_post_meta($post->ID, '_contact_replies', true) ?: array();
    
    ?>
    <div class="foodgo-contact-admin-wrapper" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; padding: 10px 0;">
        
        <!-- Trạng thái xử lý tin nhắn -->
        <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <strong style="font-size: 14px; color: #333;">Trạng thái xử lý:</strong>
            <?php if ($status === 'replied') : ?>
                <span style="background: #f6ffed; color: #389e0d; border: 1px solid #b7eb8f; padding: 5px 14px; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                    ✓ Đã phản hồi qua Email
                </span>
            <?php else : ?>
                <span style="background: #fff7e6; color: #d46b08; border: 1px solid #ffd591; padding: 5px 14px; border-radius: 6px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                    ⌛ Chưa phản hồi (Đang chờ)
                </span>
            <?php endif; ?>
        </div>

        <!-- Thẻ thông tin Khách hàng -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
            <div style="background: #fafafa; padding: 15px; border-radius: 10px; border: 1px solid #e8e8e8; box-shadow: 0 2px 5px rgba(0,0,0,0.01);">
                <div style="font-size: 11px; text-transform: uppercase; color: #8c8c8c; font-weight: 700; margin-bottom: 6px; letter-spacing: 0.5px;">Hộp thư nhận phản hồi</div>
                <div style="font-size: 15px; font-weight: 600; color: #262626;">
                    <a href="mailto:<?php echo esc_attr($email); ?>" style="text-decoration: none; color: #1890ff;"><?php echo esc_html($email); ?></a>
                </div>
            </div>
            <div style="background: #fafafa; padding: 15px; border-radius: 10px; border: 1px solid #e8e8e8; box-shadow: 0 2px 5px rgba(0,0,0,0.01);">
                <div style="font-size: 11px; text-transform: uppercase; color: #8c8c8c; font-weight: 700; margin-bottom: 6px; letter-spacing: 0.5px;">Số điện thoại khách hàng</div>
                <div style="font-size: 15px; font-weight: 600; color: #262626;"><?php echo esc_html($phone ?: 'Không cung cấp'); ?></div>
            </div>
        </div>

        <!-- Lịch sử các phản hồi trước đây -->
        <div style="margin-bottom: 25px;">
            <h3 style="border-bottom: 1px solid #f0f0f0; padding-bottom: 10px; font-size: 15px; color: #262626; margin-bottom: 15px; font-weight: 700;">Lịch sử phản hồi</h3>
            <?php if (empty($replies)) : ?>
                <p style="color: #bfbfbf; font-style: italic; font-size: 13px;">Chưa có phản hồi nào được gửi đi.</p>
            <?php else : ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach ($replies as $reply) : ?>
                        <div style="background: #fafafa; border-left: 4px solid #1890ff; padding: 15px; border-radius: 0 8px 8px 0; border: 1px solid #e8e8e8; border-left: 4px solid #1890ff;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 12px; color: #8c8c8c;">
                                <strong>👤 Người gửi: <?php echo esc_html($reply['sender']); ?></strong>
                                <span>📅 <?php echo esc_html(date('d/m/Y H:i:s', $reply['timestamp'])); ?></span>
                            </div>
                            <div style="font-size: 13px; line-height: 1.6; color: #434343; white-space: pre-wrap; font-family: inherit;"><?php echo esc_html($reply['content']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Khung soạn văn bản phản hồi mới -->
        <div>
            <h3 style="border-bottom: 1px solid #f0f0f0; padding-bottom: 10px; font-size: 15px; color: #262626; margin-bottom: 15px; font-weight: 700;">Soạn phản hồi mới</h3>
            <div style="margin-bottom: 15px;">
                <textarea name="foodgo_contact_reply_message" rows="6" style="width: 100%; border-radius: 8px; border: 1px solid #d9d9d9; padding: 12px; font-size: 13px; line-height: 1.6; box-sizing: border-box; font-family: inherit;" placeholder="Nhập nội dung phản hồi gửi đến khách hàng tại đây..."></textarea>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" name="foodgo_contact_send_reply" value="1" class="button button-primary button-large" style="background: #ff4d4f !important; border-color: #ff4d4f !important; height: auto !important; padding: 8px 24px !important; font-size: 14px !important; font-weight: 600 !important; border-radius: 6px !important; box-shadow: 0 2px 4px rgba(255, 77, 79, 0.2) !important;">
                    ✉️ Gửi phản hồi qua Email
                </button>
            </div>
        </div>

    </div>
    <?php
}

// Xử lý gửi email phản hồi khi admin bấm Lưu bài viết
add_action('save_post_foodgo_contact', 'foodgo_contact_save_reply_handler', 10, 2);
function foodgo_contact_save_reply_handler($post_id, $post) {
    // Kiểm tra nonce
    if (!isset($_POST['foodgo_contact_reply_nonce']) || !wp_verify_nonce($_POST['foodgo_contact_reply_nonce'], 'foodgo_contact_reply_action')) {
        return;
    }
    
    // Tránh lưu đè khi WP tự động lưu bản nháp (Autosave)
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Kiểm tra xem có bấm nút gửi phản hồi và nội dung không trống
    if (isset($_POST['foodgo_contact_send_reply']) && !empty($_POST['foodgo_contact_reply_message'])) {
        $reply_content = sanitize_textarea_field($_POST['foodgo_contact_reply_message']);
        $email = get_post_meta($post_id, '_contact_email', true);
        
        if (!empty($email)) {
            // Lấy thông tin admin gửi
            $current_user = wp_get_current_user();
            $sender_name = $current_user->display_name ?: 'Ban quản trị FoodGo';
            
            $subject = 'Phản hồi từ Ban quản trị FoodGo - [' . esc_html($post->post_title) . ']';
            
            // Cấu hình headers định dạng HTML
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'Reply-To: phnhdev@gmail.com',
                'From: FoodGo <phnhdev@gmail.com>'
            );
            
            // Nội dung Email giao diện Premium cực kỳ sang trọng
            $email_body = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Phản hồi từ FoodGo</title>
                <style>
                    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f6f9fc; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
                    .wrapper { width: 100%; background-color: #f6f9fc; padding: 40px 0; }
                    .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.03); }
                    .header { background: linear-gradient(135deg, #ff7875, #ff4d4f); padding: 40px 20px; text-align: center; }
                    .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
                    .content { padding: 40px 30px; line-height: 1.6; color: #444; }
                    .content p { margin-top: 0; margin-bottom: 20px; font-size: 15px; color: #444; }
                    .reply-box { background: #f8fafc; border-left: 4px solid #ff4d4f; padding: 20px; border-radius: 4px; margin: 25px 0; font-size: 15px; white-space: pre-line; color: #222; }
                    .footer { text-align: center; padding: 30px 20px; background: #f8fafc; border-top: 1px solid #eee; font-size: 12px; color: #888; }
                    .footer a { color: #ff4d4f; text-decoration: none; }
                </style>
            </head>
            <body>
                <div class="wrapper">
                    <div class="container">
                        <div class="header">
                            <h1>FOODGO</h1>
                        </div>
                        <div class="content">
                            <p>Xin chào quý khách,</p>
                            <p>Chúng tôi đã nhận được thông tin liên hệ của bạn gửi đến hệ thống FoodGo và xin phép gửi phản hồi chính thức từ ban quản trị cửa hàng:</p>
                            
                            <div class="reply-box">' . esc_html($reply_content) . '</div>
                            
                            <p>Nếu bạn có bất kỳ câu hỏi nào khác hoặc cần hỗ trợ thêm, bạn có thể trả lời trực tiếp lại email này.</p>
                            <p>Trân trọng,<br><strong>Ban quản trị FoodGo</strong></p>
                        </div>
                        <div class="footer">
                            <p>Đây là email tự động gửi từ hệ thống cửa hàng FoodGo.</p>
                            <p>&copy; ' . date('Y') . ' <a href="' . home_url() . '">FoodGo</a>. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>';
            
            // Gửi email
            $sent = wp_mail($email, $subject, $email_body, $headers);
            
            if ($sent) {
                // Lưu vào lịch sử phản hồi
                $replies = get_post_meta($post_id, '_contact_replies', true) ?: array();
                $replies[] = array(
                    'sender' => $sender_name,
                    'timestamp' => time(),
                    'content' => $reply_content
                );
                
                update_post_meta($post_id, '_contact_replies', $replies);
                update_post_meta($post_id, '_contact_status', 'replied');
                
                // Thiết lập thông báo thành công qua transient
                set_transient('foodgo_contact_reply_success_' . $post_id, true, 45);
            }
        }
    }
}

// Hiển thị thông báo thành công sau khi gửi email trong trang Admin CPT
add_action('admin_notices', 'foodgo_contact_admin_notice');
function foodgo_contact_admin_notice() {
    global $post;
    if ($post && $post->post_type === 'foodgo_contact') {
        if (get_transient('foodgo_contact_reply_success_' . $post->ID)) {
            delete_transient('foodgo_contact_reply_success_' . $post->ID);
            ?>
            <div class="notice notice-success is-dismissible" style="border-left-color: #52c41a !important; padding: 12px 20px !important;">
                <p style="font-weight: 600; font-size: 14px; margin: 0; color: #237804;">
                    🎉 Phản hồi liên hệ đã được gửi email thành công đến khách hàng! Lịch sử phản hồi đã được cập nhật.
                </p>
            </div>
            <?php
        }
    }
}

/**
 * 8. CẤU HÌNH GỬI MAIL BẰNG GMAIL SMTP BẢO MẬT (Phương án 1)
 * Admin vui lòng thay đổi email và Mật khẩu ứng dụng ở dưới đây.
 */
add_action('phpmailer_init', 'foodgo_configure_smtp_gmail');
function foodgo_configure_smtp_gmail($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.gmail.com';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587; // Cổng bảo mật TLS
    $phpmailer->SMTPSecure = 'tls';
    
    // ========================================================
    // BƯỚC ĐIỀN THÔNG TIN TÀI KHOẢN CỦA BẠN:
    // ========================================================
    $phpmailer->Username   = 'phnhdev@gmail.com'; // Nhập Gmail của bạn vào đây
    $phpmailer->Password   = 'djgtvhjqrnjjqrvx';  // Nhập Mật khẩu ứng dụng 16 ký tự vào đây
    
    $phpmailer->From       = 'phnhdev@gmail.com'; // Trùng với Gmail của bạn
    $phpmailer->FromName   = 'FoodGo';            // Tên người gửi hiển thị khi nhận mail
}


