<?php


if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('BMP_Settings_Page', false)) :

    /**
     * WC_Settings_Page.
     */
    abstract class BMP_Settings_Page
    {

        /**
         * Setting page id.
         *
         * @var string
         */
        protected $id = '';

        /**
         * Setting page label.
         *
         * @var string
         */
        protected $label = '';

        /**
         * Constructor.
         */
        public function __construct()
        {

            add_filter('bmp_settings_tabs_array', array($this, 'add_settings_page'), 20);
            add_action('bmp_sections_' . $this->id, array($this, 'output_sections'));
            add_action('bmp_settings_' . $this->id, array($this, 'output'));
            add_action('bmp_settings_save_' . $this->id, array($this, 'save'));
        }



        public function get_id()
        {
            return $this->id;
        }



        public function get_label()
        {
            return $this->label;
        }


        public function add_settings_page($pages)
        {
            $pages[$this->id] = $this->label;

            return $pages;
        }


        public function get_settings()
        {
            return apply_filters('bmp_get_settings_' . $this->id, array());
        }


        public function get_sections()
        {
            return apply_filters('bmp_get_sections_' . $this->id, array());
        }


        public function output_sections()
        {
            global $current_section;

            $sections = $this->get_sections();

            if (empty($sections) || 1 === sizeof($sections)) {
                return;
            }

            echo '<ul class="subsubsub">';

            $array_keys = array_keys($sections);

            foreach ($sections as $id => $label) {
                echo '<li><a href="' . admin_url('admin.php?page=bmp-settings&tab=' . $this->id . '&section=' . sanitize_title($id)) . '" class="' . ($current_section == $id ? 'current' : '') . '">' . $label . '</a> ' . (end($array_keys) == $id ? '' : '|') . ' </li>';
            }

            echo '</ul><br class="clear" />';
        }



        public function output()
        {
            $settings = $this->get_settings();

            BMP_Admin_Settings::output_fields($settings);
        }

        /**
         * Save settings.
         */
        public function save()
        {
            global $current_section;

            $settings = $this->get_settings();
            BMP_Admin_Settings::save_fields($settings);
            if ($current_section) {
                do_action('bmp_update_options_' . $this->id . '_' . $current_section);
            }
        }



        public function getCurrency()
        {
            global $wpdb;
            $currency_array = array('' => __('Select Currency', 'bmp'));
            $results = $wpdb->get_results("select * from {$wpdb->prefix}bmp_currency");
            $rows = $wpdb->num_rows;
            if (!empty($rows)) {
                foreach ($results as $result) {
                    $currency_array[$result->iso3] = $result->iso3 . '-' . $result->currency;
                }
            }
            return $currency_array;
        }
    }

endif;
