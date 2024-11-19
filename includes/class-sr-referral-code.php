<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class SR_Referral_Code {

    public static function generate_referral_codes_for_existing_users() {
        $users = get_users( array( 'fields' => array( 'ID', 'user_login' ) ) );
        foreach ( $users as $user ) {
            self::generate_referral_code( $user->ID );
        }
    }

    public static function generate_referral_code( $user_id ) {
        // Get the old referral code
        $old_referral_code = get_user_meta( $user_id, 'sr_referral_code', true );

        // Delete the old coupon if it exists
        if ( $old_referral_code ) {
            $old_coupon = new WC_Coupon( $old_referral_code );
            if ( $old_coupon->get_id() ) {
                $old_coupon->delete( true );
            }
        }

        // Generate the new referral code
        $user_info = get_userdata( $user_id );
        $username = strtoupper( substr( $user_info->user_login, 0, 6 ) );
        $random_letters = strtoupper( substr( str_shuffle( 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 3 ) );
        $referral_code = $random_letters . $username;

        // Update the user meta with the new code
        update_user_meta( $user_id, 'sr_referral_code', $referral_code );

        // Create or update the WooCommerce coupon
        self::create_or_update_woocommerce_coupon( $referral_code, $user_id );
    }

    public static function create_or_update_woocommerce_coupon( $referral_code, $user_id ) {
        $discount_amount = get_option( 'sr_discount_value', 10 ); // Default value 10%
        $coupon = new WC_Coupon( $referral_code );

        // Set coupon properties
        $coupon->set_discount_type( 'percent' );
        $coupon->set_amount( $discount_amount );
        $coupon->set_individual_use( false );
        $coupon->set_usage_limit( '' ); // No overall usage limit
        $coupon->set_usage_limit_per_user( 1 ); // Limit per user to 1
        $coupon->set_description( 'Coupon generated for the referral code.' );

        // Mark as a referral coupon
        $coupon->update_meta_data( '_sr_referral_coupon', 'yes' );

        // Save the coupon
        $coupon->save();
    }

    public static function delete_referral_code( $user_id ) {
        $referral_code = get_user_meta( $user_id, 'sr_referral_code', true );
        if ( $referral_code ) {
            // Delete the coupon
            $coupon = new WC_Coupon( $referral_code );
            if ( $coupon->get_id() ) {
                $coupon->delete( true );
            }
            // Delete the referral code
            delete_user_meta( $user_id, 'sr_referral_code' );
        }
    }
}
