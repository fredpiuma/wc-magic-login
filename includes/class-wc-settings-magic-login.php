<?php
/**
 * Classe WC_Settings_Magic_Login responsável por montar os campos nas configurações do WooCommerce.
 *
 * @package WCMagicLogin
 */

defined( 'ABSPATH' ) || exit;

class WC_Settings_Magic_Login extends WC_Settings_Page {

    /**
     * Construtor da classe de página de configurações.
     */
    public function __construct() {
        $this->id    = 'wc_magic_login';
        $this->label = __( 'Login Mágico', 'wc-magic-login' );

        parent::__construct();
    }

    /**
     * Retorna a estrutura de configurações da aba.
     *
     * @return array
     */
    public function get_settings() {
        $settings = array(
            array(
                'title' => __( 'Configurações Gerais', 'wc-magic-login' ),
                'type'  => 'title',
                'desc'  => __( 'Configure o funcionamento básico do login por link mágico.', 'wc-magic-login' ),
                'id'    => 'wc_ml_general_section',
            ),
            array(
                'title'   => __( 'Habilitar Plugin', 'wc-magic-login' ),
                'desc'    => __( 'Ativar o login por link de e-mail e código OTP.', 'wc-magic-login' ),
                'id'      => 'wc_ml_enabled',
                'default' => 'yes',
                'type'    => 'checkbox',
            ),
            array(
                'title'             => __( 'Tempo de Expiração', 'wc-magic-login' ),
                'desc'              => __( 'Tempo limite de expiração do link e do código em minutos (máximo de 5 recomendado por segurança).', 'wc-magic-login' ),
                'id'                => 'wc_ml_expiration',
                'default'           => '5',
                'type'              => 'number',
                'custom_attributes' => array(
                    'min'  => 1,
                    'max'  => 60,
                    'step' => 1,
                ),
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'wc_ml_general_section',
            ),
            array(
                'title' => __( 'Integração de Webhook (Envio via WhatsApp/SMS)', 'wc-magic-login' ),
                'type'  => 'title',
                'desc'  => __( 'Configure um webhook externo para enviar o código de login por WhatsApp utilizando o telefone de faturamento do cliente (billing_phone). Compatível com EvolutionAPI, BaileysAPI, etc.', 'wc-magic-login' ),
                'id'    => 'wc_ml_webhook_section',
            ),
            array(
                'title'   => __( 'Habilitar Webhook', 'wc-magic-login' ),
                'desc'    => __( 'Enviar código de 6 dígitos e link de acesso via webhook HTTP POST.', 'wc-magic-login' ),
                'id'      => 'wc_ml_webhook_enabled',
                'default' => 'no',
                'type'    => 'checkbox',
            ),
            array(
                'title'       => __( 'URL do Webhook', 'wc-magic-login' ),
                'desc'        => __( 'URL do endpoint de envio de mensagem da API (Ex: EvolutionAPI ou BaileysAPI).', 'wc-magic-login' ),
                'id'          => 'wc_ml_webhook_url',
                'default'     => '',
                'type'        => 'text',
                'placeholder' => 'https://api.evolution.com/message/sendText/sua-instancia',
                'css'         => 'min-width: 400px;',
            ),
            array(
                'title'       => __( 'Cabeçalhos Personalizados', 'wc-magic-login' ),
                'desc'        => __( 'Cabeçalhos HTTP de autenticação passados na requisição. Um cabeçalho por linha. Exemplo: <code>apikey: seu-token-seguro</code> ou <code>Authorization: Bearer seu-token</code>', 'wc-magic-login' ),
                'id'          => 'wc_ml_webhook_headers',
                'default'     => '',
                'type'        => 'textarea',
                'placeholder' => 'apikey: token_aqui',
                'css'         => 'min-width: 400px; height: 60px; font-family: monospace;',
            ),
            array(
                'title'       => __( 'Corpo do Payload (JSON)', 'wc-magic-login' ),
                'desc'        => __( 'Estrutura JSON enviada no corpo do POST. Use as tags:<br>
                - <code>{phone}</code>: Número de telefone do cliente (billing_phone do checkout)<br>
                - <code>{link}</code>: Link de login mágico direto<br>
                - <code>{code}</code>: Código OTP de 6 dígitos para inserção manual<br>
                - <code>{name}</code>: Nome ou e-mail do cliente', 'wc-magic-login' ),
                'id'          => 'wc_ml_webhook_payload',
                'default'     => "{\n  \"number\": \"{phone}\",\n  \"options\": {\n    \"delay\": 1200,\n    \"presence\": \"composing\"\n  },\n  \"textMessage\": {\n    \"text\": \"Olá, {name}! Seu código de login é *{code}*. Ou clique no link para logar instantaneamente: {link}\"\n  }\n}",
                'type'        => 'textarea',
                'css'         => 'min-width: 400px; height: 180px; font-family: monospace;',
            ),
            array(
                'type' => 'sectionend',
                'id'   => 'wc_ml_webhook_section',
            ),
        );

        return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
    }
}
