<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SR_Admin_Menu {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
    }

    public function add_admin_menu() {
        // Agregar el menú principal
        add_menu_page(
            'Smart Referrals',
            'Smart Referrals',
            'manage_options',
            'sr-dashboard',
            ['SR_Dashboard', 'display'],
            'dashicons-megaphone',
            50
        );

        // Agregar submenús
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

        add_submenu_page(
            'sr-dashboard',
            'Referred Orders',
            'Referred Orders',
            'manage_options',
            'sr-referred-orders',
            ['SR_Referred_Orders', 'display']
        );

        // Registrar la página individual de pedido referido (no aparece en el menú)
        add_submenu_page(
            null, // No se mostrará en el menú
            'Referred Order',
            'Referred Order',
            'manage_options',
            'sr-referred-order',
            ['SR_Referred_Order_Edit', 'display']
        );

        add_submenu_page(
            null, // No se mostrará en el menú directamente
            __('User Settings', 'smart-referrals'),
            __('User Settings', 'smart-referrals'),
            'manage_options',
            'sr-user-settings',
            ['SR_User_Settings', 'display']
        );
    }
}
