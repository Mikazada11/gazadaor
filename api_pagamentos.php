<?php
header('Content-Type: application/json');
require_once 'config.php';

$request = $_GET['request'] ?? '';

if ($request === 'criar_pedido') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || empty($data['itens'])) {
        echo json_encode(['success' => false, 'message' => 'Carrinho vazio.']);
        exit;
    }

    $user = $data['user'];
    $metodo = $data['metodo'];
    $total = 0;
    $itens_nomes = [];

    foreach ($data['itens'] as $item) {
        $total += $item['price'];
        $itens_nomes[] = $item['name'];
    }
    
    $lista_produtos = implode(', ', $itens_nomes);

    // 1. Criar o registo da venda na DB (Estado: Pendente)
    $stmt = $conn->prepare("INSERT INTO vendas (utilizador, produtos, valor, metodo, estado, data_criacao) VALUES (?, ?, ?, ?, 'pendente', NOW())");
    $stmt->bind_param("ssds", $user, $lista_produtos, $total, $metodo);
    
    if ($stmt->execute()) {
        $pedido_id = $conn->insert_id;
        echo json_encode([
            'success' => true, 
            'pedido_id' => $pedido_id,
            'message' => 'Pedido gerado com sucesso.'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro na DB.']);
    }
}