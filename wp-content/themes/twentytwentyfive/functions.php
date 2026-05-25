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

// Enqueue Block Patterns
require_once get_template_directory() . '/inc/foodgo-patterns.php';

// ==========================================================
// NẠP CÁC MODULE CHỨC NĂNG TÙY BIẾN FOODGO (REFAC TO MODULAR)
// ==========================================================
require_once get_template_directory() . '/inc/foodgo-core.php';
require_once get_template_directory() . '/inc/foodgo-variations.php';
require_once get_template_directory() . '/inc/foodgo-checkout.php';
require_once get_template_directory() . '/inc/foodgo-shortcodes.php';
