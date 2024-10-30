<div class="form-row">
    <?php $row_num = 0;
    $bmp_bonus_criteria = get_option('bmp_bonus_criteria');
    $bmp_bonuses = get_option('bmp_bonus');

    ?>
    <div class="col-md-12">
        <h2><?php _e('Bonus Settings', 'bmp'); ?></h2>
    </div><br>

    <div class="col-md-12" style="float:left;">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="bmp_initial_pair" title=""><?php _e('Bonus Criteria'); ?> </label>
                    </th>
                    <td>
                        <select name="bmp_bonus_criteria" id="bmp_bonus_criteria" required="" placeholder="" required="">
                            <option value=""><?php _e('Select Bonus Criteria', 'bmp'); ?></option>
                            <option value="pair" <?php echo ($bmp_bonus_criteria == 'pair') ? 'selected=selected' : ''; ?>><?php _e('Number of Pairs', 'bmp'); ?></option>
                            <option value="personal" <?php echo ($bmp_bonus_criteria == 'personal') ? 'selected=selected' : ''; ?>><?php _e('Number of Personal Referrer', 'bmp'); ?></option>
                        </select>
                        <small id="bmp_personal_referrerHelp" class="form-text text-muted"><?php _e('Select your currency which will you use.', 'bmp'); ?></small>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
    <div class="col-md-12" style="float:left;">
        <div class="row">
            <div class="col-md-12"><label for="bmp_bonus slab" data-toggle="tooltip" title="" data-original-title="!" title=""><?php _e('Bonus Slab', 'bmp'); ?> </label></div>
        </div>
    </div>
    <div id="bonus_slab_rows" class="col-md-12" style="float:left;">
        <div class="form-group">
            <?php if ($bmp_bonuses) {
                foreach ($bmp_bonuses as $key => $bmp_bonus) { ?>
                    <div id="bmp_bonus_pair_<?php echo $row_num; ?>" class="col-md-5" style="float:left;padding-right: 5px;padding-left: 5px;">
                        <div class="form-group">
                            <input name="bmp_bonus[<?php echo $row_num; ?>][pair]" type="text" style="" value="<?php echo $bmp_bonus['pair']; ?>" class="form-control" placeholder="">
                            <small id="bmp_bonus_pair_help_<?php echo $row_num; ?>" class="form-text text-muted"></small>
                        </div>
                    </div>
                    <div id="bmp_bonus_amount_<?php echo $row_num; ?>" class="col-md-5" style="float:left;padding-right: 5px;padding-left: 5px;">
                        <div class="form-group">
                            <input name="bmp_bonus[<?php echo $row_num; ?>][amount]" type="text" style="" value="<?php echo $bmp_bonus['amount']; ?>" class="form-control" placeholder="">
                            <small id="bmp_bonus_amount_help_<?php echo $row_num; ?>" class="form-text text-muted"></small>
                        </div>
                    </div>
                    <div id="bmp_bonus_button_<?php echo $row_num; ?>" class="col-md-2" style="float:left;padding-right: 5px;padding-left: 5px;">
                        <div class="form-group">
                            <button class="btn btn-danger" type="button" onclick="removeBonusRow(<?php echo $row_num; ?>)"><?php echo _e('Remove', 'bmp'); ?></button>
                        </div>
                    </div>
            <?php
                    $row_num++;
                }
            }
            ?>
        </div>
    </div>



    <div class="col-md-12" style="float:left;">
        <div class="form-group">

            <div class="col-md-10" style="float:left;"></div>
            <div class="col-md-2" style="float:left;"><button type="button" onclick="addBonusRow()" class="btn btn-primary"><?php _e('Add', 'bmp'); ?></button></div>
        </div>
    </div>
</div>

<script>
    var row_num = '<?php echo $row_num; ?>';

    function addBonusRow() {
        $('#bonus_slab_rows>.form-group').append('<div  id="bmp_bonus_pair_' + row_num + '" class="col-md-5" style="float:left;padding-right: 5px;padding-left: 5px;"><div class="form-group"><input name="bmp_bonus[' + row_num + '][pair]" type="text" style="" value="" class="form-control" placeholder="<?php _e('Pair', 'bmp'); ?>"><small id="bmp_bonus_pair_help_' + row_num + '" class="form-text text-muted"></small></div></div><div  id="bmp_bonus_amount_' + row_num + '" class="col-md-5" style="float:left;padding-right: 5px;padding-left: 5px;"><div class="form-group"><input name="bmp_bonus[' + row_num + '][amount]" type="text" style="" value="" class="form-control" placeholder="<?php _e('Amount', 'bmp'); ?>"><small id="bmp_bonus_amount_help_' + row_num + '" class="form-text text-muted"></small></div></div><div  id="bmp_bonus_button_' + row_num + '" class="col-md-2" style="float:left;padding-right: 5px;padding-left: 5px;"><div class="form-group"><button class="btn btn-danger" type="button" onclick="removeBonusRow(' + row_num + ')"><?php echo _e('Remove', 'bmp'); ?></button></div></div>');
        row_num++;
    }

    function removeBonusRow(row_num) {
        $('#bmp_bonus_pair_' + row_num).remove();
        $('#bmp_bonus_amount_' + row_num).remove();
        $('#bmp_bonus_button_' + row_num).remove();
    }
</script>