<?php
if (!defined('ABSPATH')) {
    exit;
}

function bmp_epinGenarate($pin_length = '', $no_of_epin = '', $epin_name = '')
{
    global $wpdb;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $epins = [];
    for ($i = 0; $i < $no_of_epin; $i++) {
        do {
            $randomString = '';
            for ($j = 0; $j < $pin_length; $j++) {
                $index = rand(0, strlen($characters) - 1);
                $randomString .= $characters[$index];
            }
            $has_epin = $wpdb->get_var("SELECT Count(*) from {$wpdb->prefix}bmp_epins WHERE epin_name='" . $epin_name . "' AND epin_no='" . $randomString . "'");
        } while ($has_epin > 0);

        if (!array_search($randomString, $epins)) {
            $epins[] = $randomString;
        }
    }

    return $epins;
}

function bmp_run_payout_functions()
{

    global $wpdb;
    $results = bmp_run_payout_display_functions();

    if ($wpdb->num_rows > 0) {
        if ($results) {
            foreach ($results as $row) {
                /***********************************************************
                INSERT INTO PAYOUT TABLE
                 ***********************************************************/
                $sql_payout = "INSERT INTO {$wpdb->prefix}bmp_payout(user_id, date, commission_amount,referral_commission_amount,bonus_amount,total_amount,capped_amount,cap_limit,tax, service_charge) VALUES ('" . $row['user_id'] . "', '" . date('Y-m-d H:i:s') . "', '" . $row['commission_amount'] . "','" . $row['direct_refferal_commission'] . "','0','" . $row['net_amount'] . "', '" . $row['net_amount'] . "', '" . $row['cap_limit'] . "',  '" . $row['tax'] . "', '" . $row['service_charge'] . "')";



                $wpdb->query($sql_payout);

                $payout_id = $wpdb->insert_id;

                $wpdb->query("UPDATE {$wpdb->prefix}bmp_referral_commission set payout_id='$payout_id' where sponsor_id='" . $row['user_id'] . "' AND payout_id=0");

                $bmp_manage_email = get_option('bmp_manage_email');

                if (isset($bmp_manage_email['bmp_payout_mail']) && !empty($bmp_manage_email['bmp_payout_mail']) && $bmp_manage_email['bmp_payout_mail'] == 1) {
                    bmp_payout_generated_mail($row['user_id'], $row['net_amount'], $payout_id);
                }
            }
        }
    }
    //return "Payout Run Successfully";
}


function bmp_run_payout_display_functions()
{
    global $wpdb;
    $displayDataArray = [];
    $sql = "SELECT user_id FROM {$wpdb->prefix}bmp_users 
            WHERE user_id IN(SELECT sponsor_id AS user_id FROM {$wpdb->prefix}bmp_referral_commission 
            WHERE payout_id =0 
            UNION 
            SELECT sponsor_id AS user_id FROM {$wpdb->prefix}bmp_referral_commission   
            WHERE payout_id =0)";

    $results = $wpdb->get_results($sql);

    if ($wpdb->num_rows > 0) {
        $i = 0;
        foreach ($results as $key => $row) {
            $userId = $row->user_id;
            $directReffComm = bmp_getReferralCommissionById($row->user_id);

            $totalamount = $directReffComm;

            $bmp_manage_payout = get_option('bmp_manage_payout');
            $tax = $bmp_manage_payout['bmp_tds'];

            $tax_type = $bmp_manage_payout['bmp_tds_type'];
            $service_charge = $bmp_manage_payout['bmp_service_charge_amount'];
            $capLimitAmt = !empty($bmp_manage_payout['bmp_cap_limit_amount']) ? $bmp_manage_payout['bmp_cap_limit_amount'] : '';

            if ($totalamount <= $capLimitAmt) {
                $total = $totalamount;
            } else {
                $total = empty($capLimitAmt) ? $totalamount : ($capLimitAmt == '0.00' ? $totalamount : $capLimitAmt);
            }
            if (!empty($totalamount)) {
                $commission_amount = $totalamount;
                $taxamount = round(($total) * $tax / 100, 2);
                if ($bmp_manage_payout['bmp_service_charge_type'] == 'fixed')
                    $service_charge = $service_charge;
                if ($bmp_manage_payout['bmp_service_charge_type'] == 'percentage')
                    $service_charge = round(($total) * $service_charge / 100, 2);
                $user_info = get_userdata($row->user_id);
                $displayDataArray[$key]['user_id'] = $userId;
                $displayDataArray[$key]['username'] = $user_info->user_login;
                $displayDataArray[$key]['first_name'] = $user_info->first_name == "" ? $user_info->user_login : $user_info->first_name;
                $displayDataArray[$key]['last_name'] = $user_info->last_name == "" ? $user_info->user_login : $user_info->last_name;
                $displayDataArray[$key]['direct_refferal_commission'] = $directReffComm;
                $displayDataArray[$key]['total_amount'] = $totalamount;
                $displayDataArray[$key]['cap_limit'] = $capLimitAmt;
                $displayDataArray[$key]['commission_amount'] = $commission_amount;
                $displayDataArray[$key]['tax'] = $taxamount;
                $displayDataArray[$key]['service_charge'] = $service_charge == "" ? 0.00 : $service_charge;
                $displayDataArray[$key]['net_amount'] = ($total - $service_charge - $taxamount);
                $i++;
            }
        }
    } else {
        $displayDataArray = "";
    }


    return $displayDataArray;
}

function bmp_eligibility_check_for_commission($user_key)
{

    global $wpdb;
    //get the eligibility for commission and bonus
    $bmp_manage_eligibility = get_option('bmp_manage_eligibility');

    $leftusers = 0;
    $rightusers = 0;
    $total_referral = 0;

    $sql = "SELECT user_key FROM {$wpdb->prefix}bmp_users WHERE  payment_status = '1' AND sponsor_key = '" . $user_key . "'";

    $results = $wpdb->get_results($sql);
    //echo '<pre>'; print_r($results);die;
    $num = $wpdb->num_rows;

    if ($num) {
        foreach ($results as $result) {
            $left_active = $wpdb->get_var("SELECT COUNT(*) AS left_active FROM {$wpdb->prefix}bmp_leftposition WHERE user_key = '" . $result->user_key . "' AND parent_key = '" . $user_key . "'");


            if ($left_active >= 1) {
                $leftusers++;
            }

            $right_active = $wpdb->get_var("SELECT COUNT(*) AS right_active FROM {$wpdb->prefix}bmp_rightposition WHERE user_key = '" . $result->user_key . "' AND parent_key = '" . $user_key . "'");


            if ($right_active >= 1) {
                $rightusers++;
            }
        } //end foreach loop


        //total direct referral including left and right
        $total_referral = $leftusers + $rightusers;
    } else {
        $leftusers = $wpdb->get_var("SELECT COUNT(*) AS left_active FROM {$wpdb->prefix}bmp_leftposition WHERE parent_key = '$user_key' AND commission_status='0'");

        $rightusers = $wpdb->get_var("SELECT COUNT(*) AS ractive FROM {$wpdb->prefix}bmp_rightposition WHERE parent_key = '$user_key' AND commission_status='0'");

        $total_referral = 0;
    }

    if (
        $leftusers >= $bmp_manage_eligibility['bmp_referral_left'] &&
        $rightusers >= $bmp_manage_eligibility['bmp_referral_right'] &&
        $total_referral >= $bmp_manage_eligibility['bmp_referral']
    ) {
        return true;
    } else {
        return false;
    }
}



function bmp_distribute_calculate_commission($user_key)
{

    global $wpdb;
    $returnarray = array();

    $bmp_manage_payout = get_option('bmp_manage_payout');

    $pair1 = $bmp_manage_payout['bmp_pair1'];
    $pair2 = $bmp_manage_payout['bmp_pair2'];


    $leftquery = $wpdb->get_results("SELECT  `lp`.`user_key` FROM {$wpdb->prefix}bmp_leftposition as lp join {$wpdb->prefix}bmp_users as u on `u`.`user_key`=`lp`.`user_key` Where `lp`.`parent_key` = '" . $user_key . "' AND u.sponsor_key='" . $user_key . "'  AND lp.commission_status = '0' AND u.payment_status = '1' ORDER BY u.id LIMIT $pair1");

    $left_position_no = $wpdb->num_rows;

    if ($left_position_no >= $pair1) {


        $rightquery = $wpdb->get_results("SELECT  `rp`.`user_key` FROM {$wpdb->prefix}bmp_rightposition as rp join {$wpdb->prefix}bmp_users as u on `u`.`user_key`=`rp`.`user_key` Where `rp`.`parent_key` = '" . $user_key . "' AND u.sponsor_key='" . $user_key . "' AND rp.commission_status = '0' AND u.payment_status = '1' ORDER BY u.id LIMIT $pair2");

        $right_position_no = $wpdb->num_rows;

        if ($right_position_no >= $pair2) {

            $returnarray[] = bmp_insert_pair_commission($leftquery, $rightquery, $user_key);
        }
    }

    //check users from right leg tabl

    $rightquery = $wpdb->get_results("SELECT  rp.user_key FROM {$wpdb->prefix}bmp_rightposition as rp join {$wpdb->prefix}bmp_users as u on u.user_key=rp.user_key Where rp.parent_key = '" . $user_key . "' AND rp.commission_status = '0' AND u.sponsor_key='" . $user_key . "' AND u.payment_status = '1' ORDER BY u.id LIMIT $pair1");

    $right_position_no = $wpdb->num_rows;

    if ($right_position_no >= $pair1) {
        //check users from left leg table

        $leftquery = $wpdb->get_results("SELECT  lp.user_key FROM {$wpdb->prefix}bmp_leftposition as lp join {$wpdb->prefix}bmp_users as u on u.user_key=lp.user_key Where lp.parent_key = '" . $user_key . "' AND lp.commission_status = '0' AND u.sponsor_key='" . $user_key . "' AND u.payment_status = '1' ORDER BY u.id LIMIT $pair2");
        $left_position_no = $wpdb->num_rows;

        if ($left_position_no >= $pair2) {
            //mark users as paid and update commission table with child ids
            $returnarray[] = bmp_insert_pair_commission($leftquery, $rightquery, $user_key);
        }
    }
    return $returnarray;
}

function bmp_getPair($leftcount, $rightcount)
{
    $bmp_manage_payout = get_option('bmp_manage_payout');

    $pair1 = $bmp_manage_payout['bmp_pair1'];
    $pair2 = $bmp_manage_payout['bmp_pair2'];

    $leftpair = (int)($leftcount / $pair1);
    $rightpair = (int)($rightcount / $pair2);

    if ($leftpair <= $rightpair)
        $pair = $leftpair;
    else
        $pair = $rightpair;

    $leftbalance = $leftcount - ($pair * $pair1);
    $rightbalance = $rightcount - ($pair * $pair2);

    $returnarray['leftbal'] = $leftbalance;
    $returnarray['rightbal'] = $rightbalance;
    $returnarray['pair'] = $pair;

    return $returnarray;
}


function bmp_getReferralCommissionById($user_id)
{
    global $wpdb;
    $sql = "SELECT SUM(amount) AS reff_comm FROM {$wpdb->prefix}bmp_referral_commission WHERE   sponsor_id ='$user_id' AND   payout_id=0 GROUP BY sponsor_id";
    $refferal_comm = $wpdb->get_var($sql);

    return $refferal_comm;
}


function bmp_getUserIdByUsername($username)
{
    global $wpdb;
    $id = $wpdb->get_var("SELECT user_id  FROM {$wpdb->prefix}bmp_users  WHERE user_name = '$username' ");
    return $id;
}

function bmp_getuseridbykey($key)
{
    global $wpdb;
    $id = $wpdb->get_var("SELECT user_id FROM {$wpdb->prefix}bmp_users WHERE `user_key` = '" . $key . "'");
    return $id;
}


function bmp_getUsernameByUserId($user_id)
{
    global $wpdb;
    $sql = "SELECT user_name FROM {$wpdb->prefix}bmp_users WHERE user_id = '" . $user_id . "'";
    $username = $wpdb->get_var($sql);
    return $username;
}

function bmp_getUserInfoByKey($key)
{
    global $wpdb;

    $sql = "SELECT * FROM {$wpdb->prefix}bmp_users WHERE user_key = '" . $key . "'";
    $user = $wpdb->get_row($sql);

    return $user;
}

function bmp_getUsername($key)
{
    global $wpdb;

    $sql = "SELECT user_name FROM {$wpdb->prefix}bmp_users WHERE user_key = '" . $key . "'";

    $username = $wpdb->get_var($sql);
    return $username;
}

function bmp_checkKey($key)
{
    global $wpdb;
    $user_key = $wpdb->get_var("SELECT user_key FROM {$wpdb->prefix}bmp_users WHERE `user_key` = '" . $key . "'");

    //echo '<pre>'; print_r($user_key);die;
    if (!$user_key) {
        return false;
    } else {
        return true;
    }
}


function bmp_get_current_user_key()
{
    global $current_user, $wpdb;
    $username = $current_user->user_login;
    $user_key = $wpdb->get_var("SELECT user_key FROM {$wpdb->prefix}bmp_users WHERE user_name = '" . $username . "'");
    return $user_key;
}

function bmp_get_user_key($user_id)
{
    global $wpdb;
    $user_key = $wpdb->get_var("SELECT user_key FROM {$wpdb->prefix}bmp_users WHERE user_id='$user_id'");
    return $user_key;
}

function bmp_getproducprice($user_id)
{
    global $wpdb;
    $product_price = $wpdb->get_var("SELECT product_price FROM {$wpdb->prefix}bmp_users WHERE user_id='$user_id'");
    return $product_price;
}

function bmp_get_epin_price($user_key = '')
{
    global $wpdb;
    $epin_price = $wpdb->get_var("SELECT epin_price FROM {$wpdb->prefix}bmp_epins WHERE user_key='" . $user_key . "'");
    return $epin_price;
}

function bmp_get_parent_key_by_userid($user_id)
{
    global $wpdb;
    $parent_key = $wpdb->get_var("SELECT parent_key FROM {$wpdb->prefix}bmp_users WHERE user_id='$user_id'");
    return $parent_key;
}

function bmp_get_sponsor_key_by_userid($user_id)
{
    global $wpdb;
    $sponsor_key = $wpdb->get_var("SELECT sponsor_key FROM {$wpdb->prefix}bmp_users WHERE user_id='$user_id'");
    return $sponsor_key;
}


function bmp_insert_refferal_commision($user_id = '')
{
    global $wpdb;
    $date = current_time('mysql');
    $user_key = bmp_get_user_key($user_id);
    $bmp_manage_payout_setting = get_option('bmp_manage_payout');
    $refferal_amount = $bmp_manage_payout_setting['bmp_referral_commission_amount'];
    $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bmp_users WHERE user_id=$user_id");
    $sponsor_key = $row->sponsor_key;
    $child_id = $row->user_id;
    if ($bmp_manage_payout_setting['bmp_referral_commission_type'] == 'percentage') {
        $refferal_amount = bmp_get_epin_price($sponsor_key) * $refferal_amount / 100;
    }

    if ($sponsor_key != 0) {
        $sponsor = $wpdb->get_row("SELECT user_id FROM {$wpdb->prefix}bmp_users WHERE user_key='" . $sponsor_key . "'");
        $sponsor_user_id = $sponsor->user_id;
        $sql = "INSERT INTO {$wpdb->prefix}bmp_referral_commission SET date_notified ='$date',sponsor_id='$sponsor_user_id',child_id='$child_id',amount='$refferal_amount',payout_id='0'";
        $wpdb->query($sql);
    }
}

function bmp_admin_reset_data_function()
{
    global $wpdb;
    $tables = array(
        "{$wpdb->prefix}bmp_users",
        "{$wpdb->prefix}bmp_leftposition",
        "{$wpdb->prefix}bmp_rightposition",
        "{$wpdb->prefix}bmp_payout",
        "{$wpdb->prefix}bmp_referral_commission",
        "{$wpdb->prefix}bmp_epins",
    );


    foreach ($tables as $table) {
        $wpdb->query("TRUNCATE {$table}");
    }

    //$wpdb->query( "TRUNCATE FROM $wpdb->options WHERE option_name LIKE '%bmp_%';" );

    // Delete users & usermeta.

    //$wp_roles = new WP_Roles();
    //$wp_roles->remove_role("bmp_user");
    return true;
}
function bmp_epin_exist($epin)
{
    global $wpdb;
    $epin_row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bmp_epins WHERE epin_no='" . $epin . "' AND status='0'");

    if (!empty($epin_row)) {
        return false;
    } else {
        return true;
    }
}

function bmp_generateKey()
{
    global $wpdb;
    $characters = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
    $length = 9;
    do {
        $keys = array();
        while (count($keys) < $length) {
            $x = mt_rand(0, count($characters) - 1);
            if (!in_array($x, $keys))
                $keys[] = $x;
        }

        // extract each key from array
        $random_chars = '';
        foreach ($keys as $key)
            $random_chars .= $characters[$key];

        // display random key
        $haskey = $wpdb->get_var("SELECT count(*) from {$wpdb->prefix}bmp_users where user_key='" . $random_chars . "'");
    } while ($haskey > 0);

    return $random_chars;
}



function bmp_get_page_id($page)
{
    $page = apply_filters('bmp_get_' . $page . '_page_id', get_option('bmp_' . $page . '_page_id'));
    return $page ? absint($page) : -1;
}

function bmp_create_page($slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0)
{
    global $wpdb;

    $option_value = get_option($option);

    if ($option_value > 0 && ($page_object = get_post($option_value))) {
        if ('page' === $page_object->post_type && !in_array($page_object->post_status, array('pending', 'trash', 'future', 'auto-draft'))) {
            // Valid page is already in place

            return $page_object->ID;
        }
    }

    if (strlen($page_content) > 0) {
        // Search for an existing page with the specified page content (typically a shortcode)
        $valid_page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%"));
    } else {
        // Search for an existing page with the specified page slug
        $valid_page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug));
    }

    $valid_page_found = apply_filters('bmp_create_page_id', $valid_page_found, $slug, $page_content);

    if ($valid_page_found) {
        if ($option) {
            update_option($option, $valid_page_found);
        }

        return $valid_page_found;
    }


    // Search for a matching valid trashed page
    if (strlen($page_content) > 0) {
        // Search for an existing page with the specified page content (typically a shortcode)
        $trashed_page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%"));
    } else {
        // Search for an existing page with the specified page slug
        $trashed_page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug));
    }

    if ($trashed_page_found) {
        $page_id   = $trashed_page_found;
        $page_data = array(
            'ID'          => $page_id,
            'post_status' => 'publish',
        );
        wp_update_post($page_data);
    } else {
        $page_data = array(
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_author'    => 1,
            'post_name'      => $slug,
            'post_title'     => $page_title,
            'post_content'   => $page_content,
            'post_parent'    => $post_parent,
            'comment_status' => 'closed',
        );

        $page_id   = wp_insert_post($page_data);
        update_post_meta($page_id, 'is_bmp_page', true);
    }

    if ($option) {
        update_option($option, $page_id);
    }

    return $page_id;
}

// mail functions 

function bmp_payout_generated_mail($user_id, $amount, $payout_id)
{
    global $wpdb;

    $user_info = get_userdata($user_id);
    $siteownwer = get_bloginfo('name');
    $bmp_manage_email = get_option('bmp_manage_email');
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
    $headers .= "From: " . get_option('admin_email') . "<" . get_option('admin_email') . ">" . "\r\n";

    $subject = $bmp_manage_email['bmp_runpayout_email_subject'];
    $message = nl2br(htmlspecialchars($bmp_manage_email['bmp_runpayout_email_message']));
    $message = str_replace('[firstname]', $user_info->first_name, $message);
    $message = str_replace('[lastname]', $user_info->last_name, $message);
    $message = str_replace('[email]', $user_info->user_email, $message);
    $message = str_replace('[username]', $user_info->user_login, $message);
    $message = str_replace('[amount]', $amount, $message);
    $message = str_replace('[payoutid]', $payout_id, $message);
    $message = str_replace('[sitename]', $siteownwer, $message);
    wp_mail(get_option('admin_email'), $subject, $message, $headers);
    wp_mail($user_info->user_email, $subject, $message, $headers);
}

// If apply for with drawal From Front End

function bmp_withdrawal_initiated_mail($user_id, $comment, $payout_id)
{
    global $wpdb;

    $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bmp_payout WHERE `payout_id` = '$payout_id' AND user_id='$user_id'");

    $user_info = get_userdata($user_id);

    $siteownwer = get_bloginfo('name');
    $bmp_manage_email = get_option('bmp_manage_email');

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
    $headers .= "From: " . get_option('admin_email') . "<" . get_option('admin_email') . ">" . "\r\n";
    $subject = $bmp_manage_email['bmp_withdrawalInitiate_email_subject'];
    $message = nl2br(htmlspecialchars($bmp_manage_email['bmp_withdrawalInitiate_email_message']));
    $message = str_replace('[firstname]', $user_info->first_name, $message);
    $message = str_replace('[lastname]', $user_info->last_name, $message);
    $message = str_replace('[email]', $user_info->user_email, $message);
    $message = str_replace('[username]', $user_info->user_login, $message);
    $message = str_replace('[amount]', $row->capped_amt, $message);
    $message = str_replace('[mode]', $row->payment_mode, $message);
    $message = str_replace('[comment]', $comment, $message);
    $message = str_replace('[payoutid]', $payout_id, $message);
    $message = str_replace('[sitename]', $siteownwer, $message);
    wp_mail(get_option('admin_email'), $subject, $message, $headers);
    wp_mail($user_info->user_email, $subject, $message, $headers);
}


// mail functions 

function bmp_base_name_information()
{
    echo '<meta name="bmp_adminajax" content="' . admin_url('admin-ajax.php') . '" />';
    echo '<meta name="bmp_base_url" content="' . site_url() . '" />';
    echo '<meta name="bmp_author_url" content="https://www.letscms.com" />';
}


function bmp_add_query_vars($aVars)
{
    $aVars[] = "key";
    $aVars[] = "parent_key";
    $aVars[] = "position";
    return $aVars;
}


function bmp_add_rewrite_rules($aRules)
{
    //$downlines = array('/downlines/([^/]+)/?$' => 'index.php?pagename=downlines&key=$matches[1]');
    //$register = array('/register/([^/]+)/([^/]+)/?$' => 'index.php?pagename=register&parent_key=$matches[1]&position=$matches[2]');
    //$aRules = $downlines + $register + $aRules;
    $newrules = array();
    $newrules['/downlines/([^/]+)/?$'] = 'index.php?pagename=downlines&key=$matches[1]';
    $newrules['/register/([^/]+)/([^/]+)/?$'] = 'index.php?pagename=register&parent_key=$matches[1]&position=$matches[2]';

    //return $aRules;

    $finalrules = $newrules + $aRules;
    return $finalrules;
}



function bmp_user_referral_commission($user_id)
{
    global $wpdb;
    $referral_commission = $wpdb->get_var("SELECT SUM(amount) as total FROM {$wpdb->prefix}bmp_referral_commission WHERE sponsor_id=$user_id and payout_id!=0");
    return ($referral_commission > 0) ? $referral_commission : '0';
}



function bmp_referral_by_commission_payout($payout_id, $user_id)
{
    global $wpdb;
    if ($user_id) {
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_referral_commission where payout_id=" . $payout_id . " AND sponsor_id=" . $user_id);
    } else {
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_referral_commission where payout_id=" . $payout_id);
    }
    return $results;
}



function bmp_payout_summary_by_amount_payout($payout_id)
{
    global $wpdb;
    $results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bmp_payout where id=" . $payout_id);


    return $results;
}

function bmp_user_personal_detail_by_userid($user_id)
{
    global $wpdb;
    $results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bmp_users where user_id=" . $user_id);

    return $results;
}


function bmp_user_personal_detail_by_leftuser($user_key)
{
    global $wpdb;

    $results = $wpdb->get_results("SELECT  * FROM {$wpdb->prefix}bmp_leftposition as lp join {$wpdb->prefix}bmp_users as u on `u`.`user_key`=`lp`.`user_key` Where `lp`.`parent_key` = '" . $user_key . "' AND u.sponsor_key='" . $user_key . "'");



    return $results;
}

function bmp_user_personal_detail_by_rightuser($user_key)
{
    global $wpdb;

    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_rightposition as lp join {$wpdb->prefix}bmp_users as u on `u`.`user_key`=`lp`.`user_key` Where `lp`.`parent_key` = '" . $user_key . "' AND u.sponsor_key='" . $user_key . "'");


    return $results;
}

function bmp_user_payoutdetail($user_id)
{
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_payout where  user_id=" . $user_id . "");
    return $results;
}
function bmp_get_sum_commissionamount($user_id)
{
    global $wpdb;
    $results = $wpdb->get_var("SELECT sum(commission_amount) FROM {$wpdb->prefix}bmp_payout where  user_id=" . $user_id . "");
    $results = number_format($results, 2);
    return $results;
}
function bmp_get_sum_referral_commission_amount($user_id)
{
    global $wpdb;
    $results = $wpdb->get_var("SELECT sum(referral_commission_amount) FROM {$wpdb->prefix}bmp_payout where  user_id=" . $user_id . "");
    $results = number_format($results, 2);
    return $results;
}

function bmp_get_sum_bonus_amount($user_id)
{
    global $wpdb;
    $results = $wpdb->get_var("SELECT sum(bonus_amount) FROM {$wpdb->prefix}bmp_payout where  user_id=" . $user_id . "");
    $results = number_format($results, 2);
    return $results;
}


function bmp_get_sum_total_amount($user_id)
{
    global $wpdb;
    $results = $wpdb->get_var("SELECT sum(total_amount) FROM {$wpdb->prefix}bmp_payout where  user_id=" . $user_id . "");
    $results = number_format($results, 2);
    return $results;
}

function bmp_get_sum_capped_amount($user_id)
{
    global $wpdb;
    $results = $wpdb->get_var("SELECT sum(capped_amount) FROM {$wpdb->prefix}bmp_payout where  user_id=" . $user_id . "");
    $results = number_format($results, 2);

    return $results;
}

function bmp_pair_referral_by_commission_user_id_and_payout_id($payout_id, $user_id)
{
    global $wpdb;

    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_referral_commission where payout_id=" . $payout_id . " AND sponsor_id=" . $user_id);

    return $results;
}



function bmp_pair_summary_by_user_id_and_payout_id($payout_id, $user_id)
{
    global $wpdb;

    $results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bmp_payout where id=" . $payout_id . " and user_id=" . $user_id);



    //echo '<pre>'; print_r($results);die;

    return $results;
}


function bmp_payout_list_of_current_user()
{
    global $wpdb, $current_user;

    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_payout WHERE user_id=$current_user->ID");
    return $results;
}
function bmp_epin_of_current_user()
{
    global $wpdb, $current_user;
    $user_key = $wpdb->get_var("SELECT `user_key` FROM {$wpdb->prefix}bmp_users WHERE user_id=$current_user->ID");
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_epins WHERE user_key=$user_key");
    return $results;
}

function bmp_left_user_count_by_user_key($user_key)
{
    global $wpdb, $current_user;
    $total = $wpdb->get_var("SELECT  count(`lp`.`user_key`) as total FROM {$wpdb->prefix}bmp_leftposition as lp join {$wpdb->prefix}bmp_users as u on `u`.`user_key`=`lp`.`user_key` Where `lp`.`parent_key` = '" . $user_key . "' AND u.sponsor_key='" . $user_key . "'");
    return $total;
}

function bmp_right_user_count_by_user_key($user_key)
{
    global $wpdb, $current_user;
    $total = $wpdb->get_var("SELECT  count(`lp`.`user_key`) as total FROM {$wpdb->prefix}bmp_rightposition as lp join {$wpdb->prefix}bmp_users as u on `u`.`user_key`=`lp`.`user_key` Where `lp`.`parent_key` = '" . $user_key . "' AND u.sponsor_key='" . $user_key . "'");
    return $total;
}

function bmp_user_account_detail_of_current_user()
{
    global $wpdb, $current_user;
    $bmp_user_data = array();
    $bmp_user = bmp_user_personal_detail_by_userid($current_user->ID);
    $bmp_user_data['user_name'] = $bmp_user->user_name;
    $bmp_user_data['user_key'] = $bmp_user->user_key;
    $bmp_user_data['parent_key'] = $bmp_user->parent_key;
    $bmp_user_data['sponsor_key'] = $bmp_user->sponsor_key;
    $bmp_user_data['position'] = $bmp_user->position;
    $bmp_user_data['payment_status'] = $bmp_user->payment_status;
    $bmp_user_data['left_count'] = bmp_left_user_count_by_user_key($bmp_user->user_key);
    $bmp_user_data['right_count'] = bmp_right_user_count_by_user_key($bmp_user->user_key);
    return $bmp_user_data;
}




function bmp_user_left_downlines_of_current_user()
{
    global $wpdb, $current_user;
    $user_data = array();
    $bmp_user = bmp_user_personal_detail_by_userid($current_user->ID);
    $results = $wpdb->get_results("SELECT  `lp`.`user_key` FROM {$wpdb->prefix}bmp_leftposition as lp join {$wpdb->prefix}bmp_users as u on `u`.`user_key`=`lp`.`user_key` Where `lp`.`parent_key` = '" . $bmp_user->user_key . "' AND u.sponsor_key='" . $bmp_user->user_key . "'");

    foreach ($results as $result) {
        $user_data[] = bmp_getUserInfoByKey($result->user_key);
    }
    return $user_data;
}


function bmp_user_right_downlines_of_current_user()
{

    global $wpdb, $current_user;
    $user_data = array();
    $bmp_user = bmp_user_personal_detail_by_userid($current_user->ID);
    $results = $wpdb->get_results("SELECT  `lp`.`user_key` FROM {$wpdb->prefix}bmp_rightposition as lp join {$wpdb->prefix}bmp_users as u on `u`.`user_key`=`lp`.`user_key` Where `lp`.`parent_key` = '" . $bmp_user->user_key . "' AND u.sponsor_key='" . $bmp_user->user_key . "'");

    foreach ($results as $result) {
        $user_data[] = bmp_getUserInfoByKey($result->user_key);
    }
    return $user_data;
}

function bmp_user_left_downlines_by_key($key)
{
    global $wpdb, $current_user;
    $results = $wpdb->get_var("SELECT  count(`lp`.`user_key`) as total FROM {$wpdb->prefix}bmp_leftposition as lp join {$wpdb->prefix}bmp_users as u on `u`.`user_key`=`lp`.`user_key` Where `lp`.`parent_key` = '" . $key . "' AND u.sponsor_key='" . $key . "'");
    return $results;
}


function bmp_user_right_downlines_by_key($key)
{
    global $wpdb, $current_user;
    $results = $wpdb->get_var("SELECT  count(`lp`.`user_key`) as total FROM {$wpdb->prefix}bmp_rightposition as lp join {$wpdb->prefix}bmp_users as u on `u`.`user_key`=`lp`.`user_key` Where `lp`.`parent_key` = '" . $key . "' AND u.sponsor_key='" . $key . "'");
    return $results;
}

function bmp_user_my_total_earnings()
{
    global $wpdb, $current_user;
    $user_data = array();
    $commission_amount = 0;
    $referral_commission_amount = 0;
    $bonus_amount = 0;
    $total_amount = 0;
    $capped_amount = 0;
    $cap_limit = 0;
    $tax = 0;
    $service_charge = 0;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_payout WHERE user_id=$current_user->ID");
    foreach ($results as $result) {
        $commission_amount += $result->commission_amount;
        $referral_commission_amount += $result->referral_commission_amount;
        $bonus_amount += $result->bonus_amount;
        $total_amount += $result->total_amount;
        $capped_amount += $result->capped_amount;
        if (!empty($result->cap_limit)) {

            $cap_limit += $result->cap_limit;
        }
        $tax += $result->tax;
        $service_charge += $result->service_charge;
    }

    $user_data['commission_amount'] = $commission_amount;
    $user_data['referral_commission_amount'] = $referral_commission_amount;
    $user_data['bonus_amount'] = $bonus_amount;
    $user_data['total_amount'] = $total_amount;
    $user_data['capped_amount'] = $capped_amount;
    $user_data['cap_limit'] = $cap_limit;
    $user_data['tax'] = $tax;
    $user_data['service_charge'] = $service_charge;
    return $user_data;
}

function bmp_user_referral_commission_data($payout_id)
{
    global $wpdb, $current_user;

    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_referral_commission WHERE sponsor_id=$current_user->ID AND payout_id=$payout_id");

    return $results;
}
function bmp_user_payout_detail_of_current_user($payout_id = "")
{
    global $wpdb, $current_user;

    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_payout WHERE id='" . $payout_id . "'");

    return $results;
}

function bmp_user_payout_summary_data($payout_id)
{
    global $wpdb, $current_user;

    $results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bmp_payout WHERE user_id=$current_user->ID AND id=$payout_id");

    return $results;
}

function bmp_user_check_validate_function()
{
    global $wpdb, $current_user;
    $user = wp_get_current_user();
    $roles = (array) $user->roles;

    if (!is_user_logged_in()) {
        echo '<div class="container"><div class="user_error">' . __('You are not the Binary Mlm Plan Member. So you are not eligible to access this page.', 'bmp');
        echo  '</div></div>';
        die;
    } else if (!in_array('bmp_user', $roles)) {
        echo '<div class="container"><div class="user_error">' . __('You are not the Binary Mlm Plan Member. So you are not eligible to access this page.', 'bmp');
        echo  '</div></div>';
        die;
    }
}
function bmp_user_check_payout_function()
{
    global $wpdb, $current_user;
    if (!empty($_GET['id'])) {
        $id = sanitize_text_field($_GET['id']);
    } else {
        $id = 0;
    }
    $var_payout = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}bmp_payout WHERE id='" . $id . "' AND user_id=$current_user->ID");
    if ($var_payout) {
    } else {
        echo '<div class="container"><div class="user_error">' . __('Data not found.', 'bmp');
        echo  '</div></div>';
        die;
    }
}

function bmp_admin_user_account_detail_of_current_user($user_id)
{
    global $wpdb, $current_user;
    $bmp_user_data = array();
    $bmp_user = bmp_user_personal_detail_by_userid($user_id);
    $bmp_user_data['user_name'] = $bmp_user->user_name;
    $bmp_user_data['user_key'] = $bmp_user->user_key;
    $bmp_user_data['parent_key'] = $bmp_user->parent_key;
    $bmp_user_data['sponsor_key'] = $bmp_user->sponsor_key;
    $bmp_user_data['position'] = $bmp_user->position;
    $bmp_user_data['payment_status'] = $bmp_user->payment_status;
    $bmp_user_data['left_count'] = bmp_left_user_count_by_user_key($bmp_user->user_key);
    $bmp_user_data['right_count'] = bmp_right_user_count_by_user_key($bmp_user->user_key);

    return $bmp_user_data;
}

function bmp_admin_user_my_total_earnings($user_id)
{
    global $wpdb, $current_user;
    $user_data = array();
    $commission_amount = 0;
    $referral_commission_amount = 0;
    $bonus_amount = 0;
    $total_amount = 0;
    $capped_amount = 0;
    $cap_limit = 0;
    $tax = 0;
    $service_charge = 0;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_payout WHERE user_id=$user_id");
    foreach ($results as $result) {
        $commission_amount += $result->commission_amount;
        $referral_commission_amount += $result->referral_commission_amount;
        $bonus_amount += $result->bonus_amount;
        $total_amount += $result->total_amount;
        $capped_amount += $result->capped_amount;
        if (!empty($result->cap_limit)) {
            $cap_limit += $result->cap_limit;
        }
        $tax += $result->tax;
        $service_charge += $result->service_charge;
    }

    $user_data['commission_amount'] = $commission_amount;
    $user_data['referral_commission_amount'] = $referral_commission_amount;
    $user_data['bonus_amount'] = $bonus_amount;
    $user_data['total_amount'] = $total_amount;
    $user_data['capped_amount'] = $capped_amount;
    $user_data['cap_limit'] = $cap_limit;
    $user_data['tax'] = $tax;
    $user_data['service_charge'] = $service_charge;

    return $user_data;
}


function bmp_admin_user_left_downlines_of_current_user($user_id)
{
    global $wpdb, $current_user;
    $user_data = array();
    $bmp_user = bmp_user_personal_detail_by_userid($user_id);
    $results = $wpdb->get_results("SELECT  `lp`.`user_key` FROM {$wpdb->prefix}bmp_leftposition as lp join {$wpdb->prefix}bmp_users as u on `u`.`user_key`=`lp`.`user_key` Where `lp`.`parent_key` = '" . $bmp_user->user_key . "' AND u.sponsor_key='" . $bmp_user->user_key . "'");

    foreach ($results as $result) {
        $user_data[] = bmp_getUserInfoByKey($result->user_key);
    }
    return $user_data;
}


function bmp_admin_user_right_downlines_of_current_user($user_id)
{

    global $wpdb, $current_user;
    $user_data = array();
    $bmp_user = bmp_user_personal_detail_by_userid($user_id);
    $results = $wpdb->get_results("SELECT  `lp`.`user_key` FROM {$wpdb->prefix}bmp_rightposition as lp join {$wpdb->prefix}bmp_users as u on `u`.`user_key`=`lp`.`user_key` Where `lp`.`parent_key` = '" . $bmp_user->user_key . "' AND u.sponsor_key='" . $bmp_user->user_key . "'");

    foreach ($results as $result) {
        $user_data[] = bmp_getUserInfoByKey($result->user_key);
    }
    return $user_data;
}


function bmp_admin_payout_list_of_current_user($user_id)
{
    global $wpdb, $current_user;

    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_payout WHERE user_id=$user_id");
    return $results;
}



function bmp_mlm_deactivate_function()
{
    global $wpdb;
    $install = new BMP_Install;
    $tables = $install->get_tables();

    foreach ($tables as $table) {
        $wpdb->query("DROP TABLE IF EXISTS {$table}");
    }

    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '%bmp_%';");

    // Delete users & usermeta.

    $wp_roles = new WP_Roles();
    $wp_roles->remove_role("bmp_user");
    session_destroy();


    // pages delete
    $results = $wpdb->get_results("SELECT post_id FROM {$wpdb->prefix}postmeta where meta_key='is_bmp_page' AND meta_value='1'");
    foreach ($results as $result) {
        wp_delete_post($result->post_id);
    }

    // menu delete
    $results = $wpdb->get_results("SELECT post_id FROM {$wpdb->prefix}postmeta where meta_key='_menu_item_classes' AND meta_value LIKE '%bmp%'");
    foreach ($results as $result) {
        wp_delete_post($result->post_id);
    }
}


// admin payout list function 

function bmp_admn_user_payout_list_function()
{
    $user_id = sanitize_text_field($_GET['user_id']);

    $payouts_list = bmp_admin_payout_list_of_current_user($user_id); ?>
    <div class="container" id="bmp_user_payouts">
        <div class="row">
            <div class="col-md-12 table-p">
                <h4><?php echo __('My payouts List', 'bmp'); ?></h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo __('Payout Id', 'bmp'); ?></th>
                                <th><?php echo __('User Name', 'bmp'); ?></th>
                                <th><?php echo __('Referral Commission', 'bmp'); ?></th>
                                <th><?php echo __('Total Amount', 'bmp'); ?></th>
                                <th><?php echo __('Capped Amount', 'bmp'); ?></th>
                                <th><?php echo __('Cap Limit', 'bmp'); ?></th>
                                <th><?php echo __('Tax', 'bmp'); ?></th>
                                <th><?php echo __('Service Charge', 'bmp'); ?></th>
                                <th><?php echo __('Action', 'bmp'); ?></th>
                            </tr>
                        </thead>
                        <?php if ($payouts_list) { ?>
                            <?php foreach ($payouts_list as $payout_list) {

                            ?>
                                <tr>
                                    <td><?php echo $payout_list->id; ?></td>
                                    <td><?php echo bmp_getUsernameByUserId($payout_list->user_id); ?></td>
                                    <td><?php echo $payout_list->referral_commission_amount; ?></td>
                                    <td><?php echo $payout_list->total_amount; ?></td>
                                    <td><?php echo $payout_list->capped_amount; ?></td>
                                    <td><?php echo !empty($payout_list->cap_limit) ? $payout_list->cap_limit : 0; ?></td>
                                    <td><?php echo $payout_list->tax; ?></td>
                                    <td><?php echo $payout_list->service_charge; ?></td>
                                    <td>
                                        <a href="<?php echo admin_url() . 'admin.php?page=bmp-payout-reports&user_id=' . $payout_list->user_id . '&payout_id=' . $payout_list->id; ?>"><?php echo __('View', 'bmp'); ?></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php
}

function bmp_admin_user_downlines_list_function()
{
    $user_id = sanitize_text_field($_GET['user_id']);
    $left_downlines = bmp_admin_user_left_downlines_of_current_user($user_id);
    $right_downlines = bmp_admin_user_right_downlines_of_current_user($user_id);
?>
    <div class="container" id="bmp_user_downlines">
        <div class="row">
            <div class="col-md-6 table-b">
                <h4><?php echo __('My Left Downlines', 'bmp'); ?></h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th><?php echo __('User Name', 'bmp'); ?></th>
                            <th><?php echo __('User Key', 'bmp'); ?></th>
                            <th><?php echo __('Payment Status', 'bmp'); ?></th>
                        </tr>
                        <?php if ($left_downlines) {
                            foreach ($left_downlines as $left_downline) { ?>
                                <tr>
                                    <td><?php echo $left_downline->user_name; ?> </td>
                                    <td><?php echo $left_downline->user_key; ?></td>
                                    <td><?php echo ($left_downline->payment_status == 1) ? 'Paid' : 'UnPaid'; ?></td>
                                </tr>
                            <?php }
                        } else { ?>

                        <?php } ?>
                    </table>
                </div>
            </div>
            <div class="col-md-6 table-b">
                <h4><?php echo __('My Right Downlines', 'bmp'); ?></h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th><?php echo __('User Name', 'bmp'); ?></th>
                            <th><?php echo __('User Key', 'bmp'); ?></th>
                            <th><?php echo __('Payment Status', 'bmp'); ?></th>
                        </tr>
                        <?php if ($right_downlines) {
                            foreach ($right_downlines as $right_downline) { ?>
                                <tr>
                                    <td><?php echo $right_downline->user_name; ?> </td>
                                    <td><?php echo $right_downline->user_key; ?></td>
                                    <td><?php echo ($right_downline->payment_status == 1) ? 'Paid' : 'UnPaid'; ?></td>
                                </tr>
                            <?php }
                        } else { ?>

                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php
}

function bmp_admin_user_account_detail_function()
{
    $user_id = sanitize_text_field($_GET['user_id']);
    $account_detail = bmp_admin_user_account_detail_of_current_user($user_id);
    $my_earning = bmp_admin_user_my_total_earnings($user_id);
    // echo "<pre>";
    // print_r($my_earning);
    // die;
?>
    <div class="container" id="bmp_user_detail">
        <div class="row">
            <div class="col-md-6 table-b">
                <h4><?php echo __('My Personal Detail', 'bmp'); ?></h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th><?php echo __('User Name', 'bmp'); ?></th>
                            <td><?php echo $account_detail['user_name']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('User Key', 'bmp'); ?></th>
                            <td><?php echo $account_detail['user_key']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Parent Key', 'bmp'); ?></th>
                            <td><?php echo $account_detail['parent_key']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Sponsor Key', 'bmp'); ?></th>
                            <td><?php echo $account_detail['sponsor_key']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Position', 'bmp'); ?></th>
                            <td><?php echo $account_detail['position']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Payment Status', 'bmp'); ?></th>
                            <td><?php echo $account_detail['payment_status']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Left Position Members', 'bmp'); ?></th>
                            <td><?php echo $account_detail['left_count']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Right Position Members', 'bmp'); ?></th>
                            <td><?php echo $account_detail['right_count']; ?></td>
                        </tr>

                    </table>
                </div>
            </div>
            <div class="col-md-6 table-b mt-sm-3">
                <h4><?php echo __('My Personal Earnings', 'bmp'); ?></h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th><?php echo __('Refferal Commission', 'bmp'); ?></th>
                            <td><?php echo $my_earning['referral_commission_amount']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Capped Amount', 'bmp'); ?></th>
                            <td><?php echo $my_earning['total_amount']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Cap Limit', 'bmp'); ?></th>
                            <td><?php echo $my_earning['cap_limit']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Tax', 'bmp'); ?></th>
                            <td><?php echo $my_earning['tax']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Service Charge', 'bmp'); ?></th>
                            <td><?php echo $my_earning['service_charge']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Total Amount', 'bmp'); ?></th>
                            <td><?php echo $my_earning['total_amount']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php
}

function bmp_user_downlines_list_function()
{
    $left_downlines = bmp_user_left_downlines_of_current_user();
    $right_downlines = bmp_user_right_downlines_of_current_user(); ?>
    <div class="container mt-sm-3" id="bmp_user_downlines">
        <div class="row arrng-col-gap">
            <div class="col-md-6 table-p arrang-col">
                <h4><?php echo __('My Left Downliness', 'bmp'); ?></h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th><?php echo __('User Name', 'bmp'); ?></th>
                            <th><?php echo __('User Key', 'bmp'); ?></th>
                            <th><?php echo __('Payment Status', 'bmp'); ?></th>
                        </tr>
                        <?php if ($left_downlines) {
                            foreach ($left_downlines as $left_downline) { ?>
                                <tr>
                                    <td><?php echo $left_downline->user_name; ?> </td>
                                    <td><?php echo $left_downline->user_key; ?></td>
                                    <td><?php echo ($left_downline->payment_status == 1) ? 'Paid' : 'UnPaid'; ?></td>
                                </tr>
                            <?php }
                        } else { ?>

                        <?php } ?>
                    </table>
                </div>
            </div>
            <div class="col-md-6 table-p mt-sm-3 mt-md-0 arrang-col">
                <h4><?php echo __('My Right Downliness', 'bmp'); ?></h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th><?php echo __('User Name', 'bmp'); ?></th>
                            <th><?php echo __('User Key', 'bmp'); ?></th>
                            <th><?php echo __('Payment Status', 'bmp'); ?></th>
                        </tr>
                        <?php if ($right_downlines) {
                            foreach ($right_downlines as $right_downline) { ?>
                                <tr>
                                    <td><?php echo $right_downline->user_name; ?> </td>
                                    <td><?php echo $right_downline->user_key; ?></td>
                                    <td><?php echo ($right_downline->payment_status == 1) ? 'Paid' : 'UnPaid'; ?></td>
                                </tr>
                            <?php }
                        } else { ?>

                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php
}

function bmp_user_account_detail_function()
{
    $account_detail = bmp_user_account_detail_of_current_user();
    $my_earning = bmp_user_my_total_earnings();
?>
    <div class="container" id="bmp_user_detail">
        <div class="row arrng-col-gap">
            <div class="col-md-6 table-p arrang-col">
                <h4><?php echo __('My Personal Detail', 'bmp'); ?></h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th><?php echo __('User Name', 'bmp'); ?></th>
                            <td><?php echo $account_detail['user_name']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('User Key', 'bmp'); ?></th>
                            <td><?php echo $account_detail['user_key']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Parent Key', 'bmp'); ?></th>
                            <td><?php echo $account_detail['parent_key']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Sponsor Key', 'bmp'); ?></th>
                            <td><?php echo $account_detail['sponsor_key']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Position', 'bmp'); ?></th>
                            <td><?php echo $account_detail['position']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Payment Status', 'bmp'); ?></th>
                            <td><?php echo $account_detail['payment_status']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Left Position Members', 'bmp'); ?></th>
                            <td><?php echo $account_detail['left_count']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Right Position Members', 'bmp'); ?></th>
                            <td><?php echo $account_detail['right_count']; ?></td>
                        </tr>

                    </table>
                </div>
            </div>
            <div class="col-md-6 table-p mt-sm-3 mt-md-0 arrang-col">
                <h4><?php echo __('My Personal Earnings', 'bmp'); ?></h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tr>
                            <th><?php echo __('Refferal Commission', 'bmp'); ?></th>
                            <td><?php echo $my_earning['referral_commission_amount']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Capped Amount', 'bmp'); ?></th>
                            <td><?php echo $my_earning['capped_amount']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Cap Limit', 'bmp'); ?></th>
                            <td><?php echo $my_earning['cap_limit']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Tax', 'bmp'); ?></th>
                            <td><?php echo $my_earning['tax']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Service Charge', 'bmp'); ?></th>
                            <td><?php echo $my_earning['service_charge']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo __('Total Amount', 'bmp'); ?></th>
                            <td><?php echo $my_earning['total_amount']; ?></td>
                        </tr>

                    </table>
                </div>
            </div>
        </div>
    </div>

<?php
}
function bmp_admin_payout_detail_function()
{
    global $wpdb;
    if (!empty($_GET['payout_id'])) {
        $payout_id = sanitize_text_field($_GET['payout_id']);
    } else {
        $payout_id = 0;
    }

    if (!empty($_GET['user_id'])) {
        $user_id = sanitize_text_field($_GET['user_id']);
    } else {
        $user_id = 0;
    }


    $payout_referral = bmp_referral_by_commission_payout($payout_id, $user_id);
?>
    <div class="wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6 table-b">
                    <h4><?php echo __('Referral Commission  Detail', 'bmp'); ?></h4>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <th><?php echo __('Sponsor', 'bmp'); ?></th>
                                <th><?php echo __('Childs', 'bmp'); ?></th>
                                <th><?php echo __('Amount', 'bmp'); ?></th>
                                <th><?php echo __('Date', 'bmp'); ?></th>
                            </thead>
                            <tbody>
                                <?php if ($payout_referral) { ?>
                                    <?php foreach ($payout_referral as $payout_referrals) { ?>
                                        <tr>
                                            <td><?php echo bmp_getUsernameByUserId($payout_referrals->sponsor_id); ?></td>
                                            <td><?php echo $payout_referrals->child_id; ?></td>
                                            <td><?php echo $payout_referrals->amount; ?></td>
                                            <td><?php echo date('F j, Y', strtotime($payout_referrals->date_notified)); ?></td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="4" class="text-center"><?php echo __('There is no Referral commisison availabale.', 'bmp'); ?></td>
                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}
function bmp_admin_bonus_details_function()
{
    global $wpdb;
    if (!empty($_GET['payout_id'])) {
        $payout_id = sanitize_text_field($_GET['payout_id']);
    } else {
        $payout_id = 0;
    }

    if (!empty($_GET['user_id'])) {
        $user_id = sanitize_text_field($_GET['user_id']);
    } else {
        $user_id = 0;
    }

    $payout_summary = bmp_payout_summary_by_amount_payout($payout_id);
?>
    <div class="wrap">
        <div class="row">
            <div class="col-md-12">

                <div class="col-md-6 table-b">
                    <h4><?php echo __('Payout Summary', 'bmp'); ?></h4>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <tbody>
                                <?php if ($payout_summary) { ?>
                                    <tr class="table-active">
                                        <th scope="row"><?php echo __('Referral Commisssion Amount', 'bmp'); ?></th>
                                        <td><?php echo $payout_summary->referral_commission_amount; ?></td>
                                    </tr>

                                    <tr class="table-active">
                                        <th scope="row"><?php echo __('Cap Limit', 'bmp'); ?></th>
                                        <td><?php echo !empty($payout_summary->cap_limit) ? $payout_summary->cap_limit : 0; ?></td>
                                    </tr>
                                    <tr class="table-active">
                                        <th scope="row"><?php echo __('Service Charge Amount', 'bmp'); ?></th>
                                        <td><?php echo !empty($payout_summary->service_charge) ? $payout_summary->service_charge : 0; ?></td>
                                    </tr>
                                    <tr class="table-active">
                                        <th scope="row"><?php echo __('Tax Amount', 'bmp'); ?></th>
                                        <td><?php echo !empty($payout_summary->tax) ? $payout_summary->tax : 0; ?></td>
                                    </tr>
                                    <tr class="table-active">
                                        <th scope="row"><?php echo __('Total Amount', 'bmp'); ?></th>
                                        <td><?php echo $payout_summary->total_amount; ?></td>
                                    </tr>

                                <?php  } else { ?>
                                    <tr>
                                        <td colspan="4" class="text-center"><?php echo __('There is no availabale.', 'bmp'); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }
function bmp_user_payout_detail_function()
{
    if ($_GET['id']) {
        $payout_id = sanitize_text_field($_GET['id']);
    } else {
        $payout_id = 0;
    }

    $payout_detail = bmp_user_payout_detail_of_current_user($payout_id);
    $my_referral_data = bmp_user_referral_commission_data($payout_id);
?>
    <div class="wrap" id="bmp_user_downlines">
        <div class="row">
            <div class="col-md-12">

                <div class="col-md-6 table-p">
                    <h4><?php echo __('Commission Details', 'bmp'); ?></h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th><?php echo __('Childs', 'bmp'); ?></th>
                                <th><?php echo __('Amount', 'bmp'); ?></th>
                                <th><?php echo __('Date', 'bmp'); ?></th>
                            </tr>
                            <?php if ($payout_detail) {
                                foreach ($payout_detail as $payout_details) { ?>
                                    <tr>
                                        <td><?php echo $payout_details->child_ids; ?></td>
                                        <td><?php echo $payout_details->amount; ?></td>
                                        <td><?php echo date('F j, Y', strtotime($payout_details->date_notified)); ?></td>
                                    </tr>
                                <?php }
                            } else { ?>

                            <?php } ?>
                        </table>
                    </div>
                </div>
                <div class="col-md-6 table-p" style="margin-top: 1rem;">
                    <h4><?php echo __('Referral Commission Details', 'bmp'); ?></h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th><?php echo __('User Name', 'bmp'); ?></th>
                                <th><?php echo __('Amount', 'bmp'); ?></th>
                                <th><?php echo __('date', 'bmp'); ?></th>
                            </tr>
                            <?php if ($my_referral_data) {
                                foreach ($my_referral_data as $my_referrals_data) { ?>
                                    <tr>
                                        <td><?php echo $my_referrals_data->sponsor_id; ?> </td>
                                        <td><?php echo $my_referrals_data->amount; ?> </td>
                                        <td><?php echo date('F j, Y', strtotime($my_referrals_data->date_notified)); ?></td>
                                    </tr>
                                <?php }
                            } else { ?>

                            <?php } ?>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php
}

function bmp_user_payout_bonus_detail_function()
{
    if ($_GET['id']) {
        $payout_id = sanitize_text_field($_GET['id']);
    } else {
        $payout_id = 0;
    }
    $my_payout_summary_data = bmp_user_payout_summary_data($payout_id);

?>
    <div class="wrap" id="bmp_user_downlines">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6 table-p">
                    <h4><?php echo __('Payout Summary Details', 'bmp'); ?></h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <th><?php echo __('Commission Amount', 'bmp'); ?></th>
                                <td><?php echo $my_payout_summary_data->commission_amount; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo __('Referral Commisssion Amount', 'bmp'); ?></th>
                                <td><?php echo $my_payout_summary_data->referral_commission_amount; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo __('Total Amount', 'bmp'); ?></th>
                                <td><?php echo $my_payout_summary_data->total_amount; ?></td>
                            </tr>
                            <tr>
                                <th><?php echo __('Cap Limit', 'bmp'); ?></th>
                                <td><?php echo $my_payout_summary_data->cap_limit; ?></td>
                            </tr>

                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

<?php
}


function bmp_user_payout_list_function()
{
    $payouts_list = bmp_payout_list_of_current_user();

?>
    <div class="container mt-sm-3" id="bmp_user_payouts">
        <div class="row">
            <div class="col-md-12 table-p">
                <h4><?php echo __('My payouts List', 'bmp'); ?></h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo __('Payout Id', 'bmp'); ?></th>
                                <th><?php echo __('User Name', 'bmp'); ?></th>
                                <th><?php echo __('Referral Commission', 'bmp'); ?></th>
                                <th><?php echo __('Commission', 'bmp'); ?></th>
                                <th><?php echo __('Total Amount', 'bmp'); ?></th>
                                <th><?php echo __('Capped Amount', 'bmp'); ?></th>
                                <th><?php echo __('Cap Limit', 'bmp'); ?></th>
                                <th><?php echo __('Tax', 'bmp'); ?></th>
                                <th><?php echo __('Service Charge', 'bmp'); ?></th>
                                <th><?php echo __('Action', 'bmp'); ?></th>
                            </tr>
                        </thead>
                        <?php if ($payouts_list) { ?>
                            <?php foreach ($payouts_list as $payout_list) { ?>
                                <tr>
                                    <td><?php echo $payout_list->id; ?></td>
                                    <td><?php echo bmp_getUsernameByUserId($payout_list->user_id); ?></td>
                                    <td><?php echo $payout_list->commission_amount; ?></td>
                                    <td><?php echo $payout_list->total_amount; ?></td>
                                    <td><?php echo $payout_list->referral_commission_amount; ?></td>
                                    <td><?php echo $payout_list->capped_amount; ?></td>
                                    <td><?php echo !empty($payout_list->cap_limit) ? $payout_list->cap_limit : 0; ?></td>
                                    <td><?php echo $payout_list->tax; ?></td>
                                    <td><?php echo $payout_list->service_charge; ?></td>
                                    <td><a href="<?php echo get_permalink(get_option('bmp_bmp_payout_detail_page_id', true)); ?>?id=<?php echo $payout_list->id; ?>"><?php echo __('View', 'bmp'); ?></a></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php
}



function bmp_add_custom_column_users($columns)
{
    $columns['parent_name'] = __('Parent', 'mlm');
    $columns['position'] = __('Position', 'mlm');
    $columns['sponsor_name'] = __('Sponsor', 'mlm');
    $columns['payment_status'] = __('Payment Status', 'mlm');

    $bmp_manage_general = get_option('bmp_manage_general');
    if ($bmp_manage_general['bmp_epin_activate'] == 1) {
        $columns['ePin'] = __('ePin', 'bmp');
    }

    return $columns;
}


/* To add a value in custom columns added */
function bmp_add_custom_column_users_value($value, $column_name, $user_id)
{
    global $wpdb;
    $bmp_manage_general = get_option('bmp_manage_general');
    /***************************/

    if ('payment_status' == $column_name) {
        /*check that it is mlm_user or not */
        $sql = "SELECT user_id, payment_status FROM {$wpdb->prefix}bmp_users WHERE user_id = $user_id";
        $res = $wpdb->get_row($sql);
        $html = '';

        if ($wpdb->num_rows > 0) {
            $currStatus = $res->payment_status;

            $adminajax = "'" . admin_url('admin-ajax.php') . "'";
            global $paymentStatusArr;
            $paymentStatusArr = array(0 => 'Unpaid', 1 => 'Paid');
            if (isset($mlm_settings['binarymlm_ePin_activate']) && $mlm_settings['binarymlm_ePin_activate'] == '1') {
                $paymentStatusArr[2] = 'Free Pin';
            }

            $html .= '<select name="payment_status_' . $user_id . '" id="payment_status_' . $user_id . '" onchange="update_payment_status(' . $adminajax . ',' . $user_id . ',this.value)">';
            foreach ($paymentStatusArr as $row => $val) {
                if ($row == $currStatus) {
                    $sel = 'selected="selected"';
                } else {
                    $sel = '';
                }
                $html .= '<option value="' . $row . '" ' . $sel . '>' . $val . '</option>';
            }
            $html .= '</select><span id="resultmsg_' . $user_id . '"></span>';

            return $html;
        } else {
            return __('Not a MLM User', 'bmp');
        }
    } //payment status column value added

    if ('ePin' == $column_name) {
        global $wpdb;
        $user = get_userdata($user_id);
        $user_key = $wpdb->get_var("select user_key from {$wpdb->prefix}bmp_users where user_id='{$user->ID}'");
        /* check that it is mlm user or not */
        $res = $wpdb->get_row("SELECT epin_no FROM {$wpdb->prefix}bmp_epins WHERE user_key = '$user_key'");
        $adminajax = "'" . admin_url('admin-ajax.php') . "'";

        if ($wpdb->num_rows > 0) {
            return $res->epin_no;
        } else {
            $not_mlm = $wpdb->get_row("select id from {$wpdb->prefix}bmp_users where user_id='{$user->ID}'");
            if ($wpdb->num_rows == '0') {
                return 'Not MLM User';
            } else {
                $payment_status = $wpdb->get_var("select payment_status from {$wpdb->prefix}bmp_users where user_id='{$user->ID}'");
                if ($payment_status == '1') {
                    return __(' ', 'mlm');
                } else if ($payment_status == '2') {
                    return __(' ', 'mlm');
                } else {
                    $epin = '<input type="text" name="epin" id="epin_' . $user_id . '"><input type="button" value="Update ePin" id="update_' . $user_id . '" onclick="setePinUser(' . $adminajax . ',' . $user_id . ',document.getElementById(\'epin_' . $user_id . '\').value);"><span id="epinmsg_' . $user_id . '"></span>';
                    return $epin;
                }
            }
        }
    } //epin column value added


    if ('parent_name' == $column_name) {
        global $wpdb;

        $res = $wpdb->get_row("SELECT user_id, payment_status FROM {$wpdb->prefix}bmp_users WHERE user_id = '$user_id'");
        if ($wpdb->num_rows > 0) {

            $parent_key = bmp_get_parent_key_by_userid($user_id);

            if ($parent_key != '0') {
                $parent_name = bmp_getUsername($parent_key);
                return $parent_name;
            } else {
                return __(' ', 'mlm');
            }
        } else {
            return __(' ', 'mlm');
        }
    } //parent column value added

    if ('sponsor_name' == $column_name) {
        global $wpdb;

        $res = $wpdb->get_row("SELECT user_id, payment_status FROM {$wpdb->prefix}bmp_users WHERE user_id = '$user_id'");
        if ($wpdb->num_rows > 0) {

            $sponsor_key = bmp_get_sponsor_key_by_userid($user_id);
            if ($sponsor_key != '0') {
                $sponsor_name = bmp_getUsername($sponsor_key);
                return $sponsor_name;
            } else {
                return __(' ', 'mlm');
            }
        } else {
            return __(' ', 'mlm');
        }
    } //sponsor name column value added

    if ('position' == $column_name) {
        global $wpdb;
        $res = $wpdb->get_row("SELECT user_id, payment_status FROM {$wpdb->prefix}bmp_users WHERE user_id = '$user_id' AND parent_key!='0'");
        if ($wpdb->num_rows > 0) {

            $parent_key = bmp_get_parent_key_by_userid($user_id);
            $position = $wpdb->get_var("SELECT position FROM {$wpdb->prefix}bmp_users WHERE user_id = '$user_id' "
                . "AND parent_key='$parent_key' AND parent_key!='0'");
            if ($position == 'right') {
                $position = "Right";
            } elseif ($position == 'left') {
                $position = "Left";
            }

            return $position;
        }
    } //position column value added
}



function bmp_username_downline_search_function()
{
    global $wpdb, $current_user;

    $json = array();
    $json['status'] = false;
    // $username = sanitize_text_field($_POST['username']);
    $username = $current_user->user_login;
    $bmp_user = '';
    $user_search_key = '';
    $cur_user_key = '';
    $cur_user_key = $wpdb->get_var("SELECT user_key FROM {$wpdb->prefix}bmp_users WHERE user_name = '" . $username . "'");


    if (isset($_POST['username']) && !empty($_POST['username'])) {
        $bmp_user = $wpdb->get_var("SELECT user_key FROM {$wpdb->prefix}bmp_users WHERE user_name='" . $_POST['username'] . "'");

        $user_search_key =  $wpdb->get_var("SELECT user_key FROM {$wpdb->prefix}bmp_leftposition WHERE user_key = '" . $bmp_user . "' and parent_key='" . $cur_user_key . "'");
        if (empty($user_search_key)) {
            $user_search_key =  $wpdb->get_var("SELECT user_key FROM {$wpdb->prefix}bmp_rightposition WHERE user_key = '" . $bmp_user . "' and parent_key='" . $cur_user_key . "'");
        }
        if (!empty($user_search_key) || ($bmp_user == $cur_user_key)) {
            $json['status'] = true;
            $json['message'] = '<span style="color:green">' . __('Correct username.', 'bmp') . '</span>';
        } else {
            $json['status'] = false;
            $json['message'] = '<span style="color:red">' . __('User  does not in this network', 'bmp') . '</span>';
        }
    } else if (empty($_POST['username'])) {
        $json['status'] = false;
        $json['message'] = '<span style="color:red">' . __('User Name could not be empty.', 'bmp') . '</span>';
    } else {
        $json['status'] = false;
        $json['message'] = '<span style="color:red">' . __('incorrect User Name.', 'bmp') . '</span>';
    }

    echo json_encode($json);

    wp_die();
}

function bmp_front_register_function()
{
    global $wpdb;
    //print_r($_POST);die;

    $bmp_general_setting = get_option('bmp_manage_general');
    //print_r($bmp_general_setting);
    $jsonarray = array();
    $jsonarray['status'] = true;
    //print_r($_POST);
    $firstname = sanitize_text_field($_POST['bmp_first_name']);
    $lastname = sanitize_text_field($_POST['bmp_last_name']);
    $username = sanitize_text_field($_POST['bmp_username']);
    $password = sanitize_text_field($_POST['bmp_password']);
    $confirm_password = sanitize_text_field($_POST['bmp_confirm_password']);
    $email = sanitize_text_field($_POST['bmp_email']);
    $sponsor = sanitize_text_field($_POST['bmp_sponsor_id']);
    $telephone = sanitize_text_field($_POST['bmp_phone']);

    $position = sanitize_text_field($_POST['bmp_position']);
    $bmp_epin = sanitize_text_field($_POST['bmp_epin']);
    $parent_key = sanitize_text_field($_POST['parent_key']);


    if (empty($firstname)) {
        $jsonarray['error']['bmp_first_name_message'] = __('First Name could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    }
    if (empty($lastname)) {
        $jsonarray['error']['bmp_last_name_message'] = __('Last Name could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    }

    if (empty($username)) {
        $jsonarray['error']['bmp_username_message'] = __('Userame could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    }

    if ($password != $confirm_password) {
        $jsonarray['error']['bmp_confirm_password_message'] = __('Confirm Password does not Match', 'bmp');
        $jsonarray['status'] = false;
    }

    if (empty($email)) {
        $jsonarray['error']['bmp_email_message'] = __('Email could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    } else if (!is_email($email)) {
        $jsonarray['error']['bmp_email_message'] = __("E-mail address is invalid.", 'bmp');
        $jsonarray['status'] = false;
    } else if (email_exists($email)) {
        $jsonarray['error']['bmp_email_message'] = __("E-mail address is already in use.", 'mlm');
        $jsonarray['status'] = false;
    }

    if (empty($sponsor)) {
        $jsonarray['error']['bmp_sponsor_message'] = __('Sponsor could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    }

    if (empty($telephone)) {
        $jsonarray['error']['bmp_phone_message'] = __('Phone could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    }

    if (empty($position)) {
        $jsonarray['error']['bmp_position_message'] = __('Position could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    }

    if (empty($jsonarray['error'])) {

        $sql = "SELECT `user_key` FROM {$wpdb->prefix}bmp_users WHERE `user_id` = '" . $sponsor . "'";
        $sponsor_key = $wpdb->get_var($sql);

        $sponsor_parent_key = $sponsor_key;
        if (!empty($parent_key) && $parent_key != '') {
            $parent_key = $parent_key;
        } else {
            $readonly_sponsor = '';
            do {
                $sql = "SELECT `user_key` FROM {$wpdb->prefix}bmp_users WHERE parent_key = '" . $sponsor_parent_key . "' AND position = '" . $position . "'";
                $sponsor_key_value = $wpdb->get_var($sql);
                $num = $wpdb->num_rows;
                if ($num) {
                    $sponsor_parent_key = $sponsor_key_value;
                }
            } while ($num == 1);

            $parent_key = $sponsor_parent_key;
        }


        $user_key = bmp_generateKey();

        $user = array(
            'user_login' => $username,
            'user_pass' => $password,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'user_email' => $email
        );

        $user_id = wp_insert_user($user);
        $user = new WP_User($user_id);
        //$user->add_role( 'bmp_user' );
        $user->set_role('bmp_user');
        add_user_meta($user_id, 'bmp_first_name', $firstname);
        add_user_meta($user_id, 'bmp_last_name', $lastname);
        add_user_meta($user_id, 'bmp_username', $username);
        add_user_meta($user_id, 'bmp_sponsor_id', $sponsor);
        add_user_meta($user_id, 'bmp_phone', $telephone);
        add_user_meta($user_id, 'bmp_position', $position);

        $sponsor_key = bmp_get_user_key($sponsor);

        //insert the data into wp_mlm_user table
        $insert = "INSERT INTO {$wpdb->prefix}bmp_users (user_id, user_name, user_key, parent_key, sponsor_key, position,payment_date,payment_status,product_price)
          VALUES('" . $user_id . "','" . $username . "', '" . $user_key . "', '" . $parent_key . "', '" . $sponsor_key . "', '" . $position . "','" . current_time('mysql') . "','1','500')";
        if ($wpdb->query($insert)) {


            //entry on Left and Right Leg tables
            if ($position == 'left') {
                $insert = "INSERT INTO {$wpdb->prefix}bmp_leftposition (parent_key, user_key) VALUES ('" . $parent_key . "','" . $user_key . "')";
                $insert = $wpdb->query($insert);
            } else if ($position == 'right') {
                $insert = "INSERT INTO {$wpdb->prefix}bmp_rightposition(parent_key, user_key) VALUES('" . $parent_key . "','" . $user_key . "')";
                $insert = $wpdb->query($insert);
            }

            // while the parent_key equal 0

            while ($parent_key != '0') {
                $query = "SELECT COUNT(*) num, parent_key, position FROM {$wpdb->prefix}bmp_users WHERE user_key = '" . $parent_key . "'";
                $result = $wpdb->get_row($query);
                if ($result->num == 1) {
                    if ($result->parent_key != '0') {
                        if ($result->position == 'right') {
                            $position_right = "INSERT INTO {$wpdb->prefix}bmp_rightposition(parent_key,user_key) VALUES('" . $result->parent_key . "','" . $user_key . "')";
                            $wpdb->query($position_right);
                        } else {
                            $position_left = "INSERT INTO {$wpdb->prefix}bmp_leftposition(parent_key,user_key) VALUES('" . $result->parent_key . "','" . $user_key . "')";
                            $wpdb->query($position_left);
                        }
                    }
                    $parent_key = $result->parent_key;
                } else {
                    $parent_key = '0';
                }
            }

            if (!empty($bmp_epin)) {
                $un_used_epin = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bmp_epins WHERE epin_no = '" . $bmp_epin . "' and status='0'");
                if (!empty($un_used_epin)) {
                    $wpdb->query("UPDATE {$wpdb->prefix}bmp_epins SET user_key='" . $user_key . "', status='1', date_used='" . date('Y-m-d') . "' WHERE epin_no = '" . $bmp_epin . "'");
                    if ($un_used_epin->type == 'regular') {
                        if (bmp_eligibility_check_for_commission($sponsor_key)) {
                            bmp_insert_refferal_commision($user_id);
                        }
                    }
                }
            }
        }

        $jsonarray['status'] = true;
        $jsonarray['message'] = __('Binary User has been created successfully.', 'bmp');
    }

    echo json_encode($jsonarray);
    wp_die();
}

/////////////////join network ////////////////
function bmp_join_network_function()
{

    global $wpdb, $wp_query, $current_user;

    $bmp_users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmp_users");
    if (is_user_logged_in()) {
        $sponsor_id = $current_user->ID;
    } else {
        $sponsor_id = '';
    }
    if (!empty($_REQUEST['position'])) {
        $position = sanitize_text_field($_REQUEST['position']);
    } else {
        $position = '';
    }
    $selected = 'selected readonly';
?>
    <div class="container">
        <div class="d-block m-auto" style="border: 1px solid #9a9898;box-shadow: 1px 1px 5px 2px #9a9898;">
            <h4 class="text-center mt-3"><?php _e('Join Binary Mlm Plan', 'bmp'); ?></h4>
            <form id="bmp_join_network_form" action="">
                <input type="hidden" name="action" value="bmp_join_network">
                <!--<input type="hidden" name="parent_key" value="<?php echo sanitize_text_field($_GET['k']); ?>">-->
                <div class="row">
                    <div class="form-group text-center">
                        <label for="joisponsor"><?php esc_html_e('SPONSER', 'bmp'); ?></label>
                        <select class="form-control w-50 d-block m-auto" name="bmp_join_sponser" id="bmp_sponsor_id" required="">
                            <option value="" disabled selected><?php esc_html_e('Select Sponser', 'bmp'); ?></option>
                            <?php foreach ($bmp_users as $bmp_user) { ?>
                                <option value="<?php echo $bmp_user->user_id; ?>" <?php echo ($sponsor_id == $bmp_user->user_id) ? $selected : ''; ?>><?php echo $bmp_user->user_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group text-center">
                        <label for="joisponsor"><?php esc_html_e('LEG', 'bmp'); ?></label>
                        <select class="form-control w-50 d-block m-auto" name="bmp_join_leg" id="bmp_position" required="">
                            <option value="" disabled selected><?php esc_html_e('Select Leg', 'bmp'); ?></option>
                            <option value="left" <?php echo ($position == 'left') ? $selected : ''; ?>><?php esc_html_e('Left', 'bmp'); ?></option>
                            <option value="right" <?php echo ($position == 'right') ? $selected : ''; ?>><?php esc_html_e('Right', 'bmp'); ?></option>
                        </select>
                        <div class="bmp_position_message"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group text-center">
                        <label for="joiepin"><?php esc_html_e('Epin', 'bmp'); ?></label>
                        <input id="bmp_join_epin" name="bmp_join_epin" type="text" class="form-control w-50 d-block m-auto" placeholder="<?php esc_html_e('Epin', 'bmp'); ?>" value="" required>
                        <div class="bmp_epin_join_message"></div>
                    </div>
                </div>
                <div class="row my-3">
                    <div class="col-md-12 col-md-offset-5">
                        <button type="submit" class="btn button btn-primary d-block m-auto"><?php esc_html_e('Join', 'bmp'); ?></button>
                    </div>
                </div>
                <div id="bmp_user_success_message" style="text-align:center;color:green; margin-bottom:5px"></div>
            </form>
        </div>
    </div>

<?php }
function bmp_front_join_network_function()
{
    global $wpdb, $current_user;
    $bmp_general_setting = get_option('bmp_manage_general');
    $jsonjoinarray = array();
    $jsonjoinarray['status'] = false;


    $sponsor = sanitize_text_field($_POST['bmp_join_sponser']);
    $position = sanitize_text_field($_POST['bmp_join_leg']);
    //$parent_key = sanitize_text_field( $_POST['parent_key'] );
    $epin = sanitize_text_field(isset($_POST['bmp_join_epin']) ? sanitize_text_field($_POST['bmp_join_epin']) : '');


    if (empty($epin)) {
        $jsonjoinarray['error']['bmp_epin_join_message'] = __('ePin could Not be empty', 'bmp');
        $jsonjoinarray['status'] = false;
    } else if (!empty($epin) && bmp_epin_exist($epin)) {
        $jsonjoinarray['error']['bmp_epin_join_message'] = __('ePin Already Used', 'bmp');
        $jsonjoinarray['status'] = false;
    }

    if (empty($sponsor)) {
        $jsonjoinarray['error']['bmp_join_sponsor_message'] = __('Sponsor could Not be empty', 'bmp');
        $jsonjoinarray['status'] = false;
    }
    if (empty($position)) {
        $jsonjoinarray['error']['bmp_join_position_message'] = __('Position could Not be empty', 'bmp');
        $jsonjoinarray['status'] = false;
    }


    if (empty($jsonjoinarray['error']) && !empty($current_user->ID)) {


        $user_id = $current_user->ID;

        $user_key = bmp_generateKey();

        $sponsor_key = bmp_get_user_key($sponsor);
        $sponsor_parent_key = $sponsor_key;
        if (!empty($parent_key) && $parent_key != '') {
            $parent_key = $parent_key;
        } else {
            $readonly_sponsor = '';
            do {
                $sql = "SELECT `user_key` FROM {$wpdb->prefix}bmp_users WHERE parent_key = '" . $sponsor_parent_key . "' AND position = '" . $position . "'";
                $sponsor_key_value = $wpdb->get_var($sql);
                $num = $wpdb->num_rows;
                if ($num) {
                    $sponsor_parent_key = $sponsor_key_value;
                }
            } while ($num == 1);

            $parent_key = $sponsor_parent_key;
        }


        //insert the data into wp_mlm_user table
        $insert = "INSERT INTO {$wpdb->prefix}bmp_users (user_id, user_name, user_key, parent_key, sponsor_key, position,payment_date,payment_status,product_price)
          VALUES ('" . $user_id . "','" . $current_user->user_login . "', '" . $user_key . "', '" . $parent_key . "', '" . $sponsor_key . "', '" . $position . "','" . current_time('mysql') . "','1','0')";


        if ($wpdb->query($insert)) {

            $user = get_user_by('id', $user_id);
            $user->set_role('bmp_user');
            //entry on Left and Right Leg tables
            if ($position == 'left') {
                $insert = "INSERT INTO {$wpdb->prefix}bmp_leftposition (parent_key, user_key) VALUES ('" . $parent_key . "','" . $user_key . "')";
                $insert = $wpdb->query($insert);
            } else if ($position == 'right') {
                $insert = "INSERT INTO {$wpdb->prefix}bmp_rightposition(parent_key, user_key) VALUES('" . $parent_key . "','" . $user_key . "')";
                $insert = $wpdb->query($insert);
            }

            // while the parent_key equal 0

            while ($parent_key != '0') {
                $query = "SELECT COUNT(*) num, parent_key, position FROM {$wpdb->prefix}bmp_users WHERE user_key = '" . $parent_key . "'";
                $result = $wpdb->get_row($query);
                if ($result->num == 1) {
                    if ($result->parent_key != '0') {
                        if ($result->position == 'right') {
                            $position_right = "INSERT INTO {$wpdb->prefix}bmp_rightposition(parent_key,user_key) VALUES('" . $result->parent_key . "','" . $user_key . "')";
                            $wpdb->query($position_right);
                        } else {
                            $position_left = "INSERT INTO {$wpdb->prefix}bmp_leftposition(parent_key,user_key) VALUES('" . $result->parent_key . "','" . $user_key . "')";
                            $wpdb->query($position_left);
                        }
                    }
                    $parent_key = $result->parent_key;
                } else {
                    $parent_key = '0';
                }
            }
            if (!empty($epin)) {
                $un_used_epin = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bmp_epins WHERE epin_no = '" . $epin . "' and status='0'");
                if (!empty($un_used_epin)) {
                    $wpdb->query("UPDATE {$wpdb->prefix}bmp_epins SET user_key='" . $user_key . "', status='1', date_used='" . date('Y-m-d') . "' WHERE epin_no = '" . $epin . "'");
                    if ($un_used_epin->type == 'regular') {
                        if (bmp_eligibility_check_for_commission($sponsor_key)) {
                            bmp_insert_refferal_commision($user_id);
                        }
                    }
                }
            }
        }

        $jsonjoinarray['status'] = true;
        $jsonjoinarray['message'] = __('Binary User has been created successfully.', 'bmp');
    }
    echo json_encode($jsonjoinarray);
    wp_die();
}


//////////////. user name exist ////////////////

function bmp_username_exist_function()
{
    global $wpdb;

    $json = array();
    $json['status'] = false;
    $username = sanitize_text_field($_POST['username']);

    $bmp_user = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bmp_users WHERE user_name='" . $username . "'");

    if (!empty($bmp_user)) {
        $json['status'] = false;
        $json['message'] = '<span style="color:red">' . __('User Already Exist. Please try another user', 'bmp') . '</span>';
    } elseif (empty($username)) {
        $json['status'] = false;
        $json['message'] = '<span style="color:red">' . __('User Name could not be empty.', 'bmp') . '</span>';
    } else {
        $json['status'] = true;
        $json['message'] = '<span style="color:green">' . __('Congratulation!This username is avaiable.', 'bmp') . '</span>';
    }

    echo json_encode($json);

    wp_die();
}


///// user emaik exist /////

function bmp_position_exist_function()
{
    global $wpdb;
    $json = array();
    $json['status'] = false;
    $position = sanitize_text_field($_POST['position']);
    $sponsor = sanitize_text_field($_POST['sponsor']);

    $sql = "SELECT `user_key` FROM {$wpdb->prefix}bmp_users WHERE `user_id` = '" . $sponsor . "'";
    $sponsor_key = $wpdb->get_var($sql);
    $sponsor_parent_key = $sponsor_key;

    $l_check = "SELECT * FROM `{$wpdb->prefix}bmp_users` WHERE `sponsor_key` = '" . $sponsor_parent_key . "' AND `position`='" . $position . "'";
    $bmp_user_exist = $wpdb->get_row($l_check);

    if ($bmp_user_exist) {
        $json['status'] = true;
        $json['message'] = '<span style="color:red">' . __('Position Already Used by someone. Please try another Position', 'bmp') . '</span>';
    } else {
        if (!empty($position)) {
            $json['status'] = true;
            $json['message'] = '<span style="color:green">' . __('Congratulation! This position is avaiable.', 'bmp') . '</span>';
        } else {
            $json['status'] = false;
            $json['message'] = '<span style="color:red">' . __('Position Could not be empty.', 'bmp') . '</span>';
        }
    }

    echo json_encode($json);

    wp_die();
}
function bmp_email_exist_function()
{
    global $wpdb;

    $json = array();
    $json['status'] = false;
    $email = sanitize_text_field($_POST['email']);

    if (email_exists($email)) {
        $json['status'] = false;
        $json['message'] = '<span style="color:red">' . __('Email Already Used by someone. Please try another Email', 'bmp') . '</span>';
    } else {
        if (empty($email)) {
            $json['status'] = false;
            $json['message'] = '<span style="color:red">' . __('Email could not be empty.', 'bmp') . '</span>';
        } else {

            $json['status'] = true;
            $json['message'] = '<span style="color:green">' . __('Congratulation!This Email is avaiable.', 'bmp') . '</span>';
        }
    }

    echo json_encode($json);

    wp_die();
}


//////////////. user name exist ////////////////

function bmp_epin_exist_function()
{
    global $wpdb;

    $json = array();
    $json['status'] = false;
    $epin = sanitize_text_field($_POST['epin']);
    $eping = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bmp_epins WHERE epin_no='" . $epin . "' AND status='0'");

    if (!empty($eping)) {
        $json['status'] = true;
        $json['message'] = '<span style="color:green">' . __('Congratulation!This ePin is avaiable.', 'bmp') . '</span>';
    } else {
        $json['status'] = false;
        $json['message'] = '<span style="color:red">' . __('Epin Already Used or Not exist. Please try another ePin', 'bmp') . '</span>';
    }

    echo json_encode($json);

    wp_die();
}


//////// user password validate //////

function bmp_password_validation_function()
{

    global $wpdb;

    $json = array();
    $json['status'] = false;
    $password = sanitize_text_field($_POST['password']);
    $confirm_password = sanitize_text_field($_POST['confirm_password']);

    if ($password == $confirm_password) {
        $json['status'] = true;
        $json['message'] = '<span style="color:green">' . __('Congratulation! Password is valid.', 'bmp') . '</span>';
    } else {
        $json['status'] = false;
        $json['message'] = '<span style="color:red">' . __('Sorry Password does not match.', 'bmp') . '</span>';
    }

    echo json_encode($json);

    wp_die();
}

function bmp_level_based_childs($key, $level)
{
    global $wpdb;
    echo $key;
    $sql = "SELECT * FROM {$wpdb->prefix}bmp_users WHERE parent_key = '" . $key . "'";
    $childs = $wpdb->get_results($sql);
    return $childs;
}

function bmp_autofill_position_parent_key($sponsor_parent_key)
{
    global $wpdb, $level;
    $level++;

    $sql = "SELECT * FROM {$wpdb->prefix}bmp_users WHERE parent_key = '" . $sponsor_parent_key . "'";
    $childs = $wpdb->get_results($sql);

    if ($wpdb->num_rows == 0) {
        return array('position' => 'left', 'parent_key' => $sponsor_parent_key);
    } else if ($wpdb->num_rows == 1) {
        foreach ($childs as $child) {
            if ($child->position == 'left') {
                return array('position' => 'right', 'parent_key' => $child->parent_key);
            } else if ($child->position == 'right') {
                return array('position' => 'left', 'parent_key' => $child->parent_key);
            }
        }
    } else {
        $childs_count = pow(2, $level);
        $num_check = 0;

        foreach ($childs as $child) {
            $sql = "SELECT * FROM {$wpdb->prefix}bmp_users WHERE parent_key = '" . $child->user_key . "'";
            $wpdb->get_results($sql);
            $num_check += $wpdb->num_rows;
        }

        if ($childs_count == $num_check) {
            foreach ($childs as $child) {
                $sql = "SELECT * FROM {$wpdb->prefix}bmp_users WHERE parent_key = '" . $child->user_key . "'";
                $lchilds = $wpdb->get_results($sql);
                if ($wpdb->num_rows == 2) {
                    return bmp_autofill_position_parent_key($child->user_key);
                }
            }
        } else {

            $all_childs = bmp_level_based_childs($sponsor_parent_key, $level);
            print_r($all_childs);
            foreach ($childs as $child) {
                $sql = "SELECT * FROM {$wpdb->prefix}bmp_users WHERE parent_key = '" . $child->user_key . "'";
                $lchilds = $wpdb->get_results($sql);
                if ($wpdb->num_rows == 1 || $wpdb->num_rows == 0) {
                    return bmp_autofill_position_parent_key($child->user_key);
                }
            }
        }
    }
}


function bmp_bmp_auto_add_function()
{
    global $wpdb, $level;

    $username = $firstname = $lastname = 'user' . sanitize_text_field($_POST['number']);
    $sponsor = sanitize_text_field($_POST['sponsor']);
    $email = 'user' . sanitize_text_field($_POST['number']) . '@test.com';
    $password = '123456';
    $position = sanitize_text_field($_POST['position']);
    $telephone = '1234567890';
    $epin = sanitize_text_field($_POST['epin']);

    $jsonarray = array();
    $jsonarray['status'] = true;

    if (empty($firstname)) {
        $jsonarray['error']['bmp_first_name_message'] = __('First Name could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    }
    if (empty($lastname)) {
        $jsonarray['error']['bmp_last_name_message'] = __('Last Name could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    }

    if (empty($username)) {
        $jsonarray['error']['bmp_username_message'] = __('Userame could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    }

    if (empty($epin)) {
        $jsonarray['error']['bmp_epin_message'] = __('ePin could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    } else if (!empty($epin) && bmp_epin_exist($epin)) {
        $jsonarray['error']['bmp_epin_message'] = __('ePin Already Used', 'bmp');
        $jsonarray['status'] = false;
    }

    // if($password!=$confirm_password){
    // 	$jsonarray['error']['bmp_confirm_password_message']= __('Password Not Confirmd','bmp');
    // 	$jsonarray['status']=false;
    // }

    if (empty($email)) {
        $jsonarray['error']['bmp_email_message'] = __('Email could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    } else if (!is_email($email)) {
        $jsonarray['error']['bmp_email_message'] = __("E-mail address is invalid.", 'bmp');
        $jsonarray['status'] = false;
    } else if (email_exists($email)) {
        $jsonarray['error']['bmp_email_message'] = __("E-mail address is already in use.", 'mlm');
        $jsonarray['status'] = false;
    }

    if (empty($sponsor)) {
        $jsonarray['error']['bmp_sponsor_message'] = __('Sponsor could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    }

    if (empty($telephone)) {
        $jsonarray['error']['bmp_phone_message'] = __('Phone could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    }

    if (empty($position)) {
        $jsonarray['error']['bmp_position_message'] = __('Position could Not be empty', 'bmp');
        $jsonarray['status'] = false;
    }

    if (empty($jsonarray['error'])) {

        $sql = "SELECT `user_key` FROM {$wpdb->prefix}bmp_users WHERE `user_id` = '" . $sponsor . "'";
        $sponsor_key = $wpdb->get_var($sql);
        $sponsor_parent_key = $sponsor_key;

        $autofill = false;
        if ($autofill) {
            $level = 1;
            $data_auto_file = bmp_autofill_position_parent_key($sponsor_key);
            //print_r($data_auto_file); 
            $position = $data_auto_file['position'];
            $parent_key = $data_auto_file['parent_key'];
        } else {

            if (!empty($parent_key) && $parent_key != '') {
                $parent_key = $parent_key;
            } else {
                $readonly_sponsor = '';
                do {
                    $sql = "SELECT `user_key` FROM {$wpdb->prefix}bmp_users WHERE parent_key = '" . $sponsor_parent_key . "' AND position = '" . $position . "'";
                    $sponsor_key_value = $wpdb->get_var($sql);
                    $num = $wpdb->num_rows;
                    if ($num) {
                        $sponsor_parent_key = $sponsor_key_value;
                    }
                } while ($num == 1);

                $parent_key = $sponsor_parent_key;
            }
        }

        //echo $parent_key; die;
        //die;
        $user_key = bmp_generateKey();

        $user = array(
            'user_login' => $username,
            'user_pass' => $password,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'user_email' => $email
        );

        $user_id = wp_insert_user($user);
        $user = new WP_User($user_id);
        //$user->add_role( 'bmp_user' );
        $user->set_role('bmp_user');
        add_user_meta($user_id, 'bmp_first_name', $firstname);
        add_user_meta($user_id, 'bmp_last_name', $lastname);
        add_user_meta($user_id, 'bmp_username', $username);
        add_user_meta($user_id, 'bmp_sponsor_id', $sponsor);
        add_user_meta($user_id, 'bmp_phone', $telephone);
        add_user_meta($user_id, 'bmp_position', $position);

        //wp_new_user_notification($user_id, $password);


        if (!empty($epin)) {
            $pointresult = $wpdb->get_row("SELECT product_id,type from {$wpdb->prefix}bmp_epins where epin_no = '{$epin}'");
            $epin_type = $pointresult->type;
            // to epin point status 1 
            if ($epin_type == 'regular') {
                $paymentstatus = '1';
                $payment_date = current_time('mysql');
            }
            // to epin point status 1 
            else if ($epin_type == 'free') {
                $paymentstatus = '2';
                $payment_date = current_time('mysql');
            }
        }

        $sponsor_key = bmp_get_user_key($sponsor);

        //insert the data into wp_mlm_user table
        $insert = "INSERT INTO {$wpdb->prefix}bmp_users (user_id, user_name, user_key, parent_key, sponsor_key, position,payment_date,payment_status,product_price)
          VALUES('" . $user_id . "','" . $username . "', '" . $user_key . "', '" . $parent_key . "', '" . $sponsor_key . "', '" . $position . "','" . $payment_date . "','" . $paymentstatus . "','0')";
        if ($wpdb->query($insert)) {
            //entry on Left and Right Leg tables
            if ($position == 'left') {
                $insert = "INSERT INTO {$wpdb->prefix}bmp_leftposition (parent_key, user_key) VALUES ('" . $parent_key . "','" . $user_key . "')";
                $insert = $wpdb->query($insert);
            } else if ($position == 'right') {
                $insert = "INSERT INTO {$wpdb->prefix}bmp_rightposition(parent_key, user_key) VALUES('" . $parent_key . "','" . $user_key . "')";
                $insert = $wpdb->query($insert);
            }

            // while the parent_key equal 0

            while ($parent_key != '0') {
                $query = "SELECT COUNT(*) num, parent_key, position FROM {$wpdb->prefix}bmp_users WHERE user_key = '" . $parent_key . "'";
                $result = $wpdb->get_row($query);
                if ($result->num == 1) {
                    if ($result->parent_key != '0') {
                        if ($result->position == 'right') {
                            $position_right = "INSERT INTO {$wpdb->prefix}bmp_rightposition(parent_key,user_key) VALUES('" . $result->parent_key . "','" . $user_key . "')";
                            $wpdb->query($position_right);
                        } else {
                            $position_left = "INSERT INTO {$wpdb->prefix}bmp_leftposition(parent_key,user_key) VALUES('" . $result->parent_key . "','" . $user_key . "')";
                            $wpdb->query($position_left);
                        }
                    }
                    $parent_key = $result->parent_key;
                } else {
                    $parent_key = '0';
                }
            }


            if (isset($epin) && !empty($epin)) {
                $sql = "update {$wpdb->prefix}bmp_epins set user_key='{$user_key}', date_used='" . current_time('mysql') . "', status=1 where epin_no ='{$epin}' ";
                $wpdb->query($sql);
            }

            if ($paymentstatus == 1) {
                bmp_insert_refferal_commision($user_id);
            }
        }

        $jsonarray['status'] = true;
        $jsonarray['message'] = __('Binary User has been created successfully.', 'bmp');
    }

    echo json_encode($jsonarray);
    wp_die();
}
