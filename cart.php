<?php 
$title = 'Giỏ hàng - ShoeStore';
include 'header.php'; 

$cart = $_SESSION['cart'] ?? [];
$cartItems = [];
$total = 0;

if (!empty($cart)) {
    foreach ($cart as $cartKey => $quantity) {
        $parts = explode('_', $cartKey);
        $productId = (int)$parts[0];
        $size = isset($parts[1]) ? (int)$parts[1] : 40;

        $stmt = $pdo->prepare("
            SELECT p.*, pi.image_url 
            FROM products p 
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 
            WHERE p.id = ?
        ");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if ($product) {
            $product['cart_key'] = $cartKey;
            $product['size'] = $size;
            $product['quantity'] = $quantity;
            $product['subtotal'] = $product['price'] * $quantity;
            $total += $product['subtotal'];
            $cartItems[] = $product;
        } else {
            unset($_SESSION['cart'][$cartKey]);
        }
    }
}
?>

<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8">Giỏ hàng của bạn</h1>
    
    <?php if(empty($cartItems)): ?>
        <div class="bg-white p-12 rounded-xl text-center shadow-sm max-w-2xl mx-auto">
            <h3 class="text-2xl font-bold mb-2">Giỏ hàng trống</h3>
            <p class="text-gray-500 mb-8">Bạn chưa có sản phẩm nào trong giỏ hàng.</p>
            <a href="products.php" class="inline-block bg-primary text-white font-medium px-8 py-3 rounded-md">Mua sắm ngay</a>
        </div>
    <?php else: ?>
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="w-full lg:w-2/3 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="divide-y divide-gray-100">
                    <?php foreach($cartItems as $item): ?>
                    <div class="p-6 flex flex-col sm:flex-row items-center sm:items-center gap-6">
                        <div class="w-24 h-24 bg-gray-50 rounded-lg overflow-hidden flex-shrink-0">
                            <img src="<?= !empty($item['image_url']) ? 'public' . $item['image_url'] : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=200&q=80' ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-grow w-full text-center sm:text-left">
                            <h3 class="font-bold text-lg"><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded w-fit mt-1 mx-auto sm:mx-0">Size: <?= $item['size'] ?></p>
                            <p class="text-gray-500 mt-1"><?= format_price($item['price']) ?></p>
                            <a href="cart_action.php?action=remove&id=<?= $item['cart_key'] ?>" class="text-rose-500 font-bold text-sm mt-2 inline-block">Xóa</a>
                        </div>
                        <div class="flex items-center justify-between w-full sm:w-auto gap-6 border-t sm:border-none pt-4 sm:pt-0">
                            <div class="w-32 flex items-center justify-start">
                                <form action="cart_action.php?action=update" method="POST" class="flex items-center">
                                    <input type="hidden" name="cart_key" value="<?= $item['cart_key'] ?>">
                                    <span class="text-xs text-slate-400 mr-2 sm:hidden">SL:</span>
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="w-16 text-center border rounded-xl py-1.5 font-bold" onchange="this.form.submit()">
                                </form>
                            </div>
                            <div class="text-right font-black text-indigo-600 min-w-[100px] text-lg">
                                <?= format_price($item['subtotal']) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold mb-6 pb-4 border-b">Tóm tắt đơn hàng</h3>
                    <div class="flex justify-between font-bold text-xl mb-8">
                        <span>Tổng cộng</span>
                        <span class="text-primary"><?= format_price($total) ?></span>
                    </div>
                    <a href="checkout.php" class="w-full block text-center bg-primary text-white font-bold py-3 rounded-lg">Thanh toán</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
