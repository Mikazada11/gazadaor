<?php
require_once 'config.php';
header('Content-Type: application/json');

$request = $_GET['request'] ?? '';

// --- LISTAGEM DE PRODUTOS (Lê do JSON) ---
if ($request === 'list') {
    $jsonFile = 'produtos1.json';
    if (file_exists($jsonFile)) {
        echo file_get_contents($jsonFile);
    } else {
        echo json_encode([]);
    }
}

// --- APAGAR PRODUTO (Remove do JSON e apaga a Imagem) ---
if ($request === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $jsonFile = 'produtos1.json';
    
    if (file_exists($jsonFile)) {
        $data = json_decode(file_get_contents($jsonFile), true);
        
        // Procuramos o produto para apagar a imagem do servidor antes de remover do JSON
        foreach ($data as $key => $p) {
            if ($p['id'] == $id) {
                // Se a imagem não for a padrão, tentamos apagar o ficheiro físico
                if (!empty($p['image']) && strpos($p['image'], 'BANNER_GAZADA') === false) {
                    if (file_exists($p['image'])) {
                        unlink($p['image']);
                    }
                }
                unset($data[$key]);
            }
        }
        
        // Guardamos o JSON atualizado
        file_put_contents($jsonFile, json_encode(array_values($data), JSON_PRETTY_PRINT));
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ficheiro não encontrado']);
    }
}

// --- GESTÃO DE TICKETS (Base de Dados SQL) ---
if ($request === 'get_all_tickets') {
    $result = $conn->query("SELECT * FROM suporte_tickets ORDER BY data_criacao DESC");
    $tickets = [];
    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
    echo json_encode($tickets);
}

// Rota para eliminar tickets (opcional)
if($request === 'delete_ticket' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM suporte_tickets WHERE id = $id");
    echo json_encode(['status' => 'ok']);
}
?>