<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SR_User_Settings {

    public static function display() {
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

        if (!$user_id) {
            echo '<p>' . esc_html__('Invalid User ID.', 'smart-referrals') . '</p>';
            return;
        }

        // Obtener datos del usuario
        $user = get_userdata($user_id);
        if (!$user) {
            echo '<p>' . esc_html__('User not found.', 'smart-referrals') . '</p>';
            return;
        }

        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Guardar estado activo/inactivo
            if (isset($_POST['active_status'])) {
                update_user_meta($user_id, 'active_status', 'active');
            } else {
                update_user_meta($user_id, 'active_status', 'inactive');
            }

            // Guardar correo de pago de PayPal
            if (isset($_POST['paypal_email'])) {
                $paypal_email = sanitize_email($_POST['paypal_email']);
                update_user_meta($user_id, 'paypal_email', $paypal_email);
            }

            echo '<div class="notice notice-success"><p>' . esc_html__('User settings saved.', 'smart-referrals') . '</p></div>';
        }

        // Obtener valores actuales
        $active_status = get_user_meta($user_id, 'active_status', true);
        $paypal_email = get_user_meta($user_id, 'paypal_email', true);
        $checked = ($active_status === 'active' || empty($active_status)) ? 'checked' : '';
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('User Settings', 'smart-referrals'); ?></h1>
            <form method="post">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Active Status', 'smart-referrals'); ?></th>
                        <td>
                            <input type="checkbox" name="active_status" value="active" <?php echo esc_attr($checked); ?> />
                            <label for="active_status"><?php esc_html_e('Activate user', 'smart-referrals'); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('PayPal Payout Email', 'smart-referrals'); ?></th>
                        <td>
                            <input type="email" name="paypal_email" value="<?php echo esc_attr($paypal_email); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e('Enter the PayPal email for payouts.', 'smart-referrals'); ?></p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" class="button button-primary" value="<?php esc_attr_e('Save Settings', 'smart-referrals'); ?>" />
                </p>
            </form>
        </div>
        <?php
    }
}
