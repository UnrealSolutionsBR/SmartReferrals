<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
    <h1><?php esc_html_e( 'General Settings', 'smart-referrals' ); ?></h1>
    <?php
    if ( isset( $_POST['sr_save_settings'] ) ) {
        // Validar el nonce para seguridad
        if ( check_admin_referer( 'sr_save_settings_nonce' ) ) {
            // Guardar el parámetro de referido
            if ( isset( $_POST['sr_referral_parameter'] ) ) {
                $referral_parameter = sanitize_text_field( $_POST['sr_referral_parameter'] );
                update_option( 'sr_referral_parameter', $referral_parameter );
            }

            // Guardar el valor del descuento
            if ( isset( $_POST['sr_discount_value'] ) ) {
                $discount_value = floatval( $_POST['sr_discount_value'] );
                update_option( 'sr_discount_value', $discount_value );
            }

            // Mostrar mensaje de éxito
            echo '<div class="updated"><p>' . esc_html__( 'Settings saved.', 'smart-referrals' ) . '</p></div>';
        } else {
            echo '<div class="error"><p>' . esc_html__( 'Nonce validation failed. Please try again.', 'smart-referrals' ) . '</p></div>';
        }
    }

    // Obtener los valores actuales
    $parameter = get_option( 'sr_referral_parameter', 'REFERRALCODE' );
    $discount_value = get_option( 'sr_discount_value', 10 );
    ?>
    <form method="post" action="">
        <?php wp_nonce_field( 'sr_save_settings_nonce' ); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="sr_referral_parameter"><?php esc_html_e( 'Referral Parameter Name', 'smart-referrals' ); ?></label>
                </th>
                <td>
                    <input name="sr_referral_parameter" type="text" id="sr_referral_parameter" value="<?php echo esc_attr( $parameter ); ?>" class="regular-text">
                    <p class="description">
                        <?php esc_html_e( 'Enter the referral parameter name (e.g., URCODE).', 'smart-referrals' ); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="sr_discount_value"><?php esc_html_e( 'Discount Value (%)', 'smart-referrals' ); ?></label>
                </th>
                <td>
                    <input name="sr_discount_value" type="number" id="sr_discount_value" value="<?php echo esc_attr( $discount_value ); ?>" class="small-text">
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
