<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Admin_Menu {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
    }

    public function add_admin_menu() {
        // Tu código aquí...
    }
}

// Elimina la instanciación de la clase aquí
// new SR_Admin_Menu();
