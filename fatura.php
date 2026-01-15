<?php
require_once 'config.php';

$pedido_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Procura os detalhes do pedido na base de dados
$stmt = $conn->prepare("SELECT * FROM vendas WHERE id = ?");
$stmt->bind_param("i", $pedido_id);
$stmt->execute();
$venda = $stmt->get_result()->fetch_assoc();

if (!$venda) {
    die("Pedido não encontrado.");
}

// Configurações fictícias (Altera para os teus dados reais)
$telemovel_mbway = "912 345 678"; 
$email_paypal = "pagamentos@gazadarp.pt";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Pagamento #<?= $pedido_id ?> | GAZADA RP</title>
     <link rel="icon" type="image/png" href="images/BANNER_GAZADA_RP.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root { --primary: #ff0000; --bg: #050505; --card: #0a0a0a; --stroke: rgba(255,255,255,0.06); }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background: var(--bg); color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; }
        
        .fatura-box { background: var(--card); border: 1px solid var(--stroke); width: 450px; padding: 40px; border-radius: 35px; box-shadow: 0 40px 100px rgba(0,0,0,0.5); text-align: center; }
        
        .status-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 204, 0, 0.1); color: #ffcc00; padding: 8px 16px; border-radius: 50px; font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 25px; }
        .status-badge.pulse { animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }

        .price-tag { background: #000; border: 1px solid var(--stroke); padding: 25px; border-radius: 20px; margin: 25px 0; }
        .price-tag h1 { font-size: 42px; font-weight: 900; color: var(--primary); }
        
        .metodo-info { text-align: left; background: rgba(255,255,255,0.02); border: 1px solid var(--stroke); padding: 20px; border-radius: 18px; margin-bottom: 25px; }
        .metodo-info b { color: var(--primary); font-size: 18px; display: block; margin-top: 5px; }

        .btn { display: block; width: 100%; padding: 16px; border-radius: 14px; border: none; font-weight: 800; cursor: pointer; transition: 0.3s; text-decoration: none; margin-bottom: 10px; font-size: 14px; }
        .btn-primary { background: #fff; color: #000; }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(255,255,255,0.1); }
        .btn-outline { background: transparent; border: 1px solid var(--stroke); color: #666; }
    </style>
</head>
<body>

    <div class="fatura-box">
        <div class="status-badge pulse">
            <i data-lucide="clock" size="14"></i> Aguardando Pagamento
        </div>

        <h2 style="font-weight: 900; letter-spacing: -1px;">Pedido #<?= $pedido_id ?></h2>
        <p style="color: #555; font-size: 14px;">Utilizador: <?= htmlspecialchars($venda['utilizador']) ?></p>

        <div class="price-tag">
            <p style="font-size: 11px; color: #444; font-weight: 800; text-transform: uppercase;">Total a Pagar</p>
            <h1><?= number_format($venda['valor'], 2, ',', '.') ?> €</h1>
        </div>

        <div class="metodo-info">
            <?php if($venda['metodo'] === 'mbway'): ?>
                <p style="font-size: 12px; color: #666;">Envia o valor via <b>MB WAY</b> para:</p>
                <b><?= $telemovel_mbway ?></b>
            <?php else: ?>
                <p style="font-size: 12px; color: #666;">Envia o valor via <b>PayPal</b> para:</p>
                <b><?= $email_paypal ?></b>
            <?php endif; ?>
        </div>

        <div style="margin-bottom: 25px;">
            <p style="font-size: 12px; color: #444; line-height: 1.5;">
                <i data-lucide="info" size="12"></i> Após o envio, o nosso sistema detetará o pagamento e ativará os teus produtos automaticamente em poucos minutos.
            </p>
        </div>

        <a href="dashboard.php" class="btn btn-primary">VERIFICAR NO DASHBOARD</a>
        <a href="#" onclick="window.print()" class="btn btn-outline">IMPRIMIR COMPROVATIVO</a>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>