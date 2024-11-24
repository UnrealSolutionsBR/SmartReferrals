<?php

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

    /**
     * Define the columns for the table
     */
    public function get_columns() {
        return [
            'cb'             => '<input type="checkbox" />',
            'user'           => __( 'User', 'smart-referrals' ),
            'referral_code'  => __( 'Referral Code', 'smart-referrals' ),
            'actions'        => __( 'Actions', 'smart-referrals' ),
        ];
    }

    /**
     * Render the checkbox column
     */
    protected function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="bulk-select[]" value="%d" />', $item['ID'] );
    }

    /**
     * Render the user column
     */
    protected function column_user( $item ) {
        return esc_html( $item['user'] );
    }

    /**
     * Render the referral code column
     */
    protected function column_referral_code( $item ) {
        return esc_html( $item['referral_code'] );
    }

    /**
     * Render the actions column
     */
    protected function column_actions( $item ) {
        return sprintf( '<a href="%s">%s</a>',
            esc_url( admin_url( 'user-edit.php?user_id=' . $item['ID'] ) ),
            __( 'Edit', 'smart-referrals' )
        );
    }

    /**
     * Default column renderer
     */
    protected function column_default( $item, $column_name ) {
        return isset( $item[ $column_name ] ) ? esc_html( $item[ $column_name ] ) : '';
    }

    /**
     * Prepare the items for display
     */
    public function prepare_items() {
        // Static data for testing
        $this->items = [
            [
                'ID'            => 1,
                'user'          => 'Test User',
                'referral_code' => 'TESTCODE',
                'actions'       => '<a href="#">Edit</a>',
            ],
            [
                'ID'            => 2,
                'user'          => 'Another User',
                'referral_code' => 'ANOTHERCODE',
                'actions'       => '<a href="#">Edit</a>',
            ],
        ];

        // Pagination setup
        $this->set_pagination_args( [
            'total_items' => count( $this->items ),
            'per_page'    => 20,
            'total_pages' => 1,
        ] );
    }
}
