<div class="mt-3">
    <div class="form-row">
        <div class="col-md-12">
            <h4 class="text-center"><?php _e('Payout Run', 'bmp'); ?></h4>
        </div><br>
        <?php

        if ($_POST) {
            $dataarray = bmp_run_payout_functions();
        } else {
            $dataarray = bmp_run_payout_display_functions();

            //echo '<pre>';print_r($dataarray);echo '</pre>'; die;

        }
        ?>

        <div class="table-responsive">
            <table class="table-bordered table ml-1  table-striped">
                <thead class="text-center">
                    <tr>
                        <th>#</th>
                        <th><?php _e('User Name', 'bmp'); ?></th>
                        <th><?php _e('First Name', 'bmp'); ?></th>
                        <th><?php _e('Last Name', 'bmp'); ?></th>
                        <th><?php _e('Direct Refferal Commission', 'bmp'); ?></th>
                        <th><?php _e('Total Amount', 'bmp'); ?></th>
                        <th><?php _e('Cap Limit', 'bmp'); ?></th>
                        <th><?php _e('Tax', 'bmp'); ?></th>
                        <th><?php _e('Service Charge', 'bmp'); ?></th>
                        <th><?php _e('Net Amount', 'bmp'); ?></th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                    if ($dataarray) {
                        foreach ($dataarray as $key => $row) { ?>
                            <tr>
                                <td><?php echo ++$key; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['first_name']; ?></td>
                                <td><?php echo $row['last_name']; ?></td>
                                <td><?php echo $row['direct_refferal_commission']; ?></td>
                                <td><?php echo $row['total_amount']; ?></td>
                                <td><?php echo $row['cap_limit']; ?></td>
                                <td><?php echo $row['tax']; ?></td>
                                <td><?php echo $row['service_charge']; ?></td>
                                <td><?php echo $row['net_amount']; ?></td>
                            </tr>
                        <?php
                        }
                    } else {
                        if (sanitize_text_field($_POST)) {
                        ?>
                            <tr>
                                <td colspan="12"><?php echo __('Payout Run successfully', 'bmp'); ?></td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td colspan="12" class="text-center"><?php echo __('There are no records Founds for Payout', 'bmp'); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>