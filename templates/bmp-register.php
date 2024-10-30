<?php

if (!defined('ABSPATH')) {
    exit;
}
get_header();

// echo TEMPLATEPATH.'assets/fonts/parts/header.php';
global $wpdb, $wp_query, $current_user;
$bmp_users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_users");
if (is_user_logged_in()) {
    $sponsor_id = $current_user->ID;
} else {
    $sponsor_id = '';
}
if (!empty($_REQUEST['position'])) {
    $position = sanitize_text_field($_REQUEST['position']);
    $position_diabled = "disabled";
} else {
    $position = '';
    $position_diabled = "";
}
if (isset($_GET['k']) && !empty($_GET['k'])) {
    $parent_key = sanitize_text_field($_GET['k']);
} else {
    $parent_key = '';
}
if (!empty($sponsor_id)) {
    $selected = 'selected';
    $disabled = 'disabled';
} else {
    $selected = '';
    $disabled = '';
}

$bmp_manage_general = get_option('bmp_manage_general');
?>
<div class="container register">
    <div class="row">
        <div class="col-md-3 register-left mb-3">
            <img src="<?php echo BMP()->plugin_url(); ?>/assets/images/logo_white.png" alt="" />
            <h3><?php esc_html_e('Welcome', 'bmp'); ?></h3>
            <p class="m-0"><?php esc_html_e('You are welcome in Binary MLM plan for earning your own money!', 'bmp'); ?></p>
            <a id="bmp_login" class="btn button btn-secondary" href="<?php bloginfo('url'); ?>/wp-login.php"><?php esc_html_e('Login', 'bmp'); ?></a>
        </div>
        <div class="col-md-9 register-right">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <h3 class="register-heading"><?php esc_html_e('Apply For Binary MLM Plan', 'bmp'); ?></h3>

                    <div class="row register-form">
                        <div class="text-center" id="bmp_user_success_message"></div>
                        <form id="bmp_register_form" name="bmp_register_form" action="" method="POST">
                            <input type="hidden" name="action" value="bmp_user_register">
                            <input type="hidden" name="parent_key" value="<?php echo $parent_key; ?>">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <input id="bmp_username" name="bmp_username" type="text" class="form-control" placeholder="<?php esc_html_e('User Name *', 'bmp'); ?>" value="" required>
                                        <div class="bmp_username_message"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <input id="bmp_email" name="bmp_email" type="email" class="form-control" placeholder="<?php esc_html_e('Your Email *', 'bmp'); ?>" value="" required>
                                        <div class="bmp_email_message"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <input id="bmp_first_name" name="bmp_first_name" type="text" class="form-control" placeholder="<?php esc_html_e('First Name *', 'bmp'); ?>" value="" required>
                                        <div class="bmp_first_name_message"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <input id="bmp_last_name" name="bmp_last_name" type="text" class="form-control" placeholder="<?php esc_html_e('Last Name *', 'bmp'); ?>" value="" required>
                                        <div class="bmp_last_name_message"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <input id="bmp_password" name="bmp_password" type="password" class="form-control" placeholder="<?php esc_html_e('Password *', 'bmp'); ?>" value="" required>
                                        <div class="bmp_password_message"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <input id="bmp_confirm_password" name="bmp_confirm_password" type="password" class="form-control" placeholder="<?php esc_html_e('Confirm Password *', 'bmp'); ?>" value="" required>

                                        <div class="bmp_confirm_password_message"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <input id="bmp_phone" name="bmp_phone" type="text" minlength="10" maxlength="10" class="form-control" placeholder="<?php esc_html_e('Your Phone *', 'bmp'); ?>" value="" required>
                                        <div class="bmp_phone_message"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <?php
                                        if ($disabled) { ?>
                                            <select id="bmp_sponsor_id" name="bmp_sponsor_id" class="form-control" required <?php echo $disabled; ?>>
                                                <option value=""><?php esc_html_e('Please select your Sponsor', 'bmp'); ?></option>
                                                <?php foreach ($bmp_users as $bmp_user) { ?>
                                                    <option value="<?php echo $bmp_user->user_id; ?>" <?php echo ($sponsor_id == $bmp_user->user_id) ? $selected : ''; ?>><?php echo $bmp_user->user_name; ?></option>
                                                <?php } ?>

                                            </select>
                                            <input type="hidden" name="bmp_sponsor_id" value="<?php echo $sponsor_id; ?>">
                                        <?php } else { ?>
                                            <select id="bmp_sponsor_id" name="bmp_sponsor_id" class="form-control" required>
                                                <option value=""><?php esc_html_e('Please select your Sponsor', 'bmp'); ?></option>
                                                <?php foreach ($bmp_users as $bmp_user) { ?>
                                                    <option value="<?php echo $bmp_user->user_id; ?>" <?php echo ($sponsor_id == $bmp_user->user_id) ? $selected : ''; ?>><?php echo $bmp_user->user_name; ?></option>
                                                <?php } ?>

                                            </select>
                                        <?php } ?>
                                        <div class="bmp_sponsor_message"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <?php if ($position_diabled) { ?>
                                            <select name="bmp_position" id="bmp_position" class="form-control" required <?php echo $position_diabled; ?>>
                                                <option value=""><?php esc_html_e('Select Position', 'bmp'); ?></option>
                                                <option value="left" <?php echo ($position == 'left') ? $selected : ''; ?>><?php esc_html_e('Left', 'bmp'); ?></option>
                                                <option value="right" <?php echo ($position == 'right') ? $selected : ''; ?>><?php esc_html_e('Right', 'bmp'); ?></option>
                                            </select>
                                            <input type="hidden" name="bmp_position" value="<?php echo $position; ?>">
                                        <?php } else {
                                        ?>
                                            <select name="bmp_position" id="bmp_position" class="form-control" required>
                                                <option value=""><?php esc_html_e('Select Position', 'bmp'); ?></option>
                                                <option value="left" <?php echo ($position == 'left') ? $selected : ''; ?>><?php esc_html_e('Left', 'bmp'); ?></option>
                                                <option value="right" <?php echo ($position == 'right') ? $selected : ''; ?>><?php esc_html_e('Right', 'bmp'); ?></option>
                                            </select>
                                        <?php
                                        } ?>
                                        <div class="bmp_position_message"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-group">
                                        <input id="bmp_epin" name="bmp_epin" type="text" class="form-control" placeholder="<?php esc_html_e('Epin Optional', 'bmp'); ?>" value="">

                                        <div class="bmp_epin_message"></div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-12 text-center">
                                <input type="submit" class="btn button btn-primary" value="Register" />
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
<?php
get_footer();

?>