<?php
require_once 'config.php';
header('Content-Type: application/json');
$request = $_GET['request'] ?? '';

// Verificar ticket ativo (Cliente usa isto para sair do loop)
if($request === 'check_status') {
    $user = $_GET['user'];
    // IMPORTANTE: Nome da tabela deve ser suporte_tickets
    $res = $conn->query("SELECT * FROM suporte_tickets WHERE utilizador_nome = '$user' AND estado != 'fechado' LIMIT 1");
    echo json_encode($res->fetch_assoc());
}

// Abrir Ticket
if($request === 'abrir_ticket') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user = $data['user'];
    $assunto = $conn->real_escape_string($data['assunto']);
    $conn->query("INSERT INTO suporte_tickets (utilizador_nome, assunto, estado) VALUES ('$user', '$assunto', 'pendente')");
    echo json_encode(['status' => 'ok']);
}

// Admin: Buscar todos os tickets para a lista
if($request === 'get_all_tickets') {
    $res = $conn->query("SELECT * FROM suporte_tickets ORDER BY id DESC");
    $tickets = [];
    while($r = $res->fetch_assoc()) { $tickets[] = $r; }
    echo json_encode($tickets);
}

// Admin: Aceitar (Muda o estado para tirar o cliente do loop)
if ($request === 'aceitar_ticket') {
    $id = $_GET['id'];
    // Corrigido: Nome da tabela suporte_tickets
    $stmt = $conn->prepare("UPDATE suporte_tickets SET estado = 'em_atendimento' WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error']);
    }
}

// Admin: Concluir (Passa para concluído, falta o cliente fechar)
if($request === 'concluir_ticket') {
    $id = $_GET['id'];
    $conn->query("UPDATE suporte_tickets SET estado = 'concluido' WHERE id = $id");
    echo json_encode(['status' => 'ok']);
}

// Cliente: Confirmar e Fechar
if($request === 'confirmar_fecho_cliente') {
    $id = $_GET['id'];
    $conn->query("UPDATE suporte_tickets SET estado = 'fechado' WHERE id = $id");
    echo json_encode(['status' => 'ok']);
}

// Chat: Buscar Mensagens
if($request === 'get_chat') {
    $tid = $_GET['ticket_id'];
    $res = $conn->query("SELECT * FROM suporte_mensagens WHERE ticket_id = $tid ORDER BY data_envio ASC");
    $m = []; 
    while($r = $res->fetch_assoc()) { $m[] = $r; }
    echo json_encode($m);
}

// Chat: Enviar Mensagem
if($request === 'send_msg') {
    $data = json_decode(file_get_contents('php://input'), true);
    $tid = $data['ticket_id'];
    $rem = $data['remetente'];
    $msg = $conn->real_escape_string($data['mensagem']);
    $conn->query("INSERT INTO suporte_mensagens (ticket_id, enviado_por, mensagem) VALUES ($tid, '$rem', '$msg')");
    echo json_encode(['status' => 'ok']);
}
?>