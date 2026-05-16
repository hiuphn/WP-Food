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

    if (empty($cart_data)) {
        wp_send_json_error('Giỏ hàng trống!');
    }

    $cart = json_decode(stripslashes($cart_data), true);
    $total = 0;
    $order_details = "";

    foreach ($cart as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        $order_details .= "- " . $item['name'] . " x " . $item['quantity'] . " (" . number_format($subtotal, 0, ',', '.') . "đ)\n";
    }

    $order_details .= "\nTổng cộng: " . number_format($total, 0, ',', '.') . "đ";

    // Tạo bài viết mới trong CPT foodgo_order
    $order_id = wp_insert_post(array(
        'post_title' => 'Đơn hàng #' . time(),
        'post_type' => 'foodgo_order',
        'post_status' => 'publish',
        'post_content' => $order_details,
    ));

    if ($order_id) {
        update_post_meta($order_id, '_order_total', $total);
        update_post_meta($order_id, '_order_items_json', $cart_data);
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
                <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                <div class="food-content">
                    <div class="food-top">
                        <h3><?php the_title(); ?></h3>
                        <p class="price"><?php echo number_format($price, 0, ',', '.'); ?>đ</p>
                    </div>
                    <p><?php echo wp_trim_words(get_the_content(), 15); ?></p>
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
                    <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                    <button class="quick-add-btn add-to-cart"
                        data-id="<?php the_ID(); ?>"
                        data-name="<?php the_title(); ?>"
                        data-price="<?php echo $price; ?>"
                        data-image="<?php echo esc_url($image); ?>">
                        +
                    </button>
                </div>

                <div class="food-content">
                    <div class="food-top">
                        <h3><?php the_title(); ?></h3>
                        <span class="food-price"><?php echo number_format($price, 0, ',', '.'); ?>đ</span>
                    </div>
                    <p><?php echo wp_trim_words(get_the_content(), 10); ?></p>
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
function foodgo_filter_products() {
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
                <img src="<?php echo esc_url($image); ?>" alt="<?php the_title(); ?>">
                <button class="quick-add-btn add-to-cart" 
                    data-id="<?php the_ID(); ?>"
                    data-name="<?php the_title(); ?>"
                    data-price="<?php echo $price; ?>"
                    data-image="<?php echo esc_url($image); ?>">
                    +
                </button>
            </div>

            <div class="food-content">
                <div class="food-top">
                    <h3><?php the_title(); ?></h3>
                    <span class="food-price"><?php echo number_format($price, 0, ',', '.'); ?>đ</span>
                </div>
                <p><?php echo wp_trim_words(get_the_content(), 10); ?></p>
            </div>
        </div>
    <?php endwhile; wp_reset_postdata();
    wp_die();
}
add_action('wp_ajax_foodgo_filter_products', 'foodgo_filter_products');
add_action('wp_ajax_nopriv_foodgo_filter_products', 'foodgo_filter_products');

// SHORTCODE TRANG TÀI KHOẢN (LOGIN, REGISTER, DASHBOARD)
function foodgo_account_shortcode() {
    ob_start();
    
    // Xử lý Đăng nhập
    if (isset($_POST['login_nonce']) && wp_verify_nonce($_POST['login_nonce'], 'foodgo_login_nonce')) {
        $creds = array(
            'user_login'    => sanitize_text_field($_POST['username']),
            'user_password' => $_POST['password'],
            'remember'      => true
        );
        $user = wp_signon($creds, false);
        if (is_wp_error($user)) {
            echo '<p style="color:red; text-align:center; padding: 10px; background: #fff1f0; border-radius: 10px;">❌ ' . $user->get_error_message() . '</p>';
        } else {
            echo '<script>window.location.href = window.location.href;</script>';
            exit;
        }
    }

    // Xử lý Đăng ký
    if (isset($_POST['register_nonce']) && wp_verify_nonce($_POST['register_nonce'], 'foodgo_register_nonce')) {
        $username = sanitize_text_field($_POST['reg_username']);
        $email = sanitize_email($_POST['reg_email']);
        $password = $_POST['reg_password'];
        
        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            echo '<p style="color:red; text-align:center; padding: 10px; background: #fff1f0; border-radius: 10px;">❌ ' . $user_id->get_error_message() . '</p>';
        } else {
            // Tự động đăng nhập sau khi đăng ký thành công
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            echo '<script>window.location.href = window.location.href;</script>';
            exit;
        }
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
            .account-auth-container { display: flex !important; justify-content: center !important; padding: 100px 0 !important; background: #f8f8fa !important; min-height: 70vh !important; margin-top: 100px !important; }
            .auth-box { background: white !important; padding: 40px !important; border-radius: 30px !important; box-shadow: 0 20px 40px rgba(0,0,0,0.05) !important; width: 100% !important; max-width: 450px !important; height: fit-content !important; }
            .auth-tabs { display: flex !important; gap: 30px !important; margin-bottom: 30px !important; border-bottom: 1px solid #eee !important; padding-bottom: 15px !important; }
            .auth-tab-btn { border: none !important; background: none !important; font-size: 20px !important; font-weight: 700 !important; color: #aaa !important; cursor: pointer !important; transition: all 0.3s !important; padding: 0 !important; }
            .auth-tab-btn.active { color: #ff4d4f !important; position: relative !important; }
            .auth-tab-btn.active::after { content: '' !important; position: absolute !important; bottom: -17px !important; left: 0 !important; width: 100% !important; height: 3px !important; background: #ff4d4f !important; border-radius: 3px !important; }
            .auth-form { display: none !important; }
            .auth-form.active { display: block !important; }
            .form-group { margin-bottom: 20px !important; position: relative !important; }
            .form-group label { display: block !important; margin-bottom: 8px !important; font-weight: 600 !important; color: #333 !important; font-size: 16px !important; position: static !important; transform: none !important; }
            .form-group input { width: 100% !important; padding: 14px !important; border: 1px solid #ddd !important; border-radius: 12px !important; font-size: 15px !important; transition: all 0.3s !important; background: white !important; height: auto !important; }
            .form-group input:focus { border-color: #ff4d4f !important; outline: none !important; box-shadow: 0 0 0 3px rgba(255, 77, 79, 0.1) !important; }
            .btn-auth { width: 100% !important; padding: 14px !important; background: #ff4d4f !important; color: white !important; border: none !important; border-radius: 12px !important; cursor: pointer !important; font-size: 16px !important; font-weight: 700 !important; transition: all 0.3s !important; }
            .btn-auth:hover { background: #ff7875 !important; transform: translateY(-2px) !important; box-shadow: 0 10px 20px rgba(255, 77, 79, 0.2) !important; }
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
                                <div class="order-item" style="border-bottom: 1px solid #eee; padding: 15px 0; margin-bottom: 15px;">
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
                            <?php endwhile; wp_reset_postdata(); ?>
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
            .account-dashboard-container { display: flex !important; gap: 30px !important; padding: 50px !important; background: #f8f8fa !important; min-height: 70vh !important; max-width: 1200px !important; margin: 100px auto !important; border-radius: 30px !important; }
            .account-sidebar { width: 300px; flex-shrink: 0; background: white; padding: 30px; border-radius: 24px; box-shadow: 0 15px 30px rgba(0,0,0,0.03); height: fit-content; }
            .user-info { text-align: center !important; margin-bottom: 30px !important; padding-bottom: 20px !important; border-bottom: 1px solid #eee !important; }
            .avatar { width: 80px !important; height: 80px !important; background: #ff4d4f !important; color: white !important; border-radius: 50% !important; display: flex !important; align-items: center !important; justify-content: center !important; font-size: 32px !important; font-weight: 700 !important; margin: 0 auto 15px !important; }
            .user-info h4 { margin: 0; font-size: 18px; color: #333; }
            .user-info p { color: #888; font-size: 14px; margin: 5px 0 0; word-break: break-all; }
            .account-menu { display: flex; flex-direction: column; gap: 8px; }
            .menu-item { padding: 14px 20px; border-radius: 12px; text-decoration: none; color: #555; font-weight: 600; transition: all 0.3s; font-size: 15px; display: flex; align-items: center; gap: 10px; }
            .menu-item:hover, .menu-item.active { background: #fff1f0; color: #ff4d4f; }
            .menu-item.logout { margin-top: 15px; color: #ff4d4f; border-top: 1px solid #eee; padding-top: 20px; border-radius: 0; }
            .menu-item.logout:hover { background: none; text-decoration: underline; }
            
            .account-content { flex: 1; background: white; padding: 40px; border-radius: 24px; box-shadow: 0 15px 30px rgba(0,0,0,0.03); }
            .content-section { display: none; }
            .content-section.active { display: block; }
            .content-section h3 { margin-top: 0; margin-bottom: 30px; font-size: 22px; color: #333; }
            
            .profile-details { display: flex; flex-direction: column; gap: 20px; }
            .detail-row { display: flex; align-items: center; padding-bottom: 15px; border-bottom: 1px solid #f5f5f5; }
            .detail-row span { width: 150px; color: #888; flex-shrink: 0; }
            .detail-row strong { color: #333; font-weight: 600; }
            
            .empty-orders { text-align: center; padding: 40px 0; }
            .empty-orders p { color: #888; margin-bottom: 20px; }
            
            @media (max-width: 768px) {
                .account-dashboard-container { flex-direction: column; padding: 20px; }
                .account-sidebar { width: 100%; }
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

// ẨN THANH ADMIN BAR Ở GIAO DIỆN NGOÀI (FRONT-END)
add_filter('show_admin_bar', '__return_false');

// CẤU HÌNH TIỀN TỆ VNĐ CHO PLUGIN WP FOOD MANAGER
add_filter('wpfm_currency', function() { return 'VND'; });
add_filter('wpfm_get_price_decimals', function() { return 0; });
add_filter('wpfm_get_price_thousand_separator', function() { return '.'; });

