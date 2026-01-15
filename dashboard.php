<?php 
session_start(); 
require_once 'config.php'; 

if (!isset($_SESSION['username'])) { 
    header("Location: login.php"); 
    exit(); 
}

$user_logado = $_SESSION['username'];
$stmt = $conn->prepare("SELECT * FROM utilizadores WHERE utilizador = ?");
$stmt->bind_param("s", $user_logado);
$stmt->execute();
$dados = $stmt->get_result()->fetch_assoc();

$vip_atual = $dados['vip_nome'] ?? "Civil";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GAZADA RP | Dashboard Elite</title>
     <link rel="icon" type="image/png" href="images/BANNER_GAZADA_RP.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root { --primary: #ff0000; --bg: #050505; --sidebar: #0a0a0a; --card: #111111; --border: rgba(255,255,255,0.08); --text-sec: #888; }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background: var(--bg); color: #fff; display: flex; height: 100vh; overflow: hidden; }

        /* Sidebar */
        .sidebar { width: 260px; background: var(--sidebar); border-right: 1px solid var(--border); display: flex; flex-direction: column; padding: 30px 20px; }
        .logo { font-size: 22px; font-weight: 900; margin-bottom: 40px; text-align: center; letter-spacing: -1px; }
        .logo span { color: var(--primary); }
        .nav-link { 
            display: flex; align-items: center; gap: 12px; padding: 12px 15px; 
            color: var(--text-sec); text-decoration: none; border-radius: 12px; margin-bottom: 5px;
            font-weight: 600; cursor: pointer; transition: 0.3s; font-size: 14px;
        }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.03); }
        .nav-link.active { background: var(--primary); color: #fff; box-shadow: 0 8px 16px rgba(255,0,0,0.2); }

        /* Main Content */
        .main-content { flex: 1; overflow-y: auto; padding: 30px 50px; scroll-behavior: smooth; }
        .top-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }

        /* Hero & Stats */
        .hero-banner { 
            background: linear-gradient(135deg, #150000 0%, #050505 100%);
            border: 1px solid var(--border); padding: 40px; border-radius: 24px;
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;
        }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 30px; }
        .stat-card { background: var(--card); border: 1px solid var(--border); padding: 20px; border-radius: 20px; }
        .stat-card h4 { color: var(--text-sec); font-size: 11px; text-transform: uppercase; margin-bottom: 5px; }
        .stat-card p { font-size: 18px; font-weight: 800; }

        /* Loja */
        .category-header { border-bottom: 1px solid var(--border); padding-bottom: 10px; margin: 40px 0 20px; color: var(--primary); text-transform: uppercase; font-size: 13px; font-weight: 800; letter-spacing: 1px; }
        .grid-loja { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 15px; }
        .card-produto { background: var(--card); border: 1px solid var(--border); border-radius: 18px; padding: 20px; text-align: center; transition: 0.3s; display: flex; flex-direction: column; height: 100%; }
        .card-produto img { width: 100%; height: 120px; object-fit: contain; margin-bottom: 15px; }
        .card-produto .price { color: var(--primary); font-size: 32px; font-weight: 900; margin: 5px 0 15px 0; }
        .features-list { text-align: left; list-style: none; margin-bottom: 20px; flex-grow: 1; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 15px; }
        .features-list li { font-size: 11px; color: #777; margin-bottom: 4px; }
        .btn-comprar { width: 100%; padding: 12px; border-radius: 10px; border: 2px solid var(--primary); background: rgba(255,0,0,0.02); color: #fff; font-weight: 800; cursor: pointer; transition: 0.3s; text-transform: uppercase; font-size: 12px; }
        .btn-comprar:hover { background: var(--primary); box-shadow: 0 5px 15px rgba(255,0,0,0.3); }

        /* Suporte Area */
        .suporte-area { height: 500px; background: #080808; border-radius: 24px; border: 1px solid var(--border); overflow: hidden; display: flex; flex-direction: column; }
        .chat-window { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; }
        .msg { max-width: 75%; padding: 12px 16px; border-radius: 16px; font-size: 13px; line-height: 1.4; }
        .msg.admin { align-self: flex-start; background: #1a1a1a; border: 1px solid var(--border); color: #eee; }
        .msg.user { align-self: flex-end; background: var(--primary); color: #fff; }
        
        .loader { width: 30px; height: 30px; border: 3px solid rgba(255,0,0,0.1); border-top-color: var(--primary); border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Drawer Carrinho */
        .drawer { position: fixed; top: 0; right: -400px; width: 380px; height: 100vh; background: #0a0a0a; border-left: 1px solid var(--border); z-index: 2000; transition: 0.4s; padding: 25px; display: flex; flex-direction: column; }
        .drawer.open { right: 0; }
        .overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1999; display: none; backdrop-filter: blur(5px); }
        .overlay.show { display: block; }
        .badge { background: var(--primary); font-size: 10px; padding: 2px 6px; border-radius: 10px; position: absolute; top: -8px; right: -12px; font-weight: 800; }

        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="logo">GAZADA<span>CLIENTE</span></div>
        <div class="nav-link active" onclick="switchTab('inicio', this)"><i data-lucide="layout-dashboard"></i> Início</div>
        <div class="nav-link" onclick="switchTab('loja', this)"><i data-lucide="shopping-bag"></i> Loja VIP</div>
        <div class="nav-link" onclick="switchTab('chat', this)"><i data-lucide="message-square"></i> Suporte</div>
        <a href="logout.php" class="nav-link" style="margin-top: auto; color: #ff4444;"><i data-lucide="log-out"></i> Sair</a>
    </div>

    <div class="main-content">
        <div class="top-header">
            <h2 id="tab-title">Painel Geral</h2>
            <div style="display:flex; align-items:center; gap:20px;">
                <div style="position:relative; cursor:pointer;" onclick="toggleCart()">
                    <i data-lucide="shopping-cart"></i>
                    <span class="badge" id="cartBadge">0</span>
                </div>
                <div style="background:var(--card); padding:8px 18px; border-radius:50px; border:1px solid var(--border); font-weight:700; font-size:13px;">
                    <?= htmlspecialchars($user_logado) ?>
                </div>
            </div>
        </div>

        <div id="inicio" class="tab-content active">
            <div class="hero-banner">
                <div>
                    <h1 style="font-size: 32px; font-weight: 900; margin-bottom: 5px;">Olá, <?= htmlspecialchars($user_logado) ?></h1>
                    <p style="color: var(--text-sec);">Bem-vindo ao teu centro de gestão GAZADA RP.</p>
                </div>
                <i data-lucide="shield-check" size="60" color="rgba(255,0,0,0.15)"></i>
            </div>

            <div class="stats-grid">
                <div class="stat-card"><h4>VIP Atual</h4><p style="color:var(--primary)"><?= $vip_atual ?></p></div>
                <div class="stat-card"><h4>Servidor</h4><p style="color:#00ff00">ONLINE</p></div>
                <div class="stat-card"><h4>Suporte</h4><p>Disponível</p></div>
            </div>

            <div style="margin-top: 40px;">
                <h3 style="font-size: 13px; text-transform: uppercase; color: #444; letter-spacing: 1px; margin-bottom: 20px; font-weight: 800;">Estado da tua última compra</h3>
                
                <?php
                // Busca a última venda do utilizador
                $stmt_venda = $conn->prepare("SELECT id, estado, valor, metodo, codigo_resgate FROM vendas WHERE utilizador = ? ORDER BY id DESC LIMIT 1");
                $stmt_venda->bind_param("s", $user_logado);
                $stmt_venda->execute();
                $venda = $stmt_venda->get_result()->fetch_assoc();

                if ($venda):
                    $is_pago = ($venda['estado'] === 'pago');
                ?>
                <div style="background: var(--card); border: 1px solid var(--border); border-radius: 24px; padding: 30px; display: flex; align-items: center; justify-content: space-between;">
                    
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <div style="background: <?= $is_pago ? 'rgba(0,255,0,0.08)' : 'rgba(255,153,0,0.08)' ?>; padding: 15px; border-radius: 18px;">
                            <i data-lucide="<?= $is_pago ? 'package-check' : 'clock' ?>" color="<?= $is_pago ? '#00ff00' : '#ff9900' ?>"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 16px; font-weight: 800;">Pedido #<?= $venda['id'] ?></h4>
                            <p style="font-size: 13px; color: var(--text-sec);"><?= $venda['valor'] ?>€ via <?= strtoupper($venda['metodo']) ?></p>
                        </div>
                    </div>

                    <div style="text-align: right;">
                        <?php if ($is_pago): ?>
                            <div style="margin-bottom: 10px;"><span style="background: #00ff00; color: #000; padding: 5px 12px; border-radius: 50px; font-size: 10px; font-weight: 900; text-transform: uppercase;">Resgate Ativo</span></div>
                            <div style="display: flex; align-items: center; gap: 10px; background: #000; padding: 8px 15px; border-radius: 12px; border: 1px solid #1a1a1a;">
                                <code style="color: #fff; font-family: monospace; font-size: 16px; font-weight: 700; letter-spacing: 1px;"><?= $venda['codigo_resgate'] ?></code>
                                <i data-lucide="copy" size="14" style="cursor: pointer; color: #555;" onclick="navigator.clipboard.writeText('<?= $venda['codigo_resgate'] ?>'); alert('Código copiado!')"></i>
                            </div>
                        <?php else: ?>
                            <div style="margin-bottom: 10px;"><span style="background: #ff9900; color: #000; padding: 5px 12px; border-radius: 50px; font-size: 10px; font-weight: 900; text-transform: uppercase;">A aguardar processamento</span></div>
                            <p style="font-size: 12px; color: #555; max-width: 280px;">Após o pagamento ser detetado, receberás aqui o teu código num espaço de 30 minutos.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                    <div style="border: 2px dashed var(--border); border-radius: 24px; padding: 40px; text-align: center; color: #333;">
                        <i data-lucide="shopping-bag" size="40" style="margin-bottom: 10px; opacity: 0.3;"></i>
                        <p style="font-size: 14px;">Ainda não tens compras. Passa na nossa loja para veres os pacotes!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="loja" class="tab-content">
            <div id="loja-render"></div>
        </div>

        <div id="chat" class="tab-content">
            <div id="suporte-area" class="suporte-area"></div>
        </div>
    </div>

    <div class="overlay" id="cartOverlay" onclick="toggleCart()"></div>
    <div class="drawer" id="cartDrawer">
        <h3 style="margin-bottom:25px; display:flex; justify-content:space-between; align-items:center;">O Teu Cesto <i data-lucide="x" onclick="toggleCart()" style="cursor:pointer; width:20px;"></i></h3>
        <div id="cartItems" style="flex:1; overflow-y:auto;"></div>

        <div style="margin: 15px 0; padding: 15px; background: rgba(255,255,255,0.03); border-radius: 15px; border: 1px solid var(--border);">
            <p style="font-size: 10px; color: #555; font-weight: 800; text-transform: uppercase; margin-bottom: 12px; letter-spacing: 1px;">Método de Pagamento</p>
            <label style="display:flex; align-items:center; gap:10px; margin-bottom:10px; cursor:pointer; font-size:13px; font-weight:600;">
                <input type="radio" name="metodo" value="mbway" checked> <span>MB WAY</span>
            </label>
            <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-size:13px; font-weight:600;">
                <input type="radio" name="metodo" value="paypal"> <span>PayPal / Cartão</span>
            </label>
        </div>

        <div style="padding-top:20px; border-top:1px solid var(--border);">
            <div style="display:flex; justify-content:space-between; font-size:22px; font-weight:900; margin-bottom:20px;">
                <span>Total:</span><span id="cartTotal">0,00 €</span>
            </div>
            <button class="btn-comprar" style="background:var(--primary); font-size:13px; padding:16px;" onclick="checkout()">Finalizar Compra</button>
        </div>
    </div>

    <script>
        lucide.createIcons();
        const fmt = new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' });
        let cart = JSON.parse(localStorage.getItem('gazada_cart')) || [];
        let ticketAtivo = null;

async function switchTab(id, el) {
    // 1. Remove classes ativas
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.nav-link').forEach(n => n.classList.remove('active'));
    
    // 2. Ativa a aba correta
    const targetTab = document.getElementById(id);
    if(targetTab) {
        targetTab.classList.add('active');
        el.classList.add('active');
        document.getElementById('tab-title').innerText = el.innerText;
    }

    // 3. Gatilhos de conteúdo
    if(id === 'loja') {
        renderStore();
    }
    if(id === 'chat') {
        checkSuporte();
    }
}

async function renderStore() {
    const container = document.getElementById('loja-render');
    if(!container) return;
    
    container.innerHTML = "<p style='text-align:center; padding:50px; color:#555;'>A carregar produtos...</p>";

    try {
        const res = await fetch('produtos1.json?v=' + Date.now()); // Date.now evita cache
        const data = await res.json();
        const cats = { 
            "coins": "💰 Pacotes de Moedas", 
            "vip_mensal": "📅 VIPs Mensais", 
            "vip_exclusivo": "💎 Edições Exclusivas", 
            "extras": "🛠️ Serviços Extras" 
        };
        
        let html = "";
        for (let key in cats) {
            const items = data.filter(p => p.category === key);
            if(items.length > 0) {
                html += `<div class="category-header"><h3>${cats[key]}</h3></div><div class="grid-loja">`;
                html += items.map(p => `
                    <div class="card-produto">
                        <img src="${p.image || 'images/BANNER_GAZADA_RP.png'}" onerror="this.src='images/BANNER_GAZADA_RP.png'">
                        <h4>${p.name}</h4>
                        <div class="price">${fmt.format(p.price_coins)}</div>
                        <ul class="features-list">
                            ${(p.features || "Vantagens Exclusivas").split('\n').map(f => `<li>• ${f}</li>`).join('')}
                        </ul>
                        <button class="btn-comprar" onclick='add(${JSON.stringify(p)})'>Adicionar</button>
                    </div>`).join('') + `</div>`;
            }
        }
        container.innerHTML = html || "<p style='text-align:center; padding:50px;'>Nenhum produto disponível.</p>";
        lucide.createIcons();
    } catch (e) { 
        container.innerHTML = "<p style='text-align:center; padding:50px; color:red;'>Erro ao ler produtos1.json</p>";
    }
}
let estadoTicketAnterior = null; // Variável global para controlar mudanças

async function checkSuporte() {
    const res = await fetch(`api_suporte.php?request=check_status&user=<?= $user_logado ?>`);
    const ticket = await res.json();
    const area = document.getElementById('suporte-area');
    
    // Define o estado atual (se não houver ticket, o estado é 'nenhum')
    const estadoAtual = ticket ? ticket.estado : 'nenhum';

    // SÓ ATUALIZA O HTML SE O ESTADO MUDOU
    if (estadoAtual !== estadoTicketAnterior) {
        estadoTicketAnterior = estadoAtual; // Guarda o novo estado
        
        if (estadoAtual === 'nenhum') {
            ticketAtivo = null;
            area.innerHTML = `
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:20px;padding:40px;text-align:center;">
                    <div style="background:rgba(255,0,0,0.1);padding:20px;border-radius:50%;"><i data-lucide="help-circle" size="40" color="#ff0000"></i></div>
                    <h3>Como podemos ajudar?</h3>
                    <input type="text" id="assunto" placeholder="Assunto do ticket..." style="background:#111;border:1px solid #333;color:#fff;padding:12px;border-radius:12px;width:300px;outline:none;">
                    <button onclick="abrirTicket()" class="btn-comprar" style="width:300px;">Abrir Ticket</button>
                </div>`;
        } 
        else if (estadoAtual === 'pendente') {
            area.innerHTML = `
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;text-align:center;">
                    <div class="loader"></div>
                    <h3 style="color:#ffcc00;margin-top:20px;">Aguardando Staff...</h3>
                    <p style="font-size:13px; color:#555; margin-top:10px;">Assunto: <b>${ticket.assunto}</b></p>
                </div>`;
        } 
        else if (estadoAtual === 'concluido') {
            area.innerHTML = `
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;padding:40px;text-align:center;">
                    <i data-lucide="check-circle" size="60" color="#00ff00"></i>
                    <h2 style="margin:20px 0 10px 0;">Atendimento Concluído</h2>
                    <button onclick="confirmarFecho(${ticket.id})" class="btn-comprar" style="background:#00ff00;border:none;color:#000;width:250px;">Confirmar e Fechar</button>
                </div>`;
        } 
        else {
            // ESTADO: em_andamento
            ticketAtivo = ticket.id;
            area.innerHTML = `
                <div style="background:#0a0a0a;padding:15px 25px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <span style="font-weight:800;color:var(--primary);font-size:12px;">TICKET #${ticket.id}</span>
                        <p style="font-size:11px; color:#555;">Assunto: ${ticket.assunto}</p>
                    </div>
                </div>
                <div class="chat-window" id="msgWin"></div>
                <div style="padding:20px;background:#0a0a0a;border-top:1px solid var(--border);display:flex;gap:12px;">
                    <input type="text" id="msgIn" onkeypress="if(event.key==='Enter') sendChatMsg()" placeholder="Escreve aqui..." style="flex:1;background:#111;border:1px solid #222;border-radius:12px;padding:14px;color:#fff;outline:none;">
                    <button onclick="sendChatMsg()" style="background:var(--primary);color:#fff;border:none;width:50px;border-radius:12px;cursor:pointer;"><i data-lucide="send" size="20"></i></button>
                </div>`;
            refreshChat();
        }
        lucide.createIcons();
    }
}
        async function abrirTicket() {
            const ass = document.getElementById('assunto').value;
            if(!ass) return alert("Indica um assunto.");
            await fetch('api_suporte.php?request=abrir_ticket', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ user: '<?= $user_logado ?>', assunto: ass }) });
            checkSuporte();
        }

        async function confirmarFecho(id) {
            await fetch(`api_suporte.php?request=confirmar_fecho_cliente&id=${id}`);
            ticketAtivo = null;
            checkSuporte();
        }

        async function sendChatMsg() {
            const input = document.getElementById('msgIn');
            if(!input.value.trim()) return;
            await fetch('api_suporte.php?request=send_msg', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ ticket_id: ticketAtivo, remetente: 'cliente', mensagem: input.value }) });
            input.value = "";
            refreshChat();
        }

        async function refreshChat() {
            if(!ticketAtivo) return;
            const res = await fetch(`api_suporte.php?request=get_chat&ticket_id=${ticketAtivo}`);
            const msgs = await res.json();
            const win = document.getElementById('msgWin');
            if(win) {
                win.innerHTML = msgs.map(m => `<div class="msg ${m.enviado_por === 'admin' ? 'admin' : 'user'}">${m.mensagem}</div>`).join('');
                win.scrollTop = win.scrollHeight;
            }
        }

        function add(p) { cart.push({ name: p.name, price: p.price_coins, image: p.image }); updateUI(); }
        function rem(idx) { cart.splice(idx, 1); updateUI(); }
        function updateUI() {
            localStorage.setItem('gazada_cart', JSON.stringify(cart));
            document.getElementById('cartBadge').innerText = cart.length;
            let total = 0;
            document.getElementById('cartItems').innerHTML = cart.map((i, idx) => {
                total += i.price;
                return `<div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--border);"><img src="${i.image}" style="width:35px;"><div style="flex:1"><h5 style="font-size:13px;">${i.name}</h5><span style="color:var(--primary);font-size:12px;font-weight:700;">${fmt.format(i.price)}</span></div><i data-lucide="trash-2" size="16" style="color:#ff4444;cursor:pointer;" onclick="rem(${idx})"></i></div>`;
            }).join('');
            document.getElementById('cartTotal').innerText = fmt.format(total);
            lucide.createIcons();
        }

        async function checkout() {
            if (cart.length === 0) return alert("Cesto vazio!");
            const metodo = document.querySelector('input[name="metodo"]:checked').value;
            const res = await fetch('api_pagamentos.php?request=criar_pedido', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ user: '<?= $user_logado ?>', itens: cart, metodo: metodo }) });
            const data = await res.json();
            if(data.success) {
                cart = [];
                updateUI();
                window.location.href = 'fatura.php?id=' + data.pedido_id;
            }
        }

        function toggleCart() { document.getElementById('cartDrawer').classList.toggle('open'); document.getElementById('cartOverlay').classList.toggle('show'); }

        window.onload = () => { updateUI(); };
setInterval(() => { 
    // Atualiza apenas as mensagens se houver um ticket ativo
    if(ticketAtivo) {
        refreshChat(); 
    }
    
    // Verifica se o estado do ticket mudou (ex: de pendente para em andamento)
    // apenas se estiveres na tab de suporte
    if(document.getElementById('chat').classList.contains('active')) {
        checkSuporte();
    }
}, 4000);
    </script>
</body>
</html>