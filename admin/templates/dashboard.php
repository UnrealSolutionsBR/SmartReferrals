<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
    <h1><?php esc_html_e( 'Smart Referrals Dashboard', 'smart-referrals' ); ?></h1>
    <h2 class="nav-tab-wrapper">
        <a href="?page=sr-dashboard&tab=modules" class="nav-tab <?php echo $tab == 'modules' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Modules', 'smart-referrals' ); ?></a>
        <a href="?page=sr-dashboard&tab=help" class="nav-tab <?php echo $tab == 'help' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Help', 'smart-referrals' ); ?></a>
    </h2>
    <?php
    if ( $tab == 'modules' ) {
        include SR_PLUGIN_DIR . 'admin/templates/modules.php';
    } else {
        include SR_PLUGIN_DIR . 'admin/templates/help.php';
    }
    ?>
</div>
