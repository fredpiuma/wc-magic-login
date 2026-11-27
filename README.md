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
- **Notification-Optimized Email Subject**: Places the 6-digit OTP code at the very beginning of the email subject line (e.g., `123456 is your login code`), letting users view and copy it directly from their mobile notifications screen.
- **Theme Overrides**: Fully overridable HTML template file.

---

## Installation

**[Download the plugin (latest release zip)](https://github.com/fredpiuma/wc-magic-login/releases/latest/download/wc-magic-login.zip)**

This zip contains only the plugin files. Do not install GitHub's "Source code (zip)": it includes development files and the folder gets the wrong name.

### From the WordPress dashboard

1. Download the zip from the link above.
2. Go to **Plugins → Add New → Upload Plugin**, choose the file and click **Install Now**.
3. Activate **WooCommerce Magic Login**.

To update, upload the new version's zip the same way and confirm replacing the installed version.

### With WP-CLI

```bash
wp plugin install https://github.com/fredpiuma/wc-magic-login/releases/latest/download/wc-magic-login.zip --force --activate
```

The same command installs and updates: `--force` replaces the installed version with the latest one, since the plugin has no automatic updates from the dashboard.

Requires WordPress 5.6+, PHP 7.4+ and WooCommerce 5.0+.

See the [CHANGELOG](CHANGELOG.md) for release notes.

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
    - `{phone}`: Customer's billing phone (automatically formatted for WhatsApp API compatibilities. For absolute security, this phone is retrieved strictly from the customer's verified user profile in the WordPress database and cannot be modified or input during the login process, preventing token hijacking).
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

### 📫 Direct Contact / Contato Direto

[![E-mail](https://img.shields.io/badge/E--mail-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:contato@fredericodecastro.com.br)
[![WhatsApp](https://img.shields.io/badge/WhatsApp-25D366?style=for-the-badge&logo=whatsapp&logoColor=white)](https://wa.me/551114636944)
[![Telegram](https://img.shields.io/badge/Telegram-2CA5E0?style=for-the-badge&logo=telegram&logoColor=white)](https://t.me/fredericomdecastro)

