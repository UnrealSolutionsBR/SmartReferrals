<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class SR_WooCommerce_Integration {

    public static function init() {
        // Hooks
        add_action( 'template_redirect', array( __CLASS__, 'apply_referral_coupon' ) );
        add_action( 'woocommerce_before_cart', array( __CLASS__, 'add_referral_coupon_to_cart' ) );
        add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'add_referral_coupon_to_cart' ) );
        add_filter( 'woocommerce_coupon_is_valid_for_user', array( __CLASS__, 'validate_referral_coupon_for_user' ), 10, 3 );

        // Hook to handle order completion in various statuses
        add_action( 'woocommerce_order_status_completed', array( __CLASS__, 'handle_referral_after_purchase' ) );
        add_action( 'woocommerce_order_status_processing', array( __CLASS__, 'handle_referral_after_purchase' ) );
        add_action( 'woocommerce_order_status_on-hold', array( __CLASS__, 'handle_referral_after_purchase' ) );

        // Enqueue the script for displaying referral errors
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_referral_error_script' ) );
    }

    public static function apply_referral_coupon() {
        $parameter = get_option( 'sr_referral_parameter', 'REFERRALCODE' );
        if ( isset( $_GET[ $parameter ] ) ) {
            $referral_code = sanitize_text_field( $_GET[ $parameter ] );

            // Check if the user is blocked from storing referral codes
            if ( is_user_logged_in() && get_user_meta( get_current_user_id(), 'sr_referral_blocked', true ) ) {
                return; // Block storing the referral code
            }

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
        $user_id = get_current_user_id();

        // Check if the coupon is a referral coupon using the meta '_sr_referral_coupon'
        $is_referral_coupon = $coupon->get_meta( '_sr_referral_coupon', true );

        if ( $is_referral_coupon === 'yes' ) {
            // Check if the user is blocked from using referral codes
            $has_used_referral_coupon = get_user_meta( $user_id, 'sr_referral_blocked', true );

            if ( $has_used_referral_coupon ) {
                // Set error message for blocked user
                if ( ! session_id() ) {
                    session_start();
                }
                $_SESSION['sr_referral_code_error'] = __( 'You have already used a referral code before and cannot use another.', 'smart-referrals' );

                // Remove the coupon from the cart if applied
                if ( WC()->cart->has_discount( $coupon->get_code() ) ) {
                    WC()->cart->remove_coupon( $coupon->get_code() );
                }

                // Return false to indicate the coupon is not valid
                return false;
            }
        }

        return $valid;
    }

    public static function handle_referral_after_purchase( $order_id ) {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return;
        }

        $user_id = $order->get_user_id();

        if ( ! $user_id ) {
            return;
        }

        // Check if the order used a referral coupon
        foreach ( $order->get_used_coupons() as $coupon_code ) {
            $coupon = new WC_Coupon( $coupon_code );

            if ( 'yes' === $coupon->get_meta( '_sr_referral_coupon', true ) ) {
                // Block future referral code storage and usage
                update_user_meta( $user_id, 'sr_referral_blocked', true );

                // Clear referral code from session, WooCommerce session, and cookies
                if ( class_exists( 'WooCommerce' ) && isset( WC()->session ) ) {
                    WC()->session->__unset( 'sr_referral_code' );
                }

                if ( ! session_id() ) {
                    session_start();
                }

                unset( $_SESSION['sr_referral_code'] );
                setcookie( 'sr_referral_code', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN );

                break;
            }
        }
    }

    public static function enqueue_referral_error_script() {
        // Only on cart and checkout pages
        if ( is_cart() || is_checkout() ) {
            wp_enqueue_script( 'sr-referral-error-script', SR_PLUGIN_URL . 'assets/js/referral-error.js', array( 'jquery' ), '1.0', true );

            $error_message = '';
            if ( ! session_id() ) {
                session_start();
            }
            if ( isset( $_SESSION['sr_referral_code_error'] ) ) {
                $error_message = $_SESSION['sr_referral_code_error'];
                unset( $_SESSION['sr_referral_code_error'] );
            }

            wp_localize_script( 'sr-referral-error-script', 'srReferralError', array(
                'errorMessage' => $error_message,
            ) );

            // Enqueue styles if necessary
            wp_enqueue_style( 'sr-referral-error-styles', SR_PLUGIN_URL . 'assets/css/referral-error.css', array(), '1.0' );
        }
    }

}

SR_WooCommerce_Integration::init();
