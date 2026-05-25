<?php
include 'config.php';
check_admin();
$title = 'Quản lý sản phẩm - Admin';
include 'header.php';

// lấy sản phẩm và thương hiệu
$stmt = $pdo->query("
    SELECT p.*, b.name as brand_name 
    FROM products p 
    LEFT JOIN brands b ON p.brand_id = b.id 
    ORDER BY p.id DESC
");
$products = $stmt->fetchAll();
?>

<div class="container mx-auto px-4 py-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Quản lý sản phẩm</h1>
        <a href="admin_product_form.php" class="bg-green-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-green-700 transition">Thêm sản phẩm mới</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[600px]">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 font-bold">ID</th>
                        <th class="px-6 py-4 font-bold">Tên sản phẩm</th>
                        <th class="px-6 py-4 font-bold">Thương hiệu</th>
                        <th class="px-6 py-4 font-bold">Giá</th>
                        <th class="px-6 py-4 font-bold text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($products as $p): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4"><?= $p['id'] ?></td>
                            <td class="px-6 py-4 font-medium"><?= htmlspecialchars($p['name']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($p['brand_name'] ?? $p['brand']) ?></td>
                            <td class="px-6 py-4"><?= format_price($p['price']) ?></td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="admin_product_form.php?id=<?= $p['id'] ?>" class="text-blue-600 hover:underline">Sửa</a>
                                <a href="admin_product_action.php?action=delete&id=<?= $p['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>