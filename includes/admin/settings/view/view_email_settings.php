<div class="form-row">
	<?php $row_num=0;
	$settings=get_option('bmp_manage_email');
	
	?>
	<div class="col-md-12"><h2><?php _e('Email Settings','bmp');?></h2></div><br>	

<div class="email-shortcodes" style="background: #cecece1a;">
    <div class="col-md-12"><h4><?php _e('Please use the short code in the email description.','bmp');?></h4></div>
    <table class="form-table text-center e_table_style">
        <tr><td><?php _e('First Name','bmp');?></td><td>:</td><td>[firstname]</td></tr>
        <tr><td><?php _e('Last Name','bmp');?></td><td>:</td><td>[lastname]</td></tr>
        <tr><td><?php _e('Email','bmp');?></td><td>:</td><td>[email]</td></tr>
        <tr><td><?php _e('User Name','bmp');?></td><td>:</td><td>[username]</td></tr>
        <tr><td><?php _e('Amount','bmp');?></td><td>:</td><td>[amount]</td></tr>
        <tr><td><?php _e('Withdrawal','bmp');?></td><td>:</td><td>[withdrawalmode]</td></tr>
        <tr><td><?php _e('Payout Id','bmp');?></td><td>:</td><td>[payoutid]</td></tr>
        <tr><td><?php _e('Site Name','bmp');?></td><td>:</td><td>[sitename]</td></tr>
    </table>
</div>

  <div class="form-group">
	<div class="col-md-12 float-left">
	 <div class="form-group ">
	    <table class="form-table">
        <tbody>
         <tr>
         	<h5 for="bmp_payout_email" class="thick" data-toggle="tooltip" title="" data-original-title="!"><?php _e('Payout Recieve Mail','bmp');?> </h5>
         	<th scope="row"><label for=""><?php _e('Subject','bmp');?></label></th>

         	  <td><input name="bmp_runpayout_email_subject" id="bmp_runpayout_email_subject" type="text" style="" value="<?php echo $settings['bmp_runpayout_email_subject'];?>" required class="regular-text" placeholder="<?php _e('Subject','bmp');?>"></td>
         	
        
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
         	<th scope="row"><label  for="bmp_payout_email_message"><?php _e('Message','bmp');?></label></th>
         	   <td>
         	   			
         	<textarea type="text" name="bmp_runpayout_email_message" rows="6" id="bmp_runpayout_email_message" class="form-control textareawidth"  placeholder="<?php _e('Message','bmp');?>" required><?php echo $settings['bmp_runpayout_email_message'];?>
			</textarea>
			<input type="checkbox" name="bmp_payout_mail" value="1" <?php echo ($settings['bmp_payout_mail']==1)?'checked':'';?>> <?php _e('Enabled this Mail functionality','bmp');?>
		</td>
		  </tr>
	     </tbody>
        </table>
		</div>
	</div>
	</div>	
 <div class="form-group">
	<div class="col-md-12 float-left">
	 <div class="form-group ">
	    <table class="form-table">
        <tbody>
         <tr>
         	<h5 for="bmp_networkgrowing_email" class="thick"  data-toggle="tooltip" title="" data-original-title="!"><?php _e('Network Growing Mail','bmp');?> </h5>
         	<th scope="row"><label for="bmp_networkgrowing_email_subject"><?php _e('Subject','bmp');?></label></th>

         	  <td><input name="bmp_networkgrowing_email_subject" id="bmp_networkgrowing_email_subject" type="text" style="" value="<?php echo $settings['bmp_networkgrowing_email_subject'];?>" required class="regular-text" placeholder="<?php _e('Subject','bmp');?>"></td>
         	
        
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
         	<th scope="row"><label  for="bmp_networkgrowing_email_message"><?php _e('Message','bmp');?> </label></th>
         	   <td>
         	  
         	<textarea type="text" name="bmp_networkgrowing_email_message" rows="6" id="bmp_networkgrowing_email_message" class="form-control textareawidth"  placeholder="<?php _e('Message','bmp');?>" required><?php echo $settings['bmp_networkgrowing_email_message'];?>
			</textarea>

			<input type="checkbox" name="bmp_networkgrowing_mail" value="1"  <?php echo ($settings['bmp_networkgrowing_mail']==1)?'checked':'';?>> <?php _e('Enabled this Mail functionality','bmp');?>
		</td>
		  </tr>
	     </tbody>
        </table>
		</div>
	</div>	
</div>
	 <div class="form-group ">
	<div class="col-md-12 float-left">
	 <div class="form-group ">
	    <table class="form-table">
        <tbody>
         <tr>
         	<h5 for="bmp_withdrawalInitiate_email"  class="thick"  data-toggle="tooltip" title="" data-original-title="!"><?php _e('Withdrawal Initiated Mail','bmp');?> </h5>

         	<th scope="row"><label for="bmp_withdrawalInitiate_email_subject"><?php _e('Subject','bmp');?> </label></th>

         	  <td><input name="bmp_withdrawalInitiate_email_subject" id="bmp_withdrawalInitiate_email_subject" type="text" style="" value="<?php echo $settings['bmp_networkgrowing_email_subject'];?>" required class="regular-text" placeholder="<?php _e('Subject','bmp');?>"></td>
        
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
         	<th scope="row"><label  for="bmp_withdrawalInitiate_email_message"><?php _e('Message','bmp');?> </label></th>
         	   <td>
         	  
         	<textarea type="text" name="bmp_withdrawalInitiate_email_message" rows="6" id="bmp_withdrawalInitiate_email_message" class="form-control textareawidth"  placeholder="<?php _e('Message','bmp');?>" required><?php echo $settings['bmp_withdrawalInitiate_email_message'];?>
			</textarea>

			<input type="checkbox" name="bmp_withdrawalInitiate_mail" value="1" <?php echo (isset($settings['bmp_withdrawalInitiate_mail'])==1)?'checked':'';?>> <?php echo __('Enabled this Mail functionality','bmp');?>
		</td>
		  </tr>
	     </tbody>
        </table>
		</div>
	</div>	
</div>

 <div class="form-group ">
<div class="col-md-12 float-left">
	 <div class="form-group ">
	    <table class="form-table">
        <tbody>
         <tr>
         	<h5 for="bmp_withdrawalProcess_email" class="thick"  data-toggle="tooltip" title="" data-original-title="!"><?php _e('Withdrawal Processed Mail','bmp');?> </h5>

         	<th scope="row"><label for="bmp_withdrawalProcess_email_subject"><?php _e('Subject','bmp');?>  </label></th>

         	  <td><input name="bmp_withdrawalProcess_email_subject" id="bmp_withdrawalProcess_email_subject" type="text" style="" value="<?php echo $settings['bmp_withdrawalProcess_email_subject'];?>" required class="regular-text" placeholder="<?php _e('Subject','bmp');?>"></td>
        
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
         	<th scope="row"><label  for="bmp_withdrawalProcess_email_message"><?php _e('Message','bmp');?> </label></th>
         	   <td>
         	  

			<textarea type="text" name="bmp_withdrawalProcess_email_message" rows="6" id="bmp_withdrawalProcess_email_message" class="form-control textareawidth"  placeholder="<?php _e('Message','bmp');?>" required><?php echo $settings['bmp_withdrawalProcess_email_message'];?>
			</textarea>

			<input type="checkbox" name="bmp_withdrawalProcess_mail" value="1" <?php echo ($settings['bmp_withdrawalProcess_mail']==1)?'checked':'';?>> <?php _e('Enabled this Mail functionality','bmp');?>

		</td>
		  </tr>
	     </tbody>
        </table>
		</div>
	</div>
	</div>	

</div>