<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
do_action('bmp_user_check_validate');
do_action('bmp_user_account_detail');
do_action('bmp_user_downlines_list');
do_action('bmp_user_payout_list');
get_footer();
