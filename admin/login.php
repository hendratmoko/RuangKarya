<?php
require_once dirname(__DIR__) . '/config.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . url('admin/index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare(
        "SELECT id, name, email, password_hash
         FROM admins WHERE email = ? LIMIT 1"
    );
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin &&
        password_verify($password, $admin['password_hash'])) {

        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$admin['id'];
        $_SESSION['admin_name'] = $admin['name'];

        header('Location: ' . url('admin/index.php'));
        exit;
    }

    $error = 'Email atau password tidak sesuai.';
}

$pageTitle = 'Login Admin | RuangKarya';
require dirname(__DIR__) . '/includes/header.php';
?>

<!-- Wrapper Utama Seluruh Layar (Full Dark Background) -->
<div class="min-h-[calc(100vh-80px)] bg-slate-950 text-slate-100 flex items-center justify-center p-6 relative overflow-hidden">

    <!-- Ambient Glow Background Effects -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-indigo-600/15 blur-[160px] rounded-full pointer-events-none"></div>
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#475569_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        
        <!-- Maskot / Karakter Guru & Admin Sekolah -->
        <div class="text-center mb-8">
            <div class="relative inline-block">
                <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-full bg-gradient-to-tr from-indigo-600/30 via-purple-500/20 to-cyan-400/20 p-1 flex items-center justify-center shadow-2xl mx-auto">
                    <div class="w-full h-full rounded-full bg-slate-950/80 backdrop-blur-md flex items-center justify-center overflow-hidden relative">
                        <!-- Maskot SVG Guru / Administrator Sekolah -->
                        <svg class="w-20 h-20 text-indigo-400 drop-shadow-[0_8px_16px_rgba(99,102,241,0.4)]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Kacamata / Aksesoris Guru -->
                            <path d="M7 11C8.10457 11 9 10.1046 9 9C9 7.89543 8.10457 7 7 7C5.89543 7 5 7.89543 5 9C5 10.1046 5.89543 11 7 11Z" stroke="#818CF8" stroke-width="1.5"/>
                            <path d="M17 11C18.1046 11 19 10.1046 19 9C19 7.89543 18.1046 7 17 7C15.8954 7 15 7.89543 15 9C15 10.1046 15.8954 11 17 11Z" stroke="#818CF8" stroke-width="1.5"/>
                            <path d="M9 9H15" stroke="#818CF8" stroke-width="1.5"/>
                            <!-- Kepala Guru -->
                            <circle cx="12" cy="11" r="5" fill="#312E81" stroke="#A5B4FC" stroke-width="1.5"/>
                            <!-- Kemeja & Dasi/Toga Admin -->
                            <path d="M5 21C5 17.6863 7.68629 15 11 15H13C16.3137 15 19 17.6863 19 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M12 15V19L11 20H13L12 19" fill="#38BDF8" stroke="#38BDF8" stroke-width="1"/>
                            <!-- Bintang Keamanan -->
                            <path d="M19 2L19.4 3.2L20.6 3.6L19.4 4L19 5.2L18.6 4L17.4 3.6L18.6 3.2L19 2Z" fill="#38BDF8"/>
                        </svg>
                    </div>
                </div>
                <!-- Badge Admin -->
                <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-[10px] font-bold px-3 py-0.5 rounded-full shadow-lg whitespace-nowrap tracking-wider">
                    👨‍🏫 PORTAL ADMIN
                </span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-100 tracking-tight mt-6 mb-1">
                Login Administrator
            </h1>
            <p class="text-slate-400 text-sm font-light">
                Masuk untuk mengelola dan memoderasi karya siswa.
            </p>
        </div>

        <!-- Form Card (Tanpa Garis Pembatas) -->
        <div class="bg-slate-900/70 backdrop-blur-md rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
            
            <?php if ($error): ?>
                <div class="p-4 bg-red-950/40 backdrop-blur-md rounded-2xl text-red-300 text-sm flex items-center gap-2.5">
                    <span class="text-red-400 text-base">⚠️</span>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <!-- Email Input -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        Email Administrator
                    </label>
                    <input type="email" name="email" autocomplete="username" required
                           placeholder="admin@sekolah.sch.id"
                           class="w-full px-4 py-3.5 bg-slate-950/80 border-0 rounded-2xl text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner">
                </div>

                <!-- Password Input -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        Password
                    </label>
                    <input type="password" name="password" autocomplete="current-password" required
                           placeholder="••••••••"
                           class="w-full px-4 py-3.5 bg-slate-950/80 border-0 rounded-2xl text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-inner">
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-3.5 px-6 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm shadow-xl shadow-indigo-600/30 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        Masuk ke Dashboard <span>→</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>