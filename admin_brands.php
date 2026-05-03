<?php 
include 'config.php';
check_admin();
$title = 'Quản lý Thương hiệu - Admin';
include 'header.php'; 

// Xử lý thêm
if (isset($_POST['add_brand'])) {
    $name = $_POST['name'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO brands (name) VALUES (?)");
    $stmt->execute([$name]);
    header("Location: admin_brands.php");
    exit();
}

// Xử lý xóa
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM brands WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_brands.php");
    exit();
}

$brands = $pdo->query("SELECT * FROM brands ORDER BY id DESC")->fetchAll();
?>

<div class="container mx-auto px-4 py-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Quản lý Thương hiệu</h1>
        <a href="admin_dashboard.php" class="text-indigo-600 hover:underline">Quay lại Dashboard</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Form thêm -->
        <div class="bg-white p-6 rounded-xl shadow-sm border h-fit">
            <h3 class="text-lg font-bold mb-4">Thêm thương hiệu mới</h3>
            <form method="POST" class="space-y-4">
                <input type="text" name="name" required placeholder="Tên thương hiệu..." class="w-full border rounded-lg px-4 py-2">
                <button type="submit" name="add_brand" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-bold">Thêm</button>
            </form>
        </div>

        <!-- Danh sách -->
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm border overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 font-bold">ID</th>
                        <th class="px-6 py-4 font-bold">Tên thương hiệu</th>
                        <th class="px-6 py-4 font-bold text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach($brands as $b): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4"><?= $b['id'] ?></td>
                        <td class="px-6 py-4 font-medium"><?= htmlspecialchars($b['name']) ?></td>
                        <td class="px-6 py-4 text-right">
                            <a href="?delete=<?= $b['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Xóa thương hiệu này?')">Xóa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
