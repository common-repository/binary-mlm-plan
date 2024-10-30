<div class="content_style">
    <div class="form-row">
        <div class="col-md-12">
            <h4><?php _e('Commission Distribution Section', 'bmp'); ?></h4>
        </div><br>
        <div class="col-md-12">
            <div class="form-group  bmp-section ml-1">
                <?php _e('The Commission Distribution can be show when you will process for the commision. there are some Restriction to show the commission before run the commission. So kindly run first of all commission then you find how much commission you got now. Another Restriction is that, only one time the commission will be display. if you will run again then it will not show because commission has been distributed already when you run first time the commission distribution.', 'bmp'); ?>
            </div>
        </div>
        <?php
        if ($_POST) {
            $dataarray = bmp_distribute_pair_commission_display_function();
        } else {
            $dataarray = array();
        }
        ?>

        <div class="table-responsive">
            <table class="table table-striped ml-1">
                <thead class="thead-inverse">
                    <tr>
                        <th>#</th>
                        <th><?php _e('Parent Name', 'bmp'); ?></th>
                        <th><?php _e('Childs Name', 'bmp'); ?></th>
                        <th><?php _e('Amount', 'bmp'); ?></th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($dataarray) {
                        foreach ($dataarray[0] as $key => $row) { ?>
                            <tr>
                                <td><?php echo $key; ?></td>
                                <td><?php echo $row['parent_name']; ?></td>
                                <td><?php echo $row['childs']; ?></td>
                                <td><?php echo $row['amount']; ?></td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="4"><?php _e('No Record is here. Please click on Commission Distribute Button. If yet No Record Get , Its Mean you have not commission to distribute.'); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>