<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_WooCommerce_Integration {

    public static function apply_referral_coupon() {
        if ( isset( $_GET['REFERRALCODE'] ) ) {
            $referral_code = sanitize_text_field( $_GET['REFERRALCODE'] );
            WC()->session->set( 'sr_referral_code', $referral_code );
        }
    }

    public static function add_referral_coupon_to_cart() {
        $referral_code = WC()->session->get( 'sr_referral_code' );
        if ( $referral_code && ! WC()->cart->has_discount( $referral_code ) ) {
            WC()->cart->apply_coupon( $referral_code );
        }
    }
}

add_action( 'woocommerce_before_cart', array( 'SR_WooCommerce_Integration', 'add_referral_coupon_to_cart' ) );
