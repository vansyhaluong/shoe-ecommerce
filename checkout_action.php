<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        redirect('index.php');
    }

    $user_id = (int)$_SESSION['user_id'];
    
    // Kiểm tra xem user_id này có thực sự tồn tại trong DB không
    $checkUser = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $checkUser->execute([$user_id]);
    if (!$checkUser->fetch()) {
        $user_id = null; // Nếu không tồn tại thì để NULL để tránh lỗi Foreign Key
    }
    $shipping_name = $_POST['shipping_name'];
    $shipping_phone = $_POST['shipping_phone'];
    $shipping_address = $_POST['shipping_address'];
    
    // Tính tổng tiền
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $p = $stmt->fetch();
        if ($p) {
            $total_amount += $p['price'] * $qty;
        }
    }

    try {
        $pdo->beginTransaction();

        // 1. Lưu vào bảng orders
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount, shipping_name, shipping_phone, shipping_address, status) 
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$user_id, $total_amount, $shipping_name, $shipping_phone, $shipping_address]);
        $order_id = $pdo->lastInsertId();

        // 2. Lưu vào bảng order_items
        foreach ($_SESSION['cart'] as $id => $qty) {
            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $p = $stmt->fetch();
            if ($p) {
                $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $item_stmt->execute([$order_id, $id, $qty, $p['price']]);
            }
        }

        $pdo->commit();

        // 3. Làm sạch giỏ hàng
        unset($_SESSION['cart']);

        redirect('order_success.php?id=' . $order_id);

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Lỗi đặt hàng: " . $e->getMessage());
    }
} else {
    redirect('index.php');
}
?>
