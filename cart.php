<?php
session_start(); // Start a session to store cart data

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the product ID and price from the POST request
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;
    $price = isset($_POST['price']) ? $_POST['price'] : null;

    if ($product_id && $price) {
        // Add the product to the cart
        $_SESSION['cart'][] = [
            'productId' => $product_id,
            'price' => floatval($price),
            'quantity' => 1
        ];

        // Return success response
        echo json_encode(['status' => 'success', 'message' => 'Product added to cart.']);
    } else {
        // Return error response for invalid input
        echo json_encode(['status' => 'error', 'message' => 'Invalid product data.']);
    }
} else {
    // Return error for invalid request method
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
