<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();

global $wpdb;
do_action('bmp_check_downline_validate');
$downlines = new BMP_Genealogy();
$downlines->downlinesFunction();

get_footer();
