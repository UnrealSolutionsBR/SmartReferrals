<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SR_Referred_Orders {

    public static function display() {
        require_once SR_PLUGIN_DIR . 'includes/class-sr-referred-orders-list-table.php';

        $orders_table = new SR_Referred_Orders_List_Table();
        $orders_table->prepare_items();

        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php esc_html_e('Referred Orders', 'smart-referrals'); ?></h1>
            <form method="post">
                <?php $orders_table->display(); ?>
            </form>
        </div>
        <?php
    }
}