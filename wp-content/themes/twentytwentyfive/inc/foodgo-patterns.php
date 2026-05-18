<?php
/**
 * FoodGo Block Patterns - Organised by Page
 */

add_action('init', function() {
    $dir = get_template_directory() . '/inc/patterns/';

    // 1. Đăng ký các danh mục chi tiết
    register_block_pattern_category('foodgo-home', array('label' => 'FoodGo - Trang chủ'));
    register_block_pattern_category('foodgo-about', array('label' => 'FoodGo - Giới thiệu'));
    register_block_pattern_category('foodgo-contact', array('label' => 'FoodGo - Liên hệ'));
    register_block_pattern_category('foodgo-cart', array('label' => 'FoodGo - Giỏ hàng'));
    register_block_pattern_category('foodgo-order', array('label' => 'FoodGo - Đặt món'));
    register_block_pattern_category('foodgo-elements', array('label' => 'FoodGo - Thành phần'));

    // 2. Phân loại các mẫu

    // --- TRANG ĐẶT MÓN ---
    register_block_pattern('foodgo/order-food', array(
        'title'      => 'Order - Bento Grid Modern (Mới)',
        'categories' => array('foodgo-order'),
        'content'    => file_get_contents($dir . 'order-food.html'),
    ));

    // --- TRANG CHỦ ---
    register_block_pattern('foodgo/hero', array(
        'title'      => 'Home - Hero Premium',
        'categories' => array('foodgo-home'),
        'content'    => file_get_contents($dir . 'hero.html'),
    ));
    register_block_pattern('foodgo/features', array(
        'title'      => 'Home - Features Grid',
        'categories' => array('foodgo-home'),
        'content'    => file_get_contents($dir . 'features.html'),
    ));
    register_block_pattern('foodgo/foods', array(
        'title'      => 'Home - Foods List',
        'categories' => array('foodgo-home'),
        'content'    => file_get_contents($dir . 'foods.html'),
    ));
    register_block_pattern('foodgo/cta', array(
        'title'      => 'Home - CTA Box',
        'categories' => array('foodgo-home'),
        'content'    => file_get_contents($dir . 'cta.html'),
    ));

    // --- TRANG GIỚI THIỆU ---
    register_block_pattern('foodgo/about-intro', array(
        'title'      => 'About - Intro Hero (Mới)',
        'categories' => array('foodgo-about'),
        'content'    => file_get_contents($dir . 'about.html'),
    ));
    register_block_pattern('foodgo/about-story', array(
        'title'      => 'About - Our Story (Mới)',
        'categories' => array('foodgo-about'),
        'content'    => file_get_contents($dir . 'about-story.html'),
    ));
    register_block_pattern('foodgo/about-team', array(
        'title'      => 'About - Our Team (Mới)',
        'categories' => array('foodgo-about'),
        'content'    => file_get_contents($dir . 'about-team.html'),
    ));
    register_block_pattern('foodgo/about-stats', array(
        'title'      => 'About - Statistics (Mới)',
        'categories' => array('foodgo-about'),
        'content'    => file_get_contents($dir . 'about-stats.html'),
    ));

    // --- TRANG LIÊN HỆ ---
    register_block_pattern('foodgo/contact', array(
        'title'      => 'Contact - Full Page Layout',
        'categories' => array('foodgo-contact'),
        'content'    => file_get_contents($dir . 'contact.html'),
    ));

    // --- TRANG GIỎ HÀNG ---
    register_block_pattern('foodgo/cart', array(
        'title'      => 'Cart - Full Page Layout',
        'categories' => array('foodgo-cart'),
        'content'    => file_get_contents($dir . 'cart.html'),
    ));

    // --- THÀNH PHẦN CHUNG ---
    register_block_pattern('foodgo/page-header', array(
        'title'      => 'Common - Page Header',
        'categories' => array('foodgo-elements'),
        'content'    => file_get_contents($dir . 'page-header.html'),
    ));

});
