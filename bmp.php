<?php

/**
 * Plugin Name: Binary MLM Plan
 * Plugin URI:  https://www.mlmtrees.com/product/bmp-pro-wordpress/
 * Description: Binary MLM Plan with ePin is a plugn and play plugin which helps to manage binary networks within the WordPress CMS. Binary MLM Plan Software is suitable for all of MLM organizations.
 * Version: 2.0
 * Author: LetsCMS
 * Author URI: https://letscms.com
 * Text Domain: bmp
 * Domain Path: /i18n/languages/
 * Requires at least: 6.2
 * Requires PHP:      8.0
 *
 * @package BMP
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Define BMP_PLUGIN_FILE.
if (!defined('BMP_PLUGIN_FILE')) {
    define('BMP_PLUGIN_FILE', __FILE__);
}

// Include the main WooCommerce class.
if (!class_exists('Bmp')) {
    include_once dirname(__FILE__) . '/includes/class-bmp.php';
}


function BMP()
{
    return Bmp::instance();
}

// Global for backwards compatibility.
$GLOBALS['BMP'] = BMP();
