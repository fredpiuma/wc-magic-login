/**
 * Engine JavaScript do WooCommerce Magic Login
 *
 * @package WCMagicLogin
 */

(function($) {
    'use strict';

    // Utilitário para formatar tempo (segundos em MM:SS)
    function formatTime(seconds) {
        var minutes = Math.floor(seconds / 60);
        var remainingSeconds = seconds % 60;
        return (minutes < 10 ? '0' : '') + minutes + ':' + (remainingSeconds < 10 ? '0' : '') + remainingSeconds;
    }

    var countdownInterval = null;

    // Inicializa o temporizador regressivo do OTP
    function startCountdown(durationSeconds, $countdownElement, onComplete) {
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }

        var timeLeft = durationSeconds;
        $countdownElement.text(formatTime(timeLeft));

        countdownInterval = setInterval(function() {
            timeLeft--;
            $countdownElement.text(formatTime(timeLeft));

            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                if (typeof onComplete === 'function') {
                    onComplete();
                }
            }
        }, 1000);
    }

    // Atualiza o input oculto de código OTP concatenado
    function updateOTPValue($container) {
        var code = '';
        $container.find('.wc-ml-otp-digit').each(function() {
            code += $(this).val();
        });
        $container.find('#wc_ml_otp_code').val(code);
    }

    // Utiliza ligações delegadas (delegated events) para funcionar mesmo após atualizações via AJAX do Checkout
    $(document).ready(function() {

        // 1. ALTERNÂNCIA DE MODOS DE LOGIN
        $(document).on('click', '.wc-ml-toggle-login-mode', function(e) {
            e.preventDefault();
            var $this = $(this);
            var targetMode = $this.data('target');
            
            // Localiza o formulário principal de login do WC
            var $form = $this.closest('form.login, form.woocommerce-form-login');
            
            if ($form.length === 0) {
                // Shortcode ou fluxo customizado
                $form = $this.closest('.wc-magic-login-wrapper').parent();
            }

            if (targetMode === 'magic') {
                $form.addClass('wc-ml-active');
                $form.find('.wc-magic-login-wrapper').show();
            } else {
                $form.removeClass('wc-ml-active');
                $form.find('.wc-magic-login-wrapper').hide();
            }
        });

        // 2. RETORNO PARA A SOLICITAÇÃO (VOLTAR DA TELA DE CÓDIGO)
        $(document).on('click', '.wc-ml-back-to-request', function(e) {
            e.preventDefault();
            var $wrapper = $(this).closest('.wc-magic-login-wrapper');
            
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }

            $wrapper.find('#wc-ml-verify-form').hide();
            $wrapper.find('#wc-ml-request-form').fadeIn();
            
            // Limpa campos digitados anteriormente
            $wrapper.find('.wc-ml-otp-digit').val('');
            $wrapper.find('#wc_ml_otp_code').val('');
            $wrapper.find('.wc-ml-message-box').hide().removeClass('error success').text('');
        });

        // 3. SUBMISSÃO DO PEDIDO DE LINK (FASE 1)
        $(document).on('click', '.wc-ml-submit-btn', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var $form = $btn.closest('#wc-ml-request-form');
            var $wrapper = $form.closest('.wc-magic-login-wrapper');
            var $msgBox = $form.find('.wc-ml-message-box');

            var emailVal = $form.find('#wc_ml_email').val().trim();
            var redirectTo = $('#wc_ml_redirect_to_url').val() || '';

            // Validações básicas de frontend
            if (!emailVal) {
                $msgBox.addClass('error').text(wc_magic_login_params.i18n.empty_email).fadeIn();
                return;
            }

            // Exibe loader elegante
            $btn.prop('disabled', true).html('<span class="wc-ml-spinner"></span> ' + wc_magic_login_params.i18n.sending);
            $msgBox.fadeOut().removeClass('error success').text('');

            $.ajax({
                url: wc_magic_login_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'wc_magic_login_request',
                    nonce: wc_magic_login_params.nonce,
                    email: emailVal,
                    redirect_to: redirectTo
                },
                success: function(response) {
                    if (response.success) {
                        // Transiciona para a Fase 2 (Verificação de Código OTP)
                        $form.hide();
                        
                        var $verifyForm = $wrapper.find('#wc-ml-verify-form');
                        $verifyForm.find('.wc-ml-verify-target-email').text(response.data.email);
                        $verifyForm.fadeIn();
                        
                        // Foco automático no primeiro input do código
                        setTimeout(function() {
                            $verifyForm.find('.wc-ml-otp-digit').first().focus();
                        }, 200);

                        // Inicia o timer com os segundos retornados do servidor
                        var expiresSeconds = parseInt(response.data.expires_in) || 300;
                        startCountdown(
                            expiresSeconds, 
                            $verifyForm.find('.wc-ml-countdown'), 
                            function() {
                                // Ação executada ao expirar
                                $verifyForm.find('.wc-ml-message-box')
                                    .addClass('error')
                                    .text('O código expirou. Por favor, clique em voltar e solicite um novo link.')
                                    .fadeIn();
                                $verifyForm.find('.wc-ml-otp-digit, .wc-ml-verify-btn').prop('disabled', true);
                            }
                        );
                    } else {
                        $msgBox.addClass('error').text(response.data.message).fadeIn();
                    }
                },
                error: function() {
                    $msgBox.addClass('error').text('Ocorreu um erro ao processar sua solicitação. Tente novamente.').fadeIn();
                },
                complete: function() {
                    $btn.prop('disabled', false).html(wc_magic_login_params.i18n.send_link);
                }
            });
        });

        // 4. SUBMISSÃO E VALIDAÇÃO DO CÓDIGO OTP (FASE 2)
        $(document).on('click', '.wc-ml-verify-btn', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var $form = $btn.closest('#wc-ml-verify-form');
            var $wrapper = $form.closest('.wc-magic-login-wrapper');
            var $btnVerify = $form.find('.wc-ml-verify-btn');
            var $msgBox = $form.find('.wc-ml-message-box');

            updateOTPValue($form);
            var codeVal = $form.find('#wc_ml_otp_code').val();
            var emailVal = $wrapper.find('#wc_ml_email').val().trim();

            if (codeVal.length !== 6) {
                $msgBox.addClass('error').text(wc_magic_login_params.i18n.empty_code).fadeIn();
                return;
            }

            // Desativa botões e mostra loader
            $btnVerify.prop('disabled', true).html('<span class="wc-ml-spinner"></span> ' + wc_magic_login_params.i18n.verifying);
            $msgBox.fadeOut().removeClass('error success').text('');

            $.ajax({
                url: wc_magic_login_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'wc_magic_login_verify',
                    nonce: wc_magic_login_params.nonce,
                    email: emailVal,
                    code: codeVal
                },
                success: function(response) {
                    if (response.success) {
                        $msgBox.addClass('success').text(response.data.message).fadeIn();
                        $form.find('.wc-ml-otp-digit').prop('disabled', true);
                        
                        // Para o timer
                        if (countdownInterval) {
                            clearInterval(countdownInterval);
                        }

                        // Redireciona o usuário após login com sucesso
                        setTimeout(function() {
                            window.location.href = response.data.redirect_to;
                        }, 1200);
                    } else {
                        $msgBox.addClass('error').text(response.data.message).fadeIn();
                        // Limpa os campos do código para nova tentativa em caso de erro comum
                        if (response.data.message.indexOf('expirou') === -1) {
                            $form.find('.wc-ml-otp-digit').val('');
                            $form.find('#wc_ml_otp_code').val('');
                            $form.find('.wc-ml-otp-digit').first().focus();
                        }
                    }
                },
                error: function() {
                    $msgBox.addClass('error').text('Erro ao validar código. Tente novamente.').fadeIn();
                },
                complete: function() {
                    $btnVerify.prop('disabled', false).html(wc_magic_login_params.i18n.verify_code);
                }
            });
        });

        // 5. COMPORTAMENTO E ATALHOS DOS INPUTS OTP (CARET NAVIGATION)
        $(document).on('keyup input', '.wc-ml-otp-digit', function(e) {
            var $this = $(this);
            var $container = $this.closest('.wc-ml-otp-digits-container');
            var key = e.key || e.keyCode;

            // Se for um caractere numérico
            if (/\d/.test($this.val())) {
                // Limita a 1 caractere apenas (por segurança extra)
                if ($this.val().length > 1) {
                    $this.val($this.val().charAt(0));
                }
                // Avança o foco para a próxima caixa disponível
                $this.nextAll('.wc-ml-otp-digit').first().focus();
            }

            // Trata deleção via Backspace
            if (key === 'Backspace' || key === 8) {
                $this.val('');
                // Volta o foco para a caixa anterior
                $this.prevAll('.wc-ml-otp-digit').first().focus();
            }

            // Atualiza o input oculto de código a cada alteração
            updateOTPValue($this.closest('#wc-ml-verify-form'));
        });

        // Foco automático do campo subsequente em caso de clique
        $(document).on('click', '.wc-ml-otp-digit', function() {
            var $this = $(this);
            if ($this.val() === '') {
                var $firstEmpty = $this.closest('.wc-ml-otp-digits-container').find('.wc-ml-otp-digit').filter(function() {
                    return $(this).val() === '';
                }).first();
                if ($firstEmpty.length) {
                    $firstEmpty.focus();
                }
            }
        });

        // 6. DETECÇÃO DE COLAR DA ÁREA DE TRANSFERÊNCIA (PASTE ACTION)
        $(document).on('paste', '.wc-ml-otp-digit', function(e) {
            var $this = $(this);
            var $container = $this.closest('.wc-ml-otp-digits-container');
            var $inputs = $container.find('.wc-ml-otp-digit');
            
            var clipboardData = e.originalEvent.clipboardData || window.clipboardData;
            var pastedData = clipboardData.getData('text').trim();

            // Verifica se colou exatamente um número de 6 dígitos
            if (pastedData && pastedData.length === 6 && /^\d+$/.test(pastedData)) {
                e.preventDefault();
                
                $inputs.each(function(index) {
                    $(this).val(pastedData.charAt(index));
                });
                
                // Concatena e aciona o botão de verificação
                var $wrapper = $this.closest('.wc-magic-login-wrapper');
                updateOTPValue($wrapper);
                $wrapper.find('.wc-ml-verify-btn').click();
            }
        });

        // 7. PREVENÇÃO DE SUBMISSÃO DA TELA ORIGINAL COM ENTER E MAPEAMENTO DE BOTÕES
        $(document).on('keydown', '#wc_ml_email', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                var $wrapper = $(this).closest('.wc-magic-login-wrapper');
                $wrapper.find('.wc-ml-submit-btn').click();
            }
        });

        $(document).on('keydown', '.wc-ml-otp-digit', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                var $wrapper = $(this).closest('.wc-magic-login-wrapper');
                $wrapper.find('.wc-ml-verify-btn').click();
            }
        });
    });

})(jQuery);
