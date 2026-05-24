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
    $shipping_name = sanitize_text($_POST['shipping_name'] ?? '');
    $shipping_phone = sanitize_text($_POST['shipping_phone'] ?? '');
    $shipping_address = sanitize_text($_POST['shipping_address'] ?? '');

    $errors = [];
    if (!validate_string_len($shipping_name, 2, 100)) {
        $errors[] = 'Họ và tên người nhận phải từ 2 đến 100 ký tự!';
    }
    if (!validate_phone($shipping_phone)) {
        $errors[] = 'Số điện thoại không hợp lệ (phải từ 9 đến 15 chữ số)!';
    }
    if (!validate_string_len($shipping_address, 5, 500)) {
        $errors[] = 'Địa chỉ nhận hàng phải từ 5 đến 500 ký tự!';
    }

    if (!empty($errors)) {
        $_SESSION['validation_errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        redirect('checkout.php');
    }

    // Tính tổng tiền & xác thực giỏ hàng
    $total_amount = 0;
    $valid_cart = [];
    foreach ($_SESSION['cart'] as $cartKey => $qty) {
        $parts = explode('_', $cartKey);
        $id = (int)$parts[0];
        $size = isset($parts[1]) ? (int)$parts[1] : 40;
        $qty = (int)$qty;

        if ($qty <= 0) continue; // Bỏ qua nếu số lượng <= 0

        $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $p = $stmt->fetch();
        if ($p) {
            $total_amount += $p['price'] * $qty;
            $valid_cart[] = [
                'id' => $id,
                'qty' => $qty,
                'price' => $p['price'],
                'size' => $size
            ];
        }
    }

    if (empty($valid_cart)) {
        $_SESSION['validation_errors'] = ['Giỏ hàng của bạn không có sản phẩm hợp lệ!'];
        redirect('cart.php');
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
        foreach ($valid_cart as $item) {
            $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size) VALUES (?, ?, ?, ?, ?)");
            $item_stmt->execute([$order_id, $item['id'], $item['qty'], $item['price'], $item['size']]);
        }

        $pdo->commit();

        // 3. Làm sạch giỏ hàng
        unset($_SESSION['cart']);

        redirect('order_success.php?id=' . $order_id);

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['validation_errors'] = ['Đã xảy ra lỗi hệ thống khi xử lý đơn hàng. Vui lòng thử lại sau!'];
        redirect('checkout.php');
    }
} else {
    redirect('index.php');
}
?>
