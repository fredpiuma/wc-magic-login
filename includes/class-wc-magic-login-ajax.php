<?php
/**
 * Manipulador de Requisições AJAX do Plugin.
 *
 * @package WCMagicLogin
 */

defined( 'ABSPATH' ) || exit;

class WC_Magic_Login_Ajax {

    /**
     * Instância única.
     *
     * @var WC_Magic_Login_Ajax|null
     */
    private static $instance = null;

    /**
     * Retorna a instância única da classe.
     *
     * @return WC_Magic_Login_Ajax
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor da classe. Registra as ações AJAX no WordPress.
     */
    private function __construct() {
        // Ações AJAX para solicitar link mágico (usuários não logados e logados)
        add_action( 'wp_ajax_nopriv_wc_magic_login_request', array( $this, 'handle_login_request' ) );
        add_action( 'wp_ajax_wc_magic_login_request', array( $this, 'handle_login_request' ) );

        // Ações AJAX para verificar código OTP (usuários não logados e logados)
        add_action( 'wp_ajax_nopriv_wc_magic_login_verify', array( $this, 'handle_otp_verification' ) );
        add_action( 'wp_ajax_wc_magic_login_verify', array( $this, 'handle_otp_verification' ) );
    }

    /**
     * Processa a solicitação de envio do link mágico de login.
     */
    public function handle_login_request() {
        // Validação de segurança via nonce
        check_ajax_referer( 'wc_magic_login_nonce', 'nonce' );

        $email_input = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
        $redirect_to = isset( $_POST['redirect_to'] ) ? esc_url_raw( $_POST['redirect_to'] ) : '';

        if ( empty( $email_input ) ) {
            wp_send_json_error( array( 'message' => __( 'Por favor, informe seu e-mail ou nome de usuário.', 'wc-magic-login' ) ) );
        }

        // Tenta obter o usuário pelo E-mail, ou pelo Nome de Usuário (Username)
        $user = get_user_by( 'email', $email_input );
        if ( ! $user ) {
            $user = get_user_by( 'login', $email_input );
        }

        // Se o usuário não existir
        if ( ! $user ) {
            // Retorna erro informativo (comum em WooCommerce para melhor UX de Checkout)
            wp_send_json_error( array( 'message' => __( 'Não encontramos nenhuma conta com o e-mail ou usuário informado.', 'wc-magic-login' ) ) );
        }

        // Gera os tokens (Link Mágico + OTP de 6 dígitos)
        $token_manager = WC_Magic_Login_Token_Manager::get_instance();
        $tokens = $token_manager->generate_tokens( $user->ID, $redirect_to );

        // Dispara o envio por E-mail
        $email_sent = WC_Magic_Login_Email_Sender::send_login_email(
            $user,
            $tokens['plain_token'],
            $tokens['otp_code'],
            $tokens['expires']
        );

        // Se a integração com Webhook estiver ativa, dispara de forma assíncrona
        $webhook_triggered = false;
        if ( 'yes' === get_option( 'wc_ml_webhook_enabled', 'no' ) ) {
            $webhook_result = WC_Magic_Login_Webhook_Dispatcher::dispatch(
                $user,
                $tokens['plain_token'],
                $tokens['otp_code'],
                $tokens['expires']
            );
            $webhook_triggered = ! is_wp_error( $webhook_result ) && $webhook_result;
        }

        if ( ! $email_sent && ! $webhook_triggered ) {
            wp_send_json_error( array( 'message' => __( 'Falha ao enviar o link de acesso. Tente novamente mais tarde.', 'wc-magic-login' ) ) );
        }

        // Determina a mensagem informativa de sucesso
        if ( $webhook_triggered ) {
            $success_message = __( 'Enviamos o link de login e código OTP para o seu e-mail e WhatsApp!', 'wc-magic-login' );
        } else {
            $success_message = __( 'Enviamos o link de login e código OTP para o seu e-mail!', 'wc-magic-login' );
        }

        wp_send_json_success( array(
            'message'          => $success_message,
            'email'            => $user->user_email,
            'expires_in'       => $tokens['expires'] - time(), // Segundos restantes para o timer
            'webhook_sent'     => $webhook_triggered
        ) );
    }

    /**
     * Processa a verificação do código OTP de 6 dígitos.
     */
    public function handle_otp_verification() {
        // Validação de segurança via nonce
        check_ajax_referer( 'wc_magic_login_nonce', 'nonce' );

        $email_input = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
        $code = isset( $_POST['code'] ) ? sanitize_text_field( $_POST['code'] ) : '';

        if ( empty( $email_input ) || empty( $code ) ) {
            wp_send_json_error( array( 'message' => __( 'Dados de verificação incompletos.', 'wc-magic-login' ) ) );
        }

        // Tenta obter o usuário pelo E-mail ou pelo Nome de Usuário
        $user = get_user_by( 'email', $email_input );
        if ( ! $user ) {
            $user = get_user_by( 'login', $email_input );
        }

        if ( ! $user ) {
            wp_send_json_error( array( 'message' => __( 'Usuário inválido ou não encontrado.', 'wc-magic-login' ) ) );
        }

        // Valida o código utilizando o Token Manager
        $token_manager = WC_Magic_Login_Token_Manager::get_instance();
        $result = $token_manager->verify_otp_code( $user->ID, $code );

        // Trata erro de verificação
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        // Sucesso na autenticação
        wp_send_json_success( array(
            'message'     => __( 'Login efetuado com sucesso! Redirecionando...', 'wc-magic-login' ),
            'redirect_to' => $result['redirect_to']
        ) );
    }
}
