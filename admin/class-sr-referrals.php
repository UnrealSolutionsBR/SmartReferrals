<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Referrals {

    public static function display() {
        include SR_PLUGIN_DIR . 'admin/templates/referrals.php';
    }
}
