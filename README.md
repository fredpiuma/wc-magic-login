# WooCommerce Magic Login

A premium WooCommerce plugin that allows customers to log in securely and instantly using a **Magic Link** or a **6-Digit OTP (One-Time Password) Code** sent via Email or SMS/WhatsApp via an external webhook (such as EvolutionAPI or BaileysAPI).

Fully compatible with standard WooCommerce login forms, My Account views, and the popular **Fluid Checkout** multi-step design.

---

## Key Features

- **Dual-Verification System**: Customers receive both a clickable secure login link and a 6-digit verification code (perfect for copy-pasting on mobile).
- **5-Minute strict expiration**: Tokens and codes are stored in secure temporary WordPress Transients, naturally self-expiring and automatically deleting after consumption (single-use token protection).
- **Brute-Force Shield**: Limits incorrect OTP code attempts to 5 per session, clearing all active tokens if exceeded.
- **Webhook Dispatcher**: Integrated settings page to map external APIs (like EvolutionAPI or BaileysAPI) to send SMS/WhatsApp notifications using the customer's billing phone number.
- **Non-blocking Webhook Calls**: Uses asynchronous HTTP POST requests so your customer's checkout is never delayed waiting for external messaging APIs to respond.
- **Sleek, Responsive UI**: Modern interactive interface with smooth transitions, caret navigation for individual OTP boxes, clipboard auto-fill, and live countdown timer.
- **Dynamic Brand Matching**: Automatically inherits WooCommerce settings such as your store's logo, email footer, and brand primary color in the sent email.
- **Fluid Checkout Compatibility**: Leverages delegated jQuery selectors to remain perfectly interactive even after multi-step checkout AJAX updates.
- **Theme Overrides**: Fully overridable HTML template file.

---

## Installation

1. Copy the plugin directory `wc-magic-login` to your WordPress installation directory `/wp-content/plugins/`.
2. Navigate to **Plugins** in the WordPress admin panel.
3. Click **Activate** on **WooCommerce Magic Login**.

---

## Configuration

Navigate to **WooCommerce > Settings > Magic Login** (WooCommerce > Configurações > Login Mágico) to access the settings panel:

- **General Settings**:
  - **Enable Plugin**: Turn magic links on/off globally.
  - **Expiration Time**: Set the time limit in minutes (default is 5 minutes).

- **Webhook Settings (EvolutionAPI / BaileysAPI / SMS gateways)**:
  - **Enable Webhook**: Enable to send messages via your external messaging server.
  - **Webhook URL**: Complete API endpoint.
  - **Custom Headers**: Enter custom authorization headers (one per line, e.g., `apikey: your-token` or `Authorization: Bearer token`).
  - **Payload Body**: Enter a raw JSON format. You can map the following dynamic placeholders:
    - `{phone}`: Customer's billing phone (automatically formatted for WhatsApp API compatibilities).
    - `{link}`: Absolute secure magic login link.
    - `{code}`: The 6-digit numeric OTP code.
    - `{name}`: Customer's display name or first name.

---

## Customizing the Interface Template

To customize the visual style or markup of the Magic Login form in your theme:
1. Create a directory named `woocommerce` inside your child theme directory.
2. Copy `/templates/magic-login-form.php` from this plugin.
3. Paste it in `your-theme/woocommerce/magic-login-form.php`.
4. Customize the HTML as desired.

---

<br>

---

# WooCommerce Magic Login (Português)

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
- **Sobrescrita por Temas**: Template HTML modular e customizável.

---

## Instalação

1. Copie o diretório do plugin `wc-magic-login` para a pasta de plugins da sua instalação do WordPress `/wp-content/plugins/`.
2. Vá para **Plugins** no painel administrativo do WordPress.
3. Clique em **Ativar** no plugin **WooCommerce Magic Login**.

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
    - `{phone}`: Celular do cliente (com formatação de DDI automática).
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

### 📫 Direct Contact / Contato Direto

[![E-mail](https://img.shields.io/badge/E--mail-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:contato@fredericodecastro.com.br)
[![WhatsApp](https://img.shields.io/badge/WhatsApp-25D366?style=for-the-badge&logo=whatsapp&logoColor=white)](https://wa.me/551114636944)
[![Telegram](https://img.shields.io/badge/Telegram-2CA5E0?style=for-the-badge&logo=telegram&logoColor=white)](https://t.me/fredericomdecastro)

