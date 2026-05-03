<?php 
$title = 'Đặt hàng thành công - ShoeStore';
include 'header.php'; 

$order_id = $_GET['id'] ?? 0;
?>

<div class="container mx-auto px-4 py-24 text-center">
    <div class="max-w-xl mx-auto bg-white p-12 rounded-2xl shadow-sm border border-gray-100">
        <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Đặt hàng thành công!</h1>
        <p class="text-gray-600 mb-8 text-lg">Cảm ơn bạn đã mua sắm tại ShoeStore. Mã đơn hàng của bạn là <span class="font-bold text-primary">#<?= $order_id ?></span>.</p>
        <div class="space-y-4">
            <a href="index.php" class="block w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition">Tiếp tục mua sắm</a>
            <p class="text-sm text-gray-500">Chúng tôi sẽ sớm liên hệ với bạn để xác nhận đơn hàng.</p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
