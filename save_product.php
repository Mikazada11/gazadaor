<?php
$jsonFile = 'produtos1.json';
$uploadDir = 'images/';

if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }

$imagePath = 'images/BANNER_GAZADA_RP.png'; // Imagem padrão

if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
    $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
    $nomeFinal = uniqid('img_') . "." . $ext;
    if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $nomeFinal)) {
        $imagePath = 'images/' . $nomeFinal;
    }
}

$newProduct = [
    "id" => $_POST['id'],
    "name" => $_POST['name'],
    "category" => $_POST['category'],
    "price_coins" => (float)$_POST['price_coins'],
    "image" => $imagePath,
    "features" => $_POST['features'] ?: "Item Gazada RP"
];

$dados = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
$dados[] = $newProduct;

file_put_contents($jsonFile, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "OK";
?>