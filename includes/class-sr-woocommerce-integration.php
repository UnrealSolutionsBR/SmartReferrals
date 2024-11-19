<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_WooCommerce_Integration {

    public static function validate_referral_coupon_for_user( $valid, $coupon, $user ) {
        $user_id = $user->ID;

        // Check if the coupon is a referral code coupon
        $is_referral_coupon = $coupon->get_meta( '_sr_referral_coupon' );

        if ( $is_referral_coupon === 'yes' ) {
            // Check if the user has already used any referral code coupon before

            // Get all orders of the user
            $args = array(
                'customer_id' => $user_id,
                'status' => array( 'wc-completed', 'wc-processing', 'wc-on-hold', 'wc-pending' ),
                'limit' => -1,
            );
            $orders = wc_get_orders( $args );

            foreach ( $orders as $order ) {
                $used_coupons = $order->get_coupon_codes();

                foreach ( $used_coupons as $used_coupon_code ) {
                    // Get the coupon object
                    $used_coupon = new WC_Coupon( $used_coupon_code );

                    // Check if this coupon is a referral code coupon
                    $used_coupon_is_referral = $used_coupon->get_meta( '_sr_referral_coupon' );

                    if ( $used_coupon_is_referral === 'yes' ) {
                        // The user has already used a referral code coupon
                        wc_add_notice( __( 'You have already used a referral code coupon and cannot use another one.', 'smart-referrals' ), 'error' );
                        return false;
                    }
                }
            }
        }

        return $valid;
    }

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
// Add the filter to validate the coupon for the user
add_filter( 'woocommerce_coupon_is_valid_for_user', array( 'SR_WooCommerce_Integration', 'validate_referral_coupon_for_user' ), 10, 3 );
