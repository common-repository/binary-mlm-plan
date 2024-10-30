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

if (!class_exists('BMP_Admin_ePin_Reports', false)) :

    class BMP_Admin_ePin_Reports
    {

        public function get_epins_reports()
        {
            $bmp_admin_epin_list = new bmp_admin_epin_list();
            $bmp_admin_epin_list->prepare_items(); ?>
            <div class='wrap'>
                <div id="icon-users" class="icon32"></div>

                <h4><?php echo __('ePin reports', 'bmp'); ?></h4>

                <form id="epin-report" method="GET" action="">
                    <input type="hidden" name="page" value="<?php echo sanitize_text_field($_REQUEST['page']) ?>" />
                    <?php
                    $bmp_admin_epin_list->display();
                    ?>
                </form>
            </div>
<?php
        }
    }
endif;
