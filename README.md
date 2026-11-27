# WooCommerce Magic Login

Um plugin premium para WooCommerce que permite aos clientes fazer login com segurança e de forma instantânea usando um **Link Mágico** ou um **Código OTP de 6 dígitos** enviado por E-mail ou por WhatsApp/SMS através de um webhook externo (como EvolutionAPI ou BaileysAPI).

Totalmente compatível com formulários de login do WooCommerce padrão, páginas de Minha Conta e o popular fluxo de checkout **Fluid Checkout**.

---

## Recursos Principais

- **Sistema de Dupla Verificação**: O cliente recebe um link seguro de clique rápido e um código numérico de 6 dígitos (ideal para copiar e colar no celular).
- **Expiração Estrita de 5 Minutos**: Tokens e códigos são armazenados em WordPress Transients seguros, expirando sozinhos e limpando-se automaticamente após o primeiro uso.
- **Escudo contra Força Bruta**: Limita a 5 tentativas incorretas de OTP por sessão, invalidando o acesso caso excedido.
- **Despachante de Webhook**: Painel integrado para mapeamento de APIs externas (como EvolutionAPI ou BaileysAPI) para disparar mensagens usando o celular de faturamento (`billing_phone`) do cliente.
- **Chamadas Assíncronas**: Utiliza requisições HTTP POST assíncronas para que o checkout do cliente nunca trave esperando o retorno de APIs de WhatsApp externas.
- **Visual Responsivo Premium**: Interface interativa moderna com transições fluidas, foco automático em caixas OTP individuais, colagem inteligente de código e timer regressivo em tempo real.
- **Estilo Coerente com sua Loja**: Herda automaticamente o logotipo, rodapé e cor base configurados na aba de e-mails do WooCommerce.
- **Compatibilidade com Fluid Checkout**: Utiliza seletores jQuery delegados para permanecer interativo mesmo após atualizações via AJAX no checkout.
- **Assunto Otimizado para Notificações**: Inclui o código OTP de 6 dígitos no início da linha de assunto do e-mail (ex: `123456 é seu código para login`), permitindo visualizar e copiar o código diretamente pela notificação do celular.
- **Sobrescrita por Temas**: Template HTML modular e customizável.

---

## Instalação

**[Baixar o plugin (zip da última versão)](https://github.com/fredpiuma/wc-magic-login/releases/latest/download/wc-magic-login.zip)**

Esse zip tem só os arquivos do plugin. Não use o "Source code (zip)" do GitHub para instalar: ele traz arquivos de desenvolvimento e a pasta fica com o nome errado.

### Pelo painel do WordPress

1. Baixe o zip pelo link acima.
2. Em **Plugins → Adicionar novo → Enviar plugin**, escolha o arquivo e clique em **Instalar agora**.
3. Ative o **WooCommerce Magic Login**.

Para atualizar, envie o zip da versão nova da mesma forma e confirme a substituição da versão instalada.

### Pelo WP-CLI

```bash
wp plugin install https://github.com/fredpiuma/wc-magic-login/releases/latest/download/wc-magic-login.zip --force --activate
```

O mesmo comando instala e atualiza: o `--force` substitui a versão instalada pela última, já que o plugin não tem atualização automática pelo painel.

Requer WordPress 5.6+, PHP 7.4+ e WooCommerce 5.0+.

Veja as notas de cada versão no [CHANGELOG](CHANGELOG.md).

---

## Configurações

Acesse **WooCommerce > Configurações > Login Mágico** no seu painel administrativo para configurar:

- **Configurações Gerais**:
  - **Habilitar Plugin**: Ativa/desativa globalmente o formulário de login mágico.
  - **Tempo de Expiração**: Ajusta o tempo limite do login em minutos (padrão de 5 minutos).

- **Integração de Webhook (EvolutionAPI / BaileysAPI / Gateways SMS)**:
  - **Habilitar Webhook**: Ativa o envio secundário por mensagem.
  - **URL do Webhook**: Endpoint de envio da API externa.
  - **Cabeçalhos Personalizados**: Insira chaves de autenticação (uma por linha, ex: `apikey: token_seguro`).
  - **Corpo do Payload**: Escreva o payload JSON da sua API. Utilize os seguintes placeholders dinâmicos:
    - `{phone}`: Celular do cliente (com formatação de DDI automática. Para segurança absoluta, este telefone é consultado estritamente a partir do perfil do usuário verificado no banco de dados e não pode ser manipulado ou digitado durante o processo de login, eliminando qualquer risco de sequestro de token).
    - `{link}`: Link direto para login rápido de clique único.
    - `{code}`: Código numérico de 6 dígitos.
    - `{name}`: Primeiro nome ou nome de exibição do usuário.

---

## Customizando o Template de Layout

Para alterar o estilo visual ou marcação do formulário no seu tema:
1. Crie uma pasta chamada `woocommerce` dentro do diretório do seu tema filho.
2. Copie o arquivo `/templates/magic-login-form.php` deste plugin.
3. Cole-o no caminho `seu-tema/woocommerce/magic-login-form.php`.
4. Edite o código HTML conforme preferir.

---

### Precisa de uma solução sob medida para a sua loja?

Desenvolvo plugins, integrações e melhorias para WordPress e WooCommerce, do checkout ao pós-venda. Me conte o que você precisa e vamos encontrar o melhor caminho para o seu projeto.

**Frederico de Castro** · Senior Web Developer & Software Architect

[![Fale comigo no WhatsApp](https://img.shields.io/badge/Fale_comigo_no_WhatsApp-25D366?style=for-the-badge&logo=whatsapp&logoColor=white)](https://wa.me/5511914636944)
[![E-mail](https://img.shields.io/badge/E--mail-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:contato@fredericodecastro.com.br)
[![Site](https://img.shields.io/badge/Site-1F2937?style=for-the-badge&logo=googlechrome&logoColor=white)](https://www.fredericodecastro.com.br/links/)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-0A66C2?style=for-the-badge)](https://www.linkedin.com/in/fredericodecastro/)
[![Telegram](https://img.shields.io/badge/Telegram-2CA5E0?style=for-the-badge&logo=telegram&logoColor=white)](https://t.me/fredericomdecastro)
