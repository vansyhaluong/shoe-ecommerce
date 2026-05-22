<?php 
include_once 'config.php'; 

// Fetch wishlist count
$wishlistCount = 0;
if (isset($_SESSION['user_id'])) {
    $stmt_wl = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
    $stmt_wl->execute([$_SESSION['user_id']]);
    $wishlistCount = (int)$stmt_wl->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'ShoeStore - Premium Sneakers' ?></title>
    <!-- Google Fonts: Montserrat & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,700;1,900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#4338ca',
                        dark: '#0f172a'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Montserrat', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            letter-spacing: -0.01em;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .font-display {
            font-family: 'Montserrat', sans-serif;
            letter-spacing: -0.02em;
        }

        /* Premium Typography Styles */
        .premium-brand-tag {
            font-family: 'Montserrat', sans-serif;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #94a3b8; /* text-slate-400 */
        }
        
        .premium-product-name {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            letter-spacing: -0.015em;
            color: #0f172a; /* text-dark */
        }
        
        .premium-price {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a; /* text-dark */
        }
        
        .premium-btn {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        
        .premium-label {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b; /* text-slate-500 */
        }
        
        .premium-nav-link {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .premium-table-header {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #94a3b8; /* text-slate-400 */
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            filter: brightness(110%);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
        }

        .animate-heart-pop {
            animation: heart-pop 0.45s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes heart-pop {
            0% { transform: scale(1); }
            50% { transform: scale(1.4); }
            100% { transform: scale(1); }
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 flex flex-col min-h-screen">
    <header class="glass-header sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3 select-none">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30 hover:scale-105 hover:bg-indigo-700 transition-all duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <!-- Upper shoe outline -->
                        <path d="M4 11.5c.5-1 1.5-2.5 3-2.5h3l3.5-3h4c1 0 1.5.5 1.5 1.5v3l3.5 3.5v3H4v-6.5z"></path>
                        <!-- Sole pattern and details -->
                        <path d="M3 17.5h18c.8 0 1.5-.7 1.5-1.5H1.5c0 .8.7 1.5 1.5 1.5z" fill="currentColor"></path>
                        <!-- Laces detail -->
                        <path d="M12 8.5l2 2M13.5 7l2 2"></path>
                        <!-- Dynamic swoosh shape -->
                        <path d="M8 14.5c2-2.5 5.5-2.5 8 0"></path>
                    </svg>
                </div>
                <span class="font-display tracking-[0.15em] text-dark font-extrabold text-lg md:text-xl uppercase transition-colors hover:text-indigo-600">
                    Shoe Store<span class="text-indigo-600">.</span>
                </span>
            </a>

            <nav class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="text-xs font-semibold hover:text-indigo-600 transition-all uppercase tracking-[0.12em] premium-nav-link"><?= __('home') ?></a>
                <a href="products.php" class="text-xs font-semibold hover:text-indigo-600 transition-all uppercase tracking-[0.12em] premium-nav-link"><?= __('products') ?></a>
                <a href="services.php" class="text-xs font-semibold hover:text-indigo-600 transition-all uppercase tracking-[0.12em] premium-nav-link"><?= __('services') ?></a>
            </nav>

            <!-- Header Actions -->
            <div class="flex items-center space-x-2 md:space-x-5">
                <!-- Language Switcher (Hidden on mobile) -->
                <div class="hidden md:flex items-center gap-2 bg-slate-100 p-1 rounded-full border border-slate-200">
                    <a href="?lang=vi" class="w-7 h-7 rounded-full overflow-hidden border-2 <?= $current_lang == 'vi' ? 'border-indigo-600 shadow-sm' : 'border-transparent opacity-50 hover:opacity-100' ?> transition-all">
                        <img src="https://flagcdn.com/w40/vn.png" class="w-full h-full object-cover">
                    </a>
                    <a href="?lang=en" class="w-7 h-7 rounded-full overflow-hidden border-2 <?= $current_lang == 'en' ? 'border-indigo-600 shadow-sm' : 'border-transparent opacity-50 hover:opacity-100' ?> transition-all">
                        <img src="https://flagcdn.com/w40/us.png" class="w-full h-full object-cover">
                    </a>
                </div>

                <div class="hidden md:block h-6 w-px bg-slate-200"></div>

                <!-- Wishlist Icon with count badge -->
                <a href="wishlist.php" class="p-2 hover:bg-indigo-50 rounded-full transition-all duration-300 relative group" title="Wishlist">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-700 hover:text-rose-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span id="wishlist-badge" class="absolute -top-1 -right-1 bg-rose-500 text-white text-[9px] font-black rounded-full h-5 w-5 flex items-center justify-center border-2 border-white transition-all duration-300 <?= $wishlistCount > 0 ? 'scale-100 opacity-100' : 'scale-0 opacity-0' ?>">
                        <?= $wishlistCount ?>
                    </span>
                </a>

                <div class="h-6 w-px bg-slate-200"></div>

                <!-- Cart Icon with count badge -->
                <a href="cart.php" class="p-2 hover:bg-indigo-50 rounded-full transition-colors relative" title="Giỏ hàng">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="absolute top-0 right-0 bg-indigo-600 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center border-2 border-white">
                        <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
                    </span>
                </a>

                <!-- User Profile & Auth Buttons (Hidden on mobile) -->
                <div class="hidden md:flex items-center space-x-3">
                    <div class="h-6 w-px bg-slate-200"></div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="flex items-center gap-4">
                            <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                                <a href="admin_dashboard.php" class="text-xs font-bold px-3 py-1 bg-slate-900 text-white rounded-full"><?= __('admin_dashboard') ?></a>
                            <?php endif; ?>
                            <div class="flex flex-col items-end">
                                <span class="text-xs text-slate-500"><?= __('hello,') ?></span>
                                <a href="my_orders.php" class="text-sm font-bold text-dark hover:text-indigo-600 transition-colors italic uppercase tracking-tighter decoration-indigo-600 decoration-2 underline-offset-4 hover:underline"><?= htmlspecialchars($_SESSION['user_name']) ?></a>
                            </div>
                            <a href="logout.php" class="p-2 hover:bg-red-50 text-red-500 rounded-full transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="text-sm font-bold text-dark hover:text-indigo-600 transition-colors px-4"><?= __('login') ?></a>
                        <a href="register.php" class="btn-gradient text-white text-sm font-bold px-6 py-2.5 rounded-full"><?= __('register') ?></a>
                    <?php endif; ?>
                </div>

                <!-- Hamburger Mobile Trigger -->
                <button onclick="toggleMobileMenu()" class="block md:hidden p-2 hover:bg-indigo-50 rounded-full transition-colors text-slate-700" title="Menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Overlay -->
        <div id="mobile-menu" class="fixed inset-0 z-[100] bg-slate-950/60 backdrop-blur-sm hidden transition-all duration-300 opacity-0">
            <div id="mobile-menu-drawer" class="absolute right-0 top-0 bottom-0 w-[300px] bg-white shadow-2xl p-8 flex flex-col justify-between transform translate-x-full transition-transform duration-300">
                <div class="space-y-8">
                    <!-- Drawer Header -->
                    <div class="flex justify-between items-center pb-6 border-b border-slate-100">
                        <h4 class="font-black italic uppercase tracking-wider text-dark">Menu</h4>
                        <button onclick="toggleMobileMenu()" class="p-2 hover:bg-slate-100 rounded-full transition-colors">
                            <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Navigation Links -->
                    <div class="flex flex-col space-y-5">
                        <a href="index.php" onclick="toggleMobileMenu()" class="text-sm font-bold hover:text-indigo-600 transition-all uppercase tracking-[0.15em] font-display"><?= __('home') ?></a>
                        <a href="products.php" onclick="toggleMobileMenu()" class="text-sm font-bold hover:text-indigo-600 transition-all uppercase tracking-[0.15em] font-display"><?= __('products') ?></a>
                        <a href="services.php" onclick="toggleMobileMenu()" class="text-sm font-bold hover:text-indigo-600 transition-all uppercase tracking-[0.15em] font-display"><?= __('services') ?></a>
                    </div>

                    <div class="border-t border-slate-100 pt-6 space-y-4">
                        <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-2">Ngôn ngữ / Language</h5>
                        <div class="flex items-center gap-4 bg-slate-50 p-2 rounded-2xl border border-slate-200 max-w-max">
                            <a href="?lang=vi" class="flex items-center gap-2 text-xs font-bold text-slate-700 <?= $current_lang == 'vi' ? 'text-indigo-600' : 'opacity-60' ?>">
                                <img src="https://flagcdn.com/w40/vn.png" class="w-6 h-4 object-cover rounded-sm border"> Tiếng Việt
                            </a>
                            <div class="w-px h-4 bg-slate-200"></div>
                            <a href="?lang=en" class="flex items-center gap-2 text-xs font-bold text-slate-700 <?= $current_lang == 'en' ? 'text-indigo-600' : 'opacity-60' ?>">
                                <img src="https://flagcdn.com/w40/us.png" class="w-6 h-4 object-cover rounded-sm border"> English
                            </a>
                        </div>
                    </div>
                </div>

                <!-- User Panel inside mobile drawer -->
                <div class="border-t border-slate-100 pt-6">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="space-y-4">
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 flex items-center justify-between">
                                <div>
                                    <p class="text-2xs text-slate-400 font-bold uppercase tracking-wide"><?= __('hello,') ?></p>
                                    <a href="my_orders.php" onclick="toggleMobileMenu()" class="text-sm font-black text-dark hover:text-indigo-600 transition-colors italic uppercase tracking-tighter decoration-indigo-600 decoration-2 underline-offset-4 hover:underline"><?= htmlspecialchars($_SESSION['user_name']) ?></a>
                                </div>
                                <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                                    <a href="admin_dashboard.php" onclick="toggleMobileMenu()" class="text-3xs font-black px-2.5 py-1 bg-slate-900 text-white rounded-full uppercase tracking-wider">Admin</a>
                                <?php endif; ?>
                            </div>
                            <div class="flex gap-2.5">
                                <a href="my_orders.php" onclick="toggleMobileMenu()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black py-3 rounded-xl text-center text-xs uppercase tracking-widest transition-all">Đơn hàng</a>
                                <a href="logout.php" class="bg-rose-50 hover:bg-rose-100 text-rose-500 font-black px-4 rounded-xl flex items-center justify-center transition-all" title="Đăng xuất">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex flex-col gap-3">
                            <a href="login.php" onclick="toggleMobileMenu()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-black py-3.5 rounded-2xl text-center text-xs uppercase tracking-widest transition-all">Đăng nhập</a>
                            <a href="register.php" onclick="toggleMobileMenu()" class="w-full btn-gradient text-white font-black py-3.5 rounded-2xl text-center text-xs uppercase tracking-widest transition-all">Đăng ký</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script>
        let mobileMenuOpen = false;
        function toggleMobileMenu() {
            const overlay = document.getElementById('mobile-menu');
            const drawer = document.getElementById('mobile-menu-drawer');
            mobileMenuOpen = !mobileMenuOpen;
            if (mobileMenuOpen) {
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.add('opacity-100');
                    drawer.classList.remove('translate-x-full');
                }, 10);
            } else {
                overlay.classList.remove('opacity-100');
                drawer.classList.add('translate-x-full');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
            }
        }
        </script>
    </header>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none"></div>

    <script>
    // Global Toast Notification function
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        // Sleek minimal design matching high-end sneaker stores
        toast.className = `flex items-center gap-3 bg-white/95 backdrop-blur-md border border-slate-100 shadow-[0_20px_50px_rgba(0,0,0,0.12)] p-4 pr-6 rounded-2xl max-w-sm pointer-events-auto transform translate-y-4 opacity-0 transition-all duration-500 ease-out`;
        
        let icon = '';
        if (type === 'success') {
            icon = `<div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>`;
        } else if (type === 'error' || type === 'unauthorized') {
            icon = `<div class="w-8 h-8 rounded-full bg-rose-50 flex items-center justify-center text-rose-600 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>`;
        } else {
            icon = `<div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>`;
        }

        toast.innerHTML = `
            ${icon}
            <div class="flex-grow">
                <p class="text-sm font-bold text-slate-800 leading-tight">${message}</p>
            </div>
        `;

        container.appendChild(toast);

        // Slide/Fade In
        setTimeout(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
        }, 50);

        // Slide/Fade Out and auto-destroy
        setTimeout(() => {
            toast.classList.add('translate-y-4', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 500);
        }, 3500);
    }

    // Global Wishlist AJAX Toggle function
    function toggleWishlist(productId, btnElement) {
        if (btnElement) {
            btnElement.classList.add('scale-75');
            setTimeout(() => btnElement.classList.remove('scale-75'), 100);
        }

        const formData = new FormData();
        formData.append('ajax', '1');
        formData.append('product_id', productId);

        fetch('wishlist_action.php?action=toggle', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'unauthorized') {
                showToast(data.message, 'error');
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1200);
            } else if (data.status === 'success') {
                // Update badge count
                const badge = document.getElementById('wishlist-badge');
                if (badge) {
                    badge.innerText = data.count;
                    if (data.count > 0) {
                        badge.classList.remove('scale-0', 'opacity-0');
                        badge.classList.add('scale-100', 'opacity-100');
                    } else {
                        badge.classList.remove('scale-100', 'opacity-100');
                        badge.classList.add('scale-0', 'opacity-0');
                    }
                    
                    // Pop animation for badge
                    badge.classList.add('scale-125');
                    setTimeout(() => badge.classList.remove('scale-125'), 300);
                }

                // Update heart icons across the page matching this product ID
                const heartIcons = document.querySelectorAll(`.heart-icon-svg-${productId}`);
                heartIcons.forEach(svg => {
                    if (data.action === 'added') {
                        svg.setAttribute('fill', 'currentColor');
                        svg.classList.remove('text-slate-400');
                        svg.classList.add('text-rose-500', 'animate-heart-pop');
                        setTimeout(() => svg.classList.remove('animate-heart-pop'), 450);
                    } else {
                        svg.setAttribute('fill', 'none');
                        svg.classList.remove('text-rose-500');
                        svg.classList.add('text-slate-400');
                    }
                });

                showToast(data.message, 'success');

                // If on wishlist page, remove card smoothly
                const card = document.getElementById(`wishlist-card-${productId}`);
                if (card) {
                    card.classList.add('opacity-0', 'scale-90');
                    setTimeout(() => {
                        card.remove();
                        // Check if grid is now empty
                        const grid = document.getElementById('wishlist-grid');
                        if (grid && grid.children.length === 0) {
                            location.reload();
                        }
                    }, 400); // 400ms for smooth transitions
                }
            } else {
                showToast(data.message || 'Đã có lỗi xảy ra!', 'error');
            }
        })
        .catch(err => {
            console.error('Lỗi khi cập nhật wishlist:', err);
            showToast('Không thể kết nối đến máy chủ.', 'error');
        });
    }
    </script>

    <main class="flex-grow">