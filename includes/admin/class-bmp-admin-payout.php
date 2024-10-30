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

if (!class_exists('BMP_Admin_payout_Reports', false)) :

    class BMP_Admin_payout_Reports
    {

        public function get_payout_reports()
        {

            if (!empty($_GET['payout_id'])) {
                include_once dirname(__FILE__) . '/class-bmp-admin-payout-detail.php';
            } else {
                $bmp_admin_payout_list = new bmp_admin_payout_list();
                $bmp_admin_payout_list->prepare_items();

?>
                <div class='wrap'>
                    <div id="icon-users" class="icon32"></div>

                    <h4><?php echo __('MLM payout reports', 'bmp'); ?></h4>

                    <form id="payout-report" method="GET" action="">
                        <input type="hidden" name="page" value="<?php echo sanitize_text_field($_REQUEST['page']) ?>" />

                        <?php

                        $bmp_admin_payout_list->display();
                        ?>
                    </form>
                </div>
<?php
            }
        }
    }

endif;
