<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SR_User_Settings {

    public static function display() {
        if (!isset($_GET['user_id']) || !current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'smart-referrals'));
        }

        $user_id = intval($_GET['user_id']);
        $user = get_user_by('id', $user_id);

        if (!$user) {
            wp_die(__('User not found.', 'smart-referrals'));
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('sr_user_settings_save', 'sr_user_settings_nonce')) {
            $active_status = isset($_POST['active_status']) ? 'yes' : 'no';
            $paypal_email = sanitize_email($_POST['paypal_email'] ?? '');

            update_user_meta($user_id, 'sr_active_status', $active_status);
            update_user_meta($user_id, 'sr_paypal_email', $paypal_email);

            echo '<div class="updated"><p>' . __('Settings saved.', 'smart-referrals') . '</p></div>';
        }

        // Get existing meta values
        $active_status = get_user_meta($user_id, 'sr_active_status', true) === 'yes';
        $paypal_email = get_user_meta($user_id, 'sr_paypal_email', true);
        ?>
        <div class="wrap">
            <h1><?php echo sprintf(__('User Settings: %s', 'smart-referrals'), esc_html($user->display_name)); ?></h1>
            <form method="post">
                <?php wp_nonce_field('sr_user_settings_save', 'sr_user_settings_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="active_status"><?php _e('Active Status', 'smart-referrals'); ?></label></th>
                        <td>
                            <input type="checkbox" name="active_status" id="active_status" <?php checked($active_status); ?> />
                            <p class="description"><?php _e('Enable or disable this user.', 'smart-referrals'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="paypal_email"><?php _e('Paypal Payout Email', 'smart-referrals'); ?></label></th>
                        <td>
                            <input type="email" name="paypal_email" id="paypal_email" value="<?php echo esc_attr($paypal_email); ?>" class="regular-text" />
                            <p class="description"><?php _e('Enter the user\'s Paypal email for payouts.', 'smart-referrals'); ?></p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Save Changes', 'smart-referrals'); ?></button>
                </p>
            </form>
        </div>
        <?php
    }
}
