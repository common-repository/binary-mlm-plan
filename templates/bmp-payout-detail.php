<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
do_action('bmp_user_check_validate');
do_action('bmp_user_check_payout');
do_action('bmp_user_payout_detail');
do_action('bmp_user_payout_bonus_details');
get_footer();
