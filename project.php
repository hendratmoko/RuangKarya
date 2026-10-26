<?php
require_once 'config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$stmt = db()->prepare(
    "SELECT * FROM projects
     WHERE id = ? AND status = 'approved'
     LIMIT 1"
);
$stmt->execute([$id ?: 0]);
$p = $stmt->fetch();

if (!$p) {
    http_response_code(404);
}

$pageTitle = $p
    ? $p['title'] . ' | RuangKarya'
    : 'Karya Tidak Ditemukan';

require 'includes/header.php';
?>

<section class="container detail-page py-16 max-w-4xl mx-auto px-4 sm:px-6">
    <?php if (!$p): ?>
        <!-- State jika karya tidak ditemukan -->
        <div class="text-center py-24 bg-slate-900/40 rounded-2xl border border-dashed border-slate-800 max-w-xl mx-auto my-12 backdrop-blur-sm">
            <div class="text-5xl mb-4">🔍</div>
            <h1 class="text-2xl font-bold text-slate-100 mb-2">Karya tidak ditemukan</h1>
            <p class="text-slate-400 text-sm mb-6">Karya belum dipublikasikan, telah dihapus, atau tautan tidak tersedia.</p>
            <a class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl shadow-lg shadow-indigo-600/30 transition-all" href="<?= e(url('index.php')) ?>">
                ← Kembali ke Galeri
            </a>
        </div>
    <?php else: ?>

        <!-- Tombol Navigasi Kembali -->
        <div class="mb-6">
            <a href="<?= e(url('index.php')) ?>" class="text-slate-400 hover:text-indigo-400 text-sm font-medium inline-flex items-center gap-2 transition-colors">
                ← Kembali ke Galeri
            </a>
        </div>

        <!-- Kartu Detail Utama (Dark Aesthetic) -->
        <div class="bg-slate-900/80 backdrop-blur-md border border-slate-800 rounded-2xl shadow-2xl p-6 sm:p-10 text-slate-100">
            
            <!-- Kategori Badge -->
            <div class="mb-4">
                <span class="px-3 py-1 bg-indigo-950/80 border border-indigo-500/30 text-indigo-300 text-xs font-semibold rounded-lg shadow-sm">
                    <?= e($p['category']) ?>
                </span>
            </div>

            <!-- Judul Karya -->
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-100 tracking-tight leading-tight mb-6">
                <?= e($p['title']) ?>
            </h1>

            <!-- Informasi Pembuat & Tanggal -->
            <div class="flex items-center justify-between flex-wrap gap-4 mb-8 pb-6 border-b border-slate-800">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-full bg-indigo-950 border border-indigo-500/30 text-indigo-400 flex items-center justify-center font-bold text-lg shadow-inner">
                        <?= e(strtoupper(substr($p['student_name'], 0, 1))) ?>
                    </div>
                    <div>
                        <div class="font-bold text-slate-200 text-base"><?= e($p['student_name']) ?></div>
                        <div class="text-xs text-slate-400"><?= e($p['student_class']) ?></div>
                    </div>
                </div>
                <?php if (!empty($p['created_at'])): ?>
                    <div class="text-xs text-slate-500 font-medium">
                        Dipublikasikan pada <?= date('d M Y', strtotime($p['created_at'])) ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Gambar Cover / Sampul -->
            <?php if ($p['cover_image']): ?>
                <div class="mb-8 rounded-xl overflow-hidden border border-slate-800 bg-slate-950 shadow-inner">
                    <img class="w-full max-h-[500px] object-cover"
                         src="<?= e(url($p['cover_image'])) ?>"
                         alt="Sampul <?= e($p['title']) ?>">
                </div>
            <?php endif; ?>

            <!-- Deskripsi / Tentang Karya -->
            <h3 class="text-xl font-bold text-slate-200 mb-4">Tentang Karya</h3>
            <div class="text-slate-300 text-base leading-relaxed whitespace-pre-wrap mb-10 font-light"><?= e($p['description']) ?></div>

            <!-- Tombol Aksi (Demo & URL Proyek) -->
            <?php if ($p['demo_url'] || $p['project_url']): ?>
                <div class="flex flex-wrap gap-4 pt-6 border-t border-slate-800">
                    <?php if ($p['demo_url']): ?>
                        <a class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium shadow-lg shadow-indigo-600/30 transition-all text-sm"
                           href="<?= e($p['demo_url']) ?>"
                           target="_blank" rel="noopener noreferrer">
                            Lihat Demo Langsung ↗
                        </a>
                    <?php endif; ?>

                    <?php if ($p['project_url']): ?>
                        <a class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 font-medium transition-all text-sm"
                           href="<?= e($p['project_url']) ?>"
                           target="_blank" rel="noopener noreferrer">
                            Lihat Kode / Repository ↗
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>

    <?php endif; ?>
</section>

<?php require 'includes/footer.php'; ?>