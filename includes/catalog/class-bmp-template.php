<?php

if (!defined('ABSPATH')) {
    exit;
}
add_filter('page_template', 'bmp_page_template', 10, 2);

function bmp_page_template($page_template, $temp)
{
    global $post;
    // add_action('wp_enqueue_scripts', 'custom_bmp_style');
    if (is_page('join-network') && get_option('bmp_join_network_page_id', true) == $post->ID && get_post_meta($post->ID, 'is_bmp_page', true) == 1) {
        add_action('wp_enqueue_scripts', 'custom_bmp_style');
        $page_template = BMP_ABSPATH . '/templates/bmp-join-network.php';
    }
    if (is_page('register') && get_option('bmp_register_page_id', true) == $post->ID && get_post_meta($post->ID, 'is_bmp_page', true) == 1) {
        add_action('wp_enqueue_scripts', 'custom_bmp_style');
        // ob_start();
        $page_template = BMP_ABSPATH . '/templates/bmp-register.php';
    }

    if (is_page('downlines') && get_option('bmp_downlines_page_id', true) == $post->ID && get_post_meta($post->ID, 'is_bmp_page', true) == 1) {
        add_action('wp_enqueue_scripts', 'custom_bmp_style');
        $page_template = BMP_ABSPATH . '/templates/bmp-downlines.php';
    }
    if (is_page('bmp-account-detail') && get_option('bmp_bmp-acccount-detail_page_id', true) == $post->ID && get_post_meta($post->ID, 'is_bmp_page', true) == 1) {
        add_action('wp_enqueue_scripts', 'custom_bmp_style');
        $page_template = BMP_ABSPATH . '/templates/bmp-account-detail.php';
    }
    if (is_page('bmp-payout-detail') && get_option('bmp_bmp_payout_detail_page_id', true) == $post->ID && get_post_meta($post->ID, 'is_bmp_page', true) == 1) {
        add_action('wp_enqueue_scripts', 'custom_bmp_style');
        $page_template = BMP_ABSPATH . '/templates/bmp-payout-detail.php';
    }
    return $page_template;
}


function custom_bmp_style()
{


    wp_enqueue_script('jquery');
    // wp_enqueue_style( 'bmp_bootstrap', BMP()->plugin_url() . '/assets/css/genealogy.css' );  
    wp_enqueue_style('bmpcss', BMP()->plugin_url() . '/assets/css/bmp.css', array(), '', 'all');
    wp_enqueue_script('bmp_mainjs', BMP()->plugin_url() . '/assets/js/main.js', array(), '', 'all');
    wp_enqueue_script('bootstrapjs', BMP()->plugin_url() . '/assets/js/bootstrap.min.js', array(), time(), false);

    if (is_page('downlines')) {
        wp_enqueue_style('bmp_genealogy_bootstrap', BMP()->plugin_url() . '/assets/css/genealogy.css', array(), '', 'all');
    }
    if (is_page('register') || is_page('join-network') || is_page('bmp-account-detail') || is_page('bmp-payout-detail')) {
        wp_enqueue_style('bmp_bootstrap', BMP()->plugin_url() . '/assets/css/bootstrap.css', array(), '', 'all');
    }
}
