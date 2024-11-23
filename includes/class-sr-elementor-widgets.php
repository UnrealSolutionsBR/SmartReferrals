<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class SR_Elementor_Widgets {

    public static function init() {
        add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets' ) );
    }

    public static function register_widgets( $widgets_manager ) {
        require_once SR_PLUGIN_DIR . 'includes/widgets/class-sr-referral-url-widget.php';
        $widgets_manager->register( new \SR_Referral_URL_Widget() );
    }
}

SR_Elementor_Widgets::init();
