<?php
global $wpdb;
$setting = get_option('bmp_manage_eligibility'); ?>
<div class="form-row container-fluid">
    <div class="col-md-12">
        <h2><?php _e('Eligibility Settings', 'bmp'); ?></h2>
    </div><br>
    <div class="col-md-12">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="bmp_elibility_refferals"><?php _e('Direct Paid Referral(s)', 'bmp'); ?></label></th>
                    <td><input name="bmp_referral" id="bmp_referral" type="text" style="" value="<?php echo (isset($setting['bmp_referral']) && is_numeric($setting['bmp_referral'])) ? $setting['bmp_referral'] : ''; ?>" placeholder="<?php _e('Initial Pair', 'bmp'); ?>" required class="regular-text"></td>
                    <small id="bmp_referral_help" class="form-text text-muted"></small>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="col-md-12">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="bmp_elibility_refferals"><?php _e('Left Leg Referral(s)', 'bmp'); ?></label></th>
                    <td><input name="bmp_referral_left" id="bmp_referral_left" type="text" style="" value="<?php echo (isset($setting['bmp_referral_left']) && is_numeric($setting['bmp_referral_left'])) ? $setting['bmp_referral_left'] : ''; ?>" placeholder="<?php _e('Referral Left', 'bmp'); ?>" required class="regular-text"></td>
                    <small id="bmp_referral_left_help" class="form-text text-muted"></small>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-12">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="bmp_elibility_refferals"><?php _e('Right Leg Referral(s)', 'bmp'); ?></label></th>
                    <td><input name="bmp_referral_right" id="bmp_referral_right" type="text" style="" value="<?php echo (isset($setting['bmp_referral_right']) && is_numeric($setting['bmp_referral_right'])) ? $setting['bmp_referral_right'] : ''; ?>" placeholder="<?php _e('Referral Right', 'bmp'); ?>" required class="regular-text"></td>
                    <small id="bmp_referral_right_help" class="form-text text-muted"></small>
                </tr>
            </tbody>
        </table>
    </div>
</div>