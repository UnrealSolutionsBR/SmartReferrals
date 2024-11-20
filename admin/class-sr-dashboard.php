<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Dashboard {

    public static function display() {
        $tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'modules';
        include SR_PLUGIN_DIR . 'admin/templates/dashboard.php';
    }
}
