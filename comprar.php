<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['username'])) {
    $user = $_SESSION['username'];
    $prod_id = $_POST['product_id'];

    // 1. Pega dados do JSON
    $json = json_decode(file_get_contents('../produtos.json'), true);
    $item_selecionado = null;
    foreach($json as $item) { if($item['id'] == $prod_id) { $item_selecionado = $item; break; } }

    if (!$item_selecionado) die("Produto inválido.");

    // 2. Verifica saldo na BD
    $stmt = $conn->prepare("SELECT coins FROM utilizadores WHERE utilizador = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $saldo = $stmt->get_result()->fetch_assoc()['coins'];

    if ($saldo >= $item_selecionado['price_coins']) {
        $novo_saldo = $saldo - $item_selecionado['price_coins'];
        $vip_nome = $item_selecionado['name'];

        // 3. Atualiza BD
        $update = $conn->prepare("UPDATE utilizadores SET coins = ?, vip_nome = ? WHERE utilizador = ?");
        $update->bind_param("iss", $novo_saldo, $vip_nome, $user);
        
        if ($update->execute()) {
            echo "<script>alert('Sucesso! Já tens o teu VIP.'); window.location='dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Saldo insuficiente!'); window.location='dashboard.php';</script>";
    }
}