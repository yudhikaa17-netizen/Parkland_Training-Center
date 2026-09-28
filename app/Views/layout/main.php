<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Sistem Tracking Training Karyawan Baru') ?> — PT Parkland World Indonesia</title>
    <!-- Bootstrap 5 CSS (for modal/grid utility support) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter & IBM Plex Mono matching training-tracker (1).html -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Custom Dark Slate Theme CSS -->
    <link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
    <!-- Inline Theme Initializer to prevent flash -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('pwi_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>

<body>

    <!-- Header matching reference training-tracker (1).html -->
    <header class="pwi-header">
        <div class="header-inner">
            <!-- Brand -->
            <div class="brand">
                <img src="<?= base_url('asset/parkland-logo.png') ?>" alt="Logo Parkland" class="brand-logo">
                <div class="brand-divider"></div>
                <div>
                    <h1>Training Center — PT Parkland World Indonesia</h1>
                    <p>Sistem Tracking Training, Absensi &amp; Peminjaman Operator</p>
                </div>
                <div class="brand-stripes" aria-hidden="true">
                    <span></span><span></span><span></span>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <?php
            $uri = service('uri');
            $segment = $uri->getSegment(1) ?: 'dashboard';
            $role = session()->get('role') ?? 'guest';
            $isGuest = ($role === 'guest');
            ?>
            <div class="tabs">
                <a href="<?= base_url('dashboard') ?>"
                    class="tab-btn <?= ($segment === 'dashboard') ? 'active' : '' ?>">
                    Dashboard
                </a>
                <a href="<?= base_url('info-program') ?>"
                    class="tab-btn <?= ($segment === 'info-program') ? 'active' : '' ?>">
                    Info Program
                </a>
                <a href="<?= base_url('trainees') ?>" class="tab-btn <?= ($segment === 'trainees') ? 'active' : '' ?>">
                    Tracking Trainee
                </a>
                <?php if ($isGuest): ?>
                    <span class="tab-btn disabled" title="Guest tidak memiliki akses input">
                        Input Data Harian
                    </span>
                <?php else: ?>
                    <a href="<?= base_url('daily-input') ?>"
                        class="tab-btn <?= ($segment === 'daily-input') ? 'active' : '' ?>">
                        Input Data Harian
                    </a>
                <?php endif; ?>

                <a href="<?= base_url('attendance') ?>"
                    class="tab-btn <?= ($segment === 'attendance') ? 'active' : '' ?>">
                    Absensi
                </a>

                <?php if ($isGuest): ?>
                    <span class="tab-btn disabled" title="Guest tidak memiliki akses peminjaman">
                        Peminjaman Operator
                    </span>
                <?php else: ?>
                    <a href="<?= base_url('loans') ?>" class="tab-btn <?= ($segment === 'loans') ? 'active' : '' ?>">
                        Peminjaman Operator
                    </a>
                <?php endif; ?>

                <?php if ($isGuest): ?>
                    <span class="tab-btn disabled" title="Guest tidak memiliki akses materi">
                        Materi Training
                    </span>
                <?php else: ?>
                    <a href="<?= base_url('materials') ?>"
                        class="tab-btn <?= ($segment === 'materials') ? 'active' : '' ?>">
                        Materi Training
                    </a>
                <?php endif; ?>

                <?php if ($role === 'superadmin'): ?>
                    <a href="<?= base_url('content-manager') ?>"
                        class="tab-btn <?= ($segment === 'content-manager') ? 'active' : '' ?>" style="color:var(--amber);">
                        <i class="bi bi-sliders"></i> Kelola Konten
                    </a>
                <?php endif; ?>
            </div>

            <!-- Role Selector & Action Switcher -->
            <div class="role-switch d-flex align-items-center flex-wrap gap-2">
                <!-- Kiosk Absensi Quick Access -->
                <a href="<?= base_url('attendance/kiosk') ?>" class="btn btn-warning btn-sm fw-semibold text-dark"
                    style="font-size:0.78rem;padding:4px 10px;"
                    title="Buka Halaman Khusus Standalone Kiosk Absensi ID Card">
                    <i class="bi bi-display me-1"></i> Kiosk Absensi
                </a>

                <!-- Theme Toggle Button (Dark / Light) -->
                <button type="button" id="themeToggleBtn" class="theme-toggle-btn"
                    title="Ganti Mode Terang / Gelap" aria-label="Ganti Tema">
                    <span class="theme-icon-dark" title="Ganti ke Mode Terang">🌙</span>
                    <span class="theme-icon-light" title="Ganti ke Mode Gelap">☀️</span>
                </button>

                <span class="small text-muted">Peran:</span>
                <form action="<?= base_url('auth/switch-role') ?>" method="POST" class="d-inline m-0">
                    <?= csrf_field() ?>
                    <select name="role" id="roleSwitch" onchange="this.form.submit()" style="font-size:0.78rem;padding:4px 8px;">
                        <option value="superadmin" <?= ($role === 'superadmin') ? 'selected' : '' ?>>Admin Training Center
                        </option>
                        <option value="trainer" <?= ($role === 'trainer') ? 'selected' : '' ?>>Trainer Lean / IE</option>
                        <option value="guest" <?= ($role === 'guest') ? 'selected' : '' ?>>Guest (Tamu / Viewer)</option>
                    </select>
                </form>

                <?php if ($role === 'superadmin'): ?>
                    <button type="button" class="btn btn-ghost del-btn" style="font-size:0.76rem;padding:4px 8px;"
                        data-bs-toggle="modal" data-bs-target="#modalResetData">
                        Reset Data
                    </button>
                <?php endif; ?>

                <!-- Sign Out Button -->
                <a href="<?= base_url('logout') ?>" class="btn btn-ghost text-danger d-inline-flex align-items-center gap-1"
                    style="font-size:0.78rem;padding:5px 12px;border:1px solid rgba(239,68,68,0.35);border-radius:6px;font-weight:600;"
                    title="Keluar dari Sistem">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sign Out</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Flash Notifications -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4"
                style="background:var(--green-dim);border:1px solid rgba(52,211,153,0.3);color:var(--green);border-radius:8px;"
                role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div class="text-white"><?= session()->getFlashdata('success') ?></div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"
                    aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4"
                style="background:var(--red-dim);border:1px solid rgba(248,113,113,0.3);color:var(--red);border-radius:8px;"
                role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div class="text-white"><?= session()->getFlashdata('error') ?></div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"
                    aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- View Content -->
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer>
        <div>PWJ IT — PT Parkland World Indonesia &copy; <?= date('Y') ?> | Sistem Tracking Training Karyawan Baru</div>
    </footer>

    <!-- Reset Confirmation Modal -->
    <?php if ($role === 'superadmin'): ?>
        <div class="modal fade" id="modalResetData" tabindex="-1" aria-labelledby="modalResetDataLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal-dark">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="modalResetDataLabel">
                            <i class="bi bi-exclamation-octagon-fill me-2"></i> Konfirmasi Reset Data
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="mb-2 fw-semibold text-white">Apakah Anda yakin ingin menghapus dan mengembalikan semua
                            data ke status awal?</p>
                        <p class="small mb-0" style="color:var(--muted);">Tindakan ini akan mengosongkan semua riwayat dan
                            mengisi ulang database dengan data default Training Center (5 Trainee awal, jadwal absensi hari
                            ini, materi, dan peminjaman).</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                        <a href="<?= base_url('data/reset-all') ?>" class="btn btn-primary"
                            style="background:var(--red);color:#fff;">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Ya, Reset Semua Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Toggle Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('themeToggleBtn');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
                    const newTheme = (currentTheme === 'dark') ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('pwi_theme', newTheme);
                });
            }
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>