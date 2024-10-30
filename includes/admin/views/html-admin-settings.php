<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$current_tab     = empty($_GET['tab']) ? 'setting' : sanitize_title(wp_unslash($_GET['tab']));
$tab_exists        = isset($tabs[$current_tab]) || has_action('bmp_sections_' . $current_tab) || has_action('bmp_settings_' . $current_tab) || has_action('bmp_settings_tabs_' . $current_tab);
$current_tab_label = isset($tabs[$current_tab]) ? $tabs[$current_tab] : '';

if (!$tab_exists) {
    wp_safe_redirect(admin_url('admin.php?page=bmp-settings'));
    exit;
}

if ($current_tab == 'payout' && $current_section == '') {
    $current_section = 'run_payout';
}

if ($current_tab == 'setting') {
    $bmp_users = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}bmp_users");
    $settings = get_option('bmp_manage_general');

    if ($bmp_users == 0) {
        $current_section = 'first_user';
    }

    if ($current_tab == 'setting' && $current_section == '') {
        $current_section = 'general';
    }
}
$post_url = admin_url('admin.php');
if ($_GET['page']) {
    $post_url .= '?page=' . sanitize_text_field($_GET['page']);
}
if ($current_tab) {
    $post_url .= '&tab=' . $current_tab;
}
if ($current_section) {
    $post_url .= '&section=' . $current_section;
}

?>
<div id="bmp" class="wrap bmp">
    <form method="<?php echo esc_attr(apply_filters('bmp_settings_form_method_tab_' . $current_tab, 'post')); ?>" id="mainform" action="<?php echo $post_url; ?>" enctype="multipart/form-data">
        <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
            <?php
            foreach ($tabs as $slug => $label) {
                echo '<a href="' . esc_html(admin_url('admin.php?page=bmp-settings&tab=' . esc_attr($slug))) . '" class="nav-tab ' . ($current_tab === $slug ? 'nav-tab-active' : '') . '">' . esc_html($label) . '</a>';
            }

            do_action('bmp_settings_tabs');

            ?>
        </nav>
        <h1 class="screen-reader-text"><?php echo esc_html($current_tab_label); ?></h1>
        <?php
        do_action('bmp_sections_' . $current_tab);

        self::show_messages();

        do_action('bmp_settings_' . $current_tab);
        do_action('bmp_settings_tabs_' . $current_tab);
        // @deprecated hook. @todo remove in 4.0.
        ?>
        <?php //echo $current_section;
        ?>
        <p class="submit sub_btn">
            <?php if (empty($GLOBALS['hide_save_button'])) : ?>
                <?php if ($current_section) { ?>
                    <button name="save" class="button-primary bmp-save-button" type="submit" value="<?php esc_attr_e('Save changes', 'bmp'); ?>">
                        <?php if ($current_section == 'run_payout') { ?>
                            <?php esc_html_e('Payout Run', 'bmp'); ?>

                        <?php } else { ?>
                            <?php esc_html_e('Save changes', 'bmp'); ?>
                        <?php } ?>

                    </button>
                <?php }    ?>
            <?php endif; ?>
            <?php wp_nonce_field('bmp-settings'); ?>
        </p>
    </form>
</div>