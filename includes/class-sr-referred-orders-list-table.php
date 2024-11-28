<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class SR_Referred_Orders_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => __('Referred Order', 'smart-referrals'),
            'plural'   => __('Referred Orders', 'smart-referrals'),
            'ajax'     => false,
        ]);
    }

    public function get_columns() {
        $columns = [
            'order'     => __('Order', 'smart-referrals'),
            'date'      => __('Date', 'smart-referrals'),
            'status'    => __('Status', 'smart-referrals'),
            'total'     => __('Total', 'smart-referrals'),
            'commission'=> __('Commission', 'smart-referrals'),
            'referral'  => __('Referral', 'smart-referrals'),
        ];
        return $columns;
    }

    public function prepare_items() {
        $per_page     = 10;
        $current_page = $this->get_pagenum();

        $args = [
            'limit'    => $per_page,
            'offset'   => ($current_page - 1) * $per_page,
            'coupon_used' => true,
            'orderby'  => 'date',
            'order'    => 'DESC',
        ];

        // Obtener los pedidos que han usado un cupón de referido
        $orders = wc_get_orders($args);
        $items = [];

        foreach ($orders as $order) {
            $used_coupons = $order->get_coupon_codes();
            $referral_coupon = $this->get_referral_coupon($used_coupons);

            if ($referral_coupon) {
                $customer_id = $order->get_customer_id();
                $customer = $customer_id ? get_user_by('ID', $customer_id) : null;
                $customer_name = $customer ? $customer->display_name : __('Guest', 'smart-referrals');
                $referral_user = $this->get_referral_user($referral_coupon);

                // Obtener el estado personalizado
                $status = $order->get_meta('_sr_referral_status', true);
                if (!$status) {
                    $status = 'Pending'; // Valor predeterminado
                }

                $items[] = [
                    'ID'         => $order->get_id(),
                    'order'      => sprintf(
                        '<a href="%s">#%d %s</a>',
                        esc_url(admin_url('admin.php?page=sr-referred-order&order_id=' . $order->get_id())),
                        $order->get_id(),
                        esc_html($customer_name)
                    ),
                    'date'       => $order->get_date_created()->date('Y-m-d H:i:s'),
                    'status'     => $status,
                    'total'      => wc_price($order->get_total()),
                    'commission' => wc_price($order->get_total() * 0.10),
                    'referral'   => esc_html($referral_user ? $referral_user->user_login : __('Unknown', 'smart-referrals')),
                ];
            }
        }

        $this->items = $items;

        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = [];

        $this->_column_headers = [$columns, $hidden, $sortable];

        // Configuración de la paginación
        $this->set_pagination_args([
            'total_items' => count($items),
            'per_page'    => $per_page,
        ]);
    }

    private function get_referral_coupon($coupons) {
        foreach ($coupons as $coupon_code) {
            $coupon = new WC_Coupon($coupon_code);
            $is_referral_coupon = $coupon->get_meta('_sr_referral_coupon', true);

            if ($is_referral_coupon === 'yes') {
                return $coupon_code;
            }
        }
        return false;
    }

    private function get_referral_user($coupon_code) {
        // Asumiendo que el código de referido es único y está asociado a un usuario
        $users = get_users([
            'meta_key'   => 'sr_referral_code',
            'meta_value' => $coupon_code,
            'number'     => 1,
            'fields'     => 'all',
        ]);

        return !empty($users) ? $users[0] : false;
    }

    public function column_default($item, $column_name) {
        return isset($item[$column_name]) ? $item[$column_name] : '';
    }

    public function column_status($item) {
        $statuses = [
            'Pending'    => __('Pending', 'smart-referrals'),
            'Qualified'  => __('Qualified', 'smart-referrals'),
            'Rejected'   => __('Rejected', 'smart-referrals'),
            'Approved'   => __('Approved', 'smart-referrals'),
        ];

        $order_id = $item['ID'];
        $current_status = $item['status'];

        $options = '';
        foreach ($statuses as $key => $label) {
            $selected = ($current_status === $key) ? 'selected' : '';
            $options .= sprintf('<option value="%s" %s>%s</option>', esc_attr($key), $selected, esc_html($label));
        }

        $nonce = wp_create_nonce('sr_update_referral_status_' . $order_id);

        return sprintf(
            '<select class="sr-referral-status" data-order-id="%d" data-nonce="%s">%s</select>',
            $order_id,
            $nonce,
            $options
        );
    }

    public function no_items() {
        _e('No referred orders found.', 'smart-referrals');
    }
}
