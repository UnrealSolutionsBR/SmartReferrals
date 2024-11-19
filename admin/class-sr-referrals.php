<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SR_Referrals {

    public static function display() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Referrals', 'smart-referrals' ); ?></h1>
            <p><?php esc_html_e( 'Manage your referrals here.', 'smart-referrals' ); ?></p>
            <!-- Add referrals management content here -->
        </div>
        <?php
    }
}
