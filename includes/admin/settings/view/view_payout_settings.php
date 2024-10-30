<?php
global $wpdb;
$setting = get_option('bmp_manage_payout');
?>
<div class="form-row container-fluid">
    <div class="col-md-12">
        <h2><?php _e('Payout Settings', 'bmp'); ?></h2>
    </div><br>

    <div class="col-md-12 float-left">
        <div class="form-group ">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for=""><?php _e('Direct Referral Commission', 'bmp'); ?></label></th>
                        <td><input name="bmp_referral_commission_amount" id="bmp_referral_commission_amount" type="text" style="" value="<?php echo (isset($setting['bmp_referral_commission_amount']) && is_numeric($setting['bmp_referral_commission_amount'])) ? $setting['bmp_referral_commission_amount'] : ''; ?>" class="regular-text" placeholder="<?php _e('Bmp Referral Commission Amount', 'bmp'); ?>" required></td>
                        <small id="bmp_referral_commission_amount_help" class="form-text text-muted"></small>

                        <td style="float:left;padding-right: 250px;padding-left: 0px;"><select name="bmp_referral_commission_type" id="bmp_referral_commission_type" type="text" style="" value="1" class="" required>
                                <option value="fixed" <?php echo (isset($setting['bmp_referral_commission_type']) && $setting['bmp_referral_commission_type'] == 'fixed') ? 'selected' : ''; ?>><?php _e('Fixed', 'bmp'); ?></option>
                                <option value="percentage" <?php echo (isset($setting['bmp_referral_commission_type']) && $setting['bmp_referral_commission_type'] == 'percentage') ? 'selected' : ''; ?>><?php _e('Percentage', 'bmp'); ?></option>
                            </select>
                            <small id="bmp_referral_commission_type_help" class="form-text text-muted"></small>
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <div class="col-md-12 float-left">
        <div class="form-group ">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for=""><?php _e('Service Charge (If any)', 'bmp'); ?></label></th>

                        <td><input name="bmp_service_charge_amount" id="bmp_service_charge_amount" type="text" style="" value="<?php echo (isset($setting['bmp_service_charge_amount']) && is_numeric($setting['bmp_service_charge_amount'])) ? $setting['bmp_service_charge_amount'] : ''; ?>" class="regular-text" placeholder="<?php _e('Bmp Service Charge Amount', 'bmp'); ?>" required></td>

                        <small id="bmp_service_charge_amount_help" class="form-text text-muted"></small>

                        <td style="float:left;padding-right: 250px;padding-left: 0px;"><select name="bmp_service_charge_type" id="bmp_service_charge_type" type="text" style="" value="1" class="" required>
                                <option value="fixed" <?php echo (isset($setting['bmp_service_charge_type']) && $setting['bmp_service_charge_type'] == 'fixed') ? 'selected' : ''; ?>><?php _e('Fixed', 'bmp'); ?></option>
                                <option value="percentage" <?php echo (isset($setting['bmp_service_charge_type']) && $setting['bmp_service_charge_type'] == 'percentage') ? 'selected' : ''; ?>><?php _e('Percentage', 'bmp'); ?></option>
                            </select>
                            <small id="bmp_service_charge_type_help" class="form-text text-muted"></small>
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <div class="col-md-12 float-left">
        <div class="form-group ">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for=""><?php _e('Tax Deduction', 'bmp'); ?></label></th>

                        <td><input name="bmp_tds" id="bmp_tds" type="text" style="" value="<?php echo (isset($setting['bmp_tds']) && is_numeric($setting['bmp_tds'])) ? $setting['bmp_tds'] : ''; ?>" class="regular-text" placeholder="<?php _e('Bmp Tds', 'bmp'); ?>" required></td>

                        <small id="bmp_tds_help" class="form-text text-muted"></small>

                        <td style="float:left;padding-right: 250px;padding-left: 0px;"><select name="bmp_tds_type" id="bmp_tds_type" type="text" value="1" class="" required>
                                <option value="fixed" <?php echo (isset($setting['bmp_tds_type']) && $setting['bmp_tds_type'] == 'fixed') ? 'selected' : ''; ?>><?php _e('Fixed', 'bmp'); ?></option>
                                <option value="percentage" <?php echo (isset($setting['bmp_tds_type']) && $setting['bmp_tds_type'] == 'percentage') ? 'selected' : ''; ?>><?php _e('Percentage', 'bmp'); ?></option>
                            </select>
                            <small id="bmp_tds_type_help" class="form-text text-muted"></small>
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <div class="col-md-12 float-left">
        <div class="form-group ">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for=""><?php _e('Cap Limit Amount', 'bmp'); ?></label></th>

                        <td>
                            <input name="bmp_cap_limit_amount" id="bmp_cap_limit_amount" type="text" style="" value="<?php echo (isset($setting['bmp_cap_limit_amount']) && is_numeric($setting['bmp_cap_limit_amount'])) ? $setting['bmp_cap_limit_amount'] : ''; ?>" class="regular-text" placeholder="<?php _e('Bmp Cap Limit Amount', 'bmp'); ?>" required>
                        </td>

                        <small id="bmp_cap_limit_amount_help" class="form-text text-muted"></small>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</div>