<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_WooCommerce_Integration {

    public static function apply_referral_coupon() {
        $parameter = get_option( 'sr_referral_parameter', 'REFERRALCODE' );
        if ( isset( $_GET[ $parameter ] ) ) {
            $referral_code = sanitize_text_field( $_GET[ $parameter ] );

            // Guardar en la sesión de WooCommerce si está disponible
            if ( class_exists( 'WooCommerce' ) && isset( WC()->session ) ) {
                WC()->session->set( 'sr_referral_code', $referral_code );
            }

            // Si la sesión PHP está disponible
            if ( ! session_id() ) {
                session_start();
            }
            $_SESSION['sr_referral_code'] = $referral_code;

            // Guardar en una cookie con expiración de 30 días
            setcookie( 'sr_referral_code', $referral_code, time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );
        }
    }

    public static function add_referral_coupon_to_cart() {
        $referral_code = null;

        // Intentar obtener el código de la sesión de WooCommerce
        if ( class_exists( 'WooCommerce' ) && isset( WC()->session ) ) {
            $referral_code = WC()->session->get( 'sr_referral_code' );
        }

        // Si no está en la sesión de WooCommerce, intentar obtenerlo de la sesión PHP
        if ( ! $referral_code && isset( $_SESSION['sr_referral_code'] ) ) {
            $referral_code = $_SESSION['sr_referral_code'];
        }

        // Si no está en la sesión PHP, intentar obtenerlo de la cookie
        if ( ! $referral_code && isset( $_COOKIE['sr_referral_code'] ) ) {
            $referral_code = sanitize_text_field( $_COOKIE['sr_referral_code'] );
        }

        // Aplicar el cupón si no se ha aplicado ya
        if ( $referral_code && class_exists( 'WooCommerce' ) && ! WC()->cart->has_discount( $referral_code ) ) {
            WC()->cart->apply_coupon( $referral_code );
        }
    }
}

add_action( 'init', array( 'SR_WooCommerce_Integration', 'apply_referral_coupon' ) );
add_action( 'woocommerce_before_cart', array( 'SR_WooCommerce_Integration', 'add_referral_coupon_to_cart' ) );
add_action( 'woocommerce_before_checkout_form', array( 'SR_WooCommerce_Integration', 'add_referral_coupon_to_cart' ) );
