<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'RuangKarya') ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= e(url('assets/style.css')) ?>?v=12">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 antialiased min-h-screen">

<!-- Navbar Dark -->
<header class="sticky top-0 z-50 bg-slate-950/80 backdrop-blur-md border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        
        <!-- Logo -->
        <a class="flex items-center gap-3 group" href="<?= e(url('index.php')) ?>">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-extrabold text-lg shadow-lg shadow-indigo-600/30 group-hover:scale-105 transition-transform">
                R
            </div>
            <div class="flex flex-col">
                <span class="text-lg font-bold tracking-tight text-slate-100 leading-none">
                    ruang<span class="text-indigo-400">karya</span>
                </span>
                <span class="text-[10px] font-semibold text-slate-400 tracking-widest uppercase mt-1">
                    Portofolio Digital Siswa
                </span>
            </div>
        </a>

        <!-- Navigasi -->
        <nav class="flex items-center gap-1 sm:gap-2">
            <a href="<?= e(url('index.php')) ?>" 
               class="px-4 py-2 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-900 transition-colors">
                Galeri
            </a>
            <a href="<?= e(url('submit.php')) ?>" 
               class="px-4 py-2 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-900 transition-colors">
                Kirim Karya
            </a>
            <div class="h-4 w-px bg-slate-800 mx-1"></div>
            <a href="<?= e(url('admin/login.php')) ?>" 
               class="px-4 py-2 rounded-xl text-sm font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-900 transition-colors flex items-center gap-1">
                Admin <span class="text-xs">↗</span>
            </a>
        </nav>

    </div>
</header>

<main class="bg-slate-950 min-h-[calc(100vh-80px)]">