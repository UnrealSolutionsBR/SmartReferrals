<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Shortcodes {

    public static function register_shortcodes() {
        add_shortcode( 'sr_referral_url', array( __CLASS__, 'referral_url_shortcode' ) );
        add_shortcode( 'sr_referralcode_in_session', array( __CLASS__, 'referralcode_in_session_shortcode' ) );
    }

    public static function referral_url_shortcode() {
        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $referral_code = get_user_meta( $user_id, 'sr_referral_code', true );
            $parameter = get_option( 'sr_referral_parameter', 'REFERRALCODE' );
            $url = add_query_arg( $parameter, $referral_code, home_url( '/' ) );
            return esc_url( $url );
        }
        return '';
    }

    public static function referralcode_in_session_shortcode() {
        $referral_code = null;

        // Intentar obtener el código de la sesión de WooCommerce
        if ( class_exists( 'WooCommerce' ) && isset( WC()->session ) ) {
            $referral_code = WC()->session->get( 'sr_referral_code' );
        }

        // Si no está en la sesión de WooCommerce, intentar obtenerlo de la sesión PHP
        if ( ! $referral_code ) {
            if ( ! session_id() ) {
                session_start();
            }
            if ( isset( $_SESSION['sr_referral_code'] ) ) {
                $referral_code = $_SESSION['sr_referral_code'];
            }
        }

        // Si no está en la sesión PHP, intentar obtenerlo de la cookie
        if ( ! $referral_code && isset( $_COOKIE['sr_referral_code'] ) ) {
            $referral_code = sanitize_text_field( $_COOKIE['sr_referral_code'] );
        }

        if ( $referral_code ) {
            return esc_html( $referral_code );
        }
        return '';
    }

}