<?php
include 'config.php';

$action = $_GET['action'] ?? '';

if ($action === 'add') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $size = (int)($_POST['size'] ?? 40);
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($product_id > 0) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        
        $cart_key = $product_id . '_' . $size;
        if (isset($_SESSION['cart'][$cart_key])) {
            $_SESSION['cart'][$cart_key] += $quantity;
        } else {
            $_SESSION['cart'][$cart_key] = $quantity;
        }
    }
    redirect('cart.php');
}

if ($action === 'update') {
    $cart_key = $_POST['cart_key'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 0);

    if (!empty($cart_key) && isset($_SESSION['cart'][$cart_key])) {
        if ($quantity > 0) {
            $_SESSION['cart'][$cart_key] = $quantity;
        } else {
            unset($_SESSION['cart'][$cart_key]);
        }
    }
    redirect('cart.php');
}

if ($action === 'remove') {
    $cart_key = $_GET['id'] ?? '';
    if (!empty($cart_key) && isset($_SESSION['cart'][$cart_key])) {
        unset($_SESSION['cart'][$cart_key]);
    }
    redirect('cart.php');
}

redirect('index.php');
?>
