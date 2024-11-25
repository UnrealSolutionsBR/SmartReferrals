<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class SR_User_Settings {

    public static function display() {
        if ( ! isset( $_GET['user_id'] ) || ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have permission to access this page.', 'smart-referrals' ) );
        }

        $user_id = intval( $_GET['user_id'] );
        $status = get_user_meta( $user_id, 'sr_active_status', true );

        // Si el estado no está definido, lo configuramos como activo.
        if ( empty( $status ) ) {
            $status = 'active';
            update_user_meta( $user_id, 'sr_active_status', $status );
        }

        if ( isset( $_POST['submit'] ) && check_admin_referer( 'sr_user_settings_action', 'sr_user_settings_nonce' ) ) {
            $new_status = isset( $_POST['sr_active_status'] ) && $_POST['sr_active_status'] === 'on' ? 'active' : 'inactive';
            update_user_meta( $user_id, 'sr_active_status', $new_status );

            echo '<div class="updated"><p>' . __( 'Settings updated successfully.', 'smart-referrals' ) . '</p></div>';
        }

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'User Settings', 'smart-referrals' ); ?></h1>

            <form method="post">
                <?php wp_nonce_field( 'sr_user_settings_action', 'sr_user_settings_nonce' ); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Active Status', 'smart-referrals' ); ?></th>
                        <td>
                            <input type="checkbox" name="sr_active_status" <?php checked( $status, 'active' ); ?> />
                            <label for="sr_active_status"><?php esc_html_e( 'Enable user', 'smart-referrals' ); ?></label>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}