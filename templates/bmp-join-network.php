<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
global $current_user;
$role = get_role('bmp_user');
if (!empty($current_user->ID)) {
    if (empty($role->name)) {
        do_action('bmp_join_network');
    } else {
        echo "<h4 style='text-align:center'>You are Already Mlm User </h4>";
    }
} else {
    echo "<h4 style='text-align:center'> Please Login </h4>";
}
get_footer();
