<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * BMP_Admin class.
 */
class BMP_Admin
{

    public function __construct()
    {
        add_action('init', array($this, 'includes'));
    }


    public function includes()
    {

        include_once dirname(__FILE__) . '/tables/class-bmp-admin-epins-list-table.php';
        include_once dirname(__FILE__) . '/tables/class-bmp-admin-users-list-table.php';
        include_once dirname(__FILE__) . '/tables/class-bmp-admin-payout-list-table.php';
        include_once dirname(__FILE__) . '/class-bmp-admin-menus.php';


        include_once dirname(__FILE__) . '/class-bmp-admin-settings.php';
        include_once dirname(__FILE__) . '/class-bmp-admin-epins.php';
        include_once dirname(__FILE__) . '/class-bmp-admin-users.php';
        //include_once dirname( __FILE__ ) . '/class-bmp-admin-reset-data.php';
        include_once dirname(__FILE__) . '/class-bmp-admin-payout.php';
        include_once dirname(__FILE__) . '/class-bmp-admin-assets.php';
    }
}
return new BMP_Admin();
