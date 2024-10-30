<?php
if (!defined('ABSPATH')) {
    exit;
}

if (class_exists('BMP_Admin_Menus', false)) {
    return new BMP_Admin_Menus();
}

/**
 * BMP_Admin_Menus Class.
 */
class BMP_Admin_Menus
{

    public function __construct()
    {
        // Add menus.
        add_action('admin_menu', array($this, 'admin_menu'), 9);
        add_action('admin_menu', array($this, 'settings_menu'), 50);
    }


    public function admin_menu()
    {
        global $menu;

        if (current_user_can('manage_bmp')) {
            $menu[] = array('', 'read', 'separator-bmp', '', 'wp-menu-separator bmp');
        }
        $icon_url = BMP()->plugin_url() . '/image/mlm_tree.png';
        add_menu_page(__('Binary MLM Plan', 'bmp'), __('Binary MLM Plan', 'bmp'), 'manage_bmp', 'bmp-settings', null, $icon_url, '56.5');
        add_submenu_page('bmp-settings', __('Binary MLM Plan', 'bmp'), __('Binary MLM Plan', 'bmp'), 'manage_bmp', 'bmp-settings', null, null, '56.5');
    }

    public function settings_menu()
    {

        $settings_page = add_submenu_page('bmp', __('Binary MLM Plan settings', 'bmp'), __('Settings', 'bmp'), 'manage_bmp', 'bmp-settings', array($this, 'settings_page'));

        add_action('load-' . $settings_page, array($this, 'settings_page_init'));

        add_submenu_page('bmp-settings', __('User Reports', 'bmp'), __('User Reports', 'bmp'), 'manage_bmp', 'bmp-user-reports', array($this, 'bmp_user_reports'));
        add_submenu_page('bmp-settings', __('ePin Report', 'bmp'), __('ePin Reports', 'bmp'), 'manage_bmp', 'bmp-epin-reports', array($this, 'bmp_epin_reports'));

        add_submenu_page('bmp-settings', __('payout Reports', 'bmp'), __('payout Reports', 'bmp'), 'manage_bmp', 'bmp-payout-reports', array($this, 'bmp_payout_reports'));
    }




    public function bmp_user_reports()
    {
        global $wpdb;
        $BMP_Admin_users_Reports = new BMP_Admin_users_Reports;
        $BMP_Admin_users_Reports->get_users_reports();
    }

    public function bmp_epin_reports()
    {
        global $wpdb;
        $BMP_Admin_epins_Reports = new BMP_Admin_ePin_Reports;
        $BMP_Admin_epins_Reports->get_epins_reports();
    }

    public function bmp_payout_reports()
    {
        global $wpdb;
        $BMP_Admin_payout_Reports = new BMP_Admin_payout_Reports;
        $BMP_Admin_payout_Reports->get_payout_reports();
    }



    public function settings_page_init()
    {
        global $current_tab, $current_section;

        // Include settings pages.
        BMP_Admin_Settings::get_settings_pages();

        // Get current tab/section.
        $current_tab     = empty($_GET['tab']) ? 'general' : sanitize_title(wp_unslash($_GET['tab']));
        $current_section = empty($_REQUEST['section']) ? '' : sanitize_title(wp_unslash($_REQUEST['section']));

        // Save settings if data has been posted.

        if ('' !== $current_section && apply_filters("bmp_save_settings_{$current_tab}_{$current_section}", !empty($_POST))) {
            BMP_Admin_Settings::save();
        } elseif ('' === $current_section && apply_filters("bmp_save_settings_{$current_tab}", !empty($_POST))) {

            BMP_Admin_Settings::save();
        }

        // Add any posted messages.
        if (!empty($_GET['bmp_error'])) {
            BMP_Admin_Settings::add_error(wp_kses_post(wp_unslash(sanitize_text_field($_GET['bmp_error']))));
        }

        if (!empty($_GET['bmp_message'])) {
            BMP_Admin_Settings::add_message(wp_kses_post(wp_unslash(sanitize_text_field($_GET['umw_message']))));
        }
        do_action('bmp_settings_page_init');
    }

    public function settings_page()
    {

        BMP_Admin_Settings::output();
    }
}

return new BMP_Admin_Menus();
