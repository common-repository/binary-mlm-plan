<?php
global $wpdb;
$row_num = 0;
$gen = new BMP_Settings_General();
// $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_product_price", OBJECT);
$epin_length = $gen->getepinlength();
?>
<div class="form-row">
    <div class="col-md-12">
        <h2><?php _e('ePin Settings', 'bmp'); ?></h2>
    </div><br>
    <div id="epin_name" class="col-md-12" style="float:left;">
        <div class="form-group ">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for=""><?php _e('ePin Name', 'bmp'); ?></label></th>
                        <td>
                            <input type="text" class="form-control" name="bmp_epin_name" required id="bmp_epin_name">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="epin_typw" class="col-md-12" style="float:left;">
        <div class="form-group ">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for=""><?php _e('ePin Type', 'bmp'); ?></label></th>
                        <td>
                            <select name="bmp_epin_type" id="bmp_epin_type" class="form-control" required>
                                <option value=""><?php _e('Select ePin Type', 'bmp'); ?></option>
                                <option value="regular"><?php _e('Regular', 'bmp'); ?></option>
                                <option value="free"><?php _e('Free', 'bmp'); ?></option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="epin_number" class="col-md-12" style="float:left;">
        <div class="form-group ">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for=""><?php _e('Number Of ePins', 'bmp'); ?> </label></th>
                        <td>
                            <input name="bmp_epin_number" id="bmp_epin_number" class="form-control" required>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="epin_product" class="col-md-12" style="float:left;">
        <div class="form-group ">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for=""><?php _e('ePin length', 'bmp'); ?></label></th>
                        <td>
                            <select name="bmp_epin_length" id="bmp_epin_length" class="form-control" required>
                                <option value=""><?php _e('Select ePin Length', 'bmp'); ?></option>
                                <?php foreach ($epin_length as $key => $value) { ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="bmp_epin_price" class="col-md-12" style="float:left;">
        <div class="form-group ">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for=""><?php _e('ePin Price', 'bmp'); ?> </label></th>
                        <td>
                            <input name="bmp_epin_price" id="bmp_epin_price" class="form-control" required>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>