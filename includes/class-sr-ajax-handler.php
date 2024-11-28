<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Ajax_Handler {

    public static function init() {
        add_action( 'wp_ajax_sr_toggle_module', array( __CLASS__, 'toggle_module' ) );
    }

    public static function toggle_module() {
        check_ajax_referer( 'sr_admin_nonce', 'security' );

        $enabled = isset( $_POST['enabled'] ) && $_POST['enabled'] === 'yes' ? 'yes' : 'no';
        update_option( 'sr_referrals_module_enabled', $enabled );

        // Activate or deactivate module functions
        if ( $enabled === 'yes' ) {
            SR_Referrals::activate_module();
        } else {
            SR_Referrals::deactivate_module();
        }

        // Return the new module status
        wp_send_json_success( array( 'enabled' => $enabled ) );
    }
    public static function update_referral_status() {
        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        $status   = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        $nonce    = isset($_POST['nonce']) ? $_POST['nonce'] : '';
    
        if (!wp_verify_nonce($nonce, 'sr_update_referral_status_' . $order_id)) {
            wp_send_json_error(['message' => __('Invalid nonce.', 'smart-referrals')]);
        }
    
        if ($order_id && $status) {
            $order = wc_get_order($order_id);
            if ($order) {
                $order->update_meta_data('_sr_referral_status', $status);
                $order->save();
                wp_send_json_success(['message' => __('Status updated.', 'smart-referrals')]);
            }
        }
    
        wp_send_json_error(['message' => __('Failed to update status.', 'smart-referrals')]);
    }
    
    // Agregar el hook AJAX
    add_action('wp_ajax_sr_update_referral_status', [__CLASS__, 'update_referral_status']);    
}

SR_Ajax_Handler::init();
