<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Admin_Menu {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
    }

    public function add_admin_menu() {
        // Add the main menu
        add_menu_page(
            'Smart Referrals',
            'Smart Referrals',
            'manage_options',
            'sr-dashboard',
            array( 'SR_Dashboard', 'display' ),
            'dashicons-megaphone',
            50
        );

        // Add submenus
        add_submenu_page(
            'sr-dashboard',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'sr-dashboard',
            array( 'SR_Dashboard', 'display' )
        );

        add_submenu_page(
            'sr-dashboard',
            'General Settings',
            'General Settings',
            'manage_options',
            'sr-settings',
            array( 'SR_Settings', 'display' )
        );

        // Check if the referrals module is enabled
        if ( get_option( 'sr_referrals_module_enabled', 'yes' ) === 'yes' ) {
            add_submenu_page(
                'sr-dashboard',
                'Referrals',
                'Referrals',
                'manage_options',
                'sr-referrals',
                array( 'SR_Referrals', 'display' )
            );
        }
    }
}
