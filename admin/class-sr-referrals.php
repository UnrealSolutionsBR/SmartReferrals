<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Referrals {

    public static function display() {
        include SR_PLUGIN_DIR . 'admin/templates/referrals.php';
    }

    public static function activate_module() {
        // Activate module functions
        // For example:
        add_action( 'woocommerce_before_cart', array( 'SR_WooCommerce_Integration', 'add_referral_coupon_to_cart' ) );
        // Add other actions or filters as needed
    }

    public static function deactivate_module() {
        // Deactivate module functions
        // For example:
        remove_action( 'woocommerce_before_cart', array( 'SR_WooCommerce_Integration', 'add_referral_coupon_to_cart' ) );
        // Remove other actions or filters as needed
    }
}

// On plugin load, check module status and activate functions if needed
if ( get_option( 'sr_referrals_module_enabled', 'yes' ) === 'yes' ) {
    SR_Referrals::activate_module();
}
