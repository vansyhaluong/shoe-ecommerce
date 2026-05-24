<?php 
include 'config.php';
check_admin();
$title = 'Cập nhật sản phẩm - Admin';
include 'header.php'; 

$id = (int)($_GET['id'] ?? 0);
$product = [
    'name' => '',
    'brand_id' => '',
    'category_id' => '',
    'price' => '',
    'description' => ''
];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        redirect('admin_products.php');
    }

    // Lấy ảnh chính
    $stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND is_primary = 1");
    $stmt->execute([$id]);
    $img_data = $stmt->fetch();
    $product['image_url'] = $img_data['image_url'] ?? '';

    // Lấy ảnh phụ
    $stmt = $pdo->prepare("SELECT id, image_url FROM product_images WHERE product_id = ? AND is_primary = 0 LIMIT 3");
    $stmt->execute([$id]);
    $product['gallery'] = $stmt->fetchAll();
}

// Get validation errors and old input
$errors = get_validation_errors();
$old = get_old_input();

if (!empty($old)) {
    $product['name'] = $old['name'] ?? $product['name'];
    $product['brand_id'] = $old['brand_id'] ?? ($product['brand_id'] ?? '');
    $product['category_id'] = $old['category_id'] ?? ($product['category_id'] ?? '');
    $product['price'] = $old['price'] ?? $product['price'];
    $product['description'] = $old['description'] ?? $product['description'];
}

// Lấy danh sách thương hiệu và danh mục
$brands = $pdo->query("SELECT * FROM brands ORDER BY name")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-sm border">
        <h1 class="text-3xl font-bold mb-8"><?= $id > 0 ? 'Chỉnh sửa' : 'Thêm' ?> sản phẩm</h1>
        
        <?php if(!empty($errors)): ?>
            <div class="bg-rose-50 border border-rose-100 text-rose-600 px-5 py-4 rounded-2xl mb-6 text-xs font-bold font-sans space-y-1">
                <?php foreach($errors as $err): ?>
                    <p>• <?= htmlspecialchars($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="admin_product_action.php?action=<?= $id > 0 ? 'edit' : 'add' ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php if($id > 0): ?>
                <input type="hidden" name="id" value="<?= $id ?>">
            <?php endif; ?>

            <div>
                <label class="block font-bold mb-1">Tên sản phẩm</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required class="w-full border rounded-lg px-4 py-2">
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold mb-1">Thương hiệu</label>
                    <select name="brand_id" required class="w-full border rounded-lg px-4 py-2">
                        <option value="">-- Chọn thương hiệu --</option>
                        <?php foreach($brands as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= ($product['brand_id'] ?? 0) == $b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-bold mb-1">Danh mục</label>
                    <select name="category_id" required class="w-full border rounded-lg px-4 py-2">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($product['category_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold mb-1">Giá bán</label>
                <input type="number" name="price" value="<?= $product['price'] ?>" required class="w-full border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="block font-bold mb-1">Mô tả</label>
                <textarea name="description" rows="5" class="w-full border rounded-lg px-4 py-2"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>

            <div>
                <label class="block font-bold mb-1">Hình ảnh chính</label>
                <?php if(!empty($product['image_url'])): ?>
                    <div class="mb-2">
                        <img src="public<?= $product['image_url'] ?>" alt="Product" class="w-32 h-32 object-cover rounded-lg border">
                    </div>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*" class="w-full border rounded-lg px-4 py-2">
            </div>

            <div class="space-y-4">
                <label class="block font-bold">Hình ảnh liên quan (tối đa 3)</label>
                <div class="grid grid-cols-3 gap-4">
                    <?php for($i = 0; $i < 3; $i++): ?>
                        <div class="space-y-2">
                            <?php if(isset($product['gallery'][$i])): ?>
                                <div class="relative group">
                                    <img src="public<?= $product['gallery'][$i]['image_url'] ?>" class="w-full h-32 object-cover rounded-lg border">
                                    <input type="hidden" name="old_gallery[]" value="<?= $product['gallery'][$i]['id'] ?>">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="gallery[]" accept="image/*" class="text-xs w-full">
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-primary text-white px-8 py-3 rounded-lg font-bold">Lưu sản phẩm</button>
                <a href="admin_products.php" class="bg-gray-100 text-gray-700 px-8 py-3 rounded-lg font-bold">Hủy</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
