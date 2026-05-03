<?php 
$title = 'Tất cả sản phẩm - ShoeStore';
include 'header.php'; 

$query = $_GET['q'] ?? '';
$brand_id = (int)($_GET['brand_id'] ?? 0);
$category_id = (int)($_GET['category_id'] ?? 0);

$sql = "
    SELECT p.*, pi.image_url 
    FROM products p 
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 
    WHERE 1=1
";
$params = [];

if ($query) {
    $sql .= " AND p.name LIKE ?";
    $params[] = "%$query%";
}
if ($brand_id > 0) {
    $sql .= " AND p.brand_id = ?";
    $params[] = $brand_id;
}
if ($category_id > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
}

$sql .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Lấy danh sách để hiển thị bộ lọc
$filter_brands = $pdo->query("SELECT * FROM brands ORDER BY name")->fetchAll();
$filter_categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="w-full md:w-64 flex-shrink-0">
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 sticky top-24">
                <form action="products.php" method="GET" class="space-y-8">
                    <div>
                        <h4 class="font-black text-dark uppercase tracking-widest text-xs mb-4">Tìm kiếm</h4>
                        <div class="relative">
                            <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500" placeholder="Tên sản phẩm...">
                        </div>
                    </div>

                    <div>
                        <h4 class="font-black text-dark uppercase tracking-widest text-xs mb-4">Danh mục</h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="category_id" value="0" <?= $category_id == 0 ? 'checked' : '' ?> class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500" onchange="this.form.submit()">
                                <span class="text-sm font-medium text-slate-600 group-hover:text-indigo-600 transition-colors">Tất cả</span>
                            </label>
                            <?php foreach($filter_categories as $cat): ?>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="category_id" value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'checked' : '' ?> class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500" onchange="this.form.submit()">
                                <span class="text-sm font-medium text-slate-600 group-hover:text-indigo-600 transition-colors"><?= htmlspecialchars($cat['name']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-black text-dark uppercase tracking-widest text-xs mb-4">Thương hiệu</h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="brand_id" value="0" <?= $brand_id == 0 ? 'checked' : '' ?> class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500" onchange="this.form.submit()">
                                <span class="text-sm font-medium text-slate-600 group-hover:text-indigo-600 transition-colors">Tất cả</span>
                            </label>
                            <?php foreach($filter_brands as $br): ?>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="brand_id" value="<?= $br['id'] ?>" <?= $brand_id == $br['id'] ? 'checked' : '' ?> class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500" onchange="this.form.submit()">
                                <span class="text-sm font-medium text-slate-600 group-hover:text-indigo-600 transition-colors"><?= htmlspecialchars($br['name']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="w-full btn-gradient text-white py-3 rounded-xl font-bold shadow-lg shadow-indigo-200">ÁP DỤNG</button>
                </form>
            </div>
        </aside>

        <!-- Product Grid -->
        <div class="flex-grow">
            <h2 class="text-2xl font-bold mb-8">Danh sách sản phẩm</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($products as $product): ?>
                <div class="bg-white rounded-xl overflow-hidden shadow-sm border border-gray-100 card-hover">
                    <div class="relative pb-[100%] bg-gray-50">
                        <a href="product_detail.php?id=<?= $product['id'] ?>" class="absolute inset-0 z-10"></a>
                        <img src="<?= !empty($product['image_url']) ? 'public' . $product['image_url'] : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80' ?>" class="absolute inset-0 w-full h-full object-cover mix-blend-multiply">
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg mb-2 truncate"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="text-primary font-bold text-xl"><?= format_price($product['price']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
