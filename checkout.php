<?php 
$title = 'Thanh toán - ShoeStore';
include 'header.php'; 

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    redirect('login.php');
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    redirect('index.php');
}

$total = 0;
$cartItems = [];
foreach ($_SESSION['cart'] as $id => $qty) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();
    if ($p) {
        $p['quantity'] = $qty;
        $p['subtotal'] = $p['price'] * $qty;
        $total += $p['subtotal'];
        $cartItems[] = $p;
    }
}
?>

<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col lg:flex-row gap-12">
        <!-- Form thông tin -->
        <div class="w-full lg:w-2/3">
            <h2 class="text-3xl font-bold mb-8">Thông tin giao hàng</h2>
            <form action="checkout_action.php" method="POST" class="bg-white p-8 rounded-xl shadow-sm border space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block font-bold mb-1">Họ và tên</label>
                        <input type="text" name="shipping_name" required class="w-full border rounded-lg px-4 py-2" value="<?= $_SESSION['user_name'] ?? '' ?>">
                    </div>
                    <div>
                        <label class="block font-bold mb-1">Số điện thoại</label>
                        <input type="text" name="shipping_phone" required class="w-full border rounded-lg px-4 py-2">
                    </div>
                </div>
                <div>
                    <label class="block font-bold mb-1">Địa chỉ nhận hàng</label>
                    <textarea name="shipping_address" rows="3" required class="w-full border rounded-lg px-4 py-2" placeholder="Số nhà, tên đường, phường/xã..."></textarea>
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full bg-primary text-white font-bold py-4 rounded-lg text-lg shadow-lg hover:bg-indigo-700 transition">Xác nhận đặt hàng</button>
                </div>
            </form>
        </div>

        <!-- Tóm tắt đơn hàng -->
        <div class="w-full lg:w-1/3">
            <div class="bg-gray-50 p-6 rounded-xl border">
                <h3 class="text-xl font-bold mb-6">Đơn hàng của bạn</h3>
                <div class="space-y-4 mb-6">
                    <?php foreach($cartItems as $item): ?>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600"><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?></span>
                        <span class="font-bold"><?= format_price($item['subtotal']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="border-t pt-4 flex justify-between font-bold text-xl">
                    <span>Tổng tiền</span>
                    <span class="text-primary"><?= format_price($total) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
