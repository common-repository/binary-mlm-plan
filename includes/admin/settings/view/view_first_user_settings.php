<?php
global $wpdb;
$users = $wpdb->get_results("SELECT u.* FROM {$wpdb->prefix}users as u JOIN {$wpdb->prefix}usermeta as um On u.id=um.user_id AND um.meta_key='wp_capabilities' AND um.meta_value NOT LIKE '%administrator%'"); ?>
<div class="form container-fluid">
    <div class="col-md-12">
        <h2 class="text-center"><?php _e('Binary MLM Fisrt User Create', 'bmp'); ?></h2>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="" data-toggle="tooltip" class="label-control" title="" data-original-title="!"><?php _e('Binary MLM User By Existing User', 'bmp'); ?> </label>
            <select name="bmp_existing_user" id="bmp_existing_user" class="form-control" required>
                <option value=""><?php _e('Select User', 'bmp'); ?></option>
                <?php foreach ($users as $user) { ?>
                    <option value="<?php echo $user->ID; ?>"><?php echo $user->user_login; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <input type="checkbox" name="new_bmp_user" id="new_bmp_user" value="1">
            <span class="ml-5">
                <?php _e(' New Binary MLM User Create'); ?>
            </span>
        </div>
    </div>

    <div id="bmp_new_user" class="col-md-12 bmp-section">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="" data-toggle="tooltip" title="" data-original-title="!"><?php _e('User Name', 'mlm'); ?> </label>
                    <input type="text" class="form-control" name="bmp_first_username" id="bmp_first_username">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="" data-toggle="tooltip" title="" data-original-title="!"><?php _e('User Email', 'mlm'); ?> </label>
                    <input type="email" class="form-control" name="bmp_first_email" id="bmp_first_email">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="" data-toggle="tooltip" title="" data-original-title="!"><?php _e('Password', 'mlm'); ?> </label>
                    <input type="password" class="form-control" name="bmp_first_password" id="bmp_first_password">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="" data-toggle="tooltip" title="" data-original-title="!"><?php _e('Confirm Password', 'mlm'); ?> </label>
                    <input type="password" class="form-control" name="bmp_first_confirm_password" id="bmp_first_confirm_password">
                </div>
            </div>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            if ($('#new_bmp_user').is(':checked')) {
                $('#bmp_new_user').css('display', 'block');
                $('#bmp_existing_user').removeAttr('required');
                $('#bmp_first_username').attr('required', true);
                $('#bmp_first_email').attr('required', true);
                $('#bmp_first_password').attr('required', true);
                $('#bmp_first_confirm_password').attr('required', true);
            }

            $('#new_bmp_user').click(function() {
                if ($(this).is(':checked')) {
                    $('#bmp_new_user').css('display', 'block');
                    $('#bmp_existing_user').removeAttr('required');
                    $('#bmp_first_username').attr('required', true);
                    $('#bmp_first_email').attr('required', true);
                    $('#bmp_first_password').attr('required', true);
                    $('#bmp_first_confirm_password').attr('required', true);
                } else {
                    $('#bmp_new_user').css('display', 'none');
                    $('#bmp_existing_user').attr('required', true);
                    $('#bmp_first_username').removeAttr('required');
                    $('#bmp_first_email').removeAttr('required');
                    $('#bmp_first_password').removeAttr('required');
                    $('#bmp_first_confirm_password').removeAttr('required');
                }

            });

        });
    </script>
</div>