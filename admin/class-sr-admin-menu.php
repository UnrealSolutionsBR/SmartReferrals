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
            'Smart Referrals',                   // Page title
            'Smart Referrals',                   // Menu title
            'manage_options',                    // Capability
            'sr-dashboard',                      // Menu slug
            array( 'SR_Dashboard', 'display' ),  // Callback function
            'dashicons-megaphone',               // Menu icon
            6                                    // Position
        );

        // Add submenus
        add_submenu_page(
            'sr-dashboard',                      // Parent slug
            'Dashboard',                         // Page title
            'Dashboard',                         // Submenu title
            'manage_options',                    // Capability
            'sr-dashboard',                      // Submenu slug
            array( 'SR_Dashboard', 'display' )   // Callback function
        );

        add_submenu_page(
            'sr-dashboard',
            'General Settings',
            'General Settings',
            'manage_options',
            'sr-settings',
            array( 'SR_Settings', 'display' )
        );

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
