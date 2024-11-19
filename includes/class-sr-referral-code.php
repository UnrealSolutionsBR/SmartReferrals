<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Referral_Code {

    public static function generate_referral_codes_for_existing_users() {
        $users = get_users( array( 'fields' => array( 'ID', 'user_login' ) ) );
        foreach ( $users as $user ) {
            self::generate_referral_code( $user->ID );
        }
    }

    public static function generate_referral_code( $user_id ) {
        $user_info = get_userdata( $user_id );
        $username = strtoupper( substr( $user_info->user_login, 0, 6 ) );
        $random_letters = strtoupper( substr( str_shuffle( 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 3 ) );
        $referral_code = $random_letters . $username;

        update_user_meta( $user_id, 'sr_referral_code', $referral_code );

        // Crear el cupón de WooCommerce
        self::create_woocommerce_coupon( $referral_code );
    }

    public static function create_woocommerce_coupon( $referral_code ) {
        $discount_amount = get_option( 'sr_discount_value', 10 ); // Valor por defecto 10%
        $coupon = new WC_Coupon();
        $coupon->set_code( $referral_code );
        $coupon->set_discount_type( 'percent' );
        $coupon->set_amount( $discount_amount );
        $coupon->set_individual_use( false );
        $coupon->set_usage_limit( '' );
        $coupon->set_usage_limit_per_user( '' );
        $coupon->set_description( 'Cupón generado para el código de referido.' );
        $coupon->save();
    }

    public static function delete_referral_code( $user_id ) {
        $referral_code = get_user_meta( $user_id, 'sr_referral_code', true );
        if ( $referral_code ) {
            // Eliminar el cupón
            $coupon = new WC_Coupon( $referral_code );
            $coupon->delete( true );
            // Eliminar el código de referido
            delete_user_meta( $user_id, 'sr_referral_code' );
        }
    }
}
