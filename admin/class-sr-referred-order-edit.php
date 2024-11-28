<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SR_Referred_Order_Edit {

    public static function display() {
        // Manejar acción de eliminación
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['order_id'])) {
            $order_id = intval($_GET['order_id']);
            $nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';

            if (!wp_verify_nonce($nonce, 'sr_delete_order_nonce')) {
                wp_die(__('Invalid nonce specified', 'smart-referrals'), __('Error', 'smart-referrals'), ['response' => 403]);
            }

            // Eliminar el pedido
            $order = wc_get_order($order_id);
            if ($order) {
                $order->delete(true);
                wp_redirect(admin_url('admin.php?page=sr-referred-orders'));
                exit;
            } else {
                echo '<div class="notice notice-error"><p>' . esc_html__('Order not found.', 'smart-referrals') . '</p></div>';
            }
            return;
        }

        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

        if (!$order_id) {
            echo '<p>' . esc_html__('Invalid Order ID.', 'smart-referrals') . '</p>';
            return;
        }

        $order = wc_get_order($order_id);

        if (!$order) {
            echo '<p>' . esc_html__('Order not found.', 'smart-referrals') . '</p>';
            return;
        }

        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sr_save_order'])) {
            check_admin_referer('sr_save_order_nonce');

            // Actualizar metadatos del pedido
            $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : 'Pending';
            $order->update_meta_data('_sr_referral_status', $status);

            // Actualizar cliente
            $customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : 0;
            if ($customer_id) {
                $order->set_customer_id($customer_id);
            }

            // Guardar el pedido
            $order->save();

            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Order updated successfully.', 'smart-referrals') . '</p></div>';
        }

        // Obtener detalles del pedido
        $date_created = $order->get_date_created() ? $order->get_date_created()->date('Y-m-d H:i:s') : '';
        $status = $order->get_meta('_sr_referral_status', true);
        if (!$status) {
            $status = 'Pending';
        }
        $customer_id = $order->get_customer_id();
        $customer = $customer_id ? get_user_by('ID', $customer_id) : null;

        // Obtener código de referido y usuario referido
        $used_coupons = $order->get_coupon_codes();
        $referral_coupon = self::get_referral_coupon($used_coupons);
        $referral_user = self::get_referral_user($referral_coupon);

        // Comisión
        $commission = $order->get_total() * 0.10;

        // Opciones de estado
        $statuses = [
            'Pending'    => __('Pending', 'smart-referrals'),
            'Qualified'  => __('Qualified', 'smart-referrals'),
            'Rejected'   => __('Rejected', 'smart-referrals'),
            'Approved'   => __('Approved', 'smart-referrals'),
        ];

        ?>
        <div class="wrap">
            <h1><?php printf(__('Referred Order #%d', 'smart-referrals'), $order->get_id()); ?></h1>

            <form method="post">
                <?php wp_nonce_field('sr_save_order_nonce'); ?>
                <h2><?php esc_html_e('Order Details', 'smart-referrals'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Date Created', 'smart-referrals'); ?></th>
                        <td><?php echo esc_html($date_created); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Status', 'smart-referrals'); ?></th>
                        <td>
                            <select name="status">
                                <?php
                                foreach ($statuses as $key => $label) {
                                    $selected = ($status === $key) ? 'selected' : '';
                                    printf('<option value="%s" %s>%s</option>', esc_attr($key), $selected, esc_html($label));
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Customer', 'smart-referrals'); ?></th>
                        <td>
                            <?php
                            wp_dropdown_users([
                                'name'             => 'customer_id',
                                'selected'         => $customer_id,
                                'show_option_none' => __('Guest', 'smart-referrals'),
                            ]);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Referral', 'smart-referrals'); ?></th>
                        <td><?php echo esc_html($referral_user ? $referral_user->user_login : __('Unknown', 'smart-referrals')); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Commission', 'smart-referrals'); ?></th>
                        <td><?php echo wc_price($commission); ?></td>
                    </tr>
                </table>

                <h2><?php esc_html_e('Order Actions', 'smart-referrals'); ?></h2>
                <p class="submit">
                    <input type="submit" name="sr_save_order" id="sr_save_order" class="button button-primary" value="<?php esc_attr_e('Update Order', 'smart-referrals'); ?>">
                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=sr-referred-order&action=delete&order_id=' . $order->get_id()), 'sr_delete_order_nonce'); ?>" class="button button-secondary"><?php esc_html_e('Move to Trash', 'smart-referrals'); ?></a>
                </p>
            </form>
        </div>
        <?php
    }

    private static function get_referral_coupon($coupons) {
        foreach ($coupons as $coupon_code) {
            $coupon = new WC_Coupon($coupon_code);
            $is_referral_coupon = $coupon->get_meta('_sr_referral_coupon', true);

            if ($is_referral_coupon === 'yes') {
                return $coupon_code;
            }
        }
        return false;
    }

    private static function get_referral_user($coupon_code) {
        // Asumiendo que el código de referido es único y está asociado a un usuario
        $users = get_users([
            'meta_key'   => 'sr_referral_code',
            'meta_value' => $coupon_code,
            'number'     => 1,
            'fields'     => 'all',
        ]);

        return !empty($users) ? $users[0] : false;
    }
}