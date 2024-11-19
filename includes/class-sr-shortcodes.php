<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Shortcodes {

    public static function register_shortcodes() {
        add_shortcode( 'sr_referral_url', array( __CLASS__, 'referral_url_shortcode' ) );
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
}
