<?php
/*
Plugin Name: Smart Referrals
Author: Unreal Solutions
Author URI: https://www.unrealsolutions.com.br
Version: 2.0.0
Requires at least: 6.6.2
Description: Elevate your earnings with a powerful toolkit for effective referral management.
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define constants first
if ( ! defined( 'SR_PLUGIN_DIR' ) ) {
    define( 'SR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'SR_PLUGIN_URL' ) ) {
    define( 'SR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// Include the 'includes/' classes after defining constants
require_once SR_PLUGIN_DIR . 'includes/class-sr-referral-code.php';
require_once SR_PLUGIN_DIR . 'includes/class-sr-woocommerce-integration.php';
require_once SR_PLUGIN_DIR . 'includes/class-sr-shortcodes.php';

class Smart_Referrals {

    public function __construct() {
        $this->includes();
        $this->init_hooks();
        $this->init_admin();

        // Enqueue frontend scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
    }

    private function includes() {
        // Include the 'admin/' classes here
        if ( is_admin() ) {
            require_once SR_PLUGIN_DIR . 'admin/class-sr-admin-menu.php';
            require_once SR_PLUGIN_DIR . 'admin/class-sr-dashboard.php';
            require_once SR_PLUGIN_DIR . 'admin/class-sr-settings.php';
            require_once SR_PLUGIN_DIR . 'admin/class-sr-referrals.php';
        }
    }

    private function init_hooks() {
        // Activation hooks and actions
        register_activation_hook( __FILE__, array( 'SR_Referral_Code', 'generate_referral_codes_for_existing_users' ) );
        add_action( 'user_register', array( 'SR_Referral_Code', 'generate_referral_code' ), 10, 1 );
        add_action( 'init', array( 'SR_Shortcodes', 'register_shortcodes' ) );
        // The apply_referral_coupon() is hooked in SR_WooCommerce_Integration
    }

    private function init_admin() {
        if ( is_admin() ) {
            new SR_Admin_Menu();
            // Instantiate other admin classes if necessary
        }
    }

    public function enqueue_admin_styles() {
        wp_enqueue_style( 'sr-admin-styles', SR_PLUGIN_URL . 'assets/css/admin-styles.css', array(), '1.0.0' );
    }

    public function enqueue_frontend_styles() {
        wp_enqueue_style( 'sr-frontend-styles', SR_PLUGIN_URL . 'assets/css/frontend-styles.css', array(), '1.0.0' );

        // Enqueue custom styles for the referral copy link
        wp_enqueue_style( 'sr-referral-copylink-styles', SR_PLUGIN_URL . 'assets/css/referral-copylink.css', array(), '1.0.0' );
    }

    public function enqueue_frontend_scripts() {
        wp_enqueue_script( 'sr-referral-copylink-script', SR_PLUGIN_URL . 'assets/js/referral-copylink.js', array( 'jquery' ), '1.0.0', true );

        // Localize script for translation and data
        wp_localize_script( 'sr-referral-copylink-script', 'srCopyLink', array(
            'copiedText' => __( 'El enlace se copió al portapapeles exitosamente', 'smart-referrals' ),
        ) );
    }

}

new Smart_Referrals();
