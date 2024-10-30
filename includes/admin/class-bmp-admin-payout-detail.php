<?php
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;
if (!empty($_GET['payout_id'])) {
    $payout_id = sanitize_text_field($_GET['payout_id']);
} else {
    $payout_id = 0;
}

if (!empty($_GET['user_id'])) {
    $user_id = sanitize_text_field($_GET['user_id']);
} else {
    $user_id = 0;
} ?>
<div id="profile-page">
    <h1 class="wp-heading-inline">
        <?php echo __('Payout Detail', 'bmp'); ?></h1>
    <?php do_action('bmp_admin_payout_detail'); ?>
    <?php
    do_action('bmp_admin_bonus_details');
    ?>
</div>