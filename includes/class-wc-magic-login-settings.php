<?php
/**
 * Aba de Configurações no WooCommerce (Gerenciador).
 *
 * @package WCMagicLogin
 */

defined( 'ABSPATH' ) || exit;

class WC_Magic_Login_Settings {

    /**
     * Instância única.
     *
     * @var WC_Magic_Login_Settings|null
     */
    private static $instance = null;

    /**
     * Retorna a instância única da classe.
     *
     * @return WC_Magic_Login_Settings
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor da classe. Registra o filtro de abas do WooCommerce.
     */
    private function __construct() {
        add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_settings_page' ) );
    }

    /**
     * Registra a classe de configurações dentro do WooCommerce.
     *
     * Carrega o arquivo da subclasse de forma dinâmica e tardia (lazy loading) apenas
     * quando as abas estão sendo de fato compiladas pelo WooCommerce, garantindo que
     * a dependência WC_Settings_Page já tenha sido carregada pelo core do WooCommerce.
     *
     * @param array $settings Páginas de configurações ativas.
     * @return array
     */
    public function add_settings_page( $settings ) {
        if ( class_exists( 'WC_Settings_Page' ) ) {
            // Requer de forma segura a definição da classe filha
            require_once WC_ML_PATH . 'includes/class-wc-settings-magic-login.php';
            
            if ( class_exists( 'WC_Settings_Magic_Login' ) ) {
                $settings[] = new WC_Settings_Magic_Login();
            }
        }
        return $settings;
    }
}
