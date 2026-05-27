<?php
/**
 * Shortcodes e Injeção de Interface do Plugin.
 *
 * @package WCMagicLogin
 */

defined( 'ABSPATH' ) || exit;

class WC_Magic_Login_Shortcode {

    /**
     * Instância única.
     *
     * @var WC_Magic_Login_Shortcode|null
     */
    private static $instance = null;

    /**
     * Retorna a instância única da classe.
     *
     * @return WC_Magic_Login_Shortcode
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor da classe. Registra ganchos de frontend.
     */
    private function __construct() {
        // Registra o shortcode
        add_shortcode( 'wc_magic_login_form', array( $this, 'render_shortcode' ) );

        // Injeta o gatilho de alternância no início do formulário de login padrão WC
        add_action( 'woocommerce_login_form_start', array( $this, 'inject_toggle_link' ) );

        // Injeta o formulário mágico oculto no final do formulário de login padrão WC
        add_action( 'woocommerce_login_form_end', array( $this, 'inject_magic_form' ) );
    }

    /**
     * Renderiza o formulário do plugin via shortcode.
     *
     * Uso: [wc_magic_login_form redirect_to="url_personalizada"]
     *
     * @param array $atts Atributos do shortcode.
     * @return string HTML do formulário.
     */
    public function render_shortcode( $atts ) {
        if ( 'yes' !== get_option( 'wc_ml_enabled', 'yes' ) ) {
            return '';
        }

        $args = shortcode_atts( array(
            'redirect_to' => '',
        ), $atts );

        // Se estiver logado, não exibe o formulário
        if ( is_user_logged_in() ) {
            return '<p class="wc-ml-already-logged-in">' . esc_html__( 'Você já está conectado.', 'wc-magic-login' ) . '</p>';
        }

        return $this->get_template_html( $args['redirect_to'] );
    }

    /**
     * Injeta o link de alternância para o modo login mágico no início do formulário do WooCommerce.
     */
    public function inject_toggle_link() {
        if ( 'yes' !== get_option( 'wc_ml_enabled', 'yes' ) ) {
            return;
        }
        ?>
        <div class="wc-ml-toggle-link-container">
            <a href="#" class="wc-ml-toggle-login-mode" data-target="magic">
                <span class="dashicons dashicons-email"></span> <?php esc_html_e( 'Entrar com Link no E-mail (Acesso Rápido)', 'wc-magic-login' ); ?>
            </a>
        </div>
        <?php
    }

    /**
     * Injeta o formulário de login mágico no fim do formulário de login do WooCommerce.
     */
    public function inject_magic_form() {
        if ( 'yes' !== get_option( 'wc_ml_enabled', 'yes' ) ) {
            return;
        }

        // Determina o link de redirecionamento adequado
        $redirect_to = '';
        if ( is_checkout() ) {
            $redirect_to = wc_get_checkout_url();
        } else {
            $redirect_to = wc_get_page_permalink( 'myaccount' );
        }

        // Renderiza o template interno do formulário mágico
        echo $this->get_template_html( $redirect_to );
    }

    /**
     * Carrega o HTML do formulário de login mágico, respeitando sobreposições do tema.
     *
     * @param string $redirect_to URL de redirecionamento de sucesso.
     * @return string HTML compilado.
     */
    public function get_template_html( $redirect_to = '' ) {
        // Sanitiza o redirecionamento
        $redirect_url = ! empty( $redirect_to ) ? esc_url( $redirect_to ) : '';

        ob_start();
        
        $template_name = 'magic-login-form.php';
        $override_path = locate_template( 'woocommerce/' . $template_name );

        // Torna a variável de redirect visível dentro do escopo do template
        // através do input hidden dinâmico que o script JS irá ler ou enviar
        echo '<input type="hidden" id="wc_ml_redirect_to_url" value="' . esc_attr( $redirect_url ) . '" />';

        if ( $override_path ) {
            include $override_path;
        } else {
            include WC_ML_PATH . 'templates/' . $template_name;
        }

        return ob_get_clean();
    }
}
