<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class SR_Shortcodes {

    public static function register_shortcodes() {
        add_shortcode( 'sr_referralcode_in_session', array( __CLASS__, 'referralcode_in_session_shortcode' ) );
    }

    public static function referral_copylink_shortcode() {
        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $referral_code = get_user_meta( $user_id, 'sr_referral_code', true );
            $parameter = get_option( 'sr_referral_parameter', 'REFERRALCODE' );
            $url = add_query_arg( $parameter, $referral_code, home_url( '/' ) );

            // Generate the HTML output
            ob_start();
            ?>
            <div class="sr-referral-copylink">
                <input type="text" id="sr-referral-link" value="<?php echo esc_url( $url ); ?>" readonly />
                <button id="sr-copy-button">
                    <img src="<?php echo esc_url( 'https://unrealsolutions.com.br/wp-content/uploads/2024/11/copy.svg' ); ?>" alt="Copy" />
                </button>
            </div>
            <?php
            return ob_get_clean();
        }
        return '';
    }

    public static function referralcode_in_session_shortcode() {
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

        if ( $referral_code ) {
            return esc_html( $referral_code );
        }
        return '';
    }

}
