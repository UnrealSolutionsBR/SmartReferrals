<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Ajax_Handler {

    public static function init() {
        add_action( 'wp_ajax_sr_toggle_module', array( __CLASS__, 'toggle_module' ) );
        add_action( 'wp_ajax_sr_update_admin_menu', array( __CLASS__, 'update_admin_menu' ) );
    }

    public static function toggle_module() {
        check_ajax_referer( 'sr_admin_nonce', 'security' );

        $enabled = isset( $_POST['enabled'] ) && $_POST['enabled'] === 'yes' ? 'yes' : 'no';
        update_option( 'sr_referrals_module_enabled', $enabled );

        // Activate or deactivate module functions
        if ( $enabled === 'yes' ) {
            SR_Referrals::activate_module();
        } else {
            SR_Referrals::deactivate_module();
        }

        wp_send_json_success();
    }

    public static function update_admin_menu() {
        check_ajax_referer( 'sr_admin_nonce', 'security' );

        // Rebuild the admin menu
        global $menu, $submenu;
        ob_start();
        require( ABSPATH . 'wp-admin/menu.php' );
        require( ABSPATH . 'wp-admin/includes/menu.php' );
        echo '<ul id="adminmenu">';
        _wp_menu_output( $menu, $submenu );
        echo '</ul>';
        $menu_html = ob_get_clean();

        echo $menu_html;
        wp_die();
    }
}

SR_Ajax_Handler::init();
