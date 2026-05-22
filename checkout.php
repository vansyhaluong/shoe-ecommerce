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
            <h2 class="text-xl md:text-2xl font-extrabold uppercase tracking-tight text-dark mb-8 font-display">Thông tin giao hàng</h2>
            <form action="checkout_action.php" method="POST" class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block premium-label mb-2">Họ và tên</label>
                        <input type="text" name="shipping_name" required class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 hover:border-slate-300 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all font-sans" value="<?= $_SESSION['user_name'] ?? '' ?>">
                    </div>
                    <div>
                        <label class="block premium-label mb-2">Số điện thoại</label>
                        <input type="text" name="shipping_phone" required class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 hover:border-slate-300 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all font-sans">
                    </div>
                </div>
                <div>
                    <label class="block premium-label mb-2">Địa chỉ nhận hàng</label>
                    <textarea name="shipping_address" rows="3" required class="w-full bg-slate-50 border border-slate-200 focus:border-indigo-500 hover:border-slate-300 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all font-sans" placeholder="Số nhà, tên đường, phường/xã..."></textarea>
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full btn-gradient text-white text-xs font-bold py-4.5 rounded-2xl shadow-xl uppercase tracking-wider premium-btn">Xác nhận đặt hàng</button>
                </div>
            </form>
        </div>

        <!-- Tóm tắt đơn hàng -->
        <div class="w-full lg:w-1/3">
            <div class="bg-slate-50/50 p-8 rounded-2xl border border-slate-100 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-6 font-display">Đơn hàng của bạn</h3>
                <div class="space-y-4 mb-6">
                    <?php foreach($cartItems as $item): ?>
                    <div class="flex justify-between text-sm items-start gap-4">
                        <span class="text-slate-600 font-sans leading-snug"><?= htmlspecialchars($item['name']) ?> <span class="text-xs text-slate-400 font-bold ml-1 font-sans">x<?= $item['quantity'] ?></span></span>
                        <span class="font-bold text-slate-900 font-sans flex-shrink-0"><?= format_price($item['subtotal']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="border-t border-slate-200 pt-6 flex justify-between font-extrabold text-lg font-display uppercase tracking-tight">
                    <span class="text-slate-800">Tổng tiền</span>
                    <span class="text-indigo-600 premium-price"><?= format_price($total) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
