<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class SR_Referrals_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct( [
            'singular' => __( 'Referral', 'smart-referrals' ),
            'plural'   => __( 'Referrals', 'smart-referrals' ),
            'ajax'     => false,
        ] );
    }

    public function get_columns() {
        $columns = [
            'cb'       => '<input type="checkbox" />',
            'username' => __( 'User', 'smart-referrals' ),
            'email'    => __( 'Email', 'smart-referrals' ),
            'referral' => __( 'Referral Code', 'smart-referrals' ),
            'actions'  => __( 'Actions', 'smart-referrals' ),
        ];
        return $columns;
    }

    public function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="referrals[]" value="%s" />', $item['ID'] );
    }

    public function column_username( $item ) {
        $settings_url = add_query_arg(
            [
                'page'   => 'sr-user-settings',
                'user_id' => $item['ID']
            ],
            admin_url( 'admin.php' )
        );

        return sprintf(
            '<a href="%s">%s</a>',
            esc_url( $settings_url ),
            esc_html( $item['user'] )
        );
    }

    public function column_email( $item ) {
        return esc_html( $item['email'] );
    }

    public function column_referral( $item ) {
        return esc_html( $item['referral_code'] );
    }

    public function column_actions( $item ) {
        $settings_url = add_query_arg(
            [
                'page'   => 'sr-user-settings',
                'user_id' => $item['ID']
            ],
            admin_url( 'admin.php' )
        );

        return sprintf(
            '<a href="%s">%s</a>',
            esc_url( $settings_url ),
            __( 'Edit', 'smart-referrals' )
        );
    }

    public function get_bulk_actions() {
        $actions = [
            'delete' => __( 'Delete', 'smart-referrals' ),
        ];
        return $actions;
    }

    public function prepare_items() {
        $per_page     = 10;
        $current_page = $this->get_pagenum();

        $args = [
            'number' => $per_page,
            'offset' => ( $current_page - 1 ) * $per_page,
        ];

        $user_query = new WP_User_Query( $args );
        $users      = $user_query->get_results();

        $items = [];
        foreach ( $users as $user ) {
            $referral_code = get_user_meta( $user->ID, 'sr_referral_code', true );

            $items[] = [
                'ID'            => $user->ID,
                'user'          => $user->user_login,
                'email'         => $user->user_email,
                'referral_code' => $referral_code ? $referral_code : __( 'N/A', 'smart-referrals' ),
            ];
        }

        $this->items = $items;

        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = [];

        $this->_column_headers = [ $columns, $hidden, $sortable ];

        $this->set_pagination_args( [
            'total_items' => $user_query->get_total(),
            'per_page'    => $per_page,
        ] );
    }
}
