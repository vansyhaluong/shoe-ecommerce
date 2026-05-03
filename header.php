<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'ShoeStore - Premium Sneakers' ?></title>
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,800&display=swap" rel="stylesheet">
    
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
                        display: ['Plus Jakarta Sans', 'sans-serif'],
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
        }
        h1, h2, h3, h4, h5, h6, .font-display { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.025em;
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
    </style>
</head>
<body class="bg-slate-50 text-slate-900 flex flex-col min-h-screen">
    <header class="glass-header sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-3xl font-extrabold tracking-tighter text-dark flex items-center gap-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                Shoe<span class="text-indigo-600">Store.</span>
            </a>
            
            <nav class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="text-sm font-semibold hover:text-indigo-600 transition-colors uppercase tracking-wider"><?= __('home') ?></a>
                <a href="products.php" class="text-sm font-semibold hover:text-indigo-600 transition-colors uppercase tracking-wider"><?= __('products') ?></a>
                <a href="#" class="text-sm font-semibold hover:text-indigo-600 transition-colors uppercase tracking-wider"><?= __('brands') ?></a>
            </nav>

            <div class="flex items-center space-x-5">
                <!-- Language Switcher -->
                <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-full border border-slate-200">
                    <a href="?lang=vi" class="w-7 h-7 rounded-full overflow-hidden border-2 <?= $current_lang == 'vi' ? 'border-indigo-600 shadow-sm' : 'border-transparent opacity-50 hover:opacity-100' ?> transition-all">
                        <img src="https://flagcdn.com/w40/vn.png" class="w-full h-full object-cover">
                    </a>
                    <a href="?lang=en" class="w-7 h-7 rounded-full overflow-hidden border-2 <?= $current_lang == 'en' ? 'border-indigo-600 shadow-sm' : 'border-transparent opacity-50 hover:opacity-100' ?> transition-all">
                        <img src="https://flagcdn.com/w40/us.png" class="w-full h-full object-cover">
                    </a>
                </div>

                <div class="h-6 w-px bg-slate-200"></div>

                <a href="cart.php" class="p-2 hover:bg-indigo-50 rounded-full transition-colors relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span class="absolute top-0 right-0 bg-indigo-600 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center border-2 border-white">
                        <?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>
                    </span>
                </a>
                
                <div class="h-6 w-px bg-slate-200"></div>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center gap-4">
                        <?php if(($_SESSION['user_role'] ?? '') === 'admin'): ?>
                            <a href="admin_dashboard.php" class="text-xs font-bold px-3 py-1 bg-slate-900 text-white rounded-full"><?= __('admin_dashboard') ?></a>
                        <?php endif; ?>
                        <div class="flex flex-col items-end">
                            <span class="text-xs text-slate-500">Xin chào,</span>
                            <a href="my_orders.php" class="text-sm font-bold text-dark hover:text-indigo-600 transition-colors italic uppercase tracking-tighter decoration-indigo-600 decoration-2 underline-offset-4 hover:underline"><?= htmlspecialchars($_SESSION['user_name']) ?></a>
                        </div>
                        <a href="logout.php" class="p-2 hover:bg-red-50 text-red-500 rounded-full transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="text-sm font-bold text-dark hover:text-indigo-600 transition-colors px-4"><?= __('login') ?></a>
                    <a href="register.php" class="btn-gradient text-white text-sm font-bold px-6 py-2.5 rounded-full"><?= __('register') ?></a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="flex-grow">
