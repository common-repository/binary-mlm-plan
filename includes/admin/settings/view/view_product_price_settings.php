<?php
global $wpdb;
$row_num = 0;
$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_product_price", OBJECT);

?>
<div class="form-row">
    <div class="col-md-12">
        <h2><?php _e('Product Mange Settings', 'bmp'); ?></h2>
    </div><br>

    <div id="product_manage_rows" class="col-md-12" style="float:left;">
        <div class="form-group">
            <label class="col-md-5" for="bmp_bonus slab" data-toggle="tooltip" title="" data-original-title="!" style="float:left;padding-right: 5px;padding-left: 5px;"><?php _e('Product Name', 'bmp'); ?> </label>
            <label class="col-md-5" for="bmp_bonus slab" data-toggle="tooltip" title="" data-original-title="!" style="float:left;padding-right: 5px;padding-left: 5px;"><?php _e('Product Price', 'bmp'); ?> </label>
            <lable class="col-md-2" style="float:left;padding-right: 5px;padding-left: 5px;"></lable>
            <?php foreach ($results as $key => $result) { ?>
                <div id="bmp_product_manage_name_<?php echo $row_num; ?>" class="col-md-5" style="float:left;padding-right: 5px;padding-left: 5px;">
                    <div class="form-group">
                        <input name="product_manage[<?php echo $row_num; ?>][id]" type="hidden" value="<?php echo $result->id; ?>">
                        <input name="product_manage[<?php echo $row_num; ?>][name]" type="text" style="" value="<?php echo $result->name; ?>" class="form-control" placeholder=""><small id="bmp_product_manage_name_help_<?php echo $row_num; ?>" class="form-text text-muted"></small>
                    </div>
                </div>
                <div id="bmp_product_manage_amount_<?php echo $row_num; ?>" class="col-md-5" style="float:left;padding-right: 5px;padding-left: 5px;">
                    <div class="form-group"><input name="product_manage[<?php echo $row_num; ?>][amount]" type="text" style="" value="<?php echo $result->amount; ?>" class="form-control" placeholder=""><small id="bmp_product_manage_amount_help_<?php echo $row_num; ?>" class="form-text text-muted"></small></div>
                </div>
                <div id="bmp_product_manage_button_<?php echo $row_num; ?>" class="col-md-2" style="float:left;padding-right: 5px;padding-left: 5px;">
                    <div class="form-group"><button class="btn btn-danger" type="button" onclick="removeProductRow(<?php echo $row_num; ?>)"><?php echo _e('Remove', 'bmp'); ?></button></div>
                </div>
            <?php
                $row_num++;
            }
            ?>
        </div>
    </div>

    <div class="col-md-12" style="float:left;">
        <div class="form-group">

            <div class="col-md-10" style="float:left;"></div>
            <div class="col-md-2" style="float:left;"><button type="button" onclick="addProductRow()" class="btn btn-primary"><?php _e('Add', 'bmp'); ?></button></div>
        </div>
    </div>


    <script>
        var row_num = '<?php echo $row_num; ?>';

        function addProductRow() {
            $('#product_manage_rows>.form-group').append('<div  id="bmp_product_manage_name_' + row_num + '" class="col-md-5" style="float:left;padding-right: 5px;padding-left: 5px;"><div class="form-group"><input name="product_manage[' + row_num + '][name]" type="text" style="" value="" class="form-control" placeholder=""><small id="bmp_product_manage_name_help_' + row_num + '" class="form-text text-muted"></small></div></div><div  id="bmp_product_manage_amount_' + row_num + '" class="col-md-5" style="float:left;padding-right: 5px;padding-left: 5px;"><div class="form-group"><input name="product_manage[' + row_num + '][amount]" type="text" style="" value="" class="form-control" placeholder=""><small id="bmp_product_manage_amount_help_' + row_num + '" class="form-text text-muted"></small></div></div><div  id="bmp_product_manage_button_' + row_num + '" class="col-md-2" style="float:left;padding-right: 5px;padding-left: 5px;"><div class="form-group"><button class="btn btn-danger" type="button" onclick="removeProductRow(' + row_num + ')"><?php echo _e('Remove', 'bmp'); ?></button></div></div>');
            row_num++;
        }

        function removeProductRow(row_num) {
            $('#bmp_product_manage_name_' + row_num).remove();
            $('#bmp_product_manage_amount_' + row_num).remove();
            $('#bmp_product_manage_button_' + row_num).remove();
        }
    </script>
</div>