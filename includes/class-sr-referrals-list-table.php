<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class SR_Referrals_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct( array(
            'singular' => 'referral',
            'plural'   => 'referrals',
            'ajax'     => false,
        ) );
    }

    public function get_columns() {
        $columns = array(
            'cb'           => '<input type="checkbox" />',
            'username'     => __( 'User', 'smart-referrals' ),
            'referral_code'=> __( 'Referral Code', 'smart-referrals' ),
            'active_status' => __('Active', 'smart-referrals'),
            'actions'      => __( 'Actions', 'smart-referrals' ),
        );
        return $columns;
    }

    function column_active_status($item) {
        $is_active = get_user_meta($item['ID'], 'sr_user_active', true) === 'yes';
        return $is_active ? __('Active', 'smart-referrals') : __('Inactive', 'smart-referrals');
    }

    protected function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="users[]" value="%s" />',
            esc_attr( $item->ID )
        );
    }

    function column_username($item) {
        $settings_url = add_query_arg(
            [
                'page' => 'sr-user-settings',
                'user_id' => $item['ID']
            ],
            admin_url('admin.php')
        );
    
        return sprintf('<a href="%s">%s</a>', esc_url($settings_url), esc_html($item['user']));
    }

    protected function column_referral_code( $item ) {
        $referral_code = get_user_meta( $item->ID, 'sr_referral_code', true );
        return $referral_code ? esc_html( $referral_code ) : __( 'No referral code', 'smart-referrals' );
    }

    function column_actions($item) {
        $settings_url = add_query_arg(
            [
                'page' => 'sr-user-settings',
                'user_id' => $item['ID']
            ],
            admin_url('admin.php')
        );
    
        return sprintf('<a href="%s">%s</a>', esc_url($settings_url), __('Edit', 'smart-referrals'));
    }

    public function get_bulk_actions() {
        return array(
            'delete' => __( 'Delete', 'smart-referrals' ),
        );
    }

    protected function process_bulk_action() {
        if ( 'delete' === $this->current_action() ) {
            $nonce = isset( $_REQUEST['_wpnonce'] ) ? $_REQUEST['_wpnonce'] : '';
            if ( ! wp_verify_nonce( $nonce, 'bulk-' . $this->_args['plural'] ) ) {
                wp_die( __( 'You do not have permission to perform this action.', 'smart-referrals' ) );
            }

            $user_ids = isset( $_REQUEST['users'] ) ? array_map( 'intval', $_REQUEST['users'] ) : array();

            foreach ( $user_ids as $user_id ) {
                // Aquí puedes implementar la eliminación o cualquier otra acción
            }

            wp_redirect( add_query_arg() );
            exit;
        }
    }

    public function prepare_items() {
        $this->process_bulk_action();

        $per_page = 10;
        $current_page = $this->get_pagenum();
        $offset = ( $current_page - 1 ) * $per_page;

        $args = array(
            'number' => $per_page,
            'offset' => $offset,
        );

        if ( ! empty( $_REQUEST['s'] ) ) {
            $args['search'] = '*' . esc_attr( $_REQUEST['s'] ) . '*';
            $args['search_columns'] = array( 'user_login', 'user_email' );
        }

        $user_query = new WP_User_Query( $args );
        $this->items = $user_query->get_results();

        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = array();

        $this->_column_headers = array( $columns, $hidden, $sortable );

        $total_items = $user_query->get_total();
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
        ) );
    }
}
