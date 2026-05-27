<?php
/**
 * Despachante de E-mails do Plugin.
 *
 * @package WCMagicLogin
 */

defined( 'ABSPATH' ) || exit;

class WC_Magic_Login_Email_Sender {

    /**
     * Envia o e-mail contendo o link mágico e o código de 6 dígitos.
     *
     * @param WP_User $user               Objeto do usuário.
     * @param string  $plain_token        Token puro de login.
     * @param string  $otp_code           Código OTP de 6 dígitos.
     * @param int     $expiration_time    Timestamp de expiração.
     * @return bool True se enviado com sucesso, false caso contrário.
     */
    public static function send_login_email( $user, $plain_token, $otp_code, $expiration_time ) {
        // Monta o link mágico de login
        $login_url = add_query_arg(
            array( 'wc_magic_login_token' => $plain_token ),
            home_url( '/' )
        );

        $name = ! empty( $user->first_name ) ? $user->first_name : $user->display_name;
        $expiration_minutes = round( ( $expiration_time - time() ) / 60 );

        // Assunto do e-mail
        $subject = sprintf( __( 'Seu código e link de acesso rápido - %s', 'wc-magic-login' ), get_bloginfo( 'name' ) );

        // Recupera a cor base de e-mails configurada no WooCommerce para aplicar ao botão
        $base_color = get_option( 'woocommerce_email_base_color', '#96588a' );
        $text_color = wc_light_or_dark( $base_color, '#ffffff', '#111111' );

        // Constrói o corpo do e-mail em HTML
        $email_content = '
        <p>' . sprintf( esc_html__( 'Olá, %s!', 'wc-magic-login' ), esc_html( $name ) ) . '</p>
        <p>' . esc_html__( 'Você solicitou o acesso rápido à sua conta. Clique no botão abaixo para fazer login instantaneamente sem precisar digitar sua senha:', 'wc-magic-login' ) . '</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="' . esc_url( $login_url ) . '" style="background-color: ' . esc_attr( $base_color ) . '; color: ' . esc_attr( $text_color ) . '; padding: 14px 28px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; font-size: 16px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">' . esc_html__( 'Fazer Login Instantâneo', 'wc-magic-login' ) . '</a>
        </div>
        
        <div style="text-align: center; background-color: #f9f9f9; border: 1px dashed #dcdcdc; padding: 20px; margin: 25px 0; border-radius: 6px;">
            <p style="margin: 0 0 10px 0; font-size: 14px; color: #666666;">' . esc_html__( 'Ou digite este código de 6 dígitos no site:', 'wc-magic-login' ) . '</p>
            <span style="font-size: 32px; font-weight: bold; letter-spacing: 6px; color: #333333; font-family: monospace; border: 1px solid #e2e2e2; background-color: #ffffff; padding: 5px 15px; display: inline-block; border-radius: 4px;">' . esc_html( $otp_code ) . '</span>
        </div>
        
        <p style="font-size: 12px; color: #888888; font-style: italic; text-align: center; margin-top: 30px; border-top: 1px solid #eeeeee; padding-top: 15px;">' . 
        sprintf( esc_html__( 'Atenção: Por motivos de segurança, este link e código de acesso expiram em %d minutos e só podem ser utilizados uma única vez.', 'wc-magic-login' ), esc_html( $expiration_minutes ) ) . '
        </p>';

        // Inicializa o mailer do WooCommerce para envelopar no template padrão premium
        if ( function_exists( 'WC' ) && isset( WC()->mailer ) ) {
            $mailer = WC()->mailer();
            // Utiliza o método nativo para encapsular o corpo com Cabeçalho/Rodapé, Logo e estilos globais
            $wrapped_message = $mailer->wrap_message( $subject, $email_content );
            
            // Define o cabeçalho padrão HTML
            $headers = array( 'Content-Type: text/html; charset=UTF-8' );

            return $mailer->send( $user->user_email, $subject, $wrapped_message, $headers );
        }

        // Fallback básico caso o WooCommerce Mailer não carregue por algum motivo
        $headers = array( 'Content-Type: text/html; charset=UTF-8' );
        return wp_mail( $user->user_email, $subject, $email_content, $headers );
    }
}
