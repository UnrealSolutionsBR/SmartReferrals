<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Admin_Menu {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
    }

    public function add_admin_menu() {
        // Añadir el menú principal
        add_menu_page(
            'Smart Referrals',                   // Título de la página
            'Smart Referrals',                   // Texto del menú
            'manage_options',                    // Capacidad requerida
            'sr-dashboard',                      // Slug del menú
            array( 'SR_Dashboard', 'display' ),  // Función de callback
            'dashicons-megaphone',               // Icono del menú
            6                                    // Posición en el menú
        );

        // Añadir submenús
        add_submenu_page(
            'sr-dashboard',                      // Slug del menú padre
            'Dashboard',                         // Título de la página
            'Dashboard',                         // Texto del submenú
            'manage_options',                    // Capacidad requerida
            'sr-dashboard',                      // Slug del submenú
            array( 'SR_Dashboard', 'display' )   // Función de callback
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
