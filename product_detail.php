<?php 
include 'header.php'; 

$id = (int)($_GET['id'] ?? 0);

// Xử lý gửi đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $reviewer_name = trim($_POST['reviewer_name'] ?? '');
    if (empty($reviewer_name)) {
        $reviewer_name = $_SESSION['user_name'] ?? 'Khách hàng';
    }
    $rating = (int)($_POST['rating'] ?? 5);
    $rating = max(1, min(5, $rating));
    $comment = trim($_POST['comment'] ?? '');

    if ($id > 0 && !empty($comment)) {
        $ins = $pdo->prepare("INSERT INTO product_reviews (product_id, reviewer_name, rating, comment) VALUES (?, ?, ?, ?)");
        $ins->execute([$id, $reviewer_name, $rating, $comment]);
    }
    // Redirect về trang chi tiết sản phẩm với hash để tự động mở tab đánh giá
    header("Location: product_detail.php?id=" . $id . "#tab-btn-reviews");
    exit();
}

$stmt = $pdo->prepare("
    SELECT p.*, pi.image_url, b.name AS brand_name, c.name AS category_name
    FROM products p 
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    die("Sản phẩm không tồn tại!");
}

// Kiểm tra xem sản phẩm có phải dây giày không
$isShoelaces = false;
$catName = strtolower($product['category_name'] ?? $product['category'] ?? '');
if (strpos($catName, 'dây') !== false || strpos($catName, 'shoelace') !== false || (isset($product['category_id']) && $product['category_id'] == 5)) {
    $isShoelaces = true;
}

// Lấy thêm ảnh phụ (bao gồm cả ảnh chính)
$stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ? ORDER BY is_primary DESC");
$stmt->execute([$id]);
$images = $stmt->fetchAll();

// Lấy sản phẩm liên quan
if (!empty($product['category_id'])) {
    $stmt = $pdo->prepare("
        SELECT p.*, pi.image_url, b.name AS brand_name 
        FROM products p 
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.category_id = ? AND p.id != ? 
        LIMIT 4
    ");
    $stmt->execute([$product['category_id'], $id]);
} else {
    $stmt = $pdo->prepare("
        SELECT p.*, pi.image_url, b.name AS brand_name 
        FROM products p 
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1 
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.category = ? AND p.id != ? 
        LIMIT 4
    ");
    $stmt->execute([$product['category'] ?? '', $id]);
}
$relatedProducts = $stmt->fetchAll();

// Lấy thông tin đánh giá từ database
$stmt_rev = $pdo->prepare("SELECT COUNT(*) as total, AVG(rating) as avg_rating FROM product_reviews WHERE product_id = ?");
$stmt_rev->execute([$id]);
$revStats = $stmt_rev->fetch();

$totalReviews = (int)($revStats['total'] ?? 0);
$avgRating = $totalReviews > 0 ? round((float)$revStats['avg_rating'], 1) : 5.0;

$stmt_list = $pdo->prepare("SELECT * FROM product_reviews WHERE product_id = ? ORDER BY created_at DESC");
$stmt_list->execute([$id]);
$reviewsList = $stmt_list->fetchAll();

// Lấy danh sách sản phẩm đã thích của user
$userWishlist = [];
if (isset($_SESSION['user_id'])) {
    $stmt_wl_ids = $pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $stmt_wl_ids->execute([$_SESSION['user_id']]);
    $userWishlist = $stmt_wl_ids->fetchAll(PDO::FETCH_COLUMN);
}
?>

<!-- Breadcrumbs -->
<div class="bg-white border-b border-slate-100 py-4">
    <div class="container mx-auto px-4 text-xs font-bold uppercase tracking-widest text-slate-400">
        <a href="index.php" class="hover:text-indigo-600 transition">Trang chủ</a>
        <span class="mx-2">/</span>
        <a href="products.php" class="hover:text-indigo-600 transition">Sản phẩm</a>
        <span class="mx-2">/</span>
        <span class="text-indigo-600"><?= htmlspecialchars($product['name']) ?></span>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="flex flex-col lg:flex-row gap-16 mb-24">
        <!-- Image Gallery -->
        <div class="w-full lg:w-1/2 space-y-4">
            <div class="relative pb-[100%] bg-white rounded-[3rem] overflow-hidden shadow-sm border border-slate-100 group">
                <!-- Floating heart on main image -->
                <div class="absolute top-8 right-8 z-20">
                    <?php 
                    $isFavorited = in_array($product['id'], $userWishlist);
                    ?>
                    <button type="button" onclick="toggleWishlist(<?= $product['id'] ?>, this)" class="w-12 h-12 rounded-full bg-white/80 hover:bg-white backdrop-blur-md flex items-center justify-center text-slate-700 shadow-lg transition-all duration-300 hover:scale-110 active:scale-90 select-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-colors duration-300 heart-icon-svg-<?= $product['id'] ?> <?= $isFavorited ? 'text-rose-500 fill-current animate-heart-pop' : 'text-slate-400 hover:text-rose-500' ?>" fill="<?= $isFavorited ? 'currentColor' : 'none' ?>" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                </div>
                <img id="main-image" src="<?= !empty($product['image_url']) ? 'public' . $product['image_url'] : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80' ?>" class="absolute inset-0 w-full h-full object-contain p-12 transition-transform duration-700 hover:scale-110">
                <div class="absolute top-8 left-8">
                    <span class="bg-indigo-600 text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg">New Release</span>
                </div>
            </div>
            
            <?php if(count($images) > 1): ?>
            <div class="grid grid-cols-5 gap-4">
                <?php foreach($images as $img): ?>
                <div class="cursor-pointer bg-white rounded-2xl overflow-hidden border-2 border-transparent hover:border-indigo-600 transition-all p-2 aspect-square group" onclick="document.getElementById('main-image').src = 'public<?= $img['image_url'] ?>'">
                    <img src="public<?= $img['image_url'] ?>" class="w-full h-full object-contain mix-blend-multiply group-hover:scale-110 transition-transform">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Product Info -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center">
            <div class="mb-8">
                <p class="premium-brand-tag mb-2"><?= htmlspecialchars($product['brand_name'] ?? $product['brand'] ?? '') ?></p>
                <h1 class="text-3xl sm:text-4xl font-extrabold uppercase tracking-tight text-dark mb-4 leading-tight font-display"><?= htmlspecialchars($product['name']) ?></h1>
                <div class="flex items-center gap-6 mb-6">
                    <span class="text-3xl font-extrabold text-dark tracking-tight premium-price"><?= format_price($product['price']) ?></span>
                    <div class="flex items-center gap-1 bg-amber-50 px-3 py-1 rounded-full">
                        <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <span class="text-xs font-bold text-amber-800 font-sans" id="avg-rating-badge"><?= $avgRating ?> (<?= $totalReviews ?> <?= $totalReviews <= 1 ? 'đánh giá' : 'đánh giá' ?>)</span>
                    </div>
                </div>
                <p class="text-slate-500 text-base leading-relaxed font-sans"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            </div>

            <form action="cart_action.php?action=add" method="POST" class="space-y-10">
                <input type="hidden" name="product_id" value="<?= $id ?>">
                
                <!-- Size Selector -->
                <?php if (!$isShoelaces): ?>
                <div>
                    <h4 class="premium-label mb-4">Chọn Size Giày</h4>
                    <div class="grid grid-cols-4 sm:grid-cols-7 gap-2 sm:gap-3 max-w-md">
                        <?php 
                        $sizes = [38, 39, 40, 41, 42, 43, 44];
                        foreach($sizes as $index => $sz):
                        ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="size" value="<?= $sz ?>" <?= $index === 2 ? 'checked' : '' ?> class="peer sr-only">
                            <div class="w-full py-3.5 rounded-xl border border-slate-200 text-center font-bold text-slate-700 transition-all hover:bg-slate-50 hover:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-indigo-500/20 uppercase text-sm">
                                <?= $sz ?>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <input type="hidden" name="size" value="N/A">
                <?php endif; ?>
 
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-6">
                    <div class="w-full sm:w-32">
                        <h4 class="premium-label mb-4">Số lượng</h4>
                        <div class="flex items-center bg-slate-100 rounded-2xl p-1 w-full justify-between sm:justify-start">
                            <button type="button" class="w-10 h-10 flex items-center justify-center font-bold text-slate-500" onclick="let q = document.getElementById('qty'); q.value = Math.max(1, parseInt(q.value)-1)">-</button>
                            <input type="number" id="qty" name="quantity" value="1" min="1" class="w-10 bg-transparent text-center font-black text-dark focus:outline-none border-none">
                            <button type="button" class="w-10 h-10 flex items-center justify-center font-bold text-slate-500" onclick="let q = document.getElementById('qty'); q.value = parseInt(q.value)+1">+</button>
                        </div>
                    </div>
                    <div class="flex-grow pt-4 sm:pt-8 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                        <button type="submit" class="w-full sm:flex-1 btn-gradient text-white text-xs font-bold py-5 rounded-2xl shadow-xl flex items-center justify-center gap-2 premium-btn tracking-wider transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            Thêm vào giỏ hàng
                        </button>
                        <button type="button" onclick="toggleWishlist(<?= $id ?>, this)" class="w-full sm:w-[56px] h-[56px] rounded-2xl border border-slate-200 hover:border-indigo-600 hover:bg-slate-50 flex items-center justify-center text-slate-400 hover:text-rose-500 transition-all duration-300 shadow-md flex-shrink-0 gap-2 font-bold text-xs uppercase sm:normal-case premium-btn" title="Yêu thích">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-colors duration-300 heart-icon-svg-<?= $id ?> <?= $isFavorited ? 'text-rose-500 fill-current animate-heart-pop' : 'text-slate-400 hover:text-rose-500' ?>" fill="<?= $isFavorited ? 'currentColor' : 'none' ?>" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span class="sm:hidden text-slate-600 font-bold uppercase tracking-wider">Yêu thích</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="mb-24">
        <div class="flex flex-wrap border-b border-slate-100 gap-8 mb-12 font-display">
            <button onclick="switchTab('desc')" id="tab-btn-desc" class="pb-4 text-xs font-bold uppercase tracking-widest text-indigo-600 border-b-2 border-indigo-600 transition-all premium-btn">Mô tả sản phẩm</button>
            <button onclick="switchTab('specs')" id="tab-btn-specs" class="pb-4 text-xs font-bold uppercase tracking-widest text-slate-400 border-b-2 border-transparent hover:text-dark transition-all premium-btn">Thông số kỹ thuật</button>
            <button onclick="switchTab('reviews')" id="tab-btn-reviews" class="pb-4 text-xs font-bold uppercase tracking-widest text-slate-400 border-b-2 border-transparent hover:text-dark transition-all premium-btn">Đánh giá (<?= $totalReviews ?>)</button>
        </div>

        <div id="tab-content-desc" class="tab-content animate-[fadeIn_0.5s_ease-out]">
            <div class="max-w-4xl prose prose-slate">
                <h3 class="text-2xl font-black text-dark mb-6">Trải nghiệm sự khác biệt với công nghệ mới</h3>
                <p class="text-slate-600 text-lg leading-relaxed mb-6">Sản phẩm này không chỉ là một đôi giày, nó là một tác phẩm nghệ thuật của công nghệ và thời trang. Với thiết kế tối giản nhưng đầy tinh tế, đôi giày mang lại cảm giác nhẹ nhàng, linh hoạt cho đôi chân trong mọi hoạt động.</p>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-4 list-none p-0">
                    <li class="flex items-center gap-3 bg-white p-4 rounded-2xl shadow-sm border border-slate-50"><svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg> <span class="font-bold">Đế đệm khí êm ái</span></li>
                    <li class="flex items-center gap-3 bg-white p-4 rounded-2xl shadow-sm border border-slate-50"><svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg> <span class="font-bold">Chất liệu vải Mesh thoáng khí</span></li>
                    <li class="flex items-center gap-3 bg-white p-4 rounded-2xl shadow-sm border border-slate-50"><svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg> <span class="font-bold">Độ bền vượt trội (1000km+)</span></li>
                    <li class="flex items-center gap-3 bg-white p-4 rounded-2xl shadow-sm border border-slate-50"><svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg> <span class="font-bold">Dễ dàng vệ sinh</span></li>
                </ul>
            </div>
        </div>

        <div id="tab-content-specs" class="tab-content hidden animate-[fadeIn_0.5s_ease-out]">
            <div class="max-w-2xl bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <table class="w-full text-left font-sans">
                    <tbody class="divide-y divide-slate-50">
                        <tr><th class="px-8 py-4 font-bold text-xs uppercase tracking-widest text-slate-400 bg-slate-50/50 w-1/3 premium-table-header">Thương hiệu</th><td class="px-8 py-4 text-sm font-semibold text-slate-700"><?= htmlspecialchars($product['brand_name'] ?? $product['brand'] ?? '') ?></td></tr>
                        <tr><th class="px-8 py-4 font-bold text-xs uppercase tracking-widest text-slate-400 bg-slate-50/50 premium-table-header">Mã sản phẩm</th><td class="px-8 py-4 text-sm font-semibold text-slate-700">SS-<?= $product['id'] ?></td></tr>
                        <tr><th class="px-8 py-4 font-bold text-xs uppercase tracking-widest text-slate-400 bg-slate-50/50 premium-table-header">Danh mục</th><td class="px-8 py-4 text-sm font-semibold text-slate-700"><?= htmlspecialchars($product['category_name'] ?? $product['category'] ?? '') ?></td></tr>
                        <tr><th class="px-8 py-4 font-bold text-xs uppercase tracking-widest text-slate-400 bg-slate-50/50 premium-table-header">Năm sản xuất</th><td class="px-8 py-4 text-sm font-semibold text-slate-700">2026</td></tr>
                        <tr><th class="px-8 py-4 font-bold text-xs uppercase tracking-widest text-slate-400 bg-slate-50/50 premium-table-header text-indigo-600">Bảo hành</th><td class="px-8 py-4 text-sm font-bold text-indigo-600 uppercase tracking-wide">12 Tháng</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-content-reviews" class="tab-content hidden animate-[fadeIn_0.5s_ease-out]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                <!-- Left side: Review stats -->
                <div class="bg-indigo-50 p-8 rounded-[2rem] border border-indigo-100 flex items-center justify-center flex-col text-center">
                    <span class="text-6xl font-black text-indigo-600 mb-2"><?= $avgRating ?></span>
                    <div class="flex text-amber-400 mb-4 gap-1">
                        <?php 
                        $floorRating = floor($avgRating);
                        for($i=1; $i<=5; $i++):
                            if($i <= $floorRating):
                        ?>
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <?php else: ?>
                            <svg class="w-6 h-6 text-slate-300 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        <?php endif; endfor; ?>
                    </div>
                    <p class="text-indigo-900 font-bold tracking-widest uppercase text-xs">Điểm số trung bình (<?= $totalReviews ?> đánh giá)</p>
                </div>

                <!-- Right side: Reviews List -->
                <div class="space-y-6 max-h-[450px] overflow-y-auto pr-4">
                    <?php if(empty($reviewsList)): ?>
                        <div class="text-center py-12 text-slate-400 bg-white rounded-3xl border border-slate-100">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <p class="font-bold">Chưa có đánh giá nào cho sản phẩm này.</p>
                            <p class="text-xs">Hãy là người đầu tiên đánh giá sản phẩm này!</p>
                        </div>
                    <?php else: foreach($reviewsList as $rev): ?>
                        <div class="bg-white p-6 rounded-3xl border border-slate-100 flex gap-4 shadow-sm">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-black flex-shrink-0">
                                <?= mb_strtoupper(mb_substr($rev['reviewer_name'], 0, 1)) ?>
                            </div>
                            <div class="flex-grow">
                                <div class="flex justify-between items-start">
                                    <h5 class="font-bold text-dark"><?= htmlspecialchars($rev['reviewer_name']) ?> 
                                        <span class="text-[10px] text-slate-400 font-medium ml-2"><?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?></span>
                                    </h5>
                                    <div class="flex text-amber-400 gap-0.5 scale-90 origin-right">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <svg class="w-4 h-4 <?= $i <= (int)$rev['rating'] ? 'fill-current' : 'text-slate-200 fill-current' ?>" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="text-slate-600 mt-2 text-sm leading-relaxed italic">"<?= htmlspecialchars($rev['comment']) ?>"</p>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- Write Review Form (Full Width at the bottom) -->
            <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm max-w-3xl mx-auto">
                <h4 class="font-black text-dark text-xl mb-6 italic uppercase tracking-tight text-center">Đánh giá sản phẩm này</h4>
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="submit_review" value="1">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Star Selection -->
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Số sao đánh giá</label>
                            <div class="flex gap-2" id="star-selector">
                                <?php for($i=1; $i<=5; $i++): ?>
                                <label class="cursor-pointer">
                                    <input type="radio" name="rating" value="<?= $i ?>" <?= $i === 5 ? 'checked' : '' ?> class="sr-only peer">
                                    <svg class="w-10 h-10 text-slate-300 peer-checked:text-amber-400 hover:text-amber-400 transition-colors fill-current star-icon" data-value="<?= $i ?>" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Tên hiển thị</label>
                            <input type="text" name="reviewer_name" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" placeholder="Nhập tên của bạn..." class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500 font-medium">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Bình luận của bạn</label>
                        <textarea name="comment" required rows="4" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500 font-medium"></textarea>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="inline-block btn-gradient text-white font-black py-4.5 px-12 rounded-[2rem] shadow-xl uppercase tracking-widest text-sm">GỬI ĐÁNH GIÁ CỦA BẠN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if(!empty($relatedProducts)): ?>
    <div>
        <div class="text-center mb-16">
            <h2 class="text-4xl font-black text-dark mb-4 uppercase italic">Có Thể Bạn <span class="text-indigo-600">Thích</span></h2>
            <div class="w-20 h-1.5 bg-indigo-600 mx-auto rounded-full"></div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <?php foreach($relatedProducts as $related): ?>
            <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-2xl hover:shadow-indigo-500/10 transition-all border border-slate-100 group card-hover">
                <div class="relative pb-[110%] bg-slate-50/50 p-6">
                    <!-- Floating heart on related product card -->
                    <div class="absolute top-5 right-5 z-20">
                        <?php 
                        $isRelatedFavorited = in_array($related['id'], $userWishlist);
                        ?>
                        <button onclick="event.preventDefault(); toggleWishlist(<?= $related['id'] ?>, this)" class="w-9 h-9 rounded-full bg-white/80 hover:bg-white backdrop-blur-md flex items-center justify-center text-slate-700 shadow-md transition-all duration-300 hover:scale-110 active:scale-90 select-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-colors duration-300 heart-icon-svg-<?= $related['id'] ?> <?= $isRelatedFavorited ? 'text-rose-500 fill-current animate-heart-pop' : 'text-slate-400 hover:text-rose-500' ?>" fill="<?= $isRelatedFavorited ? 'currentColor' : 'none' ?>" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                    </div>
                    <a href="product_detail.php?id=<?= $related['id'] ?>" class="absolute inset-0 z-10"></a>
                    <img src="<?= !empty($related['image_url']) ? 'public' . $related['image_url'] : 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80' ?>" class="absolute inset-0 w-full h-full object-contain p-8 mix-blend-multiply group-hover:scale-110 transition-transform duration-500">
                </div>
                <div class="p-6">
                    <p class="text-xs font-black text-indigo-500 uppercase tracking-widest mb-1"><?= htmlspecialchars($related['brand_name'] ?? $related['brand'] ?? '') ?></p>
                    <h3 class="text-lg font-black text-dark mb-4 leading-tight">
                        <a href="product_detail.php?id=<?= $related['id'] ?>" class="hover:text-indigo-600 transition-colors line-clamp-2"><?= htmlspecialchars($related['name']) ?></a>
                    </h3>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-black text-dark"><?= format_price($related['price']) ?></span>
                        <div class="w-8 h-8 rounded-full bg-slate-900 flex items-center justify-center text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function switchTab(tabId) {
    // Hide all contents
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    
    // Reset all tabs styles
    const tabs = ['desc', 'specs', 'reviews'];
    tabs.forEach(t => {
        let el = document.getElementById('tab-btn-' + t);
        el.classList.remove('text-indigo-600', 'border-indigo-600', 'font-black');
        el.classList.add('text-slate-400', 'border-transparent', 'hover:text-dark');
    });
    
    // Show active content
    document.getElementById('tab-content-' + tabId).classList.remove('hidden');
    
    // Set active tab style
    let activeTab = document.getElementById('tab-btn-' + tabId);
    activeTab.classList.remove('text-slate-400', 'border-transparent', 'hover:text-dark');
    activeTab.classList.add('text-indigo-600', 'border-indigo-600', 'font-black');
}

document.addEventListener('DOMContentLoaded', function() {
    // Set active tab based on hash URL
    if (window.location.hash === '#tab-btn-reviews') {
        switchTab('reviews');
    }

    const starLabels = document.querySelectorAll('#star-selector label');
    starLabels.forEach((label, idx) => {
        label.addEventListener('click', function() {
            // Unhighlight all stars
            document.querySelectorAll('#star-selector svg').forEach((svg, sIdx) => {
                if (sIdx <= idx) {
                    svg.classList.remove('text-slate-300');
                    svg.classList.add('text-amber-400');
                } else {
                    svg.classList.remove('text-amber-400');
                    svg.classList.add('text-slate-300');
                }
            });
        });
    });
});
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<?php include 'footer.php'; ?>
