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
        // Content Section: Icon
        $this->start_controls_section(
            'content_section',
            [
                'label' => __( 'Content', 'smart-referrals' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'icon',
            [
                'label'   => __( 'Icon', 'smart-referrals' ),
                'type'    => Controls_Manager::ICONS,
                'default' => [
                    'value'   => 'fas fa-copy',
                    'library' => 'fa-solid',
                ],
                'recommended' => [
                    'fa-solid'  => [ 'copy', 'link', 'share' ],
                    'fa-regular' => [],
                    'fa-brands' => [],
                ],
            ]
        );

        $this->end_controls_section();

        // Input Styles
        $this->start_controls_section(
            'input_styles',
            [
                'label' => __( 'Input Styles', 'smart-referrals' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'input_width',
            [
                'label'      => __( 'Width', 'smart-referrals' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px' ],
                'range'      => [
                    '%' => [ 'min' => 10, 'max' => 100 ],
                    'px' => [ 'min' => 50, 'max' => 500 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} #sr-referral-link' => 'width: {{SIZE}}{{UNIT}} !important; box-sizing: border-box;',
                ],
            ]
        );

        $this->add_responsive_control(
            'input_height',
            [
                'label'      => __( 'Height', 'smart-referrals' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => 20, 'max' => 100 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} #sr-referral-link' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'input_align',
            [
                'label'     => __( 'Align', 'smart-referrals' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => __( 'Left', 'smart-referrals' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'smart-referrals' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => __( 'Right', 'smart-referrals' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .sr-referral-copylink' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'input_padding',
            [
                'label'      => __( 'Padding', 'smart-referrals' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} #sr-referral-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Button Styles
        $this->start_controls_section(
            'button_styles',
            [
                'label' => __( 'Button Styles', 'smart-referrals' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_color',
            [
                'label'     => __( 'Button Color', 'smart-referrals' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #sr-copy-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_icon_size',
            [
                'label'      => __( 'Icon Size', 'smart-referrals' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => 10, 'max' => 100 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} #sr-copy-button i, {{WRAPPER}} #sr-copy-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label'      => __( 'Padding', 'smart-referrals' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} #sr-copy-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_margin',
            [
                'label'      => __( 'Margin', 'smart-referrals' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} #sr-copy-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $referral_code = get_user_meta( $user_id, 'sr_referral_code', true );
            $parameter = get_option( 'sr_referral_parameter', 'REFERRALCODE' );
            $url = add_query_arg( $parameter, $referral_code, home_url( '/' ) );

            echo '<div class="sr-referral-copylink">';
            echo '<input type="text" id="sr-referral-link" value="' . esc_url( $url ) . '" readonly />';
            echo '<button id="sr-copy-button">';

            if ( ! empty( $settings['icon'] ) ) {
                \Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
            } else {
                echo '<img src="' . esc_url( SR_PLUGIN_URL . 'assets/images/copy-icon.svg' ) . '" alt="Copy" />';
            }

            echo '</button>';
            echo '</div>';
        } else {
            echo '<p>' . __( 'Please log in to see your referral URL.', 'smart-referrals' ) . '</p>';
        }
    }
}
