<?php
include 'config.php';

$action = $_GET['action'] ?? '';

// thêm giỏ hàng
if ($action === 'add') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $size = (int)($_POST['size'] ?? 40);
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($product_id > 0 && db_record_exists('products', 'id', $product_id)) {
        if ($quantity <= 0) {
            $quantity = 1;
        }
        if ($size < 35 || $size > 48) {
            $size = 40; // Mặc định nếu kích thước không hợp lệ
        }

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

// cập nhật số lượng
if ($action === 'update') {
    $cart_key = $_POST['cart_key'] ?? '';
    $quantity = (int)($_POST['quantity'] ?? 0);

    if ($quantity < 0) {
        $quantity = 0; // Chặn các giá trị âm
    }

    if (!empty($cart_key) && isset($_SESSION['cart'][$cart_key])) {
        if ($quantity > 0) {
            $_SESSION['cart'][$cart_key] = $quantity;
        } else {
            unset($_SESSION['cart'][$cart_key]);
        }
    }
    redirect('cart.php');
}

// xóa item khỏi giỏ hàng
if ($action === 'remove') {
    $cart_key = $_GET['id'] ?? '';
    if (!empty($cart_key) && isset($_SESSION['cart'][$cart_key])) {
        unset($_SESSION['cart'][$cart_key]);
    }
    redirect('cart.php');
}

redirect('index.php');
