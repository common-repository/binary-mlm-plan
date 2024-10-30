<?php

/**
 * 
 *
 * @package  
 * @version  3.4.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('BMP_Admin_users_Reports', false)) :

    class BMP_Admin_users_Reports
    {
        public function get_users_reports()
        {
            if (!empty($_GET['user_id'])) {
                include_once dirname(__FILE__) . '/class-bmp-admin-user-detail.php';
            } else {
                $bmp_admin_users_list = new bmp_admin_users_list();
                $bmp_admin_users_list->prepare_items(); ?>
                <div class='wrap'>
                    <div id="icon-users" class="icon32"></div>
                    <h4><?php echo __('MLM Users reports', 'bmp'); ?></h4>
                    <form id="epin-report" method="GET" action="">
                        <input type="hidden" name="page" value="<?php echo sanitize_text_field($_REQUEST['page']) ?>" />
                        <?php
                        $bmp_admin_users_list->display(); ?>
                    </form>
                </div>
<?php
            }
        }
    }
endif;
