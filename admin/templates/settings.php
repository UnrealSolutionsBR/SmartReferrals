<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
    <h1><?php esc_html_e( 'Ajustes Generales', 'smart-referrals' ); ?></h1>
    <?php
    if ( isset( $_POST['sr_save_settings'] ) ) {
        check_admin_referer( 'sr_save_settings_nonce' );
        update_option( 'sr_referral_parameter', sanitize_text_field( $_POST['sr_referral_parameter'] ) );
        update_option( 'sr_discount_value', floatval( $_POST['sr_discount_value'] ) );
        echo '<div class="updated"><p>Configuración guardada.</p></div>';
    }

    $parameter = get_option( 'sr_referral_parameter', '?REFERRALCODE=' );
    $discount_value = get_option( 'sr_discount_value', 10 );
    ?>
    <form method="post" action="">
        <?php wp_nonce_field( 'sr_save_settings_nonce' ); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="sr_referral_parameter"><?php esc_html_e( 'Parámetro de Referido', 'smart-referrals' ); ?></label></th>
                <td><input name="sr_referral_parameter" type="text" id="sr_referral_parameter" value="<?php echo esc_attr( $parameter ); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="sr_discount_value"><?php esc_html_e( 'Valor de Descuento del Cupón (%)', 'smart-referrals' ); ?></label></th>
                <td><input name="sr_discount_value" type="number" id="sr_discount_value" value="<?php echo esc_attr( $discount_value ); ?>" class="small-text"></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
