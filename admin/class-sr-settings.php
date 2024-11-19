<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Settings {

    public static function display() {
        include SR_PLUGIN_DIR . 'admin/templates/settings.php';
    }
}
