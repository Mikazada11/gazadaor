<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['username']) || !isset($_POST['item_id'])) {
    header("Location: dashboard.php");
    exit();
}

$user = $_SESSION['username'];
$id = $_POST['item_id'];

// 1. Validar item no JSON
$produtos = json_decode(file_get_contents('produtos.json'), true);
$venda = null;
foreach ($produtos as $p) { if ($p['id'] == $id) { $venda = $p; break; } }

if (!$venda) die("Erro: Produto inexistente.");

// 2. Verificar Saldo
$stmt = $conn->prepare("SELECT coins FROM utilizadores WHERE utilizador = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$saldo = $stmt->get_result()->fetch_assoc()['coins'];

if ($saldo < $venda['price_coins']) {
    echo "<script>alert('Saldo insuficiente!'); window.location='dashboard.php';</script>";
    exit();
}

// 3. Processar
$novo_saldo = $saldo - $venda['price_coins'];
$nome_vip = $venda['name'];
$expira = date('Y-m-d', strtotime('+30 days'));

$conn->begin_transaction();
try {
    $conn->query("UPDATE utilizadores SET coins = $novo_saldo, vip_nome = '$nome_vip', vip_expira = '$expira' WHERE utilizador = '$user'");
    $conn->query("INSERT INTO compras (username, produto, valor) VALUES ('$user', '$nome_vip', {$venda['price_coins']})");
    $conn->commit();
    echo "<script>alert('Compra efetuada! Aproveita o teu VIP.'); window.location='dashboard.php';</script>";
} catch (Exception $e) {
    $conn->rollback();
    echo "Erro ao processar a compra.";
}