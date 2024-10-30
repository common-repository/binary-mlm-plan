<?php

/**
 * Bmp setup
 *
 * @package Bmp
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Bmp Class.
 *
 * @class Bmp
 */
final class Bmp
{

    public $version = '1.0.0';

    protected static $_instance = null;

    public $session = null;


    public $query = null;

    public $product_factory = null;

    public $countries = null;


    public $integrations = null;


    public $cart = null;


    public $customer = null;

    public $order_factory = null;

    public $structured_data = null;

    public $deprecated_hook_handlers = array();

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }



    public function __construct()
    {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();

        do_action('bmp_loaded');
    }



    private function init_hooks()
    {

        register_activation_hook(BMP_PLUGIN_FILE, array('BMP_Install', 'install'));
        add_action('init', array($this, 'init'), 0);

        register_deactivation_hook(BMP_PLUGIN_FILE, array('BMP_Install', 'deactivate'));

        register_uninstall_hook(BMP_PLUGIN_FILE, 'uninstall');
    }


    public function init()
    {
        $this->load_plugin_textdomain();

        if (!session_id()) {
            session_start();
        }
        if (is_admin()) {
            add_action('admin_enqueue_scripts', array($this, 'custom_bmp_admin_style'));
        }
    }

    public function custom_bmp_admin_style()
    {
        wp_enqueue_style('bmp_admin_bootstrap', BMP()->plugin_url() . '/assets/css/bootstrap.css', [], time(), 'all');
    }

    private function define_constants()
    {
        $upload_dir = wp_upload_dir(null, false);

        $this->define('BMP_ABSPATH', dirname(BMP_PLUGIN_FILE) . '/');
        $this->define('BMP_PLUGIN_BASENAME', plugin_basename(BMP_PLUGIN_FILE));
        $this->define('BMP_VERSION', $this->version);
    }


    private function define($name, $value)
    {
        if (!defined($name)) {
            define($name, $value);
        }
    }


    private function is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined('DOING_AJAX');
            case 'cron':
                return defined('DOING_CRON');
            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON') && !defined('REST_REQUEST');
        }
    }


    public function includes()
    {


        include_once BMP_ABSPATH . 'includes/bmp-hooks.php';
        include_once BMP_ABSPATH . 'includes/bmp-hook-functions.php';
        include_once BMP_ABSPATH . 'includes/class-bmp-install.php';
        include_once BMP_ABSPATH . 'includes/catalog/class-bmp-downlines.php';
        include_once BMP_ABSPATH . 'includes/catalog/class-bmp-template.php';

        if ($this->is_request('admin')) {
            include_once BMP_ABSPATH . 'includes/admin/class-bmp-admin.php';
        }

        //BMP_Install::create_pages();
    }

    public function load_plugin_textdomain()
    {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();

        $locale = apply_filters('plugin_locale', $locale, 'bmp');

        unload_textdomain('bmp');
        load_textdomain('bmp', WP_LANG_DIR . '/bmp/' . $locale . '.mo');
        load_plugin_textdomain('bmp', false, plugin_basename(dirname(BMP_PLUGIN_FILE)) . '/i18n/languages');
    }


    public function plugin_url()
    {
        return untrailingslashit(plugins_url('/', BMP_PLUGIN_FILE));
    }

    public function plugin_path()
    {
        return untrailingslashit(plugin_dir_path(BMP_PLUGIN_FILE));
    }
}
