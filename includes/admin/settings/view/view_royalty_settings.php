<?php
global $wpdb;
$row_num = 0;
$results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_royalty", OBJECT);
//echo '<pre>';print_r($results); echo '</pre>'; die;
?>
<div class="form-row">
    <div class="col-md-12">
        <h2><?php _e('Royalty Bonus Settings', 'bmp'); ?></h2>
    </div><br>

    <div id="royalty_manage_rows" class="col-md-12" style="float:left;">
        <div class="form-group">
            <label class="col-md-3" for="bmp_bonus slab" data-toggle="tooltip" title="" data-original-title="!" style="float:left;padding-right: 5px;padding-left: 5px;"><?php _e('Name', 'bmp'); ?> </label>
            <label class="col-md-3" for="bmp_bonus slab" data-toggle="tooltip" title="" data-original-title="!" style="float:left;padding-right: 5px;padding-left: 5px;"><?php _e('Pair Range	', 'bmp'); ?> </label>
            <label class="col-md-2" for="bmp_bonus slab" data-toggle="tooltip" title="" data-original-title="!" style="float:left;padding-right: 5px;padding-left: 5px;"><?php _e('Pool %	', 'bmp'); ?> </label>
            <label class="col-md-2" for="bmp_bonus slab" data-toggle="tooltip" title="" data-original-title="!" style="float:left;padding-right: 5px;padding-left: 5px;"><?php _e('Pool Cap', 'bmp'); ?> </label>
            <lable class="col-md-2" style="float:left;padding-right: 5px;padding-left: 5px;"></lable>
            <?php foreach ($results as $key => $result) { ?>
                <div id="royalty_income_<?php echo $row_num; ?>">
                    <div id="bmp_royalty_manage_name_<?php echo $row_num; ?>" class="col-md-3" style="float:left;padding-right: 5px;padding-left: 5px;">
                        <div class="form-group">
                            <!--<input name="royalty_manage[<?php echo $row_num; ?>][id]" type="hidden" value="<?php echo $result->id; ?>">-->
                            <input name="royalty_manage[<?php echo $row_num; ?>][name]" type="text" style="" value="<?php echo $result->name; ?>" class="form-control" placeholder="">
                            <small id="bmp_royalty_manage_name_help_<?php echo $row_num; ?>" class="form-text text-muted"></small>
                        </div>
                    </div>

                    <div id="bmp_royalty_manage_range_<?php echo $row_num; ?>" class="col-md-3" style="float:left;padding-right: 5px;padding-left: 5px;">
                        <div class="form-group">
                            <div class="col-md-12" style="padding:0px;">
                                <div class="col-md-5" style="padding:0px;float:left;">
                                    <input name="royalty_manage[<?php echo $row_num; ?>][pair_start]" type="text" style="" value="<?php echo $result->pair_start; ?>" class="form-control" placeholder="">
                                </div>
                                <div class="col-md-1" style="padding:0px;float:left;text-align: center;">&nbsp;</div>
                                <div class="col-md-5" style="padding:0px;float:left;">
                                    <input name="royalty_manage[<?php echo $row_num; ?>][pair_end]" type="text" style="" value="<?php echo $result->pair_end; ?>" class="form-control" placeholder="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="bmp_royalty_manage_pool_percentage_<?php echo $row_num; ?>" class="col-md-2" style="float:left;padding-right: 5px;padding-left: 5px;">
                        <div class="form-group">
                            <input name="royalty_manage[<?php echo $row_num; ?>][pool_percentage]" type="text" style="" value="<?php echo $result->pool_percentage; ?>" class="form-control" placeholder="">
                        </div>
                    </div>
                    <div id="bmp_royalty_manage_pool_percentage_<?php echo $row_num; ?>" class="col-md-2" style="float:left;padding-right: 5px;padding-left: 5px;">
                        <div class="form-group">
                            <input name="royalty_manage[<?php echo $row_num; ?>][pool_cap]" type="text" style="" value="<?php echo $result->pool_cap; ?>" class="form-control" placeholder="">
                        </div>
                    </div>


                    <div id="bmp_royalty_manage_button_<?php echo $row_num; ?>" class="col-md-2" style="float:left;padding-right: 5px;padding-left: 5px;">
                        <div class="form-group">
                            <button class="btn btn-danger" type="button" onclick="removetRow(<?php echo $row_num; ?>)"><?php echo _e('Remove', 'bmp'); ?></button>
                        </div>
                    </div>
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
            <div class="col-md-2" style="float:left;"><button type="button" onclick="addRow()" class="btn btn-primary"><?php _e('Add', 'bmp'); ?></button></div>
        </div>
    </div>


    <script>
        var row_num = '<?php echo $row_num; ?>';

        function addRow() {
            $('#royalty_manage_rows>.form-group').append('<div id="royalty_income_' + row_num + '"><div  id="bmp_royalty_manage_name_' + row_num + '" class="col-md-3" style="float:left;padding-right: 5px;padding-left: 5px;"><div class="form-group"><input name="royalty_manage[' + row_num + '][id]" type="hidden" value=""><input name="royalty_manage[' + row_num + '][name]" type="text" style="" value="" class="form-control" placeholder=""><small id="bmp_royalty_manage_name_help_' + row_num + '" class="form-text text-muted"></small></div></div><div  id="bmp_royalty_manage_range_' + row_num + '" class="col-md-3" style="float:left;padding-right: 5px;padding-left: 5px;"><div class="form-group"><div class="col-md-12" style="padding:0px;"><div class="col-md-5" style="padding:0px;float:left;"><input name="royalty_manage[' + row_num + '][pair_start]" type="text" style="" value="" class="form-control" placeholder=""></div><div class="col-md-1" style="padding:0px;float:left;text-align: center;">&nbsp;</div><div class="col-md-5" style="padding:0px;float:left;"><input name="royalty_manage[' + row_num + '][pair_end]" type="text" style="" value="" class="form-control" placeholder=""></div></div></div></div><div id="bmp_royalty_manage_pool_percentage_' + row_num + '" class="col-md-2" style="float:left;padding-right: 5px;padding-left: 5px;"><div class="form-group"><input name="royalty_manage[' + row_num + '][pool_percentage]" type="text" style="" value="" class="form-control" placeholder=""></div></div><div id="bmp_royalty_manage_pool_percentage_' + row_num + '" class="col-md-2" style="float:left;padding-right: 5px;padding-left: 5px;"><div class="form-group"><input name="royalty_manage[' + row_num + '][pool_cap]" type="text" style="" value="" class="form-control" placeholder=""></div></div><div  id="bmp_royalty_manage_button_' + row_num + '" class="col-md-2" style="float:left;padding-right: 5px;padding-left: 5px;"><div class="form-group"><button class="btn btn-danger" type="button" onclick="removeProductRow(' + row_num + ')"><?php echo _e('Remove', 'bmp'); ?></button></div></div></div>');
            row_num++;
        }

        function removeProductRow(row_num) {
            $('#royalty_income_' + row_num).remove();
            // $( '#bmp_royalty_manage_amount_'+row_num ).remove();
            // $( '#bmp_royalty_manage_button_'+row_num ).remove();
        }
    </script>
</div>