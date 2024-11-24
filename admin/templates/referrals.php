<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
    <h1><?php esc_html_e( 'Referrals', 'smart-referrals' ); ?></h1>

    <?php
    require_once SR_PLUGIN_DIR . 'admin/includes/class-sr-referrals-list-table.php';

    $referrals_list_table = new SR_Referrals_List_Table();
    $referrals_list_table->prepare_items();
    ?>

    <form method="post">
        <?php
        $referrals_list_table->display();
        ?>
    </form>
</div>
