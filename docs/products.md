# GAZADA Store — Produtos & Entrega Automática (FiveM)

Este documento explica como os produtos do website estão organizados
e como devem ser ligados ao sistema de pagamentos e ao servidor FiveM.

---

## 1) Conceito geral (muito importante)
- O website **não entrega itens sozinho**
- Ele apenas envia **o ID do produto**
- O sistema de pagamentos (ex: Tebex) usa esse ID
- O servidor FiveM recebe esse ID e **entrega o item/VIP automaticamente**

Fluxo:
Website → Pagamento → FiveM → Entrega automática

---

## 2) IDs de produtos (Website)
Os IDs abaixo são usados:
- No `compras.html` (atributo `value=""`)
- No sistema de pagamentos (produto)
- No resource FiveM (entrega)

⚠️ **Os IDs têm de ser exatamente iguais em todo o lado**

---

## 3) Lista de produtos

### COINS
| ID do Produto | Nome apresentado | Preço | Entrega FiveM |
|--------------|------------------|-------|---------------|
| coins10 | Pack 10 Coins | €9.99 | +10 coins |
| coins25 | Pack 25 Coins | €20.99 | +25 coins |
| coins50 | Pack 50 Coins | €42.99 | +50 coins |
| coins100 | Pack 100 Coins | €95.99 | +100 coins |

Entrega favorecida:
- Item (`coins`)
- Ou account (`coins`, `bank`, `black_money`)
- Ou variável custom do servidor

---

### VIPs MENSAIS
| ID do Produto | Nome | Preço | Tipo |
|--------------|------|-------|------|
| vip_diamante | VIP Diamante | €9.90 | Mensal |
| vip_gazada | VIP Gazada | €12.50 | Mensal |

Entrega recomendada:
- Dar **grupo/permission**
- Guardar **data de expiração** na base de dados
- Remover automaticamente ao fim de 30 dias

---

### VIPs EXCLUSIVOS
| ID do Produto | Nome | Preço | Tipo |
|--------------|------|-------|------|
| vip_dezembro | VIP Dezembro | €25.00 | Exclusivo |
| vip_janeiro  | VIP Janeiro  | €25.00 | Exclusivo |
| vip_dezembro | VIP Dezembro | €25.00 | Exclusivo |

Entrega:
- Grupo exclusivo
- VIP permanente ou mensal (à escolha do servidor)

---

### EXTRAS
| ID do Produto | Nome | Preço | Entrega |
|--------------|------|-------|---------|
| extra_galinha | Galinha VIP | €5.99 | Item / perk |

Entrega possível:
- Item no inventário
- Aumento de capacidade
- Perk permanente

---

## 4) Exemplo de ligação no Website
No `compras.html`:

```html
<option value="coins10" data-price="9.99">
  Coins — Pack 10 (€9.99)
</option>
