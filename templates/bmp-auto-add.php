 <?php
    if (!defined('ABSPATH')) {
        exit;
    }
    get_header();
    global $wpdb;
    $epins = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_epins WHERE status=0");
    $bmp_users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_users");

    ?>
 <div class="container">
     <form>
         <input type="text" name="number" id="number" placeholder="Number">

         <select name="sponsor" id="sponsor" placeholder="sponsor">
             <?php foreach ($bmp_users as $bmp_user) { ?>
                 <option value="<?php echo $bmp_user->user_id; ?>"><?php echo $bmp_user->user_name; ?></option>
             <?php } ?>
         </select>
         <select name="epin" id="epin" placeholder="Epin">
             <?php foreach ($epins as $epin) { ?>
                 <option value="<?php echo $epin->epin_no; ?>"><?php echo $epin->epin_no; ?></option>
             <?php } ?>
         </select>
         <select name="position" id="position">
             <option value="left">left</option>
             <option value="right">right</option>
         </select>
         <input type="submit" id="auto_add_bmp_user_fill" value="Add">
     </form>
 </div>
 <?php
    get_footer();
    ?>