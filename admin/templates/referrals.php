<div class="wrap">
    <h1><?php esc_html_e( 'Referrals', 'smart-referrals' ); ?></h1>

    <?php
    $referrals_list_table = new SR_Referrals_List_Table();
    $referrals_list_table->prepare_items();
    ?>

    <form method="post">
        <?php $referrals_list_table->display(); ?>
    </form>
</div>
