<?php
if (!defined('ABSPATH')) {
    exit;
}
class bmp_Uninstall
{
    public function uninstall()
    {
        global $wpdb;

        $tables = array(
            "{$wpdb->prefix}bmp_users",
            "{$wpdb->prefix}bmp_leftposition",
            "{$wpdb->prefix}bmp_rightposition",
            "{$wpdb->prefix}bmp_country",
            "{$wpdb->prefix}bmp_currency",
            "{$wpdb->prefix}bmp_payout",
            "{$wpdb->prefix}bmp_referral_commission",
            "{$wpdb->prefix}bmp_epins",
        );


        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$table}");
        }

        $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '%bmp_%';");

        // Delete users & usermeta.

        $wp_roles = new WP_Roles();
        $wp_roles->remove_role("bmp_user");
        session_destroy();
    }
}
