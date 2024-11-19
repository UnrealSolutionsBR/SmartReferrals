<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
    <h1><?php esc_html_e( 'Códigos de Referido', 'smart-referrals' ); ?></h1>
    <?php
    if ( isset( $_GET['action'] ) && isset( $_GET['user_id'] ) ) {
        $user_id = intval( $_GET['user_id'] );
        if ( $_GET['action'] == 'generate' ) {
            SR_Referral_Code::generate_referral_code( $user_id );
        } elseif ( $_GET['action'] == 'delete' ) {
            SR_Referral_Code::delete_referral_code( $user_id );
        }
        wp_redirect( admin_url( 'admin.php?page=sr-referrals' ) );
        exit;
    }

    $users = get_users();
    ?>
    <table class="wp-list-table widefat fixed striped users">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Usuario', 'smart-referrals' ); ?></th>
                <th><?php esc_html_e( 'Código de Referido', 'smart-referrals' ); ?></th>
                <th><?php esc_html_e( 'Acciones', 'smart-referrals' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $users as $user ) : ?>
                <tr>
                    <td><?php echo esc_html( $user->user_login ); ?></td>
                    <td><?php echo esc_html( get_user_meta( $user->ID, 'sr_referral_code', true ) ); ?></td>
                    <td>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=sr-referrals&action=generate&user_id=' . $user->ID ) ); ?>" class="button"><?php esc_html_e( 'Generar Nuevo Código', 'smart-referrals' ); ?></a>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=sr-referrals&action=delete&user_id=' . $user->ID ) ); ?>" class="button"><?php esc_html_e( 'Eliminar Código', 'smart-referrals' ); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
