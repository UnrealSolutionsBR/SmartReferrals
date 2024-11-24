<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SR_Referrals_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => __('referral', 'smart-referrals'),
            'plural'   => __('referrals', 'smart-referrals'),
            'ajax'     => false
        ]);
    }

    public function get_columns() {
        return [
            'cb'           => '<input type="checkbox" />',
            'username'     => __('Username', 'smart-referrals'),
            'email'        => __('Email', 'smart-referrals'),
            'referral_code' => __('Referral Code', 'smart-referrals'),
            'active_status' => __('Active Status', 'smart-referrals'),
            'actions'      => __('Actions', 'smart-referrals'),
        ];
    }

    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="users[]" value="%s" />', $item['ID']);
    }

    public function column_username($item) {
        $settings_url = add_query_arg(
            [
                'page' => 'sr-user-settings',
                'user_id' => $item['ID']
            ],
            admin_url('admin.php')
        );

        return sprintf('<a href="%s">%s</a>', esc_url($settings_url), esc_html($item['user']));
    }

    public function column_email($item) {
        return esc_html($item['email']);
    }

    public function column_referral_code($item) {
        return esc_html($item['referral_code']);
    }

    public function column_active_status($item) {
        $is_active = get_user_meta($item['ID'], 'sr_user_active', true) === 'yes';
        return $is_active ? __('Active', 'smart-referrals') : __('Inactive', 'smart-referrals');
    }

    public function column_actions($item) {
        $settings_url = add_query_arg(
            [
                'page' => 'sr-user-settings',
                'user_id' => $item['ID']
            ],
            admin_url('admin.php')
        );

        return sprintf('<a href="%s">%s</a>', esc_url($settings_url), __('Edit', 'smart-referrals'));
    }

    public function prepare_items() {
        $per_page = 10;
        $current_page = $this->get_pagenum();

        $args = [
            'number' => $per_page,
            'offset' => ($current_page - 1) * $per_page
        ];

        if (isset($_REQUEST['s']) && !empty($_REQUEST['s'])) {
            $args['search'] = '*' . esc_attr($_REQUEST['s']) . '*';
            $args['search_columns'] = ['user_login', 'user_email'];
        }

        $user_query = new WP_User_Query($args);
        $users = $user_query->get_results();

        $items = [];
        foreach ($users as $user) {
            $items[] = [
                'ID'            => $user->ID,
                'user'          => $user->user_login,
                'email'         => $user->user_email,
                'referral_code' => get_user_meta($user->ID, 'sr_referral_code', true),
            ];
        }

        $this->items = $items;

        $this->_column_headers = [
            $this->get_columns(),
            [],
            []
        ];

        $this->set_pagination_args([
            'total_items' => $user_query->get_total(),
            'per_page'    => $per_page
        ]);
    }
}
