<?php
/**
 * FoodGo Checkout and AJAX Request Handlers
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// 1. Xử lý AJAX Thanh toán
function foodgo_handle_checkout()
{
    // Nhận dữ liệu từ JS
    $cart_data = isset($_POST['cart']) ? $_POST['cart'] : '';
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $address = isset($_POST['address']) ? sanitize_text_field($_POST['address']) : '';
    $ward = isset($_POST['ward']) ? sanitize_text_field($_POST['ward']) : 'Phường Bạc Liêu';
    $notes = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';
    $payment_method = isset($_POST['payment_method']) ? sanitize_text_field($_POST['payment_method']) : 'cod';

    if (empty($cart_data)) {
        wp_send_json_error('Giỏ hàng trống!');
    }

    $cart = json_decode(stripslashes($cart_data), true);
    $total = 0;
    $order_details = "THÔNG TIN KHÁCH HÀNG:\n";
    $order_details .= "Họ tên: $name\n";
    $order_details .= "Số điện thoại: $phone\n";
    $order_details .= "Xã/Phường/Khu vực: $ward\n";
    $order_details .= "Địa chỉ chi tiết: $address\n";
    $order_details .= "Ghi chú: $notes\n";
    $order_details .= "Phương thức thanh toán: " . ($payment_method === 'cod' ? 'COD' : 'Chuyển khoản') . "\n\n";

    $order_details .= "CHI TIẾT ĐƠN HÀNG:\n";
    foreach ($cart as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        $order_details .= "- " . $item['name'] . " x " . $item['quantity'] . " (" . number_format($subtotal, 0, ',', '.') . "đ)\n";
    }

    // Phường Bạc Liêu được Freeship, các khu vực khác tính ship 25k
    $shipping = ($ward === 'Phường Bạc Liêu') ? 0 : 25000;
    $discount = $total >= 400000 ? round($total * 0.1) : 0;
    $final_total = $total + $shipping - $discount;

    $order_details .= "\nTạm tính: " . number_format($total, 0, ',', '.') . "đ\n";
    if ($discount > 0) {
        $order_details .= "Giảm giá tự động (10%): -" . number_format($discount, 0, ',', '.') . "đ\n";
    }
    $order_details .= "Phí vận chuyển: " . ($shipping === 0 ? 'Freeship' : number_format($shipping, 0, ',', '.') . 'đ') . "\n";
    $order_details .= "Tổng cộng: " . number_format($final_total, 0, ',', '.') . "đ";

    // Tạo bài viết mới trong CPT foodgo_order
    $order_id = wp_insert_post(array(
        'post_title' => 'Đơn hàng từ ' . $name . ' (' . date('d/m/Y H:i') . ')',
        'post_type' => 'foodgo_order',
        'post_status' => 'publish',
        'post_content' => $order_details,
    ));

    if ($order_id) {
        update_post_meta($order_id, '_order_subtotal', $total);
        update_post_meta($order_id, '_order_shipping', $shipping);
        update_post_meta($order_id, '_order_discount', $discount);
        update_post_meta($order_id, '_order_total', $final_total);
        update_post_meta($order_id, '_billing_ward', $ward);
        update_post_meta($order_id, '_order_items_json', $cart_data);
        update_post_meta($order_id, '_billing_name', $name);
        update_post_meta($order_id, '_billing_phone', $phone);
        update_post_meta($order_id, '_billing_address', $address);
        update_post_meta($order_id, '_billing_notes', $notes);
        update_post_meta($order_id, '_payment_method', $payment_method);

        wp_send_json_success(array('order_id' => $order_id));
    } else {
        wp_send_json_error('Lỗi khi tạo đơn hàng!');
    }
}
add_action('wp_ajax_foodgo_checkout', 'foodgo_handle_checkout');
add_action('wp_ajax_nopriv_foodgo_checkout', 'foodgo_handle_checkout');

// 2. Xử lý AJAX Gửi liên hệ
function foodgo_handle_contact_submission()
{
    // Nhận dữ liệu từ JS
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

    if (empty($name) || empty($email) || empty($message)) {
        wp_send_json_error('Vui lòng điền đầy đủ các trường bắt buộc!');
    }

    $post_content = "Họ tên: $name\n";
    $post_content .= "Email: $email\n";
    $post_content .= "Số điện thoại: $phone\n\n";
    $post_content .= "Nội dung:\n$message";

    // Tạo bài viết mới trong CPT foodgo_contact
    $post_id = wp_insert_post(array(
        'post_title' => 'Liên hệ từ ' . $name . ' (' . date('d/m/Y H:i') . ')',
        'post_type' => 'foodgo_contact',
        'post_status' => 'publish',
        'post_content' => $post_content,
    ));

    if ($post_id) {
        update_post_meta($post_id, '_contact_email', $email);
        update_post_meta($post_id, '_contact_phone', $phone);
        update_post_meta($post_id, '_contact_status', 'pending');

        // Gửi email tự động phản hồi cho người dùng (Auto-responder)
        $auto_subject = 'Cảm ơn bạn đã liên hệ với FoodGo!';
        
        $auto_headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: phnhdev@gmail.com',
            'From: FoodGo <phnhdev@gmail.com>'
        );
        
        $auto_body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cảm ơn bạn đã liên hệ với FoodGo</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f6f9fc; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
                .wrapper { width: 100%; background-color: #f6f9fc; padding: 40px 0; }
                .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.03); }
                .header { background: linear-gradient(135deg, #ff7875, #ff4d4f); padding: 40px 20px; text-align: center; }
                .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px; }
                .content { padding: 40px 30px; line-height: 1.6; color: #444; }
                .content p { margin-top: 0; margin-bottom: 20px; font-size: 15px; color: #444; }
                .info-box { background: #f8fafc; border-left: 4px solid #ff4d4f; padding: 20px; border-radius: 4px; margin: 25px 0; font-size: 14px; color: #444; }
                .info-box p { margin-bottom: 10px; }
                .info-box p:last-child { margin-bottom: 0; }
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
                        <p>Xin chào <strong>' . esc_html($name) . '</strong>,</p>
                        <p>Cảm ơn bạn đã gửi liên hệ cho FoodGo. Chúng tôi đã nhận được thông điệp của bạn và sẽ phản hồi trong thời gian sớm nhất.</p>
                        
                        <p><strong>Chi tiết liên hệ đã gửi:</strong></p>
                        <div class="info-box">
                            <p><strong>Họ tên:</strong> ' . esc_html($name) . '</p>
                            <p><strong>Email:</strong> ' . esc_html($email) . '</p>
                            <p><strong>Số điện thoại:</strong> ' . esc_html($phone ?: 'Không cung cấp') . '</p>
                            <p><strong>Nội dung:</strong><br>' . esc_html($message) . '</p>
                        </div>
                        
                        <p>Chúng tôi sẽ sớm phản hồi bạn qua hộp thư này.</p>
                        <p>Trân trọng,<br><strong>Ban quản trị FoodGo</strong></p>
                    </div>
                    <div class="footer">
                        <p>Đây là email tự động từ hệ thống cửa hàng FoodGo.</p>
                        <p>&copy; ' . date('Y') . ' <a href="' . home_url() . '">FoodGo</a>. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>';
        
        wp_mail($email, $auto_subject, $auto_body, $auto_headers);

        wp_send_json_success('Gửi liên hệ thành công!');
    } else {
        wp_send_json_error('Lỗi khi lưu liên hệ!');
    }
}
add_action('wp_ajax_foodgo_submit_contact', 'foodgo_handle_contact_submission');
add_action('wp_ajax_nopriv_foodgo_submit_contact', 'foodgo_handle_contact_submission');

// 3. Xử lý bộ lọc sản phẩm AJAX
function foodgo_filter_products()
{
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'all';

    $args = array(
        'post_type'      => 'food_manager',
        'posts_per_page' => 30,
        'post_status'    => 'publish',
    );

    // Nếu không phải "Tất cả", tiến hành lọc theo taxonomy
    if ($category !== 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'food_manager_category',
                'field'    => 'slug',
                'terms'    => $category,
            ),
        );
    }

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        echo '<p style="text-align:center; padding: 40px; color: #333;">Không tìm thấy món ăn nào trong danh mục: ' . esc_html($category) . '</p>';
        wp_die();
    }

    while ($query->have_posts()) : $query->the_post();
        $post_id = get_the_ID();
        $image = foodgo_get_product_image_url($post_id);
        $variant_data = foodgo_get_default_variant_data($post_id);
    ?>
        <div class="food-card">
            <div class="food-image">
                <a href="<?php the_permalink(); ?>">
                    <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                </a>
                <button class="quick-add-btn add-to-cart"
                    data-id="<?php echo $post_id; ?>"
                    data-name="<?php echo esc_attr($variant_data['name']); ?>"
                    data-price="<?php echo $variant_data['price']; ?>"
                    data-image="<?php echo esc_url($image); ?>">
                    +
                </button>
            </div>

            <div class="food-content">
                <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                    <div class="food-top">
                        <h3><?php the_title(); ?></h3>
                        <span class="food-price"><?php echo number_format($variant_data['price'], 0, ',', '.'); ?>đ</span>
                    </div>
                    <p><?php echo wp_trim_words(get_the_content(), 10); ?></p>
                </a>
            </div>
        </div>
    <?php endwhile;
    wp_reset_postdata();
    wp_die();
}
add_action('wp_ajax_foodgo_filter_products', 'foodgo_filter_products');
add_action('wp_ajax_nopriv_foodgo_filter_products', 'foodgo_filter_products');

// 4. AJAX LẤY THÔNG TIN BIẾN THỂ CHO QUICK ADD MODAL
function foodgo_ajax_get_product_options() {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    if (!$product_id) {
        wp_send_json_error('Không tìm thấy sản phẩm!');
    }
    
    $title = get_the_title($product_id);
    $price = get_post_meta($product_id, '_food_price', true);
    $sale_price = get_post_meta($product_id, '_food_sale_price', true);
    $thumbnail = foodgo_get_product_image_url($product_id);
    $variants = get_post_meta($product_id, '_food_variants', true);
    
    $display_price = $sale_price ? $sale_price : $price;
    
    ob_start();
    ?>
    <div class="fg-modal-content-inner" data-id="<?php echo $product_id; ?>" data-base-regular="<?php echo $price; ?>" data-base-sale="<?php echo $sale_price; ?>" data-image="<?php echo esc_url($thumbnail); ?>">
        <div style="display: flex; gap: 20px; align-items: flex-start; margin-bottom: 25px;">
            <div style="width: 100px; height: 100px; border-radius: 16px; overflow: hidden; border: 1px solid rgba(0,0,0,0.05); flex-shrink: 0;">
                <img src="<?php echo esc_url($thumbnail); ?>" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div>
                <h3 style="margin: 0 0 10px 0; font-size: 20px; font-weight: 800; color: #1d1d1f;"><?php echo esc_html($title); ?></h3>
                <div class="fg-modal-price" style="font-size: 18px; font-weight: 800; color: #ff4d4f;">
                    <?php echo number_format($display_price, 0, ',', '.'); ?>₫
                </div>
            </div>
        </div>
        
        <?php if (!empty($variants) && is_array($variants)) : ?>
            <div class="fg-modal-variants-section" style="margin-bottom: 25px;">
                <?php foreach ($variants as $g_index => $group) : 
                    $is_single = ($group['type'] === 'single');
                    $input_name = 'fg_modal_variant_' . sanitize_title($group['name']);
                ?>
                    <div class="fg-modal-variant-group" data-group-name="<?php echo esc_attr($group['name']); ?>" data-type="<?php echo esc_attr($group['type']); ?>" style="margin-bottom: 15px;">
                        <div class="fg-modal-variant-group-title" style="font-size: 14px; font-weight: 700; color: #1d1d1f; margin-bottom: 8px;">
                            <?php echo esc_html($group['name']); ?> 
                            <span style="font-weight: normal; font-size: 11px; color: #888;">(<?php echo $is_single ? 'Chọn 1' : 'Chọn nhiều'; ?>)</span>
                        </div>
                        <div class="fg-modal-variant-options" style="display: flex; flex-wrap: wrap; gap: 8px; margin: 15px 0 !important;">
                            <?php foreach ($group['options'] as $o_index => $opt) : 
                                $checked = ($is_single && $o_index === 0) ? 'checked' : '';
                            ?>
                                <label class="fg-variant-pill-label">
                                    <input type="<?php echo $is_single ? 'radio' : 'checkbox'; ?>" 
                                           name="<?php echo esc_attr($input_name); ?>" 
                                           value="<?php echo esc_attr($opt['name']); ?>"
                                           data-price="<?php echo intval($opt['price']); ?>"
                                           <?php echo $checked; ?>>
                                    <span class="fg-variant-pill-span">
                                        <?php echo esc_html($opt['name']); ?>
                                        <?php if ($opt['price'] > 0) : ?>
                                            <span style="font-size: 11px; margin-left: 4px; opacity: 0.9;">
                                                (+<?php echo number_format($opt['price'], 0, ',', '.'); ?>₫)
                                            </span>
                                        <?php endif; ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div style="display: flex; gap: 15px; align-items: center; border-top: 1px solid #f0f0f2; padding-top: 20px;">
            <div class="fg-quantity" style="display: flex; align-items: center; background: #f5f5f7; border-radius: 999px; height: 46px; padding: 0 8px;">
                <button type="button" class="fg-qty-btn-minus" style="border: none; background: none; width: 28px; height: 28px; font-size: 16px; cursor: pointer; color: #666;">-</button>
                <input type="text" value="1" class="fg-qty-input" style="width: 28px; text-align: center; border: none; font-size: 14px; font-weight: 700; color: #1d1d1f; outline: none; background: none;" readonly>
                <button type="button" class="fg-qty-btn-plus" style="border: none; background: none; width: 28px; height: 28px; font-size: 16px; cursor: pointer; color: #666;">+</button>
            </div>
            <button class="fg-modal-add-btn" style="flex: 1; height: 46px; border: none; border-radius: 999px; background: linear-gradient(135deg, #ff7875, #ff4d4f); color: #fff; font-size: 14px; font-weight: 700; cursor: pointer; box-shadow: 0 8px 16px rgba(255, 77, 79, 0.15); transition: 0.3s;">
                Xác nhận đặt món
            </button>
        </div>
    </div>
    <?php
    $html = ob_get_clean();
    wp_send_json_success(array(
        'html' => $html,
        'has_variants' => !empty($variants)
    ));
}
add_action('wp_ajax_foodgo_get_product_options', 'foodgo_ajax_get_product_options');
add_action('wp_ajax_nopriv_foodgo_get_product_options', 'foodgo_ajax_get_product_options');

