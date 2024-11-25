<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SR_Referrals {

    public static function display() {
        // Render the referrals list table
        require_once SR_PLUGIN_DIR . 'includes/class-sr-referrals-list-table.php';

        $referrals_table = new SR_Referrals_List_Table();
        $referrals_table->prepare_items();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php esc_html_e('Referrals', 'smart-referrals'); ?></h1>

            <!-- Search form -->
            <form method="get">
                <input type="hidden" name="page" value="sr-referrals" />
                <?php $referrals_table->search_box(__('Search Referrals', 'smart-referrals'), 'search_id'); ?>
            </form>

            <!-- Referrals table -->
            <form method="post">
                <?php $referrals_table->display(); ?>
            </form>
        </div>
        <?php
    }
}
