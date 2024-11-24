<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class SR_Admin_Menu {
    require_once SR_PLUGIN_DIR . 'includes/class-sr-referrals-list-table.php';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_filter( 'set-screen-option', [ $this, 'set_screen_option' ], 10, 3 );
    }

    public function add_admin_menu() {
        // Agregar el menú principal y submenús
        add_menu_page(
            'Smart Referrals',
            'Smart Referrals',
            'manage_options',
            'sr-dashboard',
            [ 'SR_Dashboard', 'display' ],
            'dashicons-megaphone',
            50
        );

        add_submenu_page(
            'sr-dashboard',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'sr-dashboard',
            [ 'SR_Dashboard', 'display' ]
        );

        add_submenu_page(
            'sr-dashboard',
            'General Settings',
            'General Settings',
            'manage_options',
            'sr-settings',
            [ 'SR_Settings', 'display' ]
        );
        add_submenu_page(
            null, // No se mostrará en el menú directamente
            __('User Settings', 'smart-referrals'),
            __('User Settings', 'smart-referrals'),
            'manage_options',
            'sr-user-settings',
            ['SR_User_Settings', 'display']
        );

        // Agregar la página "Referrals"
        $hook = add_submenu_page(
            'sr-dashboard',
            __( 'Referrals', 'smart-referrals' ),
            __( 'Referrals', 'smart-referrals' ),
            'manage_options',
            'sr-referrals',
            function() {
                include SR_PLUGIN_DIR . 'admin/templates/referrals.php';
            }
        );

        // Configurar opciones de pantalla para "Referrals"
        add_action( "load-$hook", function() {
            add_screen_option( 'per_page', [
                'label'   => __( 'Referrals per page', 'smart-referrals' ),
                'default' => 20,
                'option'  => 'referrals_per_page',
            ] );
        } );
    }

    public function set_screen_option( $status, $option, $value ) {
        if ( 'referrals_per_page' === $option ) {
            return $value;
        }
        return $status;
    }
}
