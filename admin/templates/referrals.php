<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Referrals', 'smart-referrals' ); ?></h1>

    <form method="get">
        <input type="hidden" name="page" value="sr-referrals" />
        <?php
        $table = new SR_Referrals_List_Table();
        $table->prepare_items();
        $table->search_box( __( 'Search Referrals', 'smart-referrals' ), 'search_id' );
        $table->display();
        ?>
    </form>
</div>
