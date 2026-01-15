<?php 
require_once 'config.php'; 
session_start();

// --- LÓGICA DE ADMINISTRAÇÃO ---

// 1. Aprovar Venda com Código Manual
if (isset($_POST['aprovar_venda'])) {
    $id_venda = $_POST['id_venda'];
    $codigo_manual = $_POST['codigo_manual'];
    
    if(!empty($codigo_manual)) {
        $stmt = $conn->prepare("UPDATE vendas SET estado = 'pago', codigo_resgate = ? WHERE id = ?");
        $stmt->bind_param("si", $codigo_manual, $id_venda);
        if($stmt->execute()) {
            // Ajustado para admin.php (garante que voltas à mesma página)
            echo "<script>alert('Código enviado com sucesso!'); window.location.href='painel.php';</script>";
        }
    }
}

// 2. Eliminar Venda
if (isset($_POST['eliminar_venda'])) {
    $id_venda = $_POST['id_venda'];
    $stmt = $conn->prepare("DELETE FROM vendas WHERE id = ?");
    $stmt->bind_param("i", $id_venda);
    if($stmt->execute()) {
        echo "<script>alert('Registo eliminado!'); window.location.href='painel.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>GAZADA RP | Painel Administrativo</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root { --primary: #ff0000; --bg: #070707; --panel: rgba(255, 255, 255, 0.03); --stroke: rgba(255, 255, 255, 0.08); --text-sec: #666; }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background: var(--bg); color: #fff; display: flex; height: 100vh; overflow: hidden; }

        /* Sidebar */
        .sidebar { width: 280px; border-right: 1px solid var(--stroke); padding: 40px 20px; display: flex; flex-direction: column; background: #000; z-index: 100; }
        .logo { font-size: 24px; font-weight: 900; margin-bottom: 50px; text-align: center; letter-spacing: -1px; }
        .logo span { color: var(--primary); }
        .nav-btn { padding: 15px; color: #888; border-radius: 14px; margin-bottom: 8px; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 12px; font-weight: 600; border: none; background: none; width: 100%; text-align: left; }
        .nav-btn:hover, .nav-btn.active { background: var(--panel); color: #fff; }
        .nav-btn.active { border-left: 4px solid var(--primary); border-radius: 4px 14px 14px 4px; }

        /* Content */
        .main { flex: 1; padding: 50px; overflow-y: auto; position: relative; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        
        /* Tabelas e Cards */
        .card { background: var(--panel); border: 1px solid var(--stroke); border-radius: 24px; padding: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: #444; font-size: 11px; text-transform: uppercase; padding: 15px; font-weight: 800; border-bottom: 1px solid var(--stroke); }
        td { padding: 18px 15px; border-bottom: 1px solid var(--stroke); font-size: 14px; vertical-align: middle; }
        
        .prod-img { width: 45px; height: 45px; border-radius: 10px; object-fit: cover; background: #000; border: 1px solid var(--stroke); }

        /* Badges */
        .badge { padding: 5px 12px; border-radius: 8px; font-size: 10px; font-weight: 800; text-transform: uppercase; display: inline-flex; align-items: center; gap: 5px; }
        .badge-pendente { background: rgba(255, 204, 0, 0.1); color: #ffcc00; }
        .badge-atendimento { background: rgba(0, 255, 0, 0.1); color: #00ff00; }
        .badge-pago { background: rgba(0, 255, 0, 0.1); color: #00ff00; }

        /* Inputs e Botões */
        .input-codigo { background: #000; border: 1px solid var(--stroke); color: #fff; padding: 10px; border-radius: 10px; font-size: 12px; width: 180px; outline: none; }
        .btn-action { background: var(--primary); color: #fff; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 12px; display: flex; align-items: center; gap: 8px; }
        .btn-aprovar { background: #00ff00; color: #000; border: none; padding: 10px 15px; border-radius: 10px; font-weight: 800; font-size: 11px; cursor: pointer; }
        .btn-delete { background: rgba(255, 255, 255, 0.05); color: #ff4444; border: 1px solid var(--stroke); padding: 8px; border-radius: 8px; cursor: pointer; }

        /* Modal Chat */
        #modalChat { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.95); z-index:9999; justify-content:center; align-items:center; backdrop-filter: blur(10px); }
        .chat-wrap { background: #0c0c0c; border: 1px solid var(--stroke); width: 600px; height: 700px; border-radius: 30px; display:flex; flex-direction:column; }
        #chat-content { flex:1; overflow-y:auto; padding:30px; display:flex; flex-direction:column; gap:12px; }
        .msg { padding: 14px 18px; border-radius: 18px; font-size: 13px; max-width: 80%; line-height: 1.5; }
        .msg.admin { align-self: flex-end; background: var(--primary); color: #fff; border-bottom-right-radius: 4px; }
        .msg.cliente { align-self: flex-start; background: #222; color: #eee; border-bottom-left-radius: 4px; }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="logo">GAZADA<span>ADMIN</span></div>
         <link rel="icon" type="image/png" href="images/BANNER_GAZADA_RP.png">
        <button class="nav-btn active" onclick="switchTab('prods', this)"><i data-lucide="package"></i> Inventário</button>
        <button class="nav-btn" onclick="switchTab('vendas', this)"><i data-lucide="shopping-cart"></i> Vendas & Códigos</button>
        <button class="nav-btn" onclick="switchTab('suporte', this)"><i data-lucide="message-circle"></i> Fila de Suporte</button>
        <a href="logout.php" class="nav-btn" style="margin-top:auto; color:#ff4444;"><i data-lucide="log-out"></i> Encerrar Sessão</a>
    </aside>

    <main class="main">
        <div id="prods" class="tab-content active">
            <div class="header">
                <h1>Produtos Ativos</h1>
                <button class="btn-action" onclick="location.href='novo_produto.php'"><i data-lucide="plus"></i> Novo Item</button>
            </div>
            <div class="card">
                <table>
                    <thead>
                        <tr><th style="width: 60px;">Img</th><th>Produto</th><th>Categoria</th><th>Preço</th><th style="text-align:right">Ações</th></tr>
                    </thead>
                    <tbody id="lista-prods"></tbody>
                </table>
            </div>
        </div>

        <div id="vendas" class="tab-content">
            <div class="header">
                <h1>Gestão de Pagamentos</h1>
                <p style="color:var(--text-sec); font-size:13px;">Aprova pedidos enviando o código ou remove registos.</p>
            </div>
            <div class="card">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Cliente</th><th>Valor</th><th>Estado</th><th>Código a Enviar</th><th style="text-align:right">Ações</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $vendas = $conn->query("SELECT * FROM vendas ORDER BY id DESC LIMIT 50");
                        while($v = $vendas->fetch_assoc()):
                            $is_pago = ($v['estado'] === 'pago');
                        ?>
                        <tr>
                            <td>#<?= $v['id'] ?></td>
                            <td><b><?= htmlspecialchars($v['utilizador']) ?></b></td>
                            <td style="color:#00ff00; font-weight:800;"><?= $v['valor'] ?>€</td>
                            <td><span class="badge <?= $is_pago ? 'badge-pago' : 'badge-pendente' ?>"><?= $v['estado'] ?></span></td>
                            <td>
                                <?php if(!$is_pago): ?>
                                    <form id="f-<?= $v['id'] ?>" method="POST" style="display:flex; gap:8px;">
                                        <input type="hidden" name="id_venda" value="<?= $v['id'] ?>">
                                        <input type="text" name="codigo_manual" class="input-codigo" placeholder="Escreve o código..." required>
                                <?php else: ?>
                                    <code style="color:#00ff00; background:#000; padding:5px 10px; border-radius:8px; border:1px solid #222;"><?= $v['codigo_resgate'] ?></code>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right">
                                <div style="display:flex; gap:8px; justify-content:flex-end; align-items:center;">
                                    <?php if(!$is_pago): ?>
                                        <button type="submit" name="aprovar_venda" class="btn-aprovar">ENVIAR</button></form>
                                    <?php endif; ?>
                                    <form method="POST" onsubmit="return confirm('Eliminar venda?')">
                                        <input type="hidden" name="id_venda" value="<?= $v['id'] ?>">
                                        <button type="submit" name="eliminar_venda" class="btn-delete"><i data-lucide="trash-2" size="16"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="suporte" class="tab-content">
            <div class="header">
                <h1>Tickets de Suporte</h1>
                <div id="badge-total" style="background:var(--primary); padding:5px 15px; border-radius:10px; font-size:12px; font-weight:900;">0 Tickets</div>
            </div>
            <div class="card">
                <table>
                    <thead>
                        <tr><th>ID</th><th>Utilizador</th><th>Assunto</th><th>Estado</th><th style="text-align:right">Ação</th></tr>
                    </thead>
                    <tbody id="lista-tickets"></tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="modalChat">
        <div class="chat-wrap">
            <div style="padding:25px; border-bottom:1px solid var(--stroke); display:flex; justify-content:space-between; align-items:center;">
                <div><h3 id="chat-user-name">...</h3><span id="chat-ticket-id" style="font-size:11px; color:var(--text-sec);"></span></div>
                <div style="display:flex; gap:12px;">
                    <button onclick="concluirTicket()" style="background:#ff0000; color:#fff; border:none; padding:8px 15px; border-radius:10px; font-weight:800; cursor:pointer;">CONCLUIR</button>
                    <button onclick="closeChat()" style="background:none; border:none; color:#555; cursor:pointer;"><i data-lucide="x"></i></button>
                </div>
            </div>
            <div id="chat-content"></div>
            <div style="padding:25px; border-top:1px solid var(--stroke); display:flex; gap:12px;">
                <input type="text" id="adminIn" onkeypress="if(event.key==='Enter') sendAdminMsg()" placeholder="Responder..." style="flex:1; background:#000; border:1px solid var(--stroke); padding:15px; border-radius:15px; color:#fff; outline:none;">
                <button onclick="sendAdminMsg()" class="btn-action">ENVIAR</button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        let ticketAtual = null;

        function switchTab(id, el) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(id).classList.add('active');
            el.classList.add('active');
            if(id === 'prods') loadProds(); else if(id === 'suporte') loadTickets();
            lucide.createIcons();
        }

async function loadProds() {
    const res = await fetch('api_admin.php?request=list');
    const data = await res.json();
    document.getElementById('lista-prods').innerHTML = data.map(p => `
        <tr>
            <td><img src="${p.image}" class="prod-img"></td>
            <td><b>${p.name}</b></td>
            <td>${p.category}</td>
            <td style="color:#00ff00">${p.price_coins}€</td>
            <td style="text-align:right">
                <div style="display:flex; gap:8px; justify-content:flex-end;">
                    <button class="btn-action" style="padding:8px" onclick="location.href='editar_produto.php?id=${p.id}'">
                        <i data-lucide="edit-3" size="14"></i>
                    </button>
                    <button class="btn-delete" style="padding:8px" onclick="deleteProd('${p.id}')">
                        <i data-lucide="trash-2" size="14"></i>
                    </button>
                </div>
            </td>
        </tr>`).join('');
    lucide.createIcons();
}

// Adiciona esta nova função logo abaixo do loadProds
async function deleteProd(id) {
    if(!confirm("Desejas eliminar este produto permanentemente?")) return;
    
    // Chamada para a nova rota unificada
    const res = await fetch(`api_admin.php?request=delete&id=${id}`);
    const data = await res.json();
    
    if(data.status === 'ok') {
        loadProds(); // Atualiza a tabela automaticamente
    } else {
        alert("Erro ao eliminar o produto.");
    }
}

        async function loadTickets() {
            const res = await fetch('api_suporte.php?request=get_all_tickets'); 
            const tickets = await res.json();
            const ativos = tickets.filter(t => t.estado !== 'fechado');
            document.getElementById('badge-total').innerText = ativos.length + " Tickets Ativos";
            document.getElementById('lista-tickets').innerHTML = ativos.map(t => {
                let badgeClass = t.estado === 'pendente' ? 'badge-pendente' : 'badge-atendimento';
                return `<tr>
                    <td>#${t.id}</td><td><b>${t.utilizador_nome}</b></td><td>${t.assunto}</td>
                    <td><span class="badge ${badgeClass}">${t.estado}</span></td>
                    <td style="text-align:right"><button onclick="abrirChat(${t.id},'${t.utilizador_nome}')" class="btn-action">Abrir Chat</button></td>
                </tr>`;
            }).join('');
            lucide.createIcons();
        }

        // FUNÇÃO CORRIGIDA: Avisa o cliente que o suporte chegou
        async function abrirChat(id, user) {
            // Avisar API para mudar estado de 'pendente' para 'em_atendimento'
            await fetch(`api_suporte.php?request=aceitar_ticket&id=${id}`);
            
            ticketAtual = id;
            document.getElementById('chat-user-name').innerText = user;
            document.getElementById('chat-ticket-id').innerText = "Ticket #" + id;
            document.getElementById('modalChat').style.display = 'flex';
            refreshAdminChat();
        }

        async function refreshAdminChat() {
            if(!ticketAtual) return;
            const res = await fetch(`api_suporte.php?request=get_chat&ticket_id=${ticketAtual}`);
            const msgs = await res.json();
            const box = document.getElementById('chat-content');
            box.innerHTML = msgs.map(m => `<div class="msg ${m.enviado_por === 'admin' ? 'admin' : 'cliente'}">${m.mensagem}</div>`).join('');
            box.scrollTop = box.scrollHeight;
        }

        async function sendAdminMsg() {
            const input = document.getElementById('adminIn');
            if(!input.value.trim()) return;
            await fetch('api_suporte.php?request=send_msg', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ ticket_id: ticketAtual, remetente: 'admin', mensagem: input.value })
            });
            input.value = "";
            refreshAdminChat();
        }

        async function concluirTicket() {
            if(!confirm("Concluir ticket?")) return;
            await fetch(`api_suporte.php?request=concluir_ticket&id=${ticketAtual}`);
            closeChat();
        }

        function closeChat() { ticketAtual = null; document.getElementById('modalChat').style.display = 'none'; loadTickets(); }

        setInterval(() => {
            if(ticketAtual) refreshAdminChat();
            if(document.getElementById('suporte').classList.contains('active')) loadTickets();
        }, 4000);

        window.onload = loadProds;
    </script>
</body>
</html>