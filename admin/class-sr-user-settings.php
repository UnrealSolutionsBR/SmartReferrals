<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SR_User_Settings {

    public static function display() {
        if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
            wp_die(__('Invalid user ID.', 'smart-referrals'));
        }

        $user_id = intval($_GET['user_id']);
        $user = get_userdata($user_id);

        if (!$user) {
            wp_die(__('User not found.', 'smart-referrals'));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('sr_user_settings')) {
            $is_active = isset($_POST['sr_user_active']) ? 'yes' : 'no';
            update_user_meta($user_id, 'sr_user_active', $is_active);

            echo '<div class="updated"><p>' . __('Settings updated.', 'smart-referrals') . '</p></div>';
        }

        $is_active = get_user_meta($user_id, 'sr_user_active', true) === 'yes';

        ?>
        <div class="wrap">
            <h1><?php echo esc_html($user->display_name); ?></h1>
            <form method="post">
                <?php wp_nonce_field('sr_user_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e('Active Status', 'smart-referrals'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="sr_user_active" <?php checked($is_active); ?> />
                                <?php esc_html_e('Activate this user', 'smart-referrals'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
                <?php submit_button(__('Save Changes', 'smart-referrals')); ?>
            </form>
        </div>
        <?php
    }
}
