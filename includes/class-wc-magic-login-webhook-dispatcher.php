<?php
/**
 * Despachante de Webhooks para EvolutionAPI / BaileysAPI.
 *
 * @package WCMagicLogin
 */

defined( 'ABSPATH' ) || exit;

class WC_Magic_Login_Webhook_Dispatcher {

     /**
     * Envia os dados de login para o Webhook configurado.
     *
     * @param WP_User $user               Objeto do usuário.
     * @param string  $plain_token        Token puro de login.
     * @param string  $otp_code           Código OTP de 6 dígitos.
     * @param int     $expiration_time    Timestamp de expiração.
     * @return bool|WP_Error Retorna true se enviado (ou agendado), ou WP_Error caso erro básico ocorra.
     */
    public static function dispatch( $user, $plain_token, $otp_code, $expiration_time ) {
        // Verifica se a integração está ativa nas configurações
        $enabled = get_option( 'wc_ml_webhook_enabled', 'no' );
        if ( 'yes' !== $enabled ) {
            return false;
        }

        $webhook_url = get_option( 'wc_ml_webhook_url', '' );
        if ( empty( $webhook_url ) ) {
            return new WP_Error( 'missing_url', __( 'URL do Webhook não configurada.', 'wc-magic-login' ) );
        }

        // Recupera o telefone estritamente do banco de dados (meta do usuário cadastrado para segurança total)
        $raw_phone = get_user_meta( $user->ID, 'billing_phone', true );
        $formatted_phone = self::format_phone( $raw_phone );

        if ( empty( $formatted_phone ) ) {
            return new WP_Error( 'missing_phone', __( 'Telefone do cliente não cadastrado na conta.', 'wc-magic-login' ) );
        }

        // Constrói o link de login
        $login_url = add_query_arg(
            array( 'wc_magic_login_token' => $plain_token ),
            home_url( '/' )
        );

        $name = ! empty( $user->first_name ) ? $user->first_name : $user->display_name;

        // Recupera e parseia os cabeçalhos personalizados
        $headers_raw = get_option( 'wc_ml_webhook_headers', '' );
        $headers = array(
            'Content-Type' => 'application/json',
        );

        if ( ! empty( $headers_raw ) ) {
            $lines = explode( "\n", str_replace( "\r", "", $headers_raw ) );
            foreach ( $lines as $line ) {
                $line = trim( $line );
                if ( empty( $line ) ) {
                    continue;
                }
                $parts = explode( ':', $line, 2 );
                if ( count( $parts ) === 2 ) {
                    $headers[ trim( $parts[0] ) ] = trim( $parts[1] );
                }
            }
        }

        // Recupera e parseia o payload JSON com placeholders
        $payload_template = get_option( 'wc_ml_webhook_payload', '' );
        if ( empty( $payload_template ) ) {
            return new WP_Error( 'missing_payload', __( 'Template de payload JSON não configurado.', 'wc-magic-login' ) );
        }

        $search = array( '{phone}', '{link}', '{code}', '{name}' );
        $replace = array( $formatted_phone, $login_url, $otp_code, $name );
        $payload_json = str_replace( $search, $replace, $payload_template );

        // Dispara o POST HTTP de forma NÃO BLOQUEANTE para não atrasar a resposta AJAX da página!
        $args = array(
            'headers'     => $headers,
            'body'        => $payload_json,
            'method'      => 'POST',
            'data_format' => 'body',
            'timeout'     => 15,
            'blocking'    => false, // Envia e prossegue sem esperar o retorno da API externa
        );

        $response = wp_remote_post( $webhook_url, $args );

        // Retorna verdadeiro de que o disparo assíncrono foi disparado
        return true;
    }

    /**
     * Formata o número de celular para o padrão esperado pelas APIs de mensagens (e.g. 5511999998888).
     *
     * @param string $phone Telefone bruto.
     * @return string Telefone formatado contendo apenas números com DDI.
     */
    public static function format_phone( $phone ) {
        // Remove qualquer caractere não numérico
        $phone = preg_replace( '/\D/', '', $phone );
        
        if ( empty( $phone ) ) {
            return '';
        }

        // Se o número tiver 10 ou 11 dígitos, consideramos que está sem o DDI do Brasil (55)
        if ( strlen( $phone ) === 10 || strlen( $phone ) === 11 ) {
            $phone = '55' . $phone;
        }

        return $phone;
    }
}
