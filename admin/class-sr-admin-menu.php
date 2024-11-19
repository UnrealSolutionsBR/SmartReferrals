<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Admin_Menu {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
    }

    public function add_admin_menu() {
        add_menu_page(
            'Smart Referrals',
            'Smart Referrals',
            'manage_options',
            'sr-dashboard',
            array( 'SR_Dashboard', 'display' ),
            'dashicons-megaphone',
            6
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

new SR_Admin_Menu();
