<?php
/**
 * Gerenciador de Tokens e OTPs do Plugin.
 *
 * @package WCMagicLogin
 */

defined( 'ABSPATH' ) || exit;

class WC_Magic_Login_Token_Manager {

    /**
     * Instância única.
     *
     * @var WC_Magic_Login_Token_Manager|null
     */
    private static $instance = null;

    /**
     * Retorna a instância única da classe.
     *
     * @return WC_Magic_Login_Token_Manager
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor da classe. Registra hooks relacionados à autenticação por query var.
     */
    private function __construct() {
        // Intercepta solicitações de login via Link Mágico
        add_action( 'init', array( $this, 'maybe_login_via_link' ), 5 );
    }

    /**
     * Gera um token de login mágico e um OTP de 6 dígitos para o usuário.
     *
     * @param int    $user_id      ID do usuário.
     * @param string $redirect_to  URL para redirecionar após o login.
     * @return array Contém o token puro (para o e-mail), o código OTP de 6 dígitos e o tempo de expiração.
     */
    public function generate_tokens( $user_id, $redirect_to = '' ) {
        // Se não for fornecido um redirect_to, define o padrão (Minha Conta)
        if ( empty( $redirect_to ) ) {
            $redirect_to = wc_get_page_permalink( 'myaccount' );
        }

        // Gera token puro seguro (32 caracteres) e seu hash SHA-256
        $plain_token = wp_generate_password( 32, false );
        $hashed_token = hash( 'sha256', $plain_token );

        // Gera código de 6 dígitos único
        $otp_code = sprintf( '%06d', wp_rand( 0, 999999 ) );

        // Obtém o tempo de expiração configurado (padrão de 5 minutos)
        $expiration_minutes = (int) get_option( 'wc_ml_expiration', 5 );
        $expiration_seconds = $expiration_minutes * 60;
        $expiration_timestamp = time() + $expiration_seconds;

        // Salva o Transient do Link Mágico (chave: wc_ml_t_hash)
        set_transient(
            'wc_ml_t_' . $hashed_token,
            array(
                'user_id'     => $user_id,
                'redirect_to' => $redirect_to,
                'expires'     => $expiration_timestamp,
            ),
            $expiration_seconds
        );

        // Salva o Transient do Código OTP (chave: wc_ml_otp_userid)
        set_transient(
            'wc_ml_otp_' . $user_id,
            array(
                'code'         => $otp_code,
                'hashed_token' => $hashed_token,
                'redirect_to'  => $redirect_to,
                'attempts'     => 0,
                'expires'      => $expiration_timestamp,
            ),
            $expiration_seconds
        );

        return array(
            'plain_token' => $plain_token,
            'otp_code'    => $otp_code,
            'expires'     => $expiration_timestamp,
        );
    }

    /**
     * Verifica e consome um código OTP de 6 dígitos inserido pelo usuário.
     *
     * @param int    $user_id ID do usuário.
     * @param string $code    Código inserido pelo usuário.
     * @return array|WP_Error Array contendo o redirecionamento se válido, ou WP_Error se inválido.
     */
    public function verify_otp_code( $user_id, $code ) {
        $transient_key = 'wc_ml_otp_' . $user_id;
        $data = get_transient( $transient_key );

        if ( ! $data || ! is_array( $data ) ) {
            return new WP_Error( 'expired_code', __( 'O código de login expirou ou é inválido. Solicite um novo link.', 'wc-magic-login' ) );
        }

        // Proteção contra brute force: Máximo de 5 tentativas
        if ( $data['attempts'] >= 5 ) {
            $this->clear_tokens( $user_id, $data['hashed_token'] );
            return new WP_Error( 'rate_limited', __( 'Muitas tentativas incorretas. Por favor, solicite um novo código.', 'wc-magic-login' ) );
        }

        // Validação do código
        if ( trim( $data['code'] ) !== trim( $code ) ) {
            $data['attempts']++;
            set_transient( $transient_key, $data, max( 0, $data['expires'] - time() ) );
            return new WP_Error( 'incorrect_code', sprintf( __( 'Código incorreto. Você tem mais %d tentativas.', 'wc-magic-login' ), 5 - $data['attempts'] ) );
        }

        // Código válido! Efetua o login
        $this->login_user( $user_id );

        $redirect_to = $data['redirect_to'];

        // Limpa os tokens para evitar reuso
        $this->clear_tokens( $user_id, $data['hashed_token'] );

        return array(
            'redirect_to' => $redirect_to,
        );
    }

    /**
     * Intercepta a inicialização e realiza o login se um token válido for detectado na URL.
     */
    public function maybe_login_via_link() {
        if ( ! isset( $_GET['wc_magic_login_token'] ) ) {
            return;
        }

        $plain_token = sanitize_text_field( $_GET['wc_magic_login_token'] );
        $hashed_token = hash( 'sha256', $plain_token );

        $transient_key = 'wc_ml_t_' . $hashed_token;
        $data = get_transient( $transient_key );

        if ( ! $data || ! is_array( $data ) ) {
            wp_die(
                esc_html__( 'Este link de login expirou (limite de 5 minutos) ou já foi utilizado. Por favor, solicite um novo link.', 'wc-magic-login' ),
                esc_html__( 'Link Expirado', 'wc-magic-login' ),
                array( 'back_link' => true )
            );
        }

        $user_id = (int) $data['user_id'];
        $redirect_to = $data['redirect_to'];

        // Autentica o usuário no WordPress
        $this->login_user( $user_id );

        // Limpa os tokens
        $this->clear_tokens( $user_id, $hashed_token );

        // Redireciona o usuário
        wp_safe_redirect( $redirect_to );
        exit;
    }

    /**
     * Autentica e loga um usuário pelo ID.
     *
     * @param int $user_id ID do usuário.
     */
    private function login_user( $user_id ) {
        if ( is_user_logged_in() ) {
            // Se já estiver logado como outra pessoa, desloga primeiro
            if ( get_current_user_id() !== $user_id ) {
                wp_logout();
            } else {
                return; // Já está logado como este usuário
            }
        }

        wp_clear_auth_cookie();
        wp_set_current_user( $user_id );
        wp_set_auth_cookie( $user_id, true ); // Lembrar login

        // Dispara ações padrões do WooCommerce pós login se necessário
        do_action( 'wp_login', get_user_by( 'id', $user_id )->user_login, get_user_by( 'id', $user_id ) );
    }

    /**
     * Limpa os transients de token e OTP associados ao login.
     *
     * @param int    $user_id      ID do usuário.
     * @param string $hashed_token Hash SHA-256 do link de login.
     */
    public function clear_tokens( $user_id, $hashed_token ) {
        delete_transient( 'wc_ml_t_' . $hashed_token );
        delete_transient( 'wc_ml_otp_' . $user_id );
    }
}
