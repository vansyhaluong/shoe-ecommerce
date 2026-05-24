<?php 
include 'config.php';
check_admin();
$title = 'Quản lý Danh mục - Admin';
include 'header.php'; 

$error = '';

// Xử lý thêm
if (isset($_POST['add_cat'])) {
    $name = sanitize_text($_POST['name'] ?? '');
    if (!validate_string_len($name, 2, 100)) {
        $error = 'Tên danh mục phải từ 2 đến 100 ký tự!';
    } elseif (db_record_exists('categories', 'name', $name)) {
        $error = 'Danh mục này đã tồn tại!';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
            header("Location: admin_categories.php");
            exit();
        } catch (PDOException $e) {
            $error = 'Có lỗi xảy ra khi thêm danh mục!';
        }
    }
}

// Xử lý xóa
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id <= 0 || !db_record_exists('categories', 'id', $id)) {
        $error = 'Danh mục không tồn tại!';
    } else {
        // Kiểm tra xem có sản phẩm thuộc danh mục không
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $stmt->execute([$id]);
        if ((int)$stmt->fetchColumn() > 0) {
            $error = 'Không thể xóa danh mục này vì đang có sản phẩm trực thuộc!';
        } else {
            try {
                $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                $stmt->execute([$id]);
                header("Location: admin_categories.php");
                exit();
            } catch (PDOException $e) {
                $error = 'Có lỗi xảy ra khi xóa danh mục!';
            }
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll();
?>

<div class="container mx-auto px-4 py-12">
    <?php if(!empty($error)): ?>
        <div class="bg-rose-50 border border-rose-100 text-rose-600 px-5 py-4 rounded-2xl mb-6 text-xs font-bold font-sans">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Quản lý Danh mục</h1>
        <a href="admin_dashboard.php" class="text-indigo-600 hover:underline">Quay lại Dashboard</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Form thêm -->
        <div class="bg-white p-6 rounded-xl shadow-sm border h-fit">
            <h3 class="text-lg font-bold mb-4">Thêm danh mục mới</h3>
            <form method="POST" class="space-y-4">
                <input type="text" name="name" required placeholder="Tên danh mục..." class="w-full border rounded-lg px-4 py-2">
                <button type="submit" name="add_cat" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-bold">Thêm</button>
            </form>
        </div>

        <!-- Danh sách -->
        <div class="md:col-span-2 bg-white rounded-xl shadow-sm border overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 font-bold">ID</th>
                        <th class="px-6 py-4 font-bold">Tên danh mục</th>
                        <th class="px-6 py-4 font-bold text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach($categories as $c): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4"><?= $c['id'] ?></td>
                        <td class="px-6 py-4 font-medium"><?= htmlspecialchars($c['name']) ?></td>
                        <td class="px-6 py-4 text-right">
                            <a href="?delete=<?= $c['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Xóa danh mục này?')">Xóa</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
