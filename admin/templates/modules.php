<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="sr-modules-grid">
    <div class="sr-module-card">
        <img src="<?php echo SR_PLUGIN_URL . 'assets/images/referral-code-management.png'; ?>" alt="">
        <h3><?php esc_html_e( 'Referral Code Management', 'smart-referrals' ); ?></h3>
        <p><?php esc_html_e( 'Manage your referral codes easily.', 'smart-referrals' ); ?></p>
        <label class="sr-switch">
            <?php $module_enabled = get_option( 'sr_referrals_module_enabled', 'yes' ); ?>
            <input type="checkbox" id="sr-module-toggle" <?php checked( $module_enabled, 'yes' ); ?>>
            <span class="sr-slider round"></span>
            <!-- Loading circle -->
            <div class="sr-loading-circle" style="display: none;"></div>
        </label>
    </div>
    <!-- Add more modules if needed -->
</div>
