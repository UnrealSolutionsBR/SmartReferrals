<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class SR_Referral_URL_Widget extends Widget_Base {

    public function get_name() {
        return 'sr_referral_url';
    }

    public function get_title() {
        return __( 'Referral URL', 'smart-referrals' );
    }

    public function get_icon() {
        return 'eicon-link';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {
        // Here you can add controls for customizing the widget if needed
    }

    protected function render() {
        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $referral_code = get_user_meta( $user_id, 'sr_referral_code', true );
            $parameter = get_option( 'sr_referral_parameter', 'REFERRALCODE' );
            $url = add_query_arg( $parameter, $referral_code, home_url( '/' ) );

            echo '<div class="sr-referral-copylink">';
            echo '<input type="text" id="sr-referral-link" value="' . esc_url( $url ) . '" readonly />';
            echo '<button id="sr-copy-button">';
            echo '<img src="' . esc_url( 'https://unrealsolutions.com.br/wp-content/uploads/2024/11/copy.svg' ) . '" alt="Copy" />';
            echo '</button>';
            echo '</div>';
        } else {
            echo '<p>' . __( 'Please log in to see your referral URL.', 'smart-referrals' ) . '</p>';
        }
    }

    protected function content_template() {
        // Optional: Add JS template if needed
    }
}
