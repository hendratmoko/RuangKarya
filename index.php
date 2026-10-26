<?php
require_once 'config.php';

$q = trim($_GET['q'] ?? '');
$kategori = trim($_GET['kategori'] ?? '');

$categories = [
    'Website', 'Game', 'Desain',
    'Aplikasi', 'Video', 'Lainnya'
];

$sql = "SELECT * FROM projects WHERE status = 'approved'";
$params = [];

if ($q !== '') {
    $sql .= " AND (title LIKE ? OR student_name LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

if (in_array($kategori, $categories, true)) {
    $sql .= " AND category = ?";
    $params[] = $kategori;
}

$sql .= " ORDER BY created_at DESC LIMIT 60";

$stmt = db()->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

$total = db()->query(
    "SELECT COUNT(*) FROM projects WHERE status='approved'"
)->fetchColumn();

$pageTitle = 'RuangKarya | Portofolio Siswa';
require 'includes/header.php';
?>

<!-- Outer Wrapper untuk Memastikan Background Gelap Penuh Tanpa Celah -->
<div class="min-h-screen bg-slate-950 text-slate-100 relative overflow-hidden">

    <!-- Ambient Glow Effect Background -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[350px] bg-indigo-600/15 blur-[160px] rounded-full pointer-events-none"></div>
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#475569_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>

    <!-- Hero Section dengan Maskot / Karakter Sekolah -->
    <section class="relative pt-16 pb-12 px-6 lg:px-12 z-10">
        <div class="max-w-6xl mx-auto flex flex-col-reverse lg:flex-row items-center justify-between gap-12 bg-slate-900/40 backdrop-blur-md p-8 sm:p-12 rounded-3xl">
            
            <!-- Teks Hero -->
            <div class="text-center lg:text-left flex-1">
                <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-indigo-950/80 text-indigo-300 text-xs font-medium tracking-widest uppercase mb-6 shadow-lg">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                    PORTOFOLIO DIGITAL SEKOLAH
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.15] mb-6 text-slate-100">
                    Ide hebat layak<br>
                    <span class="bg-gradient-to-r from-indigo-400 via-purple-400 to-cyan-400 bg-clip-text text-transparent">
                        untuk ditampilkan.
                    </span>
                </h1>
                <p class="text-slate-400 text-base sm:text-lg max-w-xl mx-auto lg:mx-0 mb-8 leading-relaxed font-light">
                    Ruang pamer eksklusif untuk berbagi kreativitas, mengeksplorasi hasil belajar terbaik, dan menginspirasi karya-karya masa depan.
                </p>
                <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                    <a href="<?= e(url('submit.php')) ?>" class="inline-flex items-center justify-center px-7 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium shadow-xl shadow-indigo-600/30 transition-all hover:-translate-y-0.5">
                        + Kirim Karya
                    </a>
                    <a href="#galeri" class="inline-flex items-center justify-center px-7 py-3.5 rounded-2xl bg-slate-900/80 hover:bg-slate-800 text-slate-300 hover:text-white font-medium transition-all hover:-translate-y-0.5">
                        Jelajahi Galeri ↓
                    </a>
                </div>
            </div>

            <!-- Maskot / Karakter Siswa Digital Sekolah -->
            <div class="relative flex-shrink-0">
                <div class="w-44 h-44 sm:w-56 sm:h-56 rounded-full bg-gradient-to-tr from-indigo-600/30 via-purple-500/20 to-cyan-400/20 p-1 flex items-center justify-center shadow-2xl">
                    <div class="w-full h-full rounded-full bg-slate-950/80 backdrop-blur-md flex items-center justify-center overflow-hidden relative">
                        <!-- Maskot SVG Siswa Kreatif -->
                        <svg class="w-32 h-32 sm:w-40 sm:h-40 text-indigo-400 drop-shadow-[0_10px_20px_rgba(99,102,241,0.4)]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Topi Kelulusan / Pelajar -->
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 7V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M12 12V21" stroke="#818CF8" stroke-width="1.5" stroke-dasharray="2 2"/>
                            <!-- Kepala Siswa -->
                            <circle cx="12" cy="12" r="3.5" fill="#312E81" stroke="#A5B4FC" stroke-width="1.5"/>
                            <!-- Badan & Pakaian Sekolah -->
                            <path d="M6 21C6 17.6863 8.68629 15 12 15C15.3137 15 18 17.6863 18 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <!-- Elemen Bintang Kreativitas -->
                            <path d="M19 3L19.5 4.5L21 5L19.5 5.5L19 7L18.5 5.5L17 5L18.5 4.5L19 3Z" fill="#38BDF8"/>
                            <path d="M5 2L5.3 3L6.3 3.3L5.3 3.6L5 4.6L4.7 3.6L3.7 3.3L4.7 3L5 2Z" fill="#C084FC"/>
                        </svg>
                    </div>
                </div>
                <!-- Badge Status di Bawah Karakter -->
                <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-[11px] font-bold px-3.5 py-1 rounded-full shadow-lg whitespace-nowrap">
                    🎓 SISWA KREATIF
                </span>
            </div>

        </div>
    </section>

    <!-- Main Gallery Section (Tanpa Garis Pembatas) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative z-10" id="galeri">
        
        <!-- Heading & Search Bar Row (Border Dihapus) -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 pb-4">
            <div>
                <span class="text-xs font-bold tracking-widest text-indigo-400 uppercase">Galeri Kreativitas</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-100 mt-1 tracking-tight">Karya Siswa</h2>
                <p class="text-slate-400 text-sm mt-1.5 font-light">Menampilkan total <strong class="text-slate-200"><?= (int)$total ?></strong> karya inspiratif yang telah dipublikasikan.</p>
            </div>

            <!-- Search Form -->
            <form class="flex items-center gap-2.5 w-full md:w-auto" method="get">
                <div class="relative w-full md:w-80">
                    <input type="search" name="q" 
                           placeholder="Cari judul atau nama siswa..." 
                           value="<?= e($q) ?>"
                           class="w-full pl-4 pr-4 py-3.5 bg-slate-900/80 border-0 rounded-2xl text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner">
                </div>
                <button type="submit" class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-2xl transition-all shadow-lg shadow-indigo-600/20 whitespace-nowrap">
                    Cari
                </button>
            </form>
        </div>

        <!-- Category Filters (Border Dihapus) -->
        <div class="flex items-center gap-2.5 overflow-x-auto pb-4 mb-10 no-scrollbar">
            <a href="<?= e(url('index.php')) ?>" 
               class="px-5 py-2.5 rounded-2xl text-sm font-medium whitespace-nowrap transition-all <?= ($kategori === '') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-900/60 text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
                Semua Karya
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= e(url('index.php?kategori=' . urlencode($cat))) ?>" 
                   class="px-5 py-2.5 rounded-2xl text-sm font-medium whitespace-nowrap transition-all <?= ($kategori === $cat) ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-900/60 text-slate-400 hover:bg-slate-800 hover:text-white' ?>">
                    <?= e($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Projects Grid -->
        <?php if (!$projects): ?>
            <div class="text-center py-20 bg-slate-900/40 rounded-3xl max-w-xl mx-auto my-6 backdrop-blur-md">
                <div class="w-16 h-16 bg-indigo-950/80 text-indigo-400 flex items-center justify-center rounded-2xl mx-auto mb-4 text-2xl">📂</div>
                <h3 class="text-xl font-semibold text-slate-200 mb-2">Belum ada karya untuk ditampilkan</h3>
                <p class="text-slate-400 text-sm mb-6 font-light">Jadilah siswa pertama yang berbagi karya hebatmu di sini!</p>
                <a href="<?= e(url('submit.php')) ?>" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-2xl shadow-lg shadow-indigo-600/30 transition-all">
                    Unggah Karya Sekarang
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($projects as $p): ?>
                    <article class="group bg-slate-900/60 backdrop-blur-md rounded-3xl shadow-xl hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-300 overflow-hidden flex flex-col justify-between">
                        <div>
                            <!-- Cover Image / Placeholder -->
                            <div class="relative aspect-video overflow-hidden bg-slate-950">
                                <a href="<?= e(url('project.php?id=' . (int)$p['id'])) ?>">
                                    <?php if ($p['cover_image']): ?>
                                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90 group-hover:opacity-100"
                                             src="<?= e(url($p['cover_image'])) ?>"
                                             alt="Sampul karya">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-indigo-950 via-slate-900 to-purple-950 text-indigo-400 font-extrabold text-2xl tracking-wider">
                                            <?= e(strtoupper(substr($p['category'], 0, 2))) ?>
                                        </div>
                                    <?php endif; ?>
                                </a>
                                <div class="absolute top-3 left-3">
                                    <span class="px-3 py-1 bg-slate-950/80 backdrop-blur-md text-indigo-300 text-xs font-semibold rounded-xl shadow-sm">
                                        <?= e($p['category']) ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Content Details -->
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-slate-100 group-hover:text-indigo-400 transition-colors line-clamp-1 mb-2.5">
                                    <a href="<?= e(url('project.php?id=' . (int)$p['id'])) ?>">
                                        <?= e($p['title']) ?>
                                    </a>
                                </h3>

                                <p class="text-slate-400 text-sm line-clamp-2 mb-4 leading-relaxed font-light">
                                    <?= e($p['description']) ?>
                                </p>
                            </div>
                        </div>

                        <!-- Card Footer / Student Info (Border Dihapus) -->
                        <div class="px-6 pb-6 pt-2 flex items-center justify-between bg-slate-950/20 mt-auto">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-slate-200"><?= e($p['student_name']) ?></span>
                                <span class="text-xs text-slate-500"><?= e($p['student_class']) ?></span>
                            </div>
                            <a href="<?= e(url('project.php?id=' . (int)$p['id'])) ?>" 
                               class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-400 hover:text-indigo-300 group-hover:translate-x-1 transition-transform">
                                Detail <span class="text-sm">→</span>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

</div>

<?php require 'includes/footer.php'; ?>