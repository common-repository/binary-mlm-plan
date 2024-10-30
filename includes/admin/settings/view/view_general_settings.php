<?php
global $wpdb;
$currencies = $this->getCurrency();
$settings = get_option('bmp_manage_general');
//echo '<pre>';print_r($settings);echo '</pre>';
?>

<div class="form container-fluid">
    <div class="col-md-12">
        <h2><?php _e('Generel Settings', 'bmp'); ?></h2>
    </div>
    <br>
    <div class="col-md-12">
        <table class="form-table">
            <tbody>
                <th scope="row"><label for="default_role" title="Select your currency which will you use.!"><?php _e('Currency', 'bmp'); ?></label></th>
                <td>
                    <select name="bmp_personal_referrer" id="bmp_personal_referrer" required="" placeholder="" required="">
                        <?php foreach ($currencies as $key => $value) { ?>
                            <option value="<?php echo $key; ?>" <?php echo (!empty($settings['bmp_personal_referrer']) && $settings['bmp_personal_referrer'] == $key) ? 'selected' : ''; ?>><?php echo $value; ?></option>
                        <?php } ?>
                    </select>
                    <small id="bmp_personal_referrerHelp" class="form-text text-muted"><?php _e('Select your currency which will you use.', 'bmp'); ?></small>
                </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>