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
                    'px' => [ 'min' => 50, 'max' => 800 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .sr-referral-input' => 'width: {{SIZE}}{{UNIT}}; box-sizing: border-box; white-space: pre-wrap;',
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 550,
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
                    '{{WRAPPER}} .sr-referral-input' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 50,
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
                'label'     => __( 'Button Background Color', 'smart-referrals' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sr-copy-button' => 'background-color: {{VALUE}};',
                ],
                'default' => '#32373c',
            ]
        );

        $this->add_responsive_control(
            'button_width',
            [
                'label'      => __( 'Width', 'smart-referrals' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px' ],
                'range'      => [
                    '%' => [ 'min' => 5, 'max' => 100 ],
                    'px' => [ 'min' => 20, 'max' => 200 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .sr-copy-button' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 50,
                ],
            ]
        );

        $this->add_responsive_control(
            'button_height',
            [
                'label'      => __( 'Height', 'smart-referrals' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => 20, 'max' => 100 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .sr-copy-button' => 'height: {{SIZE}}{{UNIT}}; display: flex; justify-content: center; align-items: center;',
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 50,
                ],
            ]
        );

        $this->add_control(
            'button_border_type',
            [
                'label'     => __( 'Border Type', 'smart-referrals' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'none'   => __( 'None', 'smart-referrals' ),
                    'solid'  => __( 'Solid', 'smart-referrals' ),
                    'dotted' => __( 'Dotted', 'smart-referrals' ),
                    'dashed' => __( 'Dashed', 'smart-referrals' ),
                    'double' => __( 'Double', 'smart-referrals' ),
                ],
                'default'   => 'none',
                'selectors' => [
                    '{{WRAPPER}} .sr-copy-button' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_border_width',
            [
                'label'      => __( 'Border Width', 'smart-referrals' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px' ],
                'selectors'  => [
                    '{{WRAPPER}} .sr-copy-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'button_border_type!' => 'none',
                ],
            ]
        );

        $this->add_control(
            'button_border_color',
            [
                'label'     => __( 'Border Color', 'smart-referrals' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sr-copy-button' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'button_border_type!' => 'none',
                ],
            ]
        );

        $this->add_control(
            'button_icon_color',
            [
                'label'     => __( 'Icon Color', 'smart-referrals' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sr-copy-button i, {{WRAPPER}} .sr-copy-button svg' => 'fill: {{VALUE}};',
                ],
                'default' => '#fff',
            ]
        );

        $this->add_responsive_control(
            'button_icon_size',
            [
                'label'      => __( 'Icon Size', 'smart-referrals' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range'      => [
                    'px' => [ 'min' => 10, 'max' => 100 ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .sr-copy-button i, {{WRAPPER}} .sr-copy-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 25,
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

            echo '<div class="sr-referral-copylink" style="display: flex; align-items: center; gap: 10px;">';
            echo '<span id="sr-referral-link" class="sr-referral-input" style="overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; max-width: calc(100% - 50px);">' . esc_url( $url ) . '</span>';
            echo '<button id="sr-copy-button" class="sr-copy-button" style="flex-shrink: 0;">';

            if ( ! empty( $settings['icon'] ) ) {
                \Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
            }

            echo '</button>';
            echo '</div>';
        } else {
            echo '<p>' . __( 'Please log in to see your referral URL.', 'smart-referrals' ) . '</p>';
        }
    }
}
