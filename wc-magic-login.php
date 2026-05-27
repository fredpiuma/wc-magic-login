<?php
/**
 * Plugin Name: WooCommerce Magic Login
 * Plugin URI: https://github.com/fred/wc-magic-login
 * Description: Permite que os usuários façam login com um link mágico enviado por e-mail ou código de 6 dígitos (OTP) via e-mail e webhook (EvolutionAPI/BaileysAPI). Compatível com Fluid Checkout.
 * Version: 1.0.0
 * Author: Frederico de Castro
 * Author URI: https://www.fredericodecastro.com.br/links
 * Text Domain: wc-magic-login
 * Domain Path: /languages
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 *
 * @package WCMagicLogin
 */

defined( 'ABSPATH' ) || exit;

// Define as constantes globais do plugin.
define( 'WC_ML_VERSION', '1.0.0' );
define( 'WC_ML_PATH', plugin_dir_path( __FILE__ ) );
define( 'WC_ML_URL', plugin_dir_url( __FILE__ ) );
define( 'WC_ML_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Autoloader para carregar automaticamente as classes do plugin baseadas no padrão WordPress.
 */
spl_autoload_register( function( $class ) {
    // Apenas carrega classes pertencentes a este plugin
    if ( strpos( $class, 'WC_Magic_Login' ) !== 0 ) {
        return;
    }

    // Traduz o nome da classe para o formato de arquivo padrão WP
    // Exemplo: WC_Magic_Login_Token_Manager -> class-wc-magic-login-token-manager.php
    $class_name = str_replace( '_', '-', strtolower( $class ) );
    $file       = 'class-' . $class_name . '.php';
    $path       = WC_ML_PATH . 'includes/' . $file;

    if ( file_exists( $path ) ) {
        require_once $path;
    }
} );

/**
 * Função principal para instanciar e retornar a instância do plugin.
 *
 * @return WC_Magic_Login
 */
function wc_magic_login() {
    return WC_Magic_Login::get_instance();
}

// Inicializa o plugin após o carregamento completo de todos os plugins ativos.
add_action( 'plugins_loaded', function() {
    // Verifica se o WooCommerce está ativo
    if ( class_exists( 'WooCommerce' ) ) {
        wc_magic_login();
    } else {
        // Exibe um aviso no painel se o WooCommerce não estiver ativo
        add_action( 'admin_notices', function() {
            ?>
            <div class="error notice">
                <p><?php esc_html_e( 'WooCommerce Magic Login requer que o WooCommerce esteja ativo!', 'wc-magic-login' ); ?></p>
            </div>
            <?php
        } );
    }
} );
