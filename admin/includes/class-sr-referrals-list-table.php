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
        return [
            'cb'             => '<input type="checkbox" />', // Checkbox column
            'user'           => __( 'User', 'smart-referrals' ),
            'referral_code'  => __( 'Referral Code', 'smart-referrals' ),
            'actions'        => __( 'Actions', 'smart-referrals' ),
        ];
    }

    protected function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="bulk-select[]" value="%d" />', $item['ID'] );
    }

    public function prepare_items() {
        $per_page = $this->get_items_per_page( 'referrals_per_page', 20 );
        $current_page = $this->get_pagenum();

        $users = get_users( [
            'number' => $per_page,
            'offset' => ( $current_page - 1 ) * $per_page,
        ] );

        $data = [];
        foreach ( $users as $user ) {
            $data[] = [
                'ID'             => $user->ID,
                'user'           => $user->user_login,
                'referral_code'  => get_user_meta( $user->ID, 'sr_referral_code', true ),
                'actions'        => $this->get_row_actions( $user->ID ),
            ];
        }

        $this->items = $data;

        $total_items = count_users()['total_users'];
        $this->set_pagination_args( [
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total_items / $per_page ),
        ] );
    }

    protected function column_default( $item, $column_name ) {
        return $item[ $column_name ] ?? '';
    }

    private function get_row_actions( $user_id ) {
        $edit_url = admin_url( 'user-edit.php?user_id=' . $user_id );
        return sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), __( 'Edit', 'smart-referrals' ) );
    }
}
