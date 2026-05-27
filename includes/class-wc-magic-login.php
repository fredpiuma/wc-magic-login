<?php
/**
 * Classe Principal do Plugin.
 *
 * @package WCMagicLogin
 */

defined( 'ABSPATH' ) || exit;

class WC_Magic_Login {

    /**
     * Instância única da classe.
     *
     * @var WC_Magic_Login|null
     */
    private static $instance = null;

    /**
     * Retorna a instância única da classe.
     *
     * @return WC_Magic_Login
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor da classe. Inicializa as ações principais.
     */
    private function __construct() {
        // Inicializa as classes filhas e componentes
        $this->init_components();

        // Registrar assets (CSS e JS)
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        
        // Adiciona links na tela de plugins
        add_filter( 'plugin_action_links_' . WC_ML_BASENAME, array( $this, 'plugin_settings_link' ) );
    }

    /**
     * Inicializa os componentes principais do plugin.
     */
    private function init_components() {
        // Inicializa o gerenciador de tokens
        WC_Magic_Login_Token_Manager::get_instance();

        // Inicializa as configurações se estiver no painel administrativo e não for requisição AJAX
        if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
            WC_Magic_Login_Settings::get_instance();
        }

        // Inicializa os endpoints AJAX
        WC_Magic_Login_Ajax::get_instance();

        // Inicializa os shortcodes e ganchos de interface
        WC_Magic_Login_Shortcode::get_instance();
    }

    /**
     * Enfileira as folhas de estilo e scripts JavaScript do plugin.
     */
    public function enqueue_assets() {
        // Verifica se a opção do plugin está ativada nas configurações
        $enabled = get_option( 'wc_ml_enabled', 'yes' );
        if ( 'yes' !== $enabled ) {
            return;
        }

        // Enfileira o CSS com visual premium
        wp_enqueue_style(
            'wc-magic-login-style',
            WC_ML_URL . 'assets/css/wc-magic-login.css',
            array(),
            WC_ML_VERSION
        );

        // Enfileira o JS para submissão AJAX e UX do OTP
        wp_enqueue_script(
            'wc-magic-login-script',
            WC_ML_URL . 'assets/js/wc-magic-login.js',
            array( 'jquery' ),
            WC_ML_VERSION,
            true
        );

        // Passa variáveis PHP para o JavaScript de forma segura
        wp_localize_script(
            'wc-magic-login-script',
            'wc_magic_login_params',
            array(
                'ajax_url'    => admin_url( 'admin-ajax.php' ),
                'nonce'       => wp_create_nonce( 'wc_magic_login_nonce' ),
                'i18n'        => array(
                    'sending'       => __( 'Enviando...', 'wc-magic-login' ),
                    'send_link'     => __( 'Enviar Link de Acesso', 'wc-magic-login' ),
                    'verifying'     => __( 'Verificando...', 'wc-magic-login' ),
                    'verify_code'   => __( 'Verificar Código', 'wc-magic-login' ),
                    'empty_email'   => __( 'Por favor, informe seu e-mail.', 'wc-magic-login' ),
                    'invalid_email' => __( 'Por favor, informe um e-mail válido.', 'wc-magic-login' ),
                    'empty_code'    => __( 'Por favor, informe o código de 6 dígitos.', 'wc-magic-login' ),
                ),
            )
        );
    }

    /**
     * Adiciona um link direto de configurações na lista de plugins do painel WP.
     *
     * @param array $links Links de ação do plugin.
     * @return array
     */
    public function plugin_settings_link( $links ) {
        $settings_url = admin_url( 'admin.php?page=wc-settings&tab=wc_magic_login' );
        $settings_link = '<a href="' . esc_url( $settings_url ) . '">' . __( 'Configurações', 'wc-magic-login' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }
}
