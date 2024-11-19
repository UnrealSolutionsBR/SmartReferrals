<?php
/*
Plugin Name: Smart Referrals
Author: Unreal Solutions
Author URI: https://www.unrealsolutions.com.br
Version: 1.0.0
Requires at least: 6.6.2
Description: Elevate your earnings with a powerful toolkit for effective referral management.
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Salir si se accede directamente.
}

class Smart_Referrals {

    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    private function define_constants() {
        define( 'SR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        define( 'SR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    }

    private function includes() {
        require_once SR_PLUGIN_DIR . 'includes/class-sr-referral-code.php';
        require_once SR_PLUGIN_DIR . 'includes/class-sr-woocommerce-integration.php';
        require_once SR_PLUGIN_DIR . 'includes/class-sr-shortcodes.php';

        if ( is_admin() ) {
            require_once SR_PLUGIN_DIR . 'admin/class-sr-admin-menu.php';
            require_once SR_PLUGIN_DIR . 'admin/class-sr-dashboard.php';
            require_once SR_PLUGIN_DIR . 'admin/class-sr-settings.php';
            require_once SR_PLUGIN_DIR . 'admin/class-sr-referrals.php';
        }
    }

    private function init_hooks() {
        register_activation_hook( __FILE__, array( 'SR_Referral_Code', 'generate_referral_codes_for_existing_users' ) );
        add_action( 'user_register', array( 'SR_Referral_Code', 'generate_referral_code' ), 10, 1 );
        add_action( 'init', array( 'SR_Shortcodes', 'register_shortcodes' ) );
        add_action( 'init', array( 'SR_WooCommerce_Integration', 'apply_referral_coupon' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_styles' ) );
    }

    public function enqueue_admin_styles() {
        wp_enqueue_style( 'sr-admin-styles', SR_PLUGIN_URL . 'assets/css/admin-styles.css', array(), '1.0.0' );
    }

    public function enqueue_frontend_styles() {
        wp_enqueue_style( 'sr-frontend-styles', SR_PLUGIN_URL . 'assets/css/frontend-styles.css', array(), '1.0.0' );
    }

}

new Smart_Referrals();
