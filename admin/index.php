<?php
require_once dirname(__DIR__) . '/config.php';
require_admin();

$profileMessage = '';
$profileError = '';
$projectMessage = '';
$projectError = '';

// Handling Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $action = $_POST['action'] ?? '';

    // 1. Fitur Edit Profil Admin
    if ($action === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';

        if ($name === '' || $email === '') {
            $profileError = 'Nama dan Email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $profileError = 'Format email tidak valid.';
        } else {
            $stmt = db()->prepare("SELECT password_hash FROM admins WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch();

            if (!$admin || !password_verify($currentPassword, $admin['password_hash'])) {
                $profileError = 'Password saat ini salah.';
            } else {
                if ($newPassword !== '') {
                    if (mb_strlen($newPassword) < 6) {
                        $profileError = 'Password baru minimal 6 karakter.';
                    } else {
                        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                        $stmt = db()->prepare("UPDATE admins SET name = ?, email = ?, password_hash = ? WHERE id = ?");
                        $stmt->execute([$name, $email, $newHash, $_SESSION['admin_id']]);
                        $profileMessage = 'Profil dan password berhasil diperbarui!';
                    }
                } else {
                    $stmt = db()->prepare("UPDATE admins SET name = ?, email = ? WHERE id = ?");
                    $stmt->execute([$name, $email, $_SESSION['admin_id']]);
                    $profileMessage = 'Profil berhasil diperbarui!';
                }

                if (!$profileError) {
                    $_SESSION['admin_name'] = $name;
                }
            }
        }
    } 
    // 2. Fitur Edit Detail Karya oleh Admin
    elseif ($action === 'edit_project') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $title = trim($_POST['title'] ?? '');
        $studentName = trim($_POST['student_name'] ?? '');
        $studentClass = trim($_POST['student_class'] ?? '');
        $category = $_POST['category'] ?? 'Lainnya';
        $description = trim($_POST['description'] ?? '');
        $demoUrl = trim($_POST['demo_url'] ?? '');
        $projectUrl = trim($_POST['project_url'] ?? '');
        $status = $_POST['status'] ?? 'pending';

        if (!$id) {
            $projectError = 'ID Karya tidak valid.';
        } elseif ($title === '' || $studentName === '' || $studentClass === '') {
            $projectError = 'Judul, Nama Siswa, dan Kelas wajib diisi.';
        } else {
            $stmt = db()->prepare(
                "UPDATE projects 
                 SET title = ?, student_name = ?, student_class = ?, category = ?, description = ?, demo_url = ?, project_url = ?, status = ? 
                 WHERE id = ?"
            );
            $stmt->execute([
                $title, $studentName, $studentClass, $category, $description, 
                $demoUrl ?: null, $projectUrl ?: null, $status, $id
            ]);
            $projectMessage = 'Karya berhasil diperbarui!';
        }
    }
    // 3. Aksi Cepat Moderasi Karya (Setujui, Tolak, Hapus)
    else {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id && in_array($action, ['approved', 'rejected', 'delete'], true)) {
            if ($action === 'delete') {
                $stmt = db()->prepare("SELECT cover_image FROM projects WHERE id=?");
                $stmt->execute([$id]);
                $project = $stmt->fetch();

                db()->prepare("DELETE FROM projects WHERE id=?")->execute([$id]);

                if ($project && $project['cover_image']) {
                    $file = dirname(__DIR__) . '/' . $project['cover_image'];
                    if (is_file($file) && str_starts_with($project['cover_image'], 'uploads/')) {
                        unlink($file);
                    }
                }
            } else {
                $stmt = db()->prepare("UPDATE projects SET status=? WHERE id=?");
                $stmt->execute([$action, $id]);
            }
        }

        header('Location: ' . url('admin/index.php'));
        exit;
    }
}

// Hitung Statistik Karya
$counts = [];
foreach (['pending', 'approved', 'rejected'] as $status) {
    $stmt = db()->prepare("SELECT COUNT(*) FROM projects WHERE status=?");
    $stmt->execute([$status]);
    $counts[$status] = (int)$stmt->fetchColumn();
}

// Ambil Data Admin untuk Form Profile
$stmt = db()->prepare("SELECT name, email FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$currentAdmin = $stmt->fetch();

// Daftar Kategori
$categories = ['Website', 'Game', 'Desain', 'Aplikasi', 'Video', 'Lainnya'];

// Fetch Karya
$projects = db()->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 100")->fetchAll();

$pageTitle = 'Dashboard Admin | RuangKarya';
require dirname(__DIR__) . '/includes/header.php';
?>

<!-- Wrapper Utama Background Gelap & Estetis -->
<div class="min-h-screen bg-slate-950 text-slate-100 relative overflow-hidden pb-16">

    <!-- Ambient Glow Effect -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[350px] bg-indigo-600/15 blur-[160px] rounded-full pointer-events-none"></div>
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#475569_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 relative z-10 space-y-8">
        
        <!-- Header Banner & Karakter Maskot Guru/Admin -->
        <div class="bg-slate-900/60 backdrop-blur-md p-6 sm:p-8 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-indigo-950/80 border-0 flex items-center justify-center flex-shrink-0 shadow-lg relative">
                    <svg class="w-14 h-14 text-indigo-400 drop-shadow-[0_8px_16px_rgba(99,102,241,0.4)]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="9" r="3.5" fill="#312E81" stroke="#A5B4FC" stroke-width="1.5"/>
                        <path d="M7 9C8 9 8.5 8 8.5 8" stroke="#818CF8" stroke-width="1.5"/>
                        <path d="M17 9C16 9 15.5 8 15.5 8" stroke="#818CF8" stroke-width="1.5"/>
                        <path d="M6 21C6 17.6863 8.68629 15 12 15C15.3137 15 18 17.6863 18 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M12 15V19" stroke="#38BDF8" stroke-width="1.5"/>
                    </svg>
                    <span class="absolute -bottom-1 bg-indigo-600 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-md">
                        ADMIN
                    </span>
                </div>

                <div>
                    <span class="inline-flex items-center gap-2 py-0.5 px-3 rounded-full bg-indigo-950/80 text-indigo-300 text-xs font-medium tracking-wider uppercase mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                        PANEL MODERASI
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-100">
                        Selamat Datang, <?= e($_SESSION['admin_name'] ?? 'Admin') ?> 👋
                    </h1>
                    <p class="text-slate-400 text-xs sm:text-sm font-light mt-0.5">
                        Kelola, edit, dan tinjau pengajuan portofolio siswa sekolah secara real-time.
                    </p>
                </div>
            </div>

            <div>
                <a href="<?= e(url('admin/logout.php')) ?>" class="inline-flex items-center px-5 py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium transition-all shadow-md">
                    Keluar / Logout ➔
                </a>
            </div>
        </div>

        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="bg-slate-900/60 backdrop-blur-md p-6 rounded-3xl shadow-xl flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-amber-400 uppercase tracking-wider block mb-1">Menunggu Persetujuan</span>
                    <strong class="text-3xl font-extrabold text-slate-100"><?= $counts['pending'] ?></strong>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-950/50 text-amber-400 flex items-center justify-center text-xl">⏳</div>
            </div>

            <div class="bg-slate-900/60 backdrop-blur-md p-6 rounded-3xl shadow-xl flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-emerald-400 uppercase tracking-wider block mb-1">Sudah Dipublikasikan</span>
                    <strong class="text-3xl font-extrabold text-slate-100"><?= $counts['approved'] ?></strong>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-950/50 text-emerald-400 flex items-center justify-center text-xl">✅</div>
            </div>

            <div class="bg-slate-900/60 backdrop-blur-md p-6 rounded-3xl shadow-xl flex items-center justify-between">
                <div>
                    <span class="text-xs font-semibold text-rose-400 uppercase tracking-wider block mb-1">Ditolak</span>
                    <strong class="text-3xl font-extrabold text-slate-100"><?= $counts['rejected'] ?></strong>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-950/50 text-rose-400 flex items-center justify-center text-xl">❌</div>
            </div>
        </div>

        <!-- Section Edit Profil & Tabel Pengajuan -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Form Edit Profil Admin -->
            <div class="lg:col-span-1">
                <div class="bg-slate-900/70 backdrop-blur-md p-6 sm:p-8 rounded-3xl shadow-2xl space-y-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                            <span>⚙️</span> Edit Profil Admin
                        </h2>
                        <p class="text-xs text-slate-400 font-light mt-0.5">Perbarui informasi akun administrator Anda.</p>
                    </div>

                    <?php if ($profileMessage): ?>
                        <div class="p-3.5 bg-emerald-950/50 text-emerald-300 text-xs rounded-2xl">
                            <?= e($profileMessage) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($profileError): ?>
                        <div class="p-3.5 bg-rose-950/50 text-rose-300 text-xs rounded-2xl">
                            ⚠️ <?= e($profileError) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="update_profile">

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                            <input type="text" name="name" value="<?= e($currentAdmin['name'] ?? '') ?>" required
                                   class="w-full px-4 py-3 bg-slate-950/80 border-0 rounded-2xl text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Email</label>
                            <input type="email" name="email" value="<?= e($currentAdmin['email'] ?? '') ?>" required
                                   class="w-full px-4 py-3 bg-slate-950/80 border-0 rounded-2xl text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Password Baru <span class="text-slate-500 font-normal">(Opsional)</span></label>
                            <input type="password" name="new_password" placeholder="Kosongkan jika tidak diubah"
                                   class="w-full px-4 py-3 bg-slate-950/80 border-0 rounded-2xl text-xs text-slate-200 placeholder-slate-600 focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Password Saat Ini <span class="text-indigo-400">*</span></label>
                            <input type="password" name="current_password" required placeholder="Verifikasi password lama"
                                   class="w-full px-4 py-3 bg-slate-950/80 border-0 rounded-2xl text-xs text-slate-200 placeholder-slate-600 focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner">
                        </div>

                        <button type="submit" class="w-full py-3 px-5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-xs shadow-lg shadow-indigo-600/30 transition-all hover:-translate-y-0.5">
                            Simpan Perubahan Profil
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tabel Daftar Pengajuan Karya -->
            <div class="lg:col-span-2">
                <div class="bg-slate-900/70 backdrop-blur-md p-6 sm:p-8 rounded-3xl shadow-2xl space-y-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                            <span>📂</span> Daftar Pengajuan Karya
                        </h2>
                        <span class="text-xs text-slate-400 font-light">Total: <?= count($projects) ?> karya</span>
                    </div>

                    <?php if ($projectMessage): ?>
                        <div class="p-3.5 bg-emerald-950/50 text-emerald-300 text-xs rounded-2xl">
                            <?= e($projectMessage) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($projectError): ?>
                        <div class="p-3.5 bg-rose-950/50 text-rose-300 text-xs rounded-2xl">
                            ⚠️ <?= e($projectError) ?>
                        </div>
                    <?php endif; ?>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-300 border-separate border-spacing-y-2">
                            <thead>
                                <tr class="text-slate-400 uppercase font-semibold">
                                    <th class="pb-3 px-3">Karya / Siswa</th>
                                    <th class="pb-3 px-3">Kategori</th>
                                    <th class="pb-3 px-3">Status</th>
                                    <th class="pb-3 px-3 text-right">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($projects as $p): ?>
                                    <tr class="bg-slate-950/60 rounded-2xl transition-all hover:bg-slate-950">
                                        <td class="py-3.5 px-4 rounded-l-2xl">
                                            <strong class="text-slate-100 font-semibold text-sm block mb-0.5"><?= e($p['title']) ?></strong>
                                            <span class="text-slate-400"><?= e($p['student_name']) ?></span>
                                            <small class="text-slate-500 block"><?= e($p['student_class']) ?></small>
                                        </td>
                                        <td class="py-3.5 px-3">
                                            <span class="px-2.5 py-1 bg-indigo-950/80 text-indigo-300 rounded-lg text-[11px] font-medium">
                                                <?= e($p['category']) ?>
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3">
                                            <?php if ($p['status'] === 'approved'): ?>
                                                <span class="px-2.5 py-1 bg-emerald-950/80 text-emerald-400 rounded-lg text-[11px] font-medium">Disetujui</span>
                                            <?php elseif ($p['status'] === 'rejected'): ?>
                                                <span class="px-2.5 py-1 bg-rose-950/80 text-rose-400 rounded-lg text-[11px] font-medium">Ditolak</span>
                                            <?php else: ?>
                                                <span class="px-2.5 py-1 bg-amber-950/80 text-amber-400 rounded-lg text-[11px] font-medium">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3.5 px-4 rounded-r-2xl text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <!-- Tombol Edit Modal -->
                                                <button onclick="openEditModal(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, 'UTF-8') ?>)" 
                                                        class="px-2.5 py-1.5 bg-indigo-600/80 hover:bg-indigo-600 text-white rounded-xl font-medium transition-all" title="Edit Karya">
                                                    ✏️ Edit
                                                </button>

                                                <?php if ($p['status'] !== 'approved'): ?>
                                                    <form method="post" class="inline">
                                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                                        <button class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-medium transition-all" name="action" value="approved" title="Setujui">
                                                            ✓
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <?php if ($p['status'] !== 'rejected'): ?>
                                                    <form method="post" class="inline">
                                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                                        <button class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-medium transition-all" name="action" value="rejected" title="Tolak">
                                                            ✕
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <form method="post" class="inline" onsubmit="return confirm('Hapus karya ini?')">
                                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                                    <button class="px-2.5 py-1.5 bg-rose-600/80 hover:bg-rose-600 text-white rounded-xl font-medium transition-all" name="action" value="delete" title="Hapus">
                                                        🗑️
                                                    </button>
                                                </form>

                                                <?php if ($p['status'] === 'approved'): ?>
                                                    <a href="<?= e(url('project.php?id=' . (int)$p['id'])) ?>" target="_blank" class="px-2 py-1.5 bg-indigo-950 text-indigo-300 hover:text-white rounded-xl font-medium transition-all" title="Lihat">
                                                        ↗
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Modal Pop-up Edit Karya -->
<div id="editModal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 sm:p-8 space-y-5 shadow-2xl relative">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <h3 class="text-lg font-bold text-slate-100 flex items-center gap-2">
                <span>✏️</span> Edit Data Karya Siswa
            </h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-white text-xl font-bold">&times;</button>
        </div>

        <form method="post" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="edit_project">
            <input type="hidden" name="id" id="edit_id">

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Judul Karya *</label>
                <input type="text" name="title" id="edit_title" required
                       class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Nama Siswa *</label>
                    <input type="text" name="student_name" id="edit_student_name" required
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Kelas *</label>
                    <input type="text" name="student_class" id="edit_student_class" required
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Kategori *</label>
                    <select name="category" id="edit_category" required
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= e($cat) ?>"><?= e($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Status *</label>
                    <select name="status" id="edit_status" required
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all">
                        <option value="pending">Pending</option>
                        <option value="approved">Disetujui (Approved)</option>
                        <option value="rejected">Ditolak (Rejected)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Deskripsi Karya</label>
                <textarea name="description" id="edit_description" rows="4"
                          class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all leading-relaxed"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Tautan Demo</label>
                    <input type="url" name="demo_url" id="edit_demo_url" placeholder="https://..."
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Tautan Repository</label>
                    <input type="url" name="project_url" id="edit_project_url" placeholder="https://..."
                           class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-2xl text-xs text-slate-200 focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-2xl text-xs font-medium transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-2xl text-xs font-medium transition-all shadow-lg shadow-indigo-600/30">
                    Simpan Perubahan Karya
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script Modal Edit Karya -->
<script>
function openEditModal(project) {
    document.getElementById('edit_id').value = project.id;
    document.getElementById('edit_title').value = project.title || '';
    document.getElementById('edit_student_name').value = project.student_name || '';
    document.getElementById('edit_student_class').value = project.student_class || '';
    document.getElementById('edit_category').value = project.category || 'Lainnya';
    document.getElementById('edit_status').value = project.status || 'pending';
    document.getElementById('edit_description').value = project.description || '';
    document.getElementById('edit_demo_url').value = project.demo_url || '';
    document.getElementById('edit_project_url').value = project.project_url || '';

    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>