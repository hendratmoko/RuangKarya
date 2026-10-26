<?php
require_once 'config.php';

$categories = [
    'Website', 'Game', 'Desain',
    'Aplikasi', 'Video', 'Lainnya'
];

$errors = [];
$success = false;

// Variabel untuk menyimpan input lama
$old = [
    'title' => '',
    'student_name' => '',
    'student_class' => '',
    'category' => 'Lainnya',
    'description' => '',
    'demo_url' => '',
    'project_url' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $old['title'] = trim($_POST['title'] ?? '');
    $old['student_name'] = trim($_POST['student_name'] ?? '');
    $old['student_class'] = trim($_POST['student_class'] ?? '');
    $old['category'] = $_POST['category'] ?? 'Lainnya';
    $old['description'] = trim($_POST['description'] ?? '');
    $old['demo_url'] = trim($_POST['demo_url'] ?? '');
    $old['project_url'] = trim($_POST['project_url'] ?? '');

    if ($old['title'] === '' || mb_strlen($old['title']) > 180) {
        $errors[] = 'Judul wajib diisi, maksimal 180 karakter.';
    }

    if ($old['student_name'] === '' || mb_strlen($old['student_name']) > 120) {
        $errors[] = 'Nama siswa wajib diisi.';
    }

    if ($old['student_class'] === '' || mb_strlen($old['student_class']) > 80) {
        $errors[] = 'Kelas wajib diisi.';
    }

    if (!in_array($old['category'], $categories, true)) {
        $errors[] = 'Kategori tidak valid.';
    }

    if (mb_strlen($old['description']) < 15) {
        $errors[] = 'Deskripsi minimal 15 karakter.';
    }

    foreach ([$old['demo_url'], $old['project_url']] as $link) {
        if ($link !== '') {
            $scheme = strtolower(
                parse_url($link, PHP_URL_SCHEME) ?? ''
            );

            if (!filter_var($link, FILTER_VALIDATE_URL) ||
                !in_array($scheme, ['http', 'https'], true)) {
                $errors[] = 'Tautan harus berupa URL HTTP/HTTPS.';
                break;
            }
        }
    }

    $coverPath = null;

    if (isset($_FILES['cover']) &&
        $_FILES['cover']['error'] !== UPLOAD_ERR_NO_FILE) {

        $file = $_FILES['cover'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload gambar gagal.';
        } elseif ($file['size'] > 3 * 1024 * 1024) {
            $errors[] = 'Ukuran gambar maksimal 3 MB.';
        } else {
            $mime = (new finfo(FILEINFO_MIME_TYPE))
                ->file($file['tmp_name']);

            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($allowed[$mime])) {
                $errors[] = 'Format gambar tidak didukung.';
            } else {
                $dir = __DIR__ . '/uploads';

                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                $filename = bin2hex(random_bytes(16))
                          . '.' . $allowed[$mime];

                if (move_uploaded_file(
                    $file['tmp_name'],
                    $dir . '/' . $filename
                )) {
                    $coverPath = 'uploads/' . $filename;
                } else {
                    $errors[] = 'Gambar gagal disimpan.';
                }
            }
        }
    }

    if (!$errors) {
        $stmt = db()->prepare(
            "INSERT INTO projects
            (title, student_name, student_class, category,
             description, cover_image, demo_url, project_url,
             status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
        );

        $stmt->execute([
            $old['title'], $old['student_name'], $old['student_class'], $old['category'],
            $old['description'], $coverPath,
            $old['demo_url'] ?: null, $old['project_url'] ?: null
        ]);

        $success = true;
    }
}

$pageTitle = 'Kirim Karya | RuangKarya';
require 'includes/header.php';
?>

<!-- Wrapper Utama Seluruh Layar (Full Dark Background Tanpa Garis Pembatas) -->
<div class="min-h-screen bg-slate-950 text-slate-100 relative overflow-hidden">

    <!-- Ambient Glow Background Background Effects -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[650px] h-[350px] bg-indigo-600/15 blur-[150px] rounded-full pointer-events-none"></div>
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#475569_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>

    <!-- Banner Header dengan Karakter / Maskot Sekolah -->
    <section class="relative pt-12 pb-6 px-6 z-10">
        <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center justify-between gap-8 bg-slate-900/40 backdrop-blur-md p-8 sm:p-10 rounded-3xl">
            
            <!-- Teks Sambutan -->
            <div class="text-center md:text-left flex-1">
                <span class="inline-flex items-center gap-2 py-1 px-3.5 rounded-full bg-indigo-950/80 border-0 text-indigo-300 text-xs font-medium tracking-widest uppercase mb-4 shadow-lg">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                    FORMULIR UNGGAH KARYA
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-100 tracking-tight mb-3">
                    Bagikan Karyamu!
                </h1>
                <p class="text-slate-400 text-sm sm:text-base font-light leading-relaxed">
                    Tunjukkan hasil belajar, kreativitas, dan inovasi terbaikmu. Karya hebatmu akan menginspirasi teman-teman satu sekolah!
                </p>
            </div>

            <!-- Karakter / Maskot Siswa Digital -->
            <div class="relative flex-shrink-0">
                <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-full bg-gradient-to-tr from-indigo-600/30 via-purple-500/20 to-cyan-400/20 p-1 flex items-center justify-center shadow-2xl">
                    <div class="w-full h-full rounded-full bg-slate-950/80 backdrop-blur-md flex items-center justify-center overflow-hidden relative">
                        <!-- Maskot / Karakter Ilustrasi SVG Siswa Kreatif -->
                        <svg class="w-24 h-24 sm:w-28 sm:h-28 text-indigo-400 drop-shadow-[0_10px_20px_rgba(99,102,241,0.4)]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Topi Kelulusan / Pelajar -->
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 7V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M12 12V21" stroke="#818CF8" stroke-width="1.5" stroke-dasharray="2 2"/>
                            <!-- Kepala Siswa -->
                            <circle cx="12" cy="12" r="3.5" fill="#312E81" stroke="#A5B4FC" stroke-width="1.5"/>
                            <!-- Badan & Buku / Laptop -->
                            <path d="M6 21C6 17.6863 8.68629 15 12 15C15.3137 15 18 17.6863 18 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <!-- Bintang Kreativitas -->
                            <path d="M19 3L19.5 4.5L21 5L19.5 5.5L19 7L18.5 5.5L17 5L18.5 4.5L19 3Z" fill="#38BDF8"/>
                            <path d="M5 2L5.3 3L6.3 3.3L5.3 3.6L5 4.6L4.7 3.6L3.7 3.3L4.7 3L5 2Z" fill="#C084FC"/>
                        </svg>
                    </div>
                </div>
                <!-- Badge 'Kreatif' di Karakter -->
                <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-[10px] font-bold px-3 py-0.5 rounded-full shadow-lg whitespace-nowrap">
                    🎓 SISWA KREATIF
                </span>
            </div>

        </div>
    </section>

    <!-- Form Container -->
    <section class="max-w-3xl mx-auto px-4 sm:px-6 py-8 relative z-10">
        <?php if ($success): ?>
            <!-- State Sukses -->
            <div class="bg-slate-900/80 backdrop-blur-md rounded-3xl p-10 text-center shadow-2xl space-y-4">
                <div class="w-20 h-20 bg-indigo-950/80 border-0 text-indigo-400 rounded-full flex items-center justify-center mx-auto text-4xl shadow-inner">
                    🎉
                </div>
                <h2 class="text-2xl font-bold text-slate-100">Karya Berhasil Dikirim!</h2>
                <p class="text-slate-400 text-sm max-w-md mx-auto leading-relaxed font-light">
                    Karya kamu sedang dalam proses peninjauan oleh tim guru/admin sebelum resmi dipublikasikan di galeri utama.
                </p>
                <div class="pt-4">
                    <a href="<?= e(url('index.php')) ?>" class="inline-flex items-center px-7 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-2xl shadow-xl shadow-indigo-600/30 transition-all hover:-translate-y-0.5">
                        ← Kembali ke Galeri
                    </a>
                </div>
            </div>
        <?php else: ?>

            <!-- Notifikasi Error -->
            <?php if ($errors): ?>
                <div class="mb-6 p-5 bg-red-950/40 backdrop-blur-md rounded-2xl text-red-300 text-sm space-y-2">
                    <?php foreach ($errors as $error): ?>
                        <p class="flex items-center gap-2">
                            <span class="text-red-400">⚠️</span> <?= e($error) ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Form Input (Tanpa Garis Pembatas) -->
            <form class="bg-slate-900/70 backdrop-blur-md rounded-3xl p-6 sm:p-10 shadow-2xl space-y-7" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <!-- Judul Karya -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2.5">
                        Judul Karya <span class="text-indigo-400">*</span>
                    </label>
                    <input type="text" name="title" maxlength="180" 
                           value="<?= e($old['title']) ?>" 
                           placeholder="Contoh: Aplikasi Pengelolaan Keuangan Siswa" required
                           class="w-full px-4 py-3.5 bg-slate-950/80 border-0 rounded-2xl text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner">
                </div>

                <!-- Nama & Kelas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2.5">
                            Nama Siswa <span class="text-indigo-400">*</span>
                        </label>
                        <input type="text" name="student_name" maxlength="120" 
                               value="<?= e($old['student_name']) ?>" 
                               placeholder="Nama lengkapmu" required
                               class="w-full px-4 py-3.5 bg-slate-950/80 border-0 rounded-2xl text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2.5">
                            Kelas <span class="text-indigo-400">*</span>
                        </label>
                        <input type="text" name="student_class" maxlength="80" 
                               value="<?= e($old['student_class']) ?>" 
                               placeholder="Contoh: XII RPL 1" required
                               class="w-full px-4 py-3.5 bg-slate-950/80 border-0 rounded-2xl text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner">
                    </div>
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2.5">
                        Kategori Karya <span class="text-indigo-400">*</span>
                    </label>
                    <select name="category" required
                            class="w-full px-4 py-3.5 bg-slate-950/80 border-0 rounded-2xl text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= e($cat) ?>" <?= ($old['category'] === $cat) ? 'selected' : '' ?>>
                                <?= e($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Deskripsi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2.5">
                        Deskripsi Karya <span class="text-indigo-400">*</span>
                    </label>
                    <textarea name="description" minlength="15" rows="5" required
                              placeholder="Jelaskan tujuan karya, fitur utama, teknologi yang digunakan, serta proses pembuatannya..."
                              class="w-full px-4 py-3.5 bg-slate-950/80 border-0 rounded-2xl text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all leading-relaxed shadow-inner"><?= e($old['description']) ?></textarea>
                    <p class="text-xs text-slate-500 mt-1.5 font-light">Minimal 15 karakter.</p>
                </div>

                <!-- Upload Cover -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2.5">
                        Gambar Sampul / Cover <span class="text-slate-500 font-normal">(Opsional)</span>
                    </label>
                    <input type="file" name="cover" accept=".jpg,.jpeg,.png,.webp"
                           class="block w-full text-sm text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-950 file:text-indigo-300 hover:file:bg-indigo-900 border-0 bg-slate-950/80 rounded-2xl cursor-pointer shadow-inner">
                    <p class="text-xs text-slate-500 mt-1.5 font-light">Format: JPG, PNG, WEBP. Ukuran maksimal 3 MB.</p>
                </div>

                <!-- Demo URL & Project URL -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2.5">
                            Tautan Demo / Website <span class="text-slate-500 font-normal">(Opsional)</span>
                        </label>
                        <input type="url" name="demo_url" value="<?= e($old['demo_url']) ?>"
                               placeholder="https://contoh-demo.com"
                               class="w-full px-4 py-3.5 bg-slate-950/80 border-0 rounded-2xl text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2.5">
                            Tautan Kode / Repository <span class="text-slate-500 font-normal">(Opsional)</span>
                        </label>
                        <input type="url" name="project_url" value="<?= e($old['project_url']) ?>"
                               placeholder="https://github.com/username/repo"
                               class="w-full px-4 py-3.5 bg-slate-950/80 border-0 rounded-2xl text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full py-4 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm shadow-xl shadow-indigo-600/30 transition-all hover:-translate-y-0.5">
                        Kirim Karya untuk Ditinjau →
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </section>

</div>

<?php require 'includes/footer.php'; ?>