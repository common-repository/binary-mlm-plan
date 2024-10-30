<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class bmp_admin_payout_list extends WP_List_Table
{

    /** Class constructor */
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => __('id', 'bmp'),
            'plural'   => __('id', 'bmp'),
            'ajax'     => false

        ));
    }

    function get_sortable_columns()
    {
        $sortable_columns = array();
        return $sortable_columns;
    }

    function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'user_id':
            case 'date':
            case 'commission_amount':
            case 'referral_commission_amount':
            case 'total_amount':
            case 'cap_limit':
            case 'action';

                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns()
    {
        $columns = array(
            'user_id'    => __('User Id', 'bmp'),
            'date' => __('Date', 'bmp'),
            'commission_amount'    => __('Commission Amount', 'bmp'),
            'referral_commission_amount'    => __('Referral Commission Amount', 'bmp'),
            'total_amount'    => __('total amount', 'bmp'),
            'cap_limit'    => __('Cap Limit', 'bmp'),
            'action'    => __('Action', 'bmp'),
        );

        return $columns;
    }


    function prepare_items()
    {

        global $wpdb;
        global $date_format;
        $per_page = 10;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $sql = "SELECT * FROM {$wpdb->prefix}bmp_payout ORDER BY id ASC";

        $results = $wpdb->get_results($sql, ARRAY_A);

        $i = 0;
        $listdata = array();
        $num = $wpdb->num_rows;
        if ($num > 0) {
            foreach ($results as $row) {
                $listdata[$i]['user_id'] = $row['user_id'];
                $listdata[$i]['date'] = $row['date'];
                $listdata[$i]['commission_amount'] = ($row['commission_amount']) ? 'Paid' : 'Un Paid';
                $listdata[$i]['referral_commission_amount'] = $row['referral_commission_amount'];
                $listdata[$i]['total_amount'] = $row['total_amount'];
                $listdata[$i]['cap_limit'] = !empty($row['cap_limit']) ? $row['cap_limit'] : 0;
                $listdata[$i]['action'] = '<a href="' . admin_url() . 'admin.php?page=bmp-payout-reports&payout_id=' . $row['id'] . '">View</a>';
                $i++;
            }
        }

        $data = $listdata;

        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data, (($current_page - 1) * $per_page), $per_page);
        $this->items = $data;
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }
}
