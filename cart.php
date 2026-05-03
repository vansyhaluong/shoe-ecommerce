<?php 
$title = 'Giỏ hàng - ShoeStore';
include 'header.php'; 

$cart = $_SESSION['cart'] ?? [];
$cartItems = [];
$total = 0;

if (!empty($cart)) {
    foreach ($cart as $productId => $quantity) {
        $stmt = $pdo->prepare("
            SELECT p.*, pi.image_url 
            FROM products p 
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 
            WHERE p.id = ?
        ");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if ($product) {
            $product['quantity'] = $quantity;
            $product['subtotal'] = $product['price'] * $quantity;
            $total += $product['subtotal'];
            $cartItems[] = $product;
        } else {
            unset($_SESSION['cart'][$productId]);
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
                    <div class="p-6 flex items-center gap-6">
                        <div class="w-24 h-24 bg-gray-50 rounded-lg overflow-hidden flex-shrink-0">
                            <img src="<?= !empty($item['image_url']) ? 'public' . $item['image_url'] : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=200&q=80' ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-grow">
                            <h3 class="font-bold text-lg"><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="text-gray-500"><?= format_price($item['price']) ?></p>
                            <a href="cart_action.php?action=remove&id=<?= $item['id'] ?>" class="text-red-500 text-sm mt-2 inline-block">Xóa</a>
                        </div>
                        <div class="w-32">
                            <form action="cart_action.php?action=update" method="POST">
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="w-16 text-center border rounded py-1" onchange="this.form.submit()">
                            </form>
                        </div>
                        <div class="text-right font-bold text-primary min-w-[100px]">
                            <?= format_price($item['subtotal']) ?>
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
