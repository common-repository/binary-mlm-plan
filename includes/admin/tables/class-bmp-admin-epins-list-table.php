<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class bmp_admin_epin_list extends WP_List_Table
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
            case 'epin_name':
            case 'epin_no':
            case 'type':
            case 'date_generated':
            case 'user_key':
            case 'date_used':
            case 'status':
            case 'epin_price':
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    function get_columns()
    {
        $columns = array(
            'epin_name'    => __('ePin Name', 'bmp'),
            'epin_no' => __('ePin No', 'bmp'),
            'type'    => __('Type', 'bmp'),
            'date_generated'    => __('Date Generated', 'bmp'),
            'user_key'    => __('User Key', 'bmp'),
            'date_used'    => __('Date Used', 'bmp'),
            'status'    => __('Status', 'bmp'),
            'epin_price'    => __('ePin Price', 'bmp')

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
        $sql = "SELECT * FROM {$wpdb->prefix}bmp_epins ORDER BY id ASC";

        $results = $wpdb->get_results($sql, ARRAY_A);

        $i = 0;
        $listdata = array();
        $num = $wpdb->num_rows;
        if ($num > 0) {
            foreach ($results as $row) {
                $listdata[$i]['epin_name'] = $row['epin_name'];
                $listdata[$i]['epin_no'] = $row['epin_no'];
                $listdata[$i]['type'] = $row['type'];
                $listdata[$i]['date_generated'] = $row['date_generated'];
                $listdata[$i]['user_key'] = $row['user_key'];
                $listdata[$i]['date_used'] = $row['date_used'];
                $listdata[$i]['status'] = $row['status'];
                $listdata[$i]['epin_price'] = $row['epin_price'];
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
