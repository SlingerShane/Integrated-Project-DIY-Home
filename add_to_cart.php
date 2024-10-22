<?php
session_start();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$_SESSION['cart'][] = [
    'product' => $data['product'],
    'price' => $data['price']
];

echo json_encode(['status' => 'success']);
?>
