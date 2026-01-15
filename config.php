<?php
// Configurações para o teu banco de dados 'gazada'
$host = "localhost";
$user = "root";
$pass = ""; 
$db   = "gazada";

$conn = new mysqli($host, $user, $pass, $db);

// Verifica se a conexão falhou
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>