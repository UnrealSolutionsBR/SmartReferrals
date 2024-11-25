<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SR_Admin_Menu {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
    }

    public function add_admin_menu() {
        // Add the main menu
        add_menu_page(
            'Smart Referrals',
            'Smart Referrals',
            'manage_options',
            'sr-dashboard',
            ['SR_Dashboard', 'display'],
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
            ['SR_Dashboard', 'display']
        );

        add_submenu_page(
            'sr-dashboard',
            'Referrals',
            'Referrals',
            'manage_options',
            'sr-referrals',
            ['SR_Referrals', 'display']
        );
    }
}
