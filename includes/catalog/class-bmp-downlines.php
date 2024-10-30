<?php
if (!defined('ABSPATH')) {
    exit;
}

class BMP_Genealogy
{

    //$clients array contain the nodes
    public $clients = array();

    //$add_page_id variable take the wordpress pageID for the network registration
    public $add_page_id;

    //$view_page_id take the wordpress pageID where the netwok to open
    public $view_page_id;

    //$counter varibale take the how many level you want to shows the network
    public $counter = 10;

    //addLeftLeg() function build the Left leg registration node of the network


    function addLeftLeg($key)
    {
        $username = bmp_getUsername($key);

        $str = "[{v:'" . $key . "ADD',f:'<a href=" . get_site_url() . "/register/?k=" . $key . "&position=left>ADD</a><br>'},'" . $username . "',''],";
        return $str;
    }

    //addRightLeg() function build the Right leg registration node of the network

    function addRightLeg($key)
    {
        $username = bmp_getUsername($key);
        $str = "[{v:'" . $key . "ADD2',f:'<a href=" . get_site_url() . "/register/?k=" . $key . "&position=right>ADD</a><br>'},'" . $username . "',''],";
        return $str;
    }

    //buildRootNetwork() function take the key and build the root node of the network
    function buildRootNetwork($key)
    {
        $level = array();
        $username = bmp_getUsername($key);
        $mlm_user = bmp_getUserInfoByKey($key);
        $payment = $this->checkPaymentStatus($key, $mlm_user->payment_status, $mlm_user->user_name, $mlm_user->sponsor_key, $mlm_user->parent_key);
        $myclients[] = "[{v:'" . $mlm_user->user_name . "',f:'" . $payment . "'}, '" . $username . "', ''],";
        $this->clients[] = $myclients;
        $level[] = $key;
        return $level;
    }

    //buildLevelByLevelNetwork() function build the 1st and more level network
    function buildLevelByLevelNetwork($key, $counter, $level)
    {
        global $wpdb;
        $level1 = array();

        for ($i = 0; $i < $counter; $i++) {

            if (!isset($level[$i])) {
                $level[$i] = null;
            }

            $myclients = array();

            if ($level[$i] != 'add' && $level[$i] != '') {
                $sql = "SELECT user_name, payment_status, user_key,sponsor_key,parent_key, position FROM {$wpdb->prefix}bmp_users WHERE parent_key = '" . $level[$i] . "' ORDER BY position DESC";
                $results = $wpdb->get_results($sql);

                $num = $wpdb->num_rows;

                // no child case

                if (!$num) {
                    $myclients[] = $this->addLeftLeg($level[$i]);
                    $myclients[] = $this->addRightLeg($level[$i]);
                    $level1[] = 'add';
                    $level1[] = 'add';
                }
                //if child exist
                else if ($num > 0) {

                    $username = bmp_getUsername($level[$i]);

                    foreach ($results as $key => $row) {
                        $user_key = $row->user_key;
                        $payment_status = $row->payment_status;

                        //check user paid or not

                        $payment = $this->checkPaymentStatus($user_key, $row->payment_status, $row->user_name, $row->sponsor_key, $row->parent_key);

                        //if only one child exist

                        if ($num == 1) {
                            //if right leg child exist
                            if ($row->position == 'right') {
                                $myclients[] = $this->addLeftLeg($level[$i]);
                                $myclients[] = "[{v:'" . $row->user_name . "',f:'" . $payment . "'}, '" . $username . "', ''],";
                                $level1[] = 'add';
                                $level1[] = $row->user_key;
                            } else if ($row->position == 'left') // if left leg child exist
                            {
                                $myclients[] = "[{v:'" . $row->user_name . "',f:'" . $payment . "'}, '" . $username . "', ''],";
                                $myclients[] = $this->addRightLeg($level[$i]);
                                $level1[] = $row->user_key;
                                $level1[] = 'add';
                            }
                        } else //both child exist left and right leg
                        {
                            $myclients[] = "[{v:'" . $row->user_name . "',f:'" . $payment . "'}, '" . $username . "', ''],";
                            $level1[] = $row->user_key;
                        }
                    } //end foreach loop
                }
                $this->clients[] = $myclients;
            } // end most outer if statement
        } //end for loop
        return $level1;
    }

    //checkPaymentStatus() function check the node user is paid or not

    function checkPaymentStatus($key, $payment, $username, $sponsor_key, $parent_key)
    {
        $left_downlines = bmp_user_left_downlines_by_key($key);
        $right_downlines = bmp_user_right_downlines_by_key($key);

        if ($payment == 1) {

            $payment_str = '<div  class="tooltip"><a href="' . home_url() . '/downlines/' . $key . '"><img src="' . BMP()->plugin_url() . '/image/paid.png" class="img"></a>'/*.$username.'<br><a href="'.get_site_url().'/downlines/'.$key.'">View</a>'*/;
            /*$payment_str.='<br><span class=\"paid\">PAID</span>';*/
        } else {
            $payment_str = '<div  class="tooltip"><a href="javascript:void(0)"><img src="' . BMP()->plugin_url() . '/image/unpaid.png" class="img">'/*.$username.'<br><a href="'.get_site_url().'/downlines/'.$key.'">View</a>'*/;
            /*$payment_str.='<br><span class=\"paid\">UNPAID</span>';*/
        }
        $payment_str .= '<span class="tooltiptext"><table class="tab_t table-striped"><tr class="tooltip_design"><td>' . __('Name :', 'bmp') . '</td><td class="text-capitalize">' . $username . '</td></tr><tr class="tooltip_design"><td>' . __('UserKey :', 'bmp') . '</td><td>' . $key . '</td></tr><tr class="tooltip_design"><td>' . __('Sponser :', 'bmp') . '</td><td>' . $sponsor_key . '</td><tr class="tooltip_design"><td>' . __('Parent :', 'bmp') . '</td><td>' . $parent_key . '</td></tr><tr class="tooltip_design"><td>' . __('Left :', 'bmp') . '</td><td>' . $left_downlines . '</td></tr><tr class="tooltip_design"><td>' . __('Right :', 'bmp') . '</td><td>' . $right_downlines . '</td> </tr><tr class="tooltip_design"><td>' . __('Earning :', 'bmp') . '</td><td>' . $key . '</td></tr></table></span>';
        $payment_str .= '</div>';

        return $payment_str;
    }

    function network()
    {
        global $wpdb, $wp_query;
        global $current_user;
        $cur_user_key = '';
        $user_search_key = '';
        $username = $current_user->user_login;
        $cur_user_key = $wpdb->get_var("SELECT user_key FROM {$wpdb->prefix}bmp_users WHERE user_name = '" . $username . "'");
        //if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['downlinessearch']) && $_POST['downlinessearch'])
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['downlines_username']) && $_POST['downlines_username']) {
            $username = sanitize_text_field($_POST['downlines_username']);
            $search_query = "SELECT user_key FROM {$wpdb->prefix}bmp_users WHERE user_name = '" . $username . "'";
            $search_query_key = $wpdb->get_var($search_query);

            $user_search_key =  $wpdb->get_var("SELECT user_key FROM {$wpdb->prefix}bmp_leftposition WHERE user_key = '" . $search_query_key . "' and parent_key='" . $cur_user_key . "'");
            if (empty($user_search_key)) {
                $user_search_key =  $wpdb->get_var("SELECT user_key FROM {$wpdb->prefix}bmp_rightposition WHERE user_key = '" . $search_query_key . "' and parent_key='" . $cur_user_key . "'");
            }
            if (!empty($user_search_key)) {
                $key = $user_search_key;
            } else {
                $key = $cur_user_key;
            }
        } else {
            if (!empty($wp_query->query_vars['page']) && $wp_query->query_vars['page'] != '') {
                $key = $wp_query->query_vars['page'];
            } else {
                $key = bmp_get_current_user_key();
            }
        }

        /*********************************************** Root/owner node ******************************************/
        $level = $this->buildRootNetwork($key);

        /*********************************************** First level ******************************************/
        $level = $this->buildLevelByLevelNetwork($key, 1, $level);

        /*********************************************** 2 and more level's ******************************************/
        if ($this->counter >= 2) {
            $j = 1;
            for ($i = 2; $i <= $this->counter; $i++) {
                $j = $j * 2;
                $level = $this->buildLevelByLevelNetwork($key, $j, $level);
            }
        }

        return $this->clients;
    } //end of function network()

    public function downlinesFunction()
    {
        global $wpdb, $wp_query;
        global $current_user;
        $username = $current_user->user_login;
        $downlinesarray = $this->network();
        $owner_user_key = $wpdb->get_var("SELECT user_key FROM {$wpdb->prefix}bmp_users WHERE user_name = '" . $username . "'"); ?>

        <div class="container">
            <h4 class="text-center">
                <a style="text-decoration:none;" href="<?php bloginfo('url'); ?>/downlines/<?php echo $owner_user_key; ?>">You</a>
            </h4>
            <div class="row">
                <div class="col-md-12">
                    <form name="downlinessearch" id="downlines-usersearch" action="" method="POST" style="margin: auto;">
                        <input type="text" name="downlines_username" id="downlines-username" required>

                        <input id="downlines-search" type="submit" name="downlines_search" value="Search">
                    </form>
                    <div class="row col-md-12 search-message"></div>
                </div>
            </div>
        </div>


        <script type='text/javascript' src='https://www.google.com/jsapi'></script>
        <script type='text/javascript'>
            google.load('visualization', '1', {
                packages: ['orgchart']
            });
            google.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Name');
                data.addColumn('string', 'Manager');
                data.addColumn('string', 'ToolTip');
                data.addRows([<?php for ($i = 0; $i < count($downlinesarray); $i++) {
                                    for ($j = 0; $j < 2; $j++) {
                                        if (!empty($downlinesarray[$i][$j])) {
                                            echo $downlinesarray[$i][$j];
                                        }
                                    }
                                } ?>['', null, '']]);
                var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
                chart.draw(data, {
                    allowHtml: true
                });
            }
        </script>


        <div style="margin:0 auto;padding:0px;clear:both; width:90%!important;" align="center">
            <div id='chart_div'></div>
        </div>
<?php
    }
}//end of Class
