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
}

SR_Ajax_Handler::init();
