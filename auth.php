<?php
require_once('config.php');
session_start();

// CONFIGURAÇÃO DO ACESSO ADMIN
$ADMIN_USER = "GAZADA"; 
$ADMIN_PASS = "admin123"; 

$action = $_POST['action'] ?? '';

// --- LÓGICA DE LOGIN ---
if ($action === 'login') {
    $user = $_POST['utilizador'];
    $pass = $_POST['password'];

    // 1. VERIFICAÇÃO DE ADMIN (MIKA)
    if ($user === $ADMIN_USER && $pass === $ADMIN_PASS) {
        $_SESSION['username'] = $ADMIN_USER;
        $_SESSION['is_admin'] = true;
        
        header("Location: painel.php");
        exit();
    }

    // 2. LOGIN DE UTILIZADOR NORMAL
    $user_escaped = mysqli_real_escape_string($conn, $user);
    $result = $conn->query("SELECT * FROM utilizadores WHERE utilizador = '$user_escaped'");
    
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        // Verifica se a senha coincide com o hash guardado
        if (password_verify($pass, $userData['password'])) {
            $_SESSION['username'] = $userData['utilizador'];
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['is_admin'] = false; // Garante que não é admin

            // Redireciona o cliente para a página de VIPs ou Loja
            header("Location: dashboard.php"); 
            exit();
        } else {
            header("Location: login.php?error=invalid&form=login");
            exit();
        }
    } else {
        header("Location: login.php?error=invalid&form=login");
        exit();
    }
}

// --- LÓGICA DE REGISTO ---
if ($action === 'register') {
    $user = mysqli_real_escape_string($conn, $_POST['utilizador']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    // Validação básica
    if (strlen($pass) < 6) {
        header("Location: login.php?error=short_pass&form=register");
        exit();
    }

    // Verifica se utilizador ou email já existem
    $check = $conn->query("SELECT id FROM utilizadores WHERE utilizador = '$user' OR email = '$email'");
    if ($check->num_rows > 0) {
        header("Location: login.php?error=exists&form=register");
        exit();
    }

    // Cria o Hash da senha para segurança
    $pass_hash = password_hash($pass, PASSWORD_DEFAULT);

    // Insere na base de dados
    $sql = "INSERT INTO utilizadores (utilizador, email, password) VALUES ('$user', '$email', '$pass_hash')";
    
    if ($conn->query($sql)) {
        header("Location: login.php?success=true");
    } else {
        header("Location: login.php?error=db&form=register");
    }
    exit();
}

// Se alguém tentar aceder ao auth.php diretamente sem POST
header("Location: login.php");
exit();
?>