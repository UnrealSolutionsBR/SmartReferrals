<?php
/*
Plugin Name: Smart Referrals
Author: Unreal Solutions
Author URI: https://www.unrealsolutions.com.br
Version: 2.2.1
Requires at least: 6.6.2
Description: Elevate your earnings with a powerful toolkit for effective referral management.
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define constants
if ( ! defined( 'SR_PLUGIN_DIR' ) ) {
    define( 'SR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'SR_PLUGIN_URL' ) ) {
    define( 'SR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// Include necessary classes
require_once SR_PLUGIN_DIR . 'includes/class-sr-referral-code.php';
require_once SR_PLUGIN_DIR . 'includes/class-sr-woocommerce-integration.php';
require_once SR_PLUGIN_DIR . 'includes/class-sr-shortcodes.php';
require_once SR_PLUGIN_DIR . 'includes/class-sr-ajax-handler.php';
require_once SR_PLUGIN_DIR . 'includes/class-sr-elementor-widgets.php';
require_once SR_PLUGIN_DIR . 'admin/class-sr-user-settings.php';
require_once SR_PLUGIN_DIR . 'includes/jetengine/class-sr-dynamic-conditions.php';
require_once SR_PLUGIN_DIR . 'admin/class-sr-referred-orders.php';
require_once SR_PLUGIN_DIR . 'includes/class-sr-referred-orders-list-table.php';

class Smart_Referrals {

    public function __construct() {
        $this->includes();
        $this->init_hooks();
        $this->init_admin();

        // Enqueue frontend styles and scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );

        // Enqueue admin styles and scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
    }

    private function includes() {
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
        }
    }

    public function enqueue_admin_styles() {
        wp_enqueue_style( 'sr-admin-styles', SR_PLUGIN_URL . 'assets/css/admin-styles.css', array(), '2.2.1' );

        // Enqueue admin scripts
        wp_enqueue_script( 'sr-admin-scripts', SR_PLUGIN_URL . 'assets/js/admin-scripts.js', array( 'jquery' ), '2.2.1', true );

        // Localize script for AJAX
        wp_localize_script( 'sr-admin-scripts', 'srAdminAjax', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'sr_admin_nonce' ),
        ) );
    }

    public function enqueue_frontend_styles() {
        wp_enqueue_style( 'sr-frontend-styles', SR_PLUGIN_URL . 'assets/css/frontend-styles.css', array(), '2.2.1' );

        // Enqueue custom styles for the referral copy link
        wp_enqueue_style( 'sr-referral-copylink-styles', SR_PLUGIN_URL . 'assets/css/referral-copylink.css', array(), '2.2.1' );
    }

    public function enqueue_frontend_scripts() {
        wp_enqueue_script( 'sr-referral-copylink-script', SR_PLUGIN_URL . 'assets/js/referral-copylink.js', array( 'jquery' ), '2.2.1', true );

        // Localize script for translation and data
        wp_localize_script( 'sr-referral-copylink-script', 'srCopyLink', array(
            'copiedText' => __( 'El enlace se copió al portapapeles exitosamente', 'smart-referrals' ),
        ) );
    }

}

new Smart_Referrals();
