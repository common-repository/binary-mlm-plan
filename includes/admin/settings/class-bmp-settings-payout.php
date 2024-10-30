<?php

/**
 * UMW General Settings
 *
 * @package UMW/Admin
 */

if (!defined('ABSPATH')) {
    exit;
}

if (class_exists('BMP_Settings_Payout', false)) {
    return new BMP_Settings_Payout();
}


/**
 * BMP_Admin_Settings_Payoutl.
 */
class BMP_Settings_Payout  extends BMP_Settings_Page
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->id    = 'payout';
        $this->label = __('Payout Run', 'bmp');

        parent::__construct();

        add_action('bmp_sections_' . $this->id, array($this, 'output_sections'));
        add_action('bmp_settings_' . $this->id, array($this, 'output'));
        add_action('bmp_settings_save_' . $this->id, array($this, 'save'));
    }


    public function get_sections()
    {
        global $wpdb;
        $sections = array();

        $sections['run_payout'] = __('Run Payout', 'bmp');

        return apply_filters('bmp_get_sections_' . $this->id, $sections);
    }


    public function output_sections()
    {
        global $current_section;

        $sections = $this->get_sections();

        // if (empty($sections) || 1 === sizeof($sections)) {
        //     return;
        // }

        echo '<div class="wrap_style"><ul>';

        $array_keys = array_keys($sections);

        foreach ($sections as $id => $label) {
            echo '<li class="list_style"><a href="' . admin_url('admin.php?page=bmp-settings&tab=' . $this->id . '&section=' . sanitize_title($id)) . '" class="' . ($current_section == $id ? 'current' : '') . '">' . $label . '</a></li>';
        }

        echo '</ul></div>';
    }

    public function output()
    {
        global $current_section;
        if ($current_section == 'run_payout') {
            include 'payout/view_payout.php';
        }
    }
}
