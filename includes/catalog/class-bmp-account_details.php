<?php
if (!defined('ABSPATH')) {
    exit;
}

function userdataFunction()
{
    global $wpdb;
    global $current_user;
    $username = $current_user->user_login;

    $owner_user_key = $wpdb->get_var("SELECT user_key FROM {$wpdb->prefix}bmp_users WHERE user_name = '" . $username . "'");

    echo "SELECT user_key FROM {$wpdb->prefix}bmp_users WHERE user_name = '" . $username . "'";
    die();
}
