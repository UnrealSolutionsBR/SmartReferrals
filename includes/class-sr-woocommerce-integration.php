<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class SR_WooCommerce_Integration {

    public static function init() {
        // Hooks
        add_action( 'template_redirect', array( __CLASS__, 'apply_referral_coupon' ) );
        add_action( 'woocommerce_before_cart', array( __CLASS__, 'add_referral_coupon_to_cart' ) );
        add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'add_referral_coupon_to_cart' ) );
        add_filter( 'woocommerce_coupon_is_valid_for_user', array( __CLASS__, 'validate_referral_coupon_for_user' ), 10, 3 );

        // Encolar el script para mostrar la notificación en el frontend
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_referral_error_script' ) );
    }

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
        $user_id = get_current_user_id();

        // Verificar si el cupón es un código de referido usando el metadato '_sr_referral_coupon'
        $is_referral_coupon = $coupon->get_meta( '_sr_referral_coupon', true );

        if ( $is_referral_coupon === 'yes' ) {
            // Verificar si el usuario ya ha usado algún código de referido en pedidos anteriores
            $has_used_referral_coupon = self::has_used_referral_coupon_before( $user_id );

            if ( $has_used_referral_coupon ) {
                // El usuario ya ha usado un código de referido
                // Guardar el mensaje de error en una variable de sesión
                if ( ! session_id() ) {
                    session_start();
                }
                $_SESSION['sr_referral_code_error'] = __( 'Ya has utilizado un código de referido anteriormente y no puedes usar otro.', 'smart-referrals' );

                // Remover el cupón del carrito si está aplicado
                if ( WC()->cart->has_discount( $coupon->get_code() ) ) {
                    WC()->cart->remove_coupon( $coupon->get_code() );
                }

                // Devolver false para indicar que el cupón no es válido
                return false;
            }
        }

        return $valid;
    }

    // Nueva función para verificar si el usuario ha usado un código de referido antes
    public static function has_used_referral_coupon_before( $user_id ) {
        if ( ! $user_id ) {
            return false;
        }

        // Obtener los pedidos completados del usuario
        $customer_orders = wc_get_orders( array(
            'customer_id' => $user_id,
            'status'      => array( 'wc-completed', 'wc-processing', 'wc-on-hold' ), // Puedes ajustar los estados según tus necesidades
            'limit'       => -1, // Sin límite
        ) );

        if ( ! empty( $customer_orders ) ) {
            foreach ( $customer_orders as $order ) {
                $used_coupons = $order->get_coupon_codes();
                foreach ( $used_coupons as $coupon_code ) {
                    $coupon = new WC_Coupon( $coupon_code );
                    $is_referral_coupon = $coupon->get_meta( '_sr_referral_coupon', true );
                    if ( $is_referral_coupon === 'yes' ) {
                        // El usuario ha usado un código de referido antes
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public static function enqueue_referral_error_script() {
        // Solo en páginas de carrito y checkout
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

            // Encolar estilos si es necesario
            wp_enqueue_style( 'sr-referral-error-styles', SR_PLUGIN_URL . 'assets/css/referral-error.css', array(), '1.0' );
        }
    }

}

// Inicializar la clase y registrar los hooks
SR_WooCommerce_Integration::init();
