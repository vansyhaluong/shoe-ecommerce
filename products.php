<?php
include 'languages.php';
$title = 'Tất cả sản phẩm - ShoeStore';
include 'header.php';

$query = $_GET['q'] ?? '';
$brand_id = (int)($_GET['brand_id'] ?? 0);
$category_id = (int)($_GET['category_id'] ?? 0);

// Hỗ trợ lookup từ query string chữ, ví dụ ?category=Men hoặc ?category=shoelaces
$cat_string = $_GET['category'] ?? '';
if (!empty($cat_string) && $category_id === 0) {
    // Kiểm tra xem cột slug có tồn tại không
    $has_slug = false;
    try {
        $pdo->query("SELECT slug FROM categories LIMIT 1");
        $has_slug = true;
    } catch (PDOException $e) {
        $has_slug = false;
    }

    if ($has_slug) {
        $stmt_cat = $pdo->prepare("SELECT id FROM categories WHERE name LIKE ? OR slug = ?");
        $stmt_cat->execute(["%$cat_string%", $cat_string]);
    } else {
        $stmt_cat = $pdo->prepare("SELECT id FROM categories WHERE name LIKE ?");
        $stmt_cat->execute(["%$cat_string%"]);
    }
    $cat_res = $stmt_cat->fetch();
    if ($cat_res) {
        $category_id = (int)$cat_res['id'];
    }
}

$brand_string = $_GET['brand'] ?? '';
if (!empty($brand_string) && $brand_id === 0) {
    $stmt_br = $pdo->prepare("SELECT id FROM brands WHERE name LIKE ?");
    $stmt_br->execute(["%$brand_string%"]);
    $br_res = $stmt_br->fetch();
    if ($br_res) {
        $brand_id = (int)$br_res['id'];
    }
}

$sql = "
    SELECT p.*, pi.image_url, b.name AS brand_name 
    FROM products p 
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 
    LEFT JOIN brands b ON p.brand_id = b.id
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

// Lấy danh sách sản phẩm đã thích của user
$userWishlist = [];
if (isset($_SESSION['user_id'])) {
    $stmt_wl_ids = $pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $stmt_wl_ids->execute([$_SESSION['user_id']]);
    $userWishlist = $stmt_wl_ids->fetchAll(PDO::FETCH_COLUMN);
}

// Lấy tên danh mục đã chọn
$selected_category_name = '';
if ($category_id > 0) {
    $stmt_sel_cat = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt_sel_cat->execute([$category_id]);
    $selected_category_name = $stmt_sel_cat->fetchColumn();
}
?>

<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Mobile Filter Toggle Button -->
        <div class="md:hidden flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm w-full mb-2">
            <span class="font-black italic uppercase tracking-wider text-dark text-sm">Bộ Lọc & Tìm Kiếm</span>
            <button onclick="toggleFilters()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black px-4 py-2.5 rounded-xl text-2xs uppercase tracking-widest transition-all shadow-md shadow-indigo-600/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                Bộ lọc
            </button>
        </div>

        <!-- Sidebar Filters -->
        <aside id="filters-sidebar" class="hidden md:block w-full md:w-64 flex-shrink-0">
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 sticky top-24">
                <form action="products.php" method="GET" class="space-y-8">
                    <div>
                        <h4 class="premium-label mb-4"><?= __('search2') ?></h4>
                        <div class="relative">
                            <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 font-sans" placeholder="<?= __('product_name') ?>...">
                        </div>
                    </div>

                    <div>
                        <h4 class="premium-label mb-4"><?= __('category') ?></h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="category_id" value="0" <?= $category_id == 0 ? 'checked' : '' ?> class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500" onchange="this.form.submit()">
                                <span class="text-sm font-medium text-slate-600 group-hover:text-indigo-600 transition-colors font-sans"><?= __('all') ?></span>
                            </label>
                            <?php foreach ($filter_categories as $cat): ?>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="category_id" value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'checked' : '' ?> class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500" onchange="this.form.submit()">
                                    <span class="text-sm font-medium text-slate-600 group-hover:text-indigo-600 transition-colors font-sans"><?= htmlspecialchars($cat['name']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <h4 class="premium-label mb-4"><?= __('brands') ?></h4>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="radio" name="brand_id" value="0" <?= $brand_id == 0 ? 'checked' : '' ?> class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500" onchange="this.form.submit()">
                                <span class="text-sm font-medium text-slate-600 group-hover:text-indigo-600 transition-colors font-sans"><?= __('all') ?></span>
                            </label>
                            <?php foreach ($filter_brands as $br): ?>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="radio" name="brand_id" value="<?= $br['id'] ?>" <?= $brand_id == $br['id'] ? 'checked' : '' ?> class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500" onchange="this.form.submit()">
                                    <span class="text-sm font-medium text-slate-600 group-hover:text-indigo-600 transition-colors font-sans"><?= htmlspecialchars($br['name']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="w-full btn-gradient text-white py-3.5 rounded-xl font-bold uppercase tracking-wider text-xs premium-btn shadow-lg shadow-indigo-100">ÁP DỤNG</button>
                </form>
            </div>
        </aside>

        <!-- Product Grid -->
        <div class="flex-grow">
            <h2 class="text-xl md:text-2xl font-extrabold uppercase tracking-tight text-dark mb-8 font-display">
                <?= !empty($selected_category_name) ? htmlspecialchars($selected_category_name) : __('list_product') ?>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-indigo-500/10 transition-all border border-slate-100 group card-hover">
                        <div class="relative pb-[110%] bg-slate-50/50 p-6">
                            <!-- Heart Wishlist button -->
                            <div class="absolute top-5 right-5 z-20">
                                <?php 
                                $isFavorited = in_array($product['id'], $userWishlist);
                                ?>
                                <button onclick="event.preventDefault(); toggleWishlist(<?= $product['id'] ?>, this)" class="w-9 h-9 rounded-full bg-white/80 hover:bg-white backdrop-blur-md flex items-center justify-center text-slate-700 shadow-md transition-all duration-300 hover:scale-110 active:scale-90 select-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-colors duration-300 heart-icon-svg-<?= $product['id'] ?> <?= $isFavorited ? 'text-rose-500 fill-current animate-heart-pop' : 'text-slate-400 hover:text-rose-500' ?>" fill="<?= $isFavorited ? 'currentColor' : 'none' ?>" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>
                            </div>
                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="absolute inset-0 z-10"></a>
                            <img src="<?= !empty($product['image_url']) ? 'public' . $product['image_url'] : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80' ?>" class="absolute inset-0 w-full h-full object-contain p-8 mix-blend-multiply group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="p-6">
                            <p class="premium-brand-tag mb-1.5"><?= htmlspecialchars($product['brand_name'] ?? $product['brand'] ?? '') ?></p>
                            <h3 class="text-sm font-semibold tracking-tight text-slate-800 mb-3.5 leading-snug">
                                <a href="product_detail.php?id=<?= $product['id'] ?>" class="hover:text-indigo-600 transition-all line-clamp-2 premium-product-name"><?= htmlspecialchars($product['name']) ?></a>
                            </h3>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-extrabold tracking-tight text-slate-900 premium-price"><?= format_price($product['price']) ?></span>
                                <div class="w-8 h-8 rounded-full bg-slate-900 flex items-center justify-center text-white cursor-pointer hover:bg-indigo-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
<script>
function toggleFilters() {
    const sidebar = document.getElementById('filters-sidebar');
    sidebar.classList.toggle('hidden');
}
</script>
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
#filters-sidebar:not(.hidden) {
    animation: fadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
<?php include 'footer.php'; ?>