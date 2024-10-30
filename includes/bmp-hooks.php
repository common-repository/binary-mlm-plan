<?php
if (!defined('ABSPATH')) {
    exit;
}
// register hook
add_action('wp_ajax_bmp_user_register', 'bmp_front_register_function');
add_action('wp_ajax_nopriv_bmp_user_register', 'bmp_front_register_function');
// register hook


// user name exist hook
add_action('wp_ajax_bmp_username_exist', 'bmp_username_exist_function');
add_action('wp_ajax_nopriv_bmp_username_exist', 'bmp_username_exist_function');

// Position exist hook
add_action('wp_ajax_bmp_position_exist', 'bmp_position_exist_function');
add_action('wp_ajax_nopriv_bmp_position_exist', 'bmp_position_exist_function');
// user name exist hook

// user email exist hook
add_action('wp_ajax_bmp_email_exist', 'bmp_email_exist_function');
add_action('wp_ajax_nopriv_bmp_email_exist', 'bmp_email_exist_function');


// user epin exist hook
add_action('wp_ajax_bmp_epin_exist', 'bmp_epin_exist_function');
add_action('wp_ajax_nopriv_bmp_epin_exist', 'bmp_epin_exist_function');
// user epin exist hook


// user epin exist hook
add_action('wp_ajax_bmp_password_validation', 'bmp_password_validation_function');
add_action('wp_ajax_nopriv_bmp_password_validation', 'bmp_password_validation_function');
// user epin exist hook


// user name downline search exist hook
add_action('wp_ajax_bmp_username_downline_search', 'bmp_username_downline_search_function');
add_action('wp_ajax_nopriv_bmp_username_downline_search', 'bmp_username_downline_search_function');
// user name downline search exist hook


add_filter('manage_users_columns', 'bmp_add_custom_column_users');
add_action('manage_users_custom_column',  'bmp_add_custom_column_users_value', 10, 3);


add_action('bmp_check_downline_validate', 'bmp_user_check_validate_function');

add_action('bmp_user_check_validate', 'bmp_user_check_validate_function');

add_action('bmp_user_payout_list', 'bmp_user_payout_list_function');

add_action('bmp_user_account_detail', 'bmp_user_account_detail_function');

add_action('bmp_user_downlines_list', 'bmp_user_downlines_list_function');


add_action('bmp_user_check_payout', 'bmp_user_check_payout_function');
add_action('bmp_user_payout_detail', 'bmp_user_payout_detail_function');
add_action('bmp_user_payout_bonus_details', 'bmp_user_payout_bonus_detail_function');


add_action('bmp_join_network', 'bmp_join_network_function');
add_action('wp_ajax_bmp_join_network', 'bmp_front_join_network_function');
add_action('wp_ajax_nopriv_bmp_join_network', 'bmp_front_join_network_function');

// user join epin exist hook
// add_action('wp_ajax_bmp_join_epin_exist', 'bmp_epin_join_exist_function');
// add_action('wp_ajax_nopriv_bmp_join_epin_exist', 'bmp_join_epin_exist_function');

add_action('wp_head', 'bmp_base_name_information');
add_filter('query_vars', 'bmp_add_query_vars');
add_filter('rewrite_rules_array', 'bmp_add_rewrite_rules');


// admin hooks

add_action('bmp_admin_payout_detail', 'bmp_admin_payout_detail_function');
add_action('bmp_admin_bonus_details', 'bmp_admin_bonus_details_function');



add_action('bmp_admin_user_account_detail', 'bmp_admin_user_account_detail_function');
add_action('bmp_admin_user_downlines_list', 'bmp_admin_user_downlines_list_function');

add_action('bmp_admin_user_payout_list', 'bmp_admn_user_payout_list_function');


add_action('bmp_mlm_deactivate_hook', 'bmp_mlm_deactivate_function');
