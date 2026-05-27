<?php
/**
 * Template do Formulário de Login Mágico.
 *
 * Este arquivo pode ser sobrescrevido copiando-o para seu-tema/woocommerce/magic-login-form.php
 *
 * @package WCMagicLogin
 */

defined( 'ABSPATH' ) || exit;

$webhook_active = ( 'yes' === get_option( 'wc_ml_webhook_enabled', 'no' ) );
?>

<div class="wc-magic-login-wrapper wc-ml-exclude" id="wc-magic-login-container">
    
    <!-- Fase 1: Formulário de Solicitação de Link/Código -->
    <form class="wc-magic-login-form-request" id="wc-ml-request-form" method="post">
        
        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="wc_ml_email"><?php esc_html_e( 'E-mail cadastrado', 'wc-magic-login' ); ?>&nbsp;<span class="required">*</span></label>
            <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="wc_ml_email" id="wc_ml_email" autocomplete="email" placeholder="<?php esc_attr_e( 'seu@email.com', 'wc-magic-login' ); ?>" required />
        </p>

        <?php if ( $webhook_active ) : ?>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide wc-ml-phone-field">
                <label for="wc_ml_phone"><?php esc_html_e( 'Celular / WhatsApp (Opcional)', 'wc-magic-login' ); ?></label>
                <input type="tel" class="woocommerce-Input woocommerce-Input--text input-text" name="wc_ml_phone" id="wc_ml_phone" autocomplete="tel" placeholder="<?php esc_attr_e( '(11) 99999-9999', 'wc-magic-login' ); ?>" />
                <span class="description"><?php esc_html_e( 'Informe seu número para também receber o código de acesso rápido via WhatsApp.', 'wc-magic-login' ); ?></span>
            </p>
        <?php endif; ?>

        <div class="wc-ml-actions">
            <button type="submit" class="woocommerce-button button woocommerce-form-login__submit wc-ml-submit-btn" name="wc_ml_submit" value="send">
                <?php esc_html_e( 'Enviar Link de Acesso', 'wc-magic-login' ); ?>
            </button>
        </div>

        <div class="wc-ml-toggle-wrapper">
            <a href="#" class="wc-ml-toggle-login-mode" data-target="standard">
                <span class="dashicons dashicons-lock"></span> <?php esc_html_e( 'Entrar com senha padrão', 'wc-magic-login' ); ?>
            </a>
        </div>
        
        <div class="wc-ml-message-box" style="display: none;"></div>
    </form>

    <!-- Fase 2: Formulário de Inserção do Código OTP de 6 Dígitos -->
    <form class="wc-magic-login-form-verify" id="wc-ml-verify-form" method="post" style="display: none;">
        
        <div class="wc-ml-verify-info">
            <span class="dashicons dashicons-email-alt wc-ml-icon-large"></span>
            <h3><?php esc_html_e( 'Código de Verificação', 'wc-magic-login' ); ?></h3>
            <p class="wc-ml-verify-text">
                <?php 
                if ( $webhook_active ) {
                    esc_html_e( 'Enviamos um link de login e um código de 6 dígitos para o seu e-mail e/ou WhatsApp.', 'wc-magic-login' );
                } else {
                    esc_html_e( 'Enviamos um link de login e um código de 6 dígitos para o seu e-mail.', 'wc-magic-login' );
                }
                ?>
            </p>
            <p class="wc-ml-verify-target-email"></p>
        </div>

        <!-- Inputs Individuais dos Dígitos (Aparência Premium) -->
        <div class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide text-center">
            <label><?php esc_html_e( 'Digite o código de 6 dígitos', 'wc-magic-login' ); ?></label>
            <div class="wc-ml-otp-digits-container">
                <input type="text" class="wc-ml-otp-digit" maxlength="1" pattern="\d*" inputmode="numeric" required />
                <input type="text" class="wc-ml-otp-digit" maxlength="1" pattern="\d*" inputmode="numeric" required />
                <input type="text" class="wc-ml-otp-digit" maxlength="1" pattern="\d*" inputmode="numeric" required />
                <span class="wc-ml-otp-divider">-</span>
                <input type="text" class="wc-ml-otp-digit" maxlength="1" pattern="\d*" inputmode="numeric" required />
                <input type="text" class="wc-ml-otp-digit" maxlength="1" pattern="\d*" inputmode="numeric" required />
                <input type="text" class="wc-ml-otp-digit" maxlength="1" pattern="\d*" inputmode="numeric" required />
            </div>
            <!-- Campo oculto que guardará o valor total concatenado -->
            <input type="hidden" name="wc_ml_otp_code" id="wc_ml_otp_code" />
        </div>

        <div class="wc-ml-actions">
            <button type="submit" class="woocommerce-button button woocommerce-form-login__submit wc-ml-verify-btn" name="wc_ml_verify" value="verify">
                <?php esc_html_e( 'Verificar Código', 'wc-magic-login' ); ?>
            </button>
        </div>

        <div class="wc-ml-timer-wrapper">
            <span class="wc-ml-timer-text">
                <?php esc_html_e( 'O código expira em: ', 'wc-magic-login' ); ?><strong class="wc-ml-countdown">05:00</strong>
            </span>
        </div>

        <div class="wc-ml-toggle-wrapper">
            <a href="#" class="wc-ml-back-to-request">
                <span class="dashicons dashicons-arrow-left-alt"></span> <?php esc_html_e( 'Voltar / Alterar E-mail', 'wc-magic-login' ); ?>
            </a>
        </div>

        <div class="wc-ml-message-box" style="display: none;"></div>
    </form>
</div>
