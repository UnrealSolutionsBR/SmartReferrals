<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Dashboard {

    public static function display() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Smart Referrals Dashboard', 'smart-referrals' ); ?></h1>
            <p><?php esc_html_e( 'Welcome to the Smart Referrals plugin dashboard.', 'smart-referrals' ); ?></p>
            <!-- Add dashboard content here -->
        </div>
        <?php
    }
}
