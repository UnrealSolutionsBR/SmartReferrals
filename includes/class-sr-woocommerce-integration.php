<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class SR_WooCommerce_Integration {

    public static function apply_referral_coupon() {
        $parameter = get_option( 'sr_referral_parameter', 'REFERRALCODE' );
        if ( isset( $_GET[ $parameter ] ) ) {
            $referral_code = sanitize_text_field( $_GET[ $parameter ] );

            // Save in WooCommerce session if available
            if ( class_exists( 'WooCommerce' ) && isset( WC()->session ) ) {
                WC()->session->set( 'sr_referral_code', $referral_code );
            }

            // Start PHP session if not started
            if ( ! session_id() ) {
                session_start();
            }
            $_SESSION['sr_referral_code'] = $referral_code;

            // Set cookie with expiration of 30 days
            setcookie( 'sr_referral_code', $referral_code, time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );
        }
    }

    public static function add_referral_coupon_to_cart() {
        $referral_code = null;

        // Try to get the code from WooCommerce session
        if ( class_exists( 'WooCommerce' ) && isset( WC()->session ) ) {
            $referral_code = WC()->session->get( 'sr_referral_code' );
        }

        // If not in WooCommerce session, try PHP session
        if ( ! $referral_code ) {
            if ( ! session_id() ) {
                session_start();
            }
            if ( isset( $_SESSION['sr_referral_code'] ) ) {
                $referral_code = $_SESSION['sr_referral_code'];
            }
        }

        // If not in PHP session, try cookie
        if ( ! $referral_code && isset( $_COOKIE['sr_referral_code'] ) ) {
            $referral_code = sanitize_text_field( $_COOKIE['sr_referral_code'] );
        }

        // Apply the coupon if not already applied
        if ( $referral_code && class_exists( 'WooCommerce' ) && WC()->cart ) {
            if ( ! WC()->cart->has_discount( $referral_code ) ) {
                WC()->cart->apply_coupon( $referral_code );
            }
        }
    }

    public static function validate_referral_coupon_for_user( $valid, $coupon, $user ) {
        $user_id = $user->ID;

        // Check if the coupon being applied is a referral code coupon
        $is_referral_coupon = $coupon->get_meta( '_sr_referral_coupon', true );

        if ( $is_referral_coupon === 'yes' ) {
            // Check if the user has already used a referral coupon
            $has_used_referral_coupon = get_user_meta( $user_id, '_sr_used_referral_coupon', true );

            if ( $has_used_referral_coupon === 'yes' ) {
                // The user has already used a referral code coupon
                wc_add_notice( __( 'You have already used a referral code coupon and cannot use another one.', 'smart-referrals' ), 'error' );
                return false;
            }
        }

        return $valid;
    }

}

// Hooks
add_action( 'init', array( 'SR_WooCommerce_Integration', 'apply_referral_coupon' ) );
add_action( 'woocommerce_before_cart', array( 'SR_WooCommerce_Integration', 'add_referral_coupon_to_cart' ) );
add_action( 'woocommerce_before_checkout_form', array( 'SR_WooCommerce_Integration', 'add_referral_coupon_to_cart' ) );
add_filter( 'woocommerce_coupon_is_valid_for_user', array( 'SR_WooCommerce_Integration', 'validate_referral_coupon_for_user' ), 10, 3 );

// Hook to order completion to mark user as having used a referral coupon
add_action( 'woocommerce_payment_complete', 'sr_mark_user_used_referral_coupon' );

function sr_mark_user_used_referral_coupon( $order_id ) {
    $order = wc_get_order( $order_id );
    $user_id = $order->get_user_id();

    if ( $user_id ) {
        $used_coupons = $order->get_coupon_codes();

        foreach ( $used_coupons as $coupon_code ) {
            $coupon = new WC_Coupon( $coupon_code );
            $is_referral_coupon = $coupon->get_meta( '_sr_referral_coupon', true );

            if ( $is_referral_coupon === 'yes' ) {
                update_user_meta( $user_id, '_sr_used_referral_coupon', 'yes' );
                break; // No need to check further coupons
            }
        }
    }
}
