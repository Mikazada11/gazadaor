# GAZADA Store — Setup (Website + FiveM Delivery)

Este pack inclui:
- Website (HTML/CSS/JS): `index.html`, `vips.html`, `compras.html`
- Fonts: `fonts/AnotherDanger.ttf`
- (Opcional) FiveM resource para entrega automática (quando ligado a um sistema de pagamentos)

---

## 1) Requisitos
- Um host para o website (qualquer um serve: VPS, cPanel, Cloudflare Pages, Netlify, etc.)
- Um servidor FiveM (ESX ou QBCore)
- Um sistema de pagamentos (recomendado: Tebex)

---

## 2) Instalar o Website
1. Faz upload desta estrutura para o teu host:
   - `index.html`
   - `vips.html`
   - `compras.html`
   - `css/`
   - `js/`
   - `fonts/`
   - `img/` (se usares imagens de fundo)

2. Confirma os ficheiros de imagem (se existirem):
   - `img/hero.jpg`
   - `img/vips-bg.jpg`

Se estiverem noutra pasta, atualiza no CSS: `url('img/vips-bg.jpg')`.

---

## 3) Alterar Links (Discord e Navegação)
No HTML, procura por:
- Botão `Discord` (classe `btn-download`)
- Links do menu (navbar)

Troca o `href="#"` do Discord pelo link real do servidor.

Exemplo:
```html
<a href="https://discord.gg/TEU_LINK" class="btn-download">Discord</a>
