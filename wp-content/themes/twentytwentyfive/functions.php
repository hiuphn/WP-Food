<?php

/**
 * Twenty Twenty-Five functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

// Adds theme support for post formats.
if (! function_exists('twentytwentyfive_post_format_setup')) :
    /**
     * Adds theme support for post formats.
     *
     * @since Twenty Twenty-Five 1.0
     *
     * @return void
     */
    function twentytwentyfive_post_format_setup()
    {
        add_theme_support('post-formats', array('aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video'));
    }
endif;
add_action('after_setup_theme', 'twentytwentyfive_post_format_setup');

// Enqueues editor-style.css in the editors.
if (! function_exists('twentytwentyfive_editor_style')) :
    /**
     * Enqueues editor-style.css in the editors.
     *
     * @since Twenty Twenty-Five 1.0
     *
     * @return void
     */
    function twentytwentyfive_editor_style()
    {
        add_editor_style('assets/css/editor-style.css');
        add_editor_style('assets/css/foodgo-core.css');
        add_editor_style('assets/css/foodgo-header-footer.css');
        add_editor_style('assets/css/foodgo-home.css');
        add_editor_style('assets/css/foodgo-cart.css');
        add_editor_style('assets/css/foodgo-internal.css');
    }
endif;
add_action('after_setup_theme', 'twentytwentyfive_editor_style');

// Enqueues the theme stylesheet on the front.
if (! function_exists('twentytwentyfive_enqueue_styles')) :
    /**
     * Enqueues the theme stylesheet on the front.
     *
     * @since Twenty Twenty-Five 1.0
     *
     * @return void
     */
    function twentytwentyfive_enqueue_styles()
    {
        $suffix = SCRIPT_DEBUG ? '' : '.min';
        $src    = 'style' . $suffix . '.css';

        wp_enqueue_style('twentytwentyfive-style', get_parent_theme_file_uri($src), array(), wp_get_theme()->get('Version'));

        // SweetAlert2 CSS
        wp_enqueue_style('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css', array(), '11.0');

        // FoodGo Modular Styles
        wp_enqueue_style('foodgo-core', get_template_directory_uri() . '/assets/css/foodgo-core.css', array(), '1.0');
        wp_enqueue_style('foodgo-header-footer', get_template_directory_uri() . '/assets/css/foodgo-header-footer.css', array('foodgo-core'), '1.0');
        wp_enqueue_style('foodgo-home', get_template_directory_uri() . '/assets/css/foodgo-home.css', array('foodgo-core'), '1.0');
        wp_enqueue_style('foodgo-cart', get_template_directory_uri() . '/assets/css/foodgo-cart.css', array('foodgo-core'), '1.0');
        wp_enqueue_style('foodgo-internal', get_template_directory_uri() . '/assets/css/foodgo-internal.css', array('foodgo-core'), '1.0');
        wp_enqueue_style('foodgo-order', get_template_directory_uri() . '/assets/css/foodgo-order.css', array('foodgo-core'), '1.0');
        wp_enqueue_style('foodgo-contact', get_template_directory_uri() . '/assets/css/foodgo-contact.css', array('foodgo-core'), '1.0');

        wp_style_add_data('twentytwentyfive-style', 'path', get_parent_theme_file_path($src));

        wp_enqueue_script(
            'foodgo-cart',
            get_template_directory_uri() . '/assets/js/foodgo-cart.js',
            array(),
            time(), // Sử dụng timestamp để tránh cache trình duyệt
            true    // Chuyển xuống FOOTER
        );

        wp_localize_script('foodgo-cart', 'foodgo_vars', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));

        // SweetAlert2 JS
        wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array(), '11.0', true);

        wp_enqueue_script(
            'foodgo-contact',
            get_template_directory_uri() . '/assets/js/foodgo-contact.js',
            array('foodgo-cart', 'sweetalert2'),
            time(),
            true
        );

        wp_enqueue_script(
            'foodgo-order',
            get_template_directory_uri() . '/assets/js/foodgo-order.js',
            array('foodgo-cart'),
            time(),
            true
        );
    }
endif;
add_action('wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles', 99);

// Registers custom block styles.
if (! function_exists('twentytwentyfive_block_styles')) :
    /**
     * Registers custom block styles.
     *
     * @since Twenty Twenty-Five 1.0
     *
     * @return void
     */
    function twentytwentyfive_block_styles()
    {
        register_block_style(
            'core/list',
            array(
                'name'         => 'checkmark-list',
                'label'        => __('Checkmark', 'twentytwentyfive'),
                'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
            )
        );
    }
endif;
add_action('init', 'twentytwentyfive_block_styles');

// Registers pattern categories.
if (! function_exists('twentytwentyfive_pattern_categories')) :
    /**
     * Registers pattern categories.
     *
     * @since Twenty Twenty-Five 1.0
     *
     * @return void
     */
    function twentytwentyfive_pattern_categories()
    {

        register_block_pattern_category(
            'twentytwentyfive_page',
            array(
                'label'       => __('Pages', 'twentytwentyfive'),
                'description' => __('A collection of full page layouts.', 'twentytwentyfive'),
            )
        );

        register_block_pattern_category(
            'twentytwentyfive_post-format',
            array(
                'label'       => __('Post formats', 'twentytwentyfive'),
                'description' => __('A collection of post format patterns.', 'twentytwentyfive'),
            )
        );
    }
endif;
add_action('init', 'twentytwentyfive_pattern_categories');

// Registers block binding sources.
if (! function_exists('twentytwentyfive_register_block_bindings')) :
    /**
     * Registers the post format block binding source.
     *
     * @since Twenty Twenty-Five 1.0
     *
     * @return void
     */
    function twentytwentyfive_register_block_bindings()
    {
        register_block_bindings_source(
            'twentytwentyfive/format',
            array(
                'label'              => _x('Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive'),
                'get_value_callback' => 'twentytwentyfive_format_binding',
            )
        );
    }
endif;
add_action('init', 'twentytwentyfive_register_block_bindings');

// Registers block binding callback function for the post format name.
if (! function_exists('twentytwentyfive_format_binding')) :
    /**
     * Callback function for the post format name block binding source.
     *
     * @since Twenty Twenty-Five 1.0
     *
     * @return string|void Post format name, or nothing if the format is 'standard'.
     */
    function twentytwentyfive_format_binding()
    {
        $post_format_slug = get_post_format();

        if ($post_format_slug && 'standard' !== $post_format_slug) {
            return get_post_format_string($post_format_slug);
        }
    }
endif;

require_once get_template_directory() . '/inc/foodgo-patterns.php';

/**
 * FOODGO ORDER SYSTEM - LƯU ĐƠN HÀNG VÀO DATABASE
 */

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

// 1.1 Đăng ký Custom Post Type "Tin nhắn Liên hệ"
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


// 2. Xử lý AJAX Thanh toán
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

// 2.1 Xử lý AJAX Gửi liên hệ
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
        wp_send_json_success('Gửi liên hệ thành công!');
    } else {
        wp_send_json_error('Lỗi khi lưu liên hệ!');
    }
}
add_action('wp_ajax_foodgo_submit_contact', 'foodgo_handle_contact_submission');
add_action('wp_ajax_nopriv_foodgo_submit_contact', 'foodgo_handle_contact_submission');


/**
 * HIỂN THỊ DANH SÁCH MÓN ĂN THẬT TỪ FOOD MANAGER
 */
function foodgo_render_menu_shortcode()
{
    $args = array(
        'post_type'      => 'food_manager',
        'posts_per_page' => 12,
        'post_status'    => 'publish',
    );

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        return '<p style="text-align:center; padding: 40px;">Chưa có món ăn nào trong thực đơn.</p>';
    }

    ob_start();
?>
    <div class="food-grid">
        <?php while ($query->have_posts()) : $query->the_post();
            $price = get_post_meta(get_the_ID(), '_food_price', true);
            $image = get_post_meta(get_the_ID(), '_food_banner', true);
            $rating = get_post_meta(get_the_ID(), '_food_rating', true) ?: '4.8';
        ?>
            <div class="food-card">
                <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit; display: block;">
                    <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                    <div class="food-content">
                        <div class="food-top">
                            <h3><?php the_title(); ?></h3>
                            <p class="price"><?php echo number_format($price, 0, ',', '.'); ?>đ</p>
                        </div>
                        <p><?php echo wp_trim_words(get_the_content(), 15); ?></p>
                    </div>
                </a>
                <div class="food-content" style="padding-top: 0 !important;">
                    <div class="food-bottom">
                        <span class="rating"><?php echo $rating; ?>★</span>
                        <p class="btn add-to-cart">Đặt món</p>
                    </div>
                </div>
            </div>
        <?php endwhile;
        wp_reset_postdata(); ?>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('foodgo_menu', 'foodgo_render_menu_shortcode');

/**
 * HIỂN THỊ DANH MỤC NỔI BẬT (Shortcode)
 */
function foodgo_render_featured_categories_shortcode()
{
    $categories = get_terms(array(
        'taxonomy' => 'food_manager_category',
        'hide_empty' => true,
    ));

    if (is_wp_error($categories) || empty($categories)) {
        return '';
    }

    ob_start();
?>
    <div class="featured-categories" style="margin-bottom: 60px; margin-top: 40px;">
        <div class="section-title" style="text-align: center; margin-bottom: 30px;">
            <h2 style="font-size: 38px; font-weight: 900; letter-spacing: -1px; color: #1d1d1f; margin-bottom: 10px;">Danh mục nổi bật</h2>
            <p style="color: #666; font-size: 16px;">Khám phá các món ăn theo danh mục</p>
        </div>
        <div class="categories-list" style="display: flex; gap: 15px; overflow-x: auto; padding: 10px 5px; justify-content: center; flex-wrap: wrap;">
            <?php foreach ($categories as $cat) : ?>
                <a href="/dat-mon?category=<?php echo esc_attr($cat->slug); ?>" class="category-card" style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; justify-content: center; padding: 12px 24px !important; background: rgba(255, 255, 255, 0.75); backdrop-filter: blur(20px); border: 1px solid rgba(0, 0, 0, 0.06); border-radius: 999px; font-weight: 700; font-size: 15px; color: #1d1d1f; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03); transition: all 0.3s ease;">
                    <?php echo esc_html($cat->name); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <style>
        .category-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 77, 79, 0.12);
            border-color: rgba(255, 77, 79, 0.3);
            background: #fff !important;
            color: #ff4d4f !important;
        }

        .categories-list::-webkit-scrollbar {
            display: none;
        }

        .categories-list {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
<?php
    return ob_get_clean();
}
add_shortcode('foodgo_featured_categories', 'foodgo_render_featured_categories_shortcode');

/**
 * HIỂN THỊ CHI TIẾT SẢN PHẨM (Shortcode)
 */
function foodgo_render_product_detail_shortcode()
{
    if (!is_singular('food_manager')) {
        return '';
    }

    $post_id = get_the_ID();
    $title = get_the_title();
    $content = get_the_content();
    $thumbnail = get_post_meta($post_id, '_food_banner', true);
    $price = get_post_meta($post_id, '_food_price', true);
    $sale_price = get_post_meta($post_id, '_food_sale_price', true);

    // Format price
    $display_price = '';
    if (!empty($sale_price)) {
        $display_price = '<del style="color: #999; font-size: 0.8em; margin-right: 10px;">' . number_format($price, 0, '', '.') . '₫</del>' . number_format($sale_price, 0, '', '.') . '₫';
    } else if (!empty($price)) {
        $display_price = number_format($price, 0, '', '.') . '₫';
    } else {
        $display_price = 'Liên hệ';
    }

    ob_start();
?>
    <div class="fg-product-detail" style="padding: 180px 0 80px 0 !important; background: #f8f8fa !important; width: 100% !important; box-sizing: border-box !important;">
        <div class="fg-container" style="max-width: 1200px !important; margin: 0 auto !important; padding: 0 20px !important; box-sizing: border-box !important;">
            <div class="fg-product-layout" style="display: flex !important; gap: 60px !important; align-items: flex-start !important; width: 100% !important; box-sizing: border-box !important;">

                <!-- Left: Image -->
                <div class="fg-image-side" style="flex: 0 0 calc(50% - 30px) !important; max-width: calc(50% - 30px) !important; position: sticky !important; top: 120px !important; box-sizing: border-box !important;">
                    <div class="fg-image-wrapper" style="border-radius: 24px !important; overflow: hidden !important; background: #fff !important; box-shadow: 0 15px 40px rgba(0,0,0,0.04) !important; border: 1px solid rgba(0,0,0,0.05) !important;">
                        <?php if ($thumbnail) : ?>
                            <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($title); ?>" style="width: 100% !important; height: auto !important; display: block !important; object-fit: cover !important;">
                        <?php else : ?>
                            <div style="width: 100% !important; height: 400px !important; background: #eee !important; display: flex !important; align-items: center !important; justify-content: center !important; color: #aaa !important;">No Image</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right: Content -->
                <div class="fg-content-side" style="flex: 0 0 calc(50% - 30px) !important; max-width: calc(50% - 30px) !important; background: rgba(255, 255, 255, 0.9) !important; backdrop-filter: blur(20px) !important; border-radius: 24px !important; padding: 40px !important; border: 1px solid rgba(0,0,0,0.05) !important; box-shadow: 0 10px 40px rgba(0,0,0,0.04) !important; box-sizing: border-box !important;">
                    <a href="/dat-mon" style="display: inline-flex !important; align-items: center !important; gap: 8px !important; color: #ff4d4f !important; text-decoration: none !important; font-weight: 700 !important; font-size: 14px !important; margin-bottom: 20px !important; text-transform: uppercase !important; letter-spacing: 1px !important;">
                        ← Quay lại thực đơn
                    </a>

                    <h1 style="font-size: 36px !important; font-weight: 800 !important; color: #1d1d1f !important; margin-bottom: 15px !important; line-height: 1.2 !important; letter-spacing: -0.5px !important;"><?php echo esc_html($title); ?></h1>

                    <div class="fg-price" style="font-size: 28px !important; font-weight: 800 !important; color: #ff4d4f !important; margin-bottom: 20px !important;">
                        <?php echo $display_price; ?>
                    </div>

                    <div class="fg-description" style="color: #666 !important; font-size: 15px !important; line-height: 1.6 !important; margin-bottom: 30px !important;">
                        <?php echo wp_kses_post($content); ?>
                    </div>

                    <div class="fg-actions" style="display: flex !important; gap: 15px !important; align-items: center !important;">
                        <div class="fg-quantity" style="display: flex !important; align-items: center !important; background: #fff !important; border: 1px solid rgba(0,0,0,0.1) !important; border-radius: 999px !important; height: 50px !important; padding: 0 10px !important;">
                            <button style="border: none !important; background: none !important; width: 30px !important; height: 30px !important; font-size: 18px !important; cursor: pointer !important; color: #666 !important;">-</button>
                            <input type="text" value="1" style="width: 30px !important; text-align: center !important; border: none !important; font-size: 15px !important; font-weight: 700 !important; color: #1d1d1f !important; outline: none !important; background: none !important;" readonly>
                            <button style="border: none !important; background: none !important; width: 30px !important; height: 30px !important; font-size: 18px !important; cursor: pointer !important; color: #666 !important;">+</button>
                        </div>

                        <button class="fg-btn add-to-cart"
                            data-id="<?php echo $post_id; ?>"
                            data-name="<?php echo esc_attr($title); ?>"
                            data-price="<?php echo $sale_price ? $sale_price : $price; ?>"
                            data-image="<?php echo esc_url($thumbnail); ?>"
                            style="flex: 1 !important; height: 50px !important; border: none !important; border-radius: 999px !important; background: linear-gradient(135deg, #ff7875, #ff4d4f) !important; color: #fff !important; font-size: 15px !important; font-weight: 700 !important; cursor: pointer !important; box-shadow: 0 10px 20px rgba(255, 77, 79, 0.15) !important; transition: all 0.3s ease !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; white-space: nowrap !important; padding: 0 30px 20px !important;">
                            Thêm vào giỏ hàng
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .fg-btn:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 15px 30px rgba(255, 77, 79, 0.2) !important;
        }

        @media (max-width: 991px) {
            .fg-product-layout {
                flex-direction: column !important;
                gap: 30px !important;
            }

            .fg-image-side {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                position: static !important;
            }

            .fg-content-side {
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('foodgo_product_detail', 'foodgo_render_product_detail_shortcode');

/**
 * HIỂN THỊ TRANG THANH TOÁN (Shortcode)
 */
function foodgo_render_checkout_shortcode()
{
    if (!is_user_logged_in()) {
        ob_start();
    ?>
        <div class="fg-checkout-page" style="padding: 160px 0 80px 0 !important; background: #f8f8fa !important; width: 100% !important; box-sizing: border-box !important;">
            <div class="fg-container" style="max-width: 600px !important; margin: 0 auto !important; padding: 0 20px !important; box-sizing: border-box !important; text-align: center !important;">
                <div style="background: rgba(255, 255, 255, 0.9) !important; backdrop-filter: blur(20px) !important; border-radius: 24px !important; padding: 40px !important; border: 1px solid rgba(0,0,0,0.05) !important; box-shadow: 0 10px 40px rgba(0,0,0,0.04) !important;">
                    <div style="font-size: 60px !important; margin-bottom: 20px !important;">🔒</div>
                    <h2 style="font-size: 28px !important; font-weight: 800 !important; color: #1d1d1f !important; margin-bottom: 10px !important;">Bạn chưa đăng nhập</h2>
                    <p style="color: #666 !important; font-size: 16px !important; margin-bottom: 30px !important;">Vui lòng đăng nhập tài khoản để tiếp tục thanh toán đơn hàng.</p>
                    <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; height: 50px !important; padding: 0 30px !important; border-radius: 999px !important; background: linear-gradient(135deg, #ff7875, #ff4d4f) !important; color: #fff !important; text-decoration: none !important; font-weight: 700 !important; box-shadow: 0 10px 20px rgba(255, 77, 79, 0.15) !important;">
                        Đăng nhập ngay
                    </a>
                    <div style="margin-top: 20px !important;">
                        <a href="/dat-mon" style="color: #666 !important; text-decoration: none !important; font-size: 14px !important;">Quay lại đặt món</a>
                    </div>
                </div>
            </div>
        </div>
    <?php
        return ob_get_clean();
    }

    ob_start();
    ?>
    <div class="fg-checkout-page" style="padding: 160px 0 80px 0 !important; background: #f8f8fa !important; width: 100% !important; box-sizing: border-box !important;">
        <div class="fg-container" style="max-width: 1200px !important; margin: 0 auto !important; padding: 0 20px !important; box-sizing: border-box !important;">
            <div class="fg-checkout-layout" style="display: flex !important; gap: 40px !important; align-items: flex-start !important; width: 100% !important; box-sizing: border-box !important;">

                <!-- Left: Checkout Form -->
                <div class="fg-checkout-form-side" style="flex: 0 0 calc(60% - 20px) !important; max-width: calc(60% - 20px) !important; background: rgba(255, 255, 255, 0.9) !important; backdrop-filter: blur(20px) !important; border-radius: 24px !important; padding: 40px !important; border: 1px solid rgba(0,0,0,0.05) !important; box-shadow: 0 10px 40px rgba(0,0,0,0.04) !important; box-sizing: border-box !important;">
                    <h2 style="font-size: 28px !important; font-weight: 800 !important; color: #1d1d1f !important; margin-bottom: 30px !important;">Thông tin giao hàng</h2>

                    <form id="fg-checkout-form">
                        <div class="form-group" style="margin-bottom: 20px !important;">
                            <label style="display: block !important; font-weight: 700 !important; color: #333 !important; margin-bottom: 8px !important;">Họ và tên *</label>
                            <input type="text" id="billing_name" required style="width: 100% !important; height: 50px !important; border-radius: 12px !important; border: 1px solid rgba(0,0,0,0.1) !important; padding: 0 15px !important; font-size: 15px !important; outline: none !important; background: #fff !important; box-sizing: border-box !important;">
                        </div>

                        <div class="form-group" style="margin-bottom: 20px !important;">
                            <label style="display: block !important; font-weight: 700 !important; color: #333 !important; margin-bottom: 8px !important;">Số điện thoại *</label>
                            <input type="tel" id="billing_phone" required style="width: 100% !important; height: 50px !important; border-radius: 12px !important; border: 1px solid rgba(0,0,0,0.1) !important; padding: 0 15px !important; font-size: 15px !important; outline: none !important; background: #fff !important; box-sizing: border-box !important;">
                        </div>

                        <div class="form-group" style="margin-bottom: 20px !important;">
                            <label style="display: block !important; font-weight: 700 !important; color: #333 !important; margin-bottom: 8px !important;">Địa chỉ chi tiết (Số nhà, tên đường...) *</label>
                            <input type="text" id="billing_address" required style="width: 100% !important; height: 50px !important; border-radius: 12px !important; border: 1px solid rgba(0,0,0,0.1) !important; padding: 0 15px !important; font-size: 15px !important; outline: none !important; background: #fff !important; box-sizing: border-box !important;">
                        </div>

                        <div class="form-group" style="margin-bottom: 20px !important;">
                            <label style="display: block !important; font-weight: 700 !important; color: #333 !important; margin-bottom: 8px !important;">Xã / Phường / Khu vực *</label>
                            <select id="billing_ward" required style="width: 100% !important; height: 50px !important; border-radius: 12px !important; border: 1px solid rgba(0,0,0,0.1) !important; padding: 0 15px !important; font-size: 15px !important; outline: none !important; background: #fff !important; box-sizing: border-box !important; appearance: auto !important;">
                                <option value="Phường Bạc Liêu">Phường Bạc Liêu (Freeship)</option>
                                <option value="Phường An Xuyên">Phường An Xuyên (Phí ship 25k)</option>
                                <option value="Xã An Trạch">Xã An Trạch (Phí ship 25k)</option>
                                <option value="Xã Đất Mũi">Xã Đất Mũi (Phí ship 25k)</option>
                                <option value="Xã Năm Căn">Xã Năm Căn (Phí ship 25k)</option>
                                <option value="Xã U Minh">Xã U Minh (Phí ship 25k)</option>
                                <option value="Xã Đầm Dơi">Xã Đầm Dơi (Phí ship 25k)</option>
                                <option value="Xã Cái Nước">Xã Cái Nước (Phí ship 25k)</option>
                                <option value="Phường Giá Rai">Phường Giá Rai (Phí ship 25k)</option>
                                <option value="Phường Láng Tròn">Phường Láng Tròn (Phí ship 25k)</option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom: 30px !important;">
                            <label style="display: block !important; font-weight: 700 !important; color: #333 !important; margin-bottom: 8px !important;">Ghi chú đơn hàng</label>
                            <textarea id="billing_notes" style="width: 100% !important; height: 100px !important; border-radius: 12px !important; border: 1px solid rgba(0,0,0,0.1) !important; padding: 15px !important; font-size: 15px !important; outline: none !important; background: #fff !important; resize: vertical !important; box-sizing: border-box !important;"></textarea>
                        </div>

                        <h2 style="font-size: 24px !important; font-weight: 800 !important; color: #1d1d1f !important; margin-bottom: 20px !important;">Phương thức thanh toán</h2>

                        <div class="payment-methods" style="display: grid !important; gap: 15px !important; margin-bottom: 30px !important;">
                            <label style="display: flex !important; align-items: center !important; gap: 10px !important; padding: 15px !important; border: 1px solid rgba(0,0,0,0.1) !important; border-radius: 12px !important; background: #fff !important; cursor: pointer !important;">
                                <input type="radio" name="payment_method" value="cod" checked style="accent-color: #ff4d4f !important;">
                                <div>
                                    <div style="font-weight: 700 !important; color: #333 !important;">Thanh toán khi nhận hàng (COD)</div>
                                    <div style="font-size: 13px !important; color: #666 !important;">Thanh toán bằng tiền mặt khi shipper giao hàng</div>
                                </div>
                            </label>

                            <label style="display: flex !important; align-items: center !important; gap: 10px !important; padding: 15px !important; border: 1px solid rgba(0,0,0,0.1) !important; border-radius: 12px !important; background: #fff !important; cursor: pointer !important;">
                                <input type="radio" name="payment_method" value="bank" style="accent-color: #ff4d4f !important;">
                                <div>
                                    <div style="font-weight: 700 !important; color: #333 !important;">Chuyển khoản ngân hàng</div>
                                    <div style="font-size: 13px !important; color: #666 !important;">Chuyển khoản qua số tài khoản (Sẽ hiển thị sau khi đặt hàng)</div>
                                </div>
                            </label>
                        </div>
                    </form>
                </div>

                <!-- Right: Order Summary -->
                <div class="fg-checkout-summary-side" style="flex: 0 0 calc(40% - 20px) !important; max-width: calc(40% - 20px) !important; background: rgba(255, 255, 255, 0.9) !important; backdrop-filter: blur(20px) !important; border-radius: 24px !important; padding: 40px !important; border: 1px solid rgba(0,0,0,0.05) !important; box-shadow: 0 10px 40px rgba(0,0,0,0.04) !important; position: sticky !important; top: 120px !important; box-sizing: border-box !important;">
                    <h2 style="font-size: 24px !important; font-weight: 800 !important; color: #1d1d1f !important; margin-bottom: 20px !important;">Đơn hàng của bạn</h2>

                    <div id="fg-checkout-items" style="max-height: 300px !important; overflow-y: auto !important; margin-bottom: 20px !important; padding-right: 10px !important;">
                        <!-- Items will be loaded here by JS -->
                        <p style="color: #666 !important; text-align: center !important;">Giỏ hàng trống</p>
                    </div>

                    <div class="summary-row" style="display: flex !important; justify-content: space-between !important; margin-bottom: 15px !important; padding-top: 15px !important; border-top: 1px solid rgba(0,0,0,0.05) !important;">
                        <span style="color: #666 !important;">Tạm tính:</span>
                        <span id="fg-checkout-subtotal" style="font-weight: 700 !important; color: #1d1d1f !important;">0₫</span>
                    </div>

                    <div class="summary-row" style="display: flex !important; justify-content: space-between !important; margin-bottom: 15px !important;">
                        <span style="color: #666 !important;">Phí vận chuyển:</span>
                        <span id="fg-checkout-shipping" style="font-weight: 700 !important; color: #1d1d1f !important;">Freeship</span>
                    </div>

                    <div id="fg-checkout-discount-row" class="summary-row" style="display: none !important; justify-content: space-between !important; margin-bottom: 15px !important; color: #52c41a !important;">
                        <span>🎁 Giảm giá tự động (10%):</span>
                        <span id="fg-checkout-discount" style="font-weight: 700 !important;">-0₫</span>
                    </div>

                    <div class="summary-row" style="display: flex !important; justify-content: space-between !important; margin-bottom: 30px !important; font-size: 20px !important; font-weight: 800 !important;">
                        <span>Tổng cộng:</span>
                        <span id="fg-checkout-total" style="color: #ff4d4f !important;">0₫</span>
                    </div>

                    <button id="fg-submit-order" style="width: 100% !important; height: 56px !important; border: none !important; border-radius: 999px !important; background: linear-gradient(135deg, #ff7875, #ff4d4f) !important; color: #fff !important; font-size: 16px !important; font-weight: 700 !important; cursor: pointer !important; box-shadow: 0 10px 20px rgba(255, 77, 79, 0.15) !important; transition: 0.3s !important;">
                        Đặt hàng ngay
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemsContainer = document.getElementById('fg-checkout-items');
            const subtotalEl = document.getElementById('fg-checkout-subtotal');
            const shippingEl = document.getElementById('fg-checkout-shipping');
            const discountRow = document.getElementById('fg-checkout-discount-row');
            const discountEl = document.getElementById('fg-checkout-discount');
            const totalEl = document.getElementById('fg-checkout-total');
            const submitBtn = document.getElementById('fg-submit-order');

            // Đọc giỏ hàng từ localStorage
            let cart = JSON.parse(localStorage.getItem('foodgo_cart')) || [];

            if (cart.length === 0) {
                itemsContainer.innerHTML = '<p style="color: #666 !important; text-align: center !important; padding: 20px !important;">Giỏ hàng của bạn đang trống. <a href="/dat-mon" style="color: #ff4d4f !important; font-weight: 700 !important;">Quay lại đặt món</a></p>';
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
                submitBtn.style.cursor = 'not-allowed';
                return;
            }

            // Render items
            itemsContainer.innerHTML = '';
            let total = 0;

            cart.forEach(item => {
                total += item.price * item.quantity;
                const itemEl = document.createElement('div');
                itemEl.style.display = 'flex';
                itemEl.style.gap = '15px';
                itemEl.style.marginBottom = '15px';
                itemEl.style.alignItems = 'center';

                itemEl.innerHTML = `
                    <div style="width: 60px; height: 60px; border-radius: 12px; overflow: hidden; border: 1px solid rgba(0,0,0,0.05);">
                        <img src="${item.image}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 700; color: #1d1d1f;">${item.name}</div>
                        <div style="color: #666; font-size: 14px;">Số lượng: ${item.quantity}</div>
                    </div>
                    <div style="font-weight: 700; color: #333;">${(item.price * item.quantity).toLocaleString('vi-VN')}₫</div>
                `;
                itemsContainer.appendChild(itemEl);
            });

            // Quản lý phí ship theo xã phường và giảm giá tự động
            const wardSelect = document.getElementById('billing_ward');

            function updateCheckoutSummary() {
                const selectedWard = wardSelect.value;
                const shipping = (selectedWard === 'Phường Bạc Liêu') ? 0 : 25000;
                const discount = total >= 400000 ? Math.round(total * 0.1) : 0;
                const finalTotal = total + shipping - discount;

                subtotalEl.textContent = total.toLocaleString('vi-VN') + '₫';
                shippingEl.textContent = shipping === 0 ? 'Freeship' : shipping.toLocaleString('vi-VN') + '₫';

                if (discount > 0) {
                    discountEl.textContent = '-' + discount.toLocaleString('vi-VN') + '₫';
                    discountRow.style.setProperty('display', 'flex', 'important');
                } else {
                    discountRow.style.setProperty('display', 'none', 'important');
                }

                totalEl.textContent = finalTotal.toLocaleString('vi-VN') + '₫';
            }

            // Lắng nghe sự kiện đổi Xã/Phường
            wardSelect.addEventListener('change', updateCheckoutSummary);
            updateCheckoutSummary(); // Chạy lần đầu

            // Xử lý đặt hàng
            submitBtn.addEventListener('click', function() {
                const name = document.getElementById('billing_name').value;
                const phone = document.getElementById('billing_phone').value;
                const address = document.getElementById('billing_address').value;
                const ward = wardSelect.value;
                const notes = document.getElementById('billing_notes').value;
                const payment_method = document.querySelector('input[name="payment_method"]:checked').value;

                if (!name || !phone || !address || !ward) {
                    alert('Vui lòng điền đầy đủ các thông tin có dấu *');
                    return;
                }

                submitBtn.innerHTML = 'Đang xử lý...';
                submitBtn.style.pointerEvents = 'none';

                // Gửi AJAX tạo đơn hàng
                const formData = new FormData();
                formData.append('action', 'foodgo_checkout');
                formData.append('name', name);
                formData.append('phone', phone);
                formData.append('address', address);
                formData.append('ward', ward);
                formData.append('notes', notes);
                formData.append('payment_method', payment_method);
                formData.append('cart', JSON.stringify(cart));

                fetch('/wp-admin/admin-ajax.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('🎉 Đặt hàng thành công! Cảm ơn bạn.');
                            localStorage.removeItem('foodgo_cart');
                            window.location.href = '/cam-on?order_id=' + data.data.order_id;
                        } else {
                            alert('❌ Lỗi: ' + (data.data || 'Không thể tạo đơn hàng'));
                            submitBtn.innerHTML = 'Đặt hàng ngay';
                            submitBtn.style.pointerEvents = 'auto';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('❌ Có lỗi xảy ra. Vui lòng thử lại.');
                        submitBtn.innerHTML = 'Đặt hàng ngay';
                        submitBtn.style.pointerEvents = 'auto';
                    });
            });
        });
    </script>
<?php
    return ob_get_clean();
}
add_shortcode('foodgo_checkout', 'foodgo_render_checkout_shortcode');

/**
 * HIỂN THỊ TRANG CẢM ƠN & MÃ QR (Shortcode)
 */
function foodgo_render_thankyou_shortcode()
{
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

    if (!$order_id) {
        return '<p style="text-align:center; padding: 40px;">Không tìm thấy đơn hàng!</p>';
    }

    $order = get_post($order_id);
    if (!$order || $order->post_type !== 'foodgo_order') {
        return '<p style="text-align:center; padding: 40px;">Đơn hàng không hợp lệ!</p>';
    }

    $total = get_post_meta($order_id, '_order_total', true);
    $subtotal = get_post_meta($order_id, '_order_subtotal', true) ?: $total;
    $shipping = get_post_meta($order_id, '_order_shipping', true);
    $shipping = ($shipping === '' || $shipping === false) ? 0 : intval($shipping);
    $payment_method = get_post_meta($order_id, '_payment_method', true);
    $name = get_post_meta($order_id, '_billing_name', true);
    $phone = get_post_meta($order_id, '_billing_phone', true);
    $address = get_post_meta($order_id, '_billing_address', true);
    $ward = get_post_meta($order_id, '_billing_ward', true) ?: 'Phường Bạc Liêu';
    $notes = get_post_meta($order_id, '_billing_notes', true);
    $items_json = get_post_meta($order_id, '_order_items_json', true);
    $items = json_decode($items_json, true) ?: [];

    // Tạo link VietQR nếu là chuyển khoản hoặc COD muốn trả trước
    $bank_id = 'OCB';
    $account_no = '0048100004557477';
    $account_name = rawurlencode('NGUYEN THI THU THAO');
    $amount = $total;
    $description = rawurlencode('DH' . $order_id);

    $qr_url = "https://img.vietqr.io/image/{$bank_id}-{$account_no}-compact.png?amount={$amount}&addInfo={$description}&accountName={$account_name}";

    ob_start();
?>
    <div class="fg-thankyou-page" style="padding: 160px 0 80px 0 !important; background: #f8f8fa !important; width: 100% !important; box-sizing: border-box !important;">
        <div class="fg-container" style="max-width: 800px !important; margin: 0 auto !important; padding: 0 20px !important; box-sizing: border-box !important;">

            <div class="fg-thankyou-box" style="background: rgba(255, 255, 255, 0.9) !important; backdrop-filter: blur(20px) !important; border-radius: 24px !important; padding: 40px !important; border: 1px solid rgba(0,0,0,0.05) !important; box-shadow: 0 10px 40px rgba(0,0,0,0.04) !important; text-align: center !important; box-sizing: border-box !important;">

                <div style="width: 80px !important; height: 80px !important; background: #52c41a !important; color: #fff !important; border-radius: 50% !important; display: flex !important; align-items: center !important; justify-content: center !important; font-size: 40px !important; margin: 0 auto 20px auto !important;">✓</div>

                <h1 style="font-size: 32px !important; font-weight: 800 !important; color: #1d1d1f !important; margin-bottom: 10px !important;">Đặt hàng thành công!</h1>
                <p style="color: #666 !important; font-size: 16px !important; margin-bottom: 30px !important;">Cảm ơn <strong><?php echo esc_html($name); ?></strong> đã tin tưởng FoodGo. Đơn hàng của bạn đang được xử lý.</p>

                <div style="background: #f9f9f9 !important; border-radius: 12px !important; padding: 20px !important; margin-bottom: 30px !important; text-align: left !important;">
                    <div style="display: flex !important; justify-content: space-between !important; margin-bottom: 10px !important;">
                        <span style="color: #666 !important;">Mã đơn hàng:</span>
                        <span style="font-weight: 700 !important; color: #1d1d1f !important;">#<?php echo $order_id; ?></span>
                    </div>
                    <div style="display: flex !important; justify-content: space-between !important; margin-bottom: 10px !important;">
                        <span style="color: #666 !important;">Tạm tính:</span>
                        <span style="font-weight: 700 !important; color: #1d1d1f !important;"><?php echo number_format($subtotal, 0, ',', '.'); ?>₫</span>
                    </div>
                    <div style="display: flex !important; justify-content: space-between !important; margin-bottom: 10px !important;">
                        <span style="color: #666 !important;">🚚 Phí vận chuyển (<?php echo esc_html($ward); ?>):</span>
                        <span style="font-weight: 700 !important; color: <?php echo $shipping === 0 ? '#52c41a' : '#1d1d1f'; ?> !important;"><?php echo $shipping === 0 ? 'Freeship 🎉' : number_format($shipping, 0, ',', '.') . '₫'; ?></span>
                    </div>
                    <?php
                    $discount = get_post_meta($order_id, '_order_discount', true) ?: 0;
                    if ($discount > 0) :
                    ?>
                        <div style="display: flex !important; justify-content: space-between !important; margin-bottom: 10px !important; color: #52c41a !important;">
                            <span>🎁 Giảm giá tự động (10%):</span>
                            <span style="font-weight: 700 !important;">-<?php echo number_format($discount, 0, ',', '.'); ?>₫</span>
                        </div>
                    <?php endif; ?>
                    <div style="display: flex !important; justify-content: space-between !important; margin-bottom: 10px !important; padding-top: 10px !important; border-top: 1px solid rgba(0,0,0,0.08) !important;">
                        <span style="color: #666 !important; font-weight: 700 !important;">Tổng thanh toán:</span>
                        <span style="font-weight: 800 !important; font-size: 18px !important; color: #ff4d4f !important;"><?php echo number_format($total, 0, ',', '.'); ?>₫</span>
                    </div>
                    <div style="display: flex !important; justify-content: space-between !important; margin-bottom: 20px !important;">
                        <span style="color: #666 !important;">Phương thức:</span>
                        <span style="font-weight: 700 !important; color: #1d1d1f !important;"><?php echo $payment_method === 'cod' ? 'COD (Tiền mặt)' : 'Chuyển khoản'; ?></span>
                    </div>

                    <div style="border-top: 1px solid rgba(0,0,0,0.05) !important; padding-top: 15px !important; margin-bottom: 15px !important;">
                        <h3 style="font-size: 16px !important; font-weight: 700 !important; color: #1d1d1f !important; margin-bottom: 10px !important;">Thông tin người đặt:</h3>
                        <div style="font-size: 14px !important; color: #666 !important; line-height: 1.6 !important;">
                            <strong>Họ tên:</strong> <?php echo esc_html($name); ?><br>
                            <strong>Số điện thoại:</strong> <?php echo esc_html($phone); ?><br>
                            <strong>Địa chỉ:</strong> <?php echo esc_html($address) . ', ' . esc_html($ward); ?><br>
                            <?php if ($notes) : ?>
                                <strong>Ghi chú:</strong> <?php echo esc_html($notes); ?><br>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="border-top: 1px solid rgba(0,0,0,0.05) !important; padding-top: 15px !important;">
                        <h3 style="font-size: 16px !important; font-weight: 700 !important; color: #1d1d1f !important; margin-bottom: 10px !important;">Sản phẩm đã đặt:</h3>
                        <div style="font-size: 14px !important; color: #666 !important;">
                            <?php foreach ($items as $item) : ?>
                                <div style="display: flex !important; justify-content: space-between !important; margin-bottom: 8px !important;">
                                    <span><?php echo esc_html($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                                    <span style="font-weight: 700 !important; color: #333 !important;"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>₫</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <?php if ($payment_method === 'bank') : ?>
                    <div class="fg-qr-section" style="border-top: 1px solid rgba(0,0,0,0.05) !important; padding-top: 30px !important;">
                        <h2 style="font-size: 20px !important; font-weight: 800 !important; color: #1d1d1f !important; margin-bottom: 15px !important;">Quét mã để thanh toán</h2>
                        <p style="color: #666 !important; font-size: 14px !important; margin-bottom: 20px !important;">Vui lòng quét mã QR dưới đây để thanh toán qua ứng dụng ngân hàng.</p>

                        <div style="max-width: 280px !important; margin: 0 auto 20px auto !important; border: 1px solid rgba(0,0,0,0.05) !important; border-radius: 16px !important; overflow: hidden !important; background: #fff !important; padding: 15px !important;">
                            <img src="<?php echo esc_url($qr_url); ?>" alt="VietQR" style="width: 100% !important; height: auto !important; display: block !important;">
                        </div>
                        <?php if ($discount > 0) : ?>
                            <div style="background: rgba(82, 196, 26, 0.08) !important; color: #52c41a !important; border: 1px solid rgba(82, 196, 26, 0.2) !important; padding: 10px 15px !important; border-radius: 10px !important; font-size: 14px !important; font-weight: 700 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 8px !important; margin-bottom: 25px !important; box-sizing: border-box !important;">
                                🎉 Đơn hàng từ 400k đã được tự động giảm giá 10%!
                            </div>
                        <?php endif; ?>

                        <div style="font-size: 14px !important; color: #666 !important; line-height: 1.6 !important;">
                            <strong>Ngân hàng:</strong> Phương Đông (OCB)<br>
                            <strong>Số tài khoản:</strong> 0048100004557477<br>
                            <strong>Chủ tài khoản:</strong> NGUYEN THI THU THAO<br>
                            <strong>Nội dung:</strong> <span style="color: #ff4d4f !important; font-weight: 700 !important;">DH<?php echo $order_id; ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <div style="margin-top: 40px !important;">
                    <a href="/dat-mon" style="display: inline-flex !important; align-items: center !important; justify-content: center !important; height: 50px !important; padding: 0 30px !important; border-radius: 999px !important; background: #1d1d1f !important; color: #fff !important; text-decoration: none !important; font-weight: 700 !important; transition: 0.3s !important;">
                        Quay lại đặt món
                    </a>
                </div>

            </div>

        </div>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('foodgo_thankyou', 'foodgo_render_thankyou_shortcode');



/**
 * HIỂN THỊ DANH SÁCH MÓN ĂN DẠNG BENTO GRID
 */
function foodgo_render_bento_menu_shortcode()
{
    $args = array(
        'post_type'      => 'food_manager',
        'posts_per_page' => 30,
        'post_status'    => 'publish',
    );

    $query = new WP_Query($args);

    $index = 0;
    ob_start();

    // Lấy tất cả danh mục món ăn (Chỉ lấy danh mục có sản phẩm)
    $categories = get_terms(array(
        'taxonomy' => 'food_manager_category',
        'hide_empty' => true,
    ));

    // Hàm ẩn danh để lấy emoji
    $get_food_emoji = function ($name) {
        $name = mb_strtolower($name);
        if (strpos($name, 'pizza') !== false) return '';
        if (strpos($name, 'burger') !== false) return '';
        if (strpos($name, 'uống') !== false || strpos($name, 'nước') !== false) return '';
        if (strpos($name, 'healthy') !== false || strpos($name, 'rau') !== false) return '';
        if (strpos($name, 'dessert') !== false || strpos($name, 'ngọt') !== false || strpos($name, 'bánh') !== false) return '';
        return '';
    };
?>

    <!-- CATEGORY TABS (Động) -->
    <div class="category-slider">
        <div class="category-list">
            <button class="category-item active" data-category="all"><span></span> Tất cả</button>
            <?php if (!is_wp_error($categories) && !empty($categories)) : ?>
                <?php foreach ($categories as $cat) : ?>
                    <button class="category-item" data-category="<?php echo esc_attr($cat->slug); ?>">
                        <span><?php echo $get_food_emoji($cat->name); ?></span> <?php echo esc_html($cat->name); ?>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="food-standard-grid" id="food-grid">
        <?php while ($query->have_posts()) : $query->the_post();
            $price = get_post_meta(get_the_ID(), '_food_price', true);
            $image = get_post_meta(get_the_ID(), '_food_banner', true);
        ?>
            <div class="food-card" data-category="all">
                <div class="food-image">
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                    </a>
                    <button class="quick-add-btn add-to-cart"
                        data-id="<?php the_ID(); ?>"
                        data-name="<?php the_title(); ?>"
                        data-price="<?php echo $price; ?>"
                        data-image="<?php echo esc_url($image); ?>">
                        +
                    </button>
                </div>

                <div class="food-content">
                    <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                        <div class="food-top">
                            <h3><?php the_title(); ?></h3>
                            <span class="food-price"><?php echo number_format($price, 0, ',', '.'); ?>đ</span>
                        </div>
                        <p><?php echo wp_trim_words(get_the_content(), 10); ?></p>
                    </a>
                </div>
            </div>
        <?php endwhile;
        wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('foodgo_bento_menu', 'foodgo_render_bento_menu_shortcode');

// XỬ LÝ AJAX LỌC SẢN PHẨM TRONG DB
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
        $price = get_post_meta(get_the_ID(), '_food_price', true);
        $image = get_post_meta(get_the_ID(), '_food_banner', true);
    ?>
        <div class="food-card">
            <div class="food-image">
                <a href="<?php the_permalink(); ?>">
                    <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                </a>
                <button class="quick-add-btn add-to-cart"
                    data-id="<?php the_ID(); ?>"
                    data-name="<?php the_title(); ?>"
                    data-price="<?php echo $price; ?>"
                    data-image="<?php echo esc_url($image); ?>">
                    +
                </button>
            </div>

            <div class="food-content">
                <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                    <div class="food-top">
                        <h3><?php the_title(); ?></h3>
                        <span class="food-price"><?php echo number_format($price, 0, ',', '.'); ?>đ</span>
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

// SHORTCODE TRANG TÀI KHOẢN (LOGIN, REGISTER, DASHBOARD)
function foodgo_account_shortcode()
{
    ob_start();

    // Prepare error containers
    $login_error = '';
    $register_error = '';

    // Xử lý Đăng nhập
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['username']) || isset($_POST['password']))) {
        if (isset($_POST['login_nonce']) && wp_verify_nonce($_POST['login_nonce'], 'foodgo_login_nonce')) {
            $creds = array(
                'user_login'    => sanitize_text_field($_POST['username']),
                'user_password' => $_POST['password'],
                'remember'      => true
            );
            $user = wp_signon($creds, false);
            if (is_wp_error($user)) {
                $login_error = wp_strip_all_tags($user->get_error_message());
                if (!headers_sent()) {
                    wp_safe_redirect(esc_url_raw(add_query_arg('login_error', rawurlencode($login_error))));
                    exit;
                }
            } else {
                echo '<script>window.location.href = window.location.href;</script>';
                exit;
            }
        } else {
            $login_error = 'Phiên đăng nhập không hợp lệ. Vui lòng thử lại.';
            if (!headers_sent()) {
                wp_safe_redirect(esc_url_raw(add_query_arg('login_error', rawurlencode($login_error))));
                exit;
            }
        }
    }

    // Xử lý Đăng ký
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['reg_username']) || isset($_POST['reg_email']))) {
        if (isset($_POST['register_nonce']) && wp_verify_nonce($_POST['register_nonce'], 'foodgo_register_nonce')) {
            $username = sanitize_text_field($_POST['reg_username']);
            $email = sanitize_email($_POST['reg_email']);
            $password = $_POST['reg_password'];

            $user_id = wp_create_user($username, $password, $email);
            if (is_wp_error($user_id)) {
                $register_error = wp_strip_all_tags($user_id->get_error_message());
                if (!headers_sent()) {
                    wp_safe_redirect(esc_url_raw(add_query_arg('register_error', rawurlencode($register_error))));
                    exit;
                }
            } else {
                // Tự động đăng nhập sau khi đăng ký thành công
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
                echo '<script>window.location.href = window.location.href;</script>';
                exit;
            }
        } else {
            $register_error = 'Phiên đăng ký không hợp lệ. Vui lòng thử lại.';
            if (!headers_sent()) {
                wp_safe_redirect(esc_url_raw(add_query_arg('register_error', rawurlencode($register_error))));
                exit;
            }
        }
    }

    // If redirected with error messages, use them
    if (empty($login_error) && isset($_GET['login_error'])) {
        $login_error = rawurldecode($_GET['login_error']);
    }
    if (empty($register_error) && isset($_GET['register_error'])) {
        $register_error = rawurldecode($_GET['register_error']);
    }

    if (!is_user_logged_in()) {
        // FORM ĐĂNG NHẬP / ĐĂNG KÝ
    ?>
        <div class="account-auth-container">
            <div class="auth-box glass-card">
                <div class="auth-tabs">
                    <button class="auth-tab-btn active" data-target="login-form">Đăng nhập</button>
                    <button class="auth-tab-btn" data-target="register-form">Đăng ký</button>
                </div>

                <!-- FORM ĐĂNG NHẬP -->
                <form id="login-form" class="auth-form active" method="post">
                    <?php if (!empty($login_error)) : ?>
                        <div class="form-error" style="color:red; text-align:center; padding: 10px; background: #fff1f0; border-radius: 10px; margin-bottom: 15px;">❌ <?php echo esc_html($login_error); ?></div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Tên tài khoản hoặc Email</label>
                        <input type="text" name="username" placeholder="Nhập tài khoản hoặc email..." required>
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password" name="password" placeholder="Nhập mật khẩu..." required>
                    </div>
                    <button type="submit" class="btn-auth">Đăng nhập</button>
                    <?php wp_nonce_field('foodgo_login_nonce', 'login_nonce'); ?>
                </form>

                <!-- FORM ĐĂNG KÝ -->
                <form id="register-form" class="auth-form" method="post">
                    <?php if (!empty($register_error)) : ?>
                        <div class="form-error" style="color:red; text-align:center; padding: 10px; background: #fff1f0; border-radius: 10px; margin-bottom: 15px;">❌ <?php echo esc_html($register_error); ?></div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Tên tài khoản</label>
                        <input type="text" name="reg_username" placeholder="Tên tài khoản viết liền không dấu..." required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="reg_email" placeholder="example@gmail.com" required>
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password" name="reg_password" placeholder="Tạo mật khẩu..." required>
                    </div>
                    <button type="submit" class="btn-auth">Tạo tài khoản</button>
                    <?php wp_nonce_field('foodgo_register_nonce', 'register_nonce'); ?>
                </form>
            </div>
        </div>

        <style>
            .account-auth-container {
                display: flex !important;
                justify-content: center !important;
                padding: 100px 0 !important;
                background: #f8f8fa !important;
                min-height: 70vh !important;
                margin-top: 100px !important;
            }

            .auth-box {
                background: white !important;
                padding: 40px !important;
                border-radius: 30px !important;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05) !important;
                width: 100% !important;
                max-width: 450px !important;
                height: fit-content !important;
            }

            .auth-tabs {
                display: flex !important;
                gap: 30px !important;
                margin-bottom: 30px !important;
                border-bottom: 1px solid #eee !important;
                padding-bottom: 15px !important;
            }

            .auth-tab-btn {
                border: none !important;
                background: none !important;
                font-size: 20px !important;
                font-weight: 700 !important;
                color: #aaa !important;
                cursor: pointer !important;
                transition: all 0.3s !important;
                padding: 0 !important;
            }

            .auth-tab-btn.active {
                color: #ff4d4f !important;
                position: relative !important;
            }

            .auth-tab-btn.active::after {
                content: '' !important;
                position: absolute !important;
                bottom: -17px !important;
                left: 0 !important;
                width: 100% !important;
                height: 3px !important;
                background: #ff4d4f !important;
                border-radius: 3px !important;
            }

            .auth-form {
                display: none !important;
            }

            .auth-form.active {
                display: block !important;
            }

            .form-group {
                margin-bottom: 20px !important;
                position: relative !important;
            }

            .form-group label {
                display: block !important;
                margin-bottom: 8px !important;
                font-weight: 600 !important;
                color: #333 !important;
                font-size: 16px !important;
                position: static !important;
                transform: none !important;
            }

            .form-group input {
                width: 100% !important;
                padding: 14px !important;
                border: 1px solid #ddd !important;
                border-radius: 12px !important;
                font-size: 15px !important;
                transition: all 0.3s !important;
                background: white !important;
                height: auto !important;
            }

            .form-group input:focus {
                border-color: #ff4d4f !important;
                outline: none !important;
                box-shadow: 0 0 0 3px rgba(255, 77, 79, 0.1) !important;
            }

            .btn-auth {
                width: 100% !important;
                padding: 14px !important;
                background: #ff4d4f !important;
                color: white !important;
                border: none !important;
                border-radius: 12px !important;
                cursor: pointer !important;
                font-size: 16px !important;
                font-weight: 700 !important;
                transition: all 0.3s !important;
            }

            .btn-auth:hover {
                background: #ff7875 !important;
                transform: translateY(-2px) !important;
                box-shadow: 0 10px 20px rgba(255, 77, 79, 0.2) !important;
            }
        </style>

        <script>
            document.querySelectorAll('.auth-tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.auth-tab-btn').forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
                    btn.classList.add('active');
                    document.getElementById(btn.dataset.target).classList.add('active');
                });
            });
        </script>
    <?php
    } else {
        // TRANG DASHBOARD (Option 2: Sidebar)
        $current_user = wp_get_current_user();
    ?>
        <div class="account-dashboard-container">
            <!-- Sidebar -->
            <div class="account-sidebar glass-card">
                <div class="user-info">
                    <div class="avatar"><?php echo strtoupper(substr($current_user->display_name, 0, 1)); ?></div>
                    <h4><?php echo esc_html($current_user->display_name); ?></h4>
                    <p><?php echo esc_html($current_user->user_email); ?></p>
                </div>
                <div class="account-menu">
                    <a href="#" class="menu-item active" data-target="profile">👤 Hồ sơ cá nhân</a>
                    <a href="#" class="menu-item" data-target="orders">📦 Đơn hàng của tôi</a>
                    <a href="<?php echo wp_logout_url(home_url('/tai-khoan')); ?>" class="menu-item logout">🚪 Đăng xuất</a>
                </div>
            </div>

            <!-- Content -->
            <div class="account-content glass-card">
                <!-- Mục Hồ sơ -->
                <div id="profile" class="content-section active">
                    <h3>Thông tin tài khoản</h3>
                    <div class="profile-details">
                        <div class="detail-row">
                            <span>Tên tài khoản:</span>
                            <strong><?php echo esc_html($current_user->user_login); ?></strong>
                        </div>
                        <div class="detail-row">
                            <span>Họ tên hiển thị:</span>
                            <strong><?php echo esc_html($current_user->display_name); ?></strong>
                        </div>
                        <div class="detail-row">
                            <span>Địa chỉ Email:</span>
                            <strong><?php echo esc_html($current_user->user_email); ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Mục Đơn hàng -->
                <div id="orders" class="content-section">
                    <h3>Đơn hàng của tôi</h3>
                    <?php
                    $current_user_id = get_current_user_id();
                    $args = array(
                        'post_type'      => 'foodgo_order',
                        'post_status'    => 'publish',
                        'posts_per_page' => -1,
                        'author'         => $current_user_id,
                    );
                    $orders_query = new WP_Query($args);

                    if ($orders_query->have_posts()) : ?>
                        <div class="orders-list">
                            <?php while ($orders_query->have_posts()) : $orders_query->the_post();
                                $total = get_post_meta(get_the_ID(), '_order_total', true);
                            ?>
                                <div class="order-item" style="border-bottom: 1px solid #eee; padding: 15px 0 !important; margin-bottom: 15px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <strong style="font-size: 16px;"><?php the_title(); ?></strong>
                                        <span style="color: #ff4d4f; font-weight: 700; font-size: 16px;"><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
                                    </div>
                                    <div style="color: #888; font-size: 13px; margin-top: 5px;">
                                        🕒 <?php the_time('d/m/Y H:i'); ?>
                                    </div>
                                    <div style="margin-top: 10px; font-size: 14px; white-space: pre-line; background: #fafafa; padding: 15px; border-radius: 12px; color: #555;">
                                        <?php echo esc_html(get_the_content()); ?>
                                    </div>
                                </div>
                            <?php endwhile;
                            wp_reset_postdata(); ?>
                        </div>
                    <?php else : ?>
                        <div class="empty-orders">
                            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" style="width: 100px; opacity: 0.5; margin-bottom: 20px;">
                            <p>Bạn chưa thực hiện đơn hàng nào.</p>
                            <a href="/dat-mon" class="btn-auth" style="display:inline-block; width:auto; text-decoration:none;">Đặt món ngay</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <style>
            .account-dashboard-container {
                display: flex !important;
                gap: 30px !important;
                padding: 50px !important;
                background: #f8f8fa !important;
                min-height: 70vh !important;
                max-width: 1200px !important;
                margin: 100px auto !important;
                border-radius: 30px !important;
            }

            .account-sidebar {
                width: 300px;
                flex-shrink: 0;
                background: white;
                padding: 30px;
                border-radius: 24px;
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.03);
                height: fit-content;
            }

            .user-info {
                text-align: center !important;
                margin-bottom: 30px !important;
                padding-bottom: 20px !important;
                border-bottom: 1px solid #eee !important;
            }

            .avatar {
                width: 80px !important;
                height: 80px !important;
                background: #ff4d4f !important;
                color: white !important;
                border-radius: 50% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 32px !important;
                font-weight: 700 !important;
                margin: 0 auto 15px !important;
            }

            .user-info h4 {
                margin: 0;
                font-size: 18px;
                color: #333;
            }

            .user-info p {
                color: #888;
                font-size: 14px;
                margin: 5px 0 0;
                word-break: break-all;
            }

            .account-menu {
                display: flex;
                flex-direction: column;
                padding: 15px 20px !important;
                gap: 15px;
            }

            .menu-item {
                padding: 10px 20px !important;
                border-radius: 12px;
                text-decoration: none;
                color: #555;
                font-weight: 600;
                transition: all 0.3s;
                font-size: 15px;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .menu-item:hover,
            .menu-item.active {
                background: #fff1f0;
                color: #ff4d4f;
            }

            .menu-item.logout {
                margin-top: 15px;
                color: #ff4d4f;
                border-top: 1px solid #eee;
                padding-top: 20px;
                border-radius: 0;
            }

            .menu-item.logout:hover {
                background: none;
                text-decoration: underline;
            }

            .account-content {
                flex: 1;
                background: white;
                padding: 40px !important;
                border-radius: 24px;
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.03);
            }

            .content-section {
                display: none;
            }

            .content-section.active {
                display: block;
            }

            .content-section h3 {
                margin-top: 0;
                margin-bottom: 30px;
                font-size: 22px;
                color: #333;
            }

            .profile-details {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }

            .detail-row {
                display: flex;
                align-items: center;
                padding-bottom: 15px;
                border-bottom: 1px solid #f5f5f5;
            }

            .detail-row span {
                width: 150px;
                color: #888;
                flex-shrink: 0;
            }

            .detail-row strong {
                color: #333;
                font-weight: 600;
            }

            .empty-orders {
                text-align: center;
                padding: 40px 0;
            }

            .empty-orders p {
                color: #888;
                margin-bottom: 20px;
            }

            @media (max-width: 768px) {
                .account-dashboard-container {
                    flex-direction: column;
                    padding: 20px;
                }

                .account-sidebar {
                    width: 100%;
                }
            }
        </style>

        <script>
            document.querySelectorAll('.menu-item[data-target]').forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
                    document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
                    item.classList.add('active');
                    document.getElementById(item.dataset.target).classList.add('active');
                });
            });
        </script>
<?php
    }

    return ob_get_clean();
}
add_shortcode('foodgo_account', 'foodgo_account_shortcode');

// AJAX login handler (fallback to show errors without full reload)
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

// ẨN THANH ADMIN BAR Ở GIAO DIỆN NGOÀI (FRONT-END)
add_filter('show_admin_bar', '__return_false');

// CẤU HÌNH TIỀN TỆ VNĐ CHO PLUGIN WP FOOD MANAGER
add_filter('wpfm_currency', function () {
    return 'VND';
});
add_filter('wpfm_get_price_decimals', function () {
    return 0;
});
add_filter('wpfm_get_price_thousand_separator', function () {
    return '.';
});
