<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal — Training Center PT Parkland World Indonesia</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --bg-base: #0a0f1d;
            --card-bg: rgba(15, 23, 42, 0.85);
            --card-border: rgba(255, 255, 255, 0.08);
            --pwi-orange: #f97316;
            --pwi-orange-hover: #ea580c;
            --pwi-orange-glow: rgba(249, 115, 22, 0.35);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-subtle: #64748b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-base);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            color: var(--text-main);
            position: relative;
            overflow-x: hidden;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        /* Ambient Glow Spheres */
        .ambient-glow-1 {
            position: fixed;
            top: -10%;
            left: -10%;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, rgba(15, 23, 42, 0) 70%);
            border-radius: 50%;
            filter: blur(60px);
            z-index: 0;
            pointer-events: none;
            animation: pulseGlow 8s ease-in-out infinite alternate;
        }

        .ambient-glow-2 {
            position: fixed;
            bottom: -15%;
            right: -10%;
            width: 650px;
            height: 650px;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.12) 0%, rgba(15, 23, 42, 0) 70%);
            border-radius: 50%;
            filter: blur(70px);
            z-index: 0;
            pointer-events: none;
            animation: pulseGlow 10s ease-in-out infinite alternate-reverse;
        }

        @keyframes pulseGlow {
            0% {
                transform: scale(1) translate(0, 0);
                opacity: 0.8;
            }

            100% {
                transform: scale(1.12) translate(25px, 15px);
                opacity: 1;
            }
        }

        /* Subtle grid background pattern */
        .grid-pattern {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 0;
            pointer-events: none;
        }

        /* Main Glass Login Card */
        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1060px;
            background: var(--card-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.05),
                0 25px 60px -15px rgba(0, 0, 0, 0.7),
                0 0 50px -10px rgba(249, 115, 22, 0.08);
            overflow: hidden;
        }

        /* Brand Left Side */
        .brand-section {
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.5) 0%, rgba(15, 23, 42, 0.75) 100%);
            border-right: 1px solid var(--card-border);
            padding: 40px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        @media (max-width: 991.98px) {
            .brand-section {
                border-right: none;
                border-bottom: 1px solid var(--card-border);
                padding: 32px 24px;
            }
        }

        .logo-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 8px 14px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
        }

        .system-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.28);
            color: #34d399;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .system-pill .dot {
            width: 7px;
            height: 7px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 8px #10b981;
            animation: pulseDot 2s infinite;
        }

        @keyframes pulseDot {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.4;
                transform: scale(0.85);
            }
        }

        .quick-role-card {
            background: rgba(30, 41, 59, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 14px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: var(--text-main);
            transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .quick-role-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 3.5px;
            background: transparent;
            transition: background 0.2s;
        }

        .quick-role-card:hover {
            background: rgba(51, 65, 85, 0.75);
            border-color: rgba(255, 255, 255, 0.18);
            transform: translateX(4px);
            color: #ffffff;
        }

        .quick-role-card.role-superadmin:hover::before {
            background: #ef4444;
        }

        .quick-role-card.role-trainer:hover::before {
            background: #3b82f6;
        }

        .quick-role-card.role-guest:hover::before {
            background: #94a3b8;
        }

        .quick-role-card.role-kiosk:hover::before {
            background: #f59e0b;
        }

        .role-icon-box {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        /* Right Form Side */
        .form-section {
            padding: 40px 38px;
        }

        @media (max-width: 991.98px) {
            .form-section {
                padding: 32px 24px;
            }
        }

        .custom-input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .custom-input-group label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: #94a3b8;
            margin-bottom: 8px;
            letter-spacing: 0.05em;
        }

        .input-box-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-box-wrapper .input-icon {
            position: absolute;
            left: 15px;
            color: #64748b;
            font-size: 1.1rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .custom-form-input {
            width: 100%;
            height: 48px;
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 0 44px;
            color: #ffffff;
            font-size: 0.92rem;
            font-family: inherit;
            transition: all 0.2s;
        }

        .custom-form-input:focus {
            outline: none;
            background: rgba(15, 23, 42, 0.95);
            border-color: var(--pwi-orange);
            box-shadow: 0 0 0 3px var(--pwi-orange-glow);
        }

        .input-box-wrapper:focus-within .input-icon {
            color: var(--pwi-orange);
        }

        .password-toggle-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #64748b;
            font-size: 1.1rem;
            cursor: pointer;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .password-toggle-btn:hover {
            color: #f8fafc;
        }

        /* Submit Button */
        .btn-submit-login {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.02em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 8px 20px -4px rgba(249, 115, 22, 0.45);
            transition: all 0.22s;
        }

        .btn-submit-login:hover {
            background: linear-gradient(135deg, #fb923c 0%, #ea580c 100%);
            box-shadow: 0 10px 24px -2px rgba(249, 115, 22, 0.6);
            transform: translateY(-1px);
        }

        .btn-submit-login:active {
            transform: translateY(1px);
        }

        /* Quick fill chips */
        .cred-chip {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 0.75rem;
            color: #cbd5e1;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .cred-chip:hover {
            background: rgba(249, 115, 22, 0.15);
            border-color: rgba(249, 115, 22, 0.35);
            color: #fdba74;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>

<body>

    <!-- Ambient Light Effects -->
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>
    <div class="grid-pattern"></div>

    <!-- Login Main Card -->
    <div class="login-wrapper">
        <div class="row g-0">
            <!-- Left Side: Portal Identity & Role Shortcuts -->
            <div class="col-lg-6 brand-section">
                <div>
                    <!-- Brand Header -->
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="logo-box">
                            <img src="<?= base_url('asset/parkland-logo.png') ?>" alt="Logo Parkland"
                                style="height: 34px; width: auto; object-fit: contain;">
                            <div>
                                <div class="fw-bold text-white small" style="line-height: 1.2;">PT Parkland World
                                    Indonesia</div>
                                <div class="text-muted" style="font-size: 0.72rem;">Training Center &amp; Lean
                                    Operations</div>
                            </div>
                        </div>
                        <div class="system-pill">
                            <span class="dot"></span> Online
                        </div>
                    </div>

                    <!-- Intro Greeting -->
                    <div class="mb-4">
                        <h3 class="fw-bold text-white mb-2" style="letter-spacing: -0.02em; font-size: 1.45rem;">
                            Selamat Datang di <span
                                style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Training
                                Portal</span>
                        </h3>
                        <p class="text-muted small mb-0" style="line-height: 1.6; font-size: 0.83rem;">
                            Sistem terintegrasi pemantauan skill matrix, evaluasi harian, mutasi line, modul induksi,
                            dan absensi ID Card operator.
                        </p>
                    </div>

                    <!-- Fast Login Role Cards -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2.5">
                            <span class="small fw-bold text-white"
                                style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em;">
                                ⚡ Akses Cepat Berdasarkan Peran
                            </span>
                            <span class="text-muted" style="font-size: 0.72rem;">1-Klik Masuk</span>
                        </div>

                        <div class="d-flex flex-column gap-2.5">
                            <!-- Superadmin -->
                            <a href="<?= base_url('login/quick/superadmin') ?>" class="quick-role-card role-superadmin">
                                <div class="role-icon-box"
                                    style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25);">
                                    <i class="bi bi-shield-fill-check"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between mb-0.5">
                                        <span class="fw-bold text-white small">Superadmin</span>
                                        <span class="badge"
                                            style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; font-size: 0.65rem; font-weight: 600;">Full
                                            Control</span>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem; line-height: 1.3;">Kelola Konten,
                                        User Management, Data &amp; Reset</div>
                                </div>
                                <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                            </a>

                            <!-- Trainer -->
                            <a href="<?= base_url('login/quick/trainer') ?>" class="quick-role-card role-trainer">
                                <div class="role-icon-box"
                                    style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.25);">
                                    <i class="bi bi-person-badge-fill"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between mb-0.5">
                                        <span class="fw-bold text-white small">Trainer Parkland</span>
                                        <span class="badge"
                                            style="background: rgba(59, 130, 246, 0.2); color: #93c5fd; font-size: 0.65rem; font-weight: 600;">Evaluator</span>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem; line-height: 1.3;">Input Nilai
                                        Harian (Motoric, Hard Skill, OJT) &amp; Peminjaman</div>
                                </div>
                                <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                            </a>

                            <!-- Guest -->
                            <a href="<?= base_url('login/quick/guest') ?>" class="quick-role-card role-guest">
                                <div class="role-icon-box"
                                    style="background: rgba(148, 163, 184, 0.15); color: #cbd5e1; border: 1px solid rgba(148, 163, 184, 0.25);">
                                    <i class="bi bi-eye-fill"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between mb-0.5">
                                        <span class="fw-bold text-white small">Guest / Tamu</span>
                                        <span class="badge"
                                            style="background: rgba(148, 163, 184, 0.2); color: #cbd5e1; font-size: 0.65rem; font-weight: 600;">Read-Only</span>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem; line-height: 1.3;">Monitoring
                                        Dashboard &amp; Tracking Skill Matrix Peserta</div>
                                </div>
                                <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                            </a>

                            <!-- Kiosk Absensi Standalone -->
                            <a href="<?= base_url('attendance/kiosk') ?>" class="quick-role-card role-kiosk"
                                style="border-color: rgba(245, 158, 11, 0.35); background: rgba(245, 158, 11, 0.08);">
                                <div class="role-icon-box"
                                    style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.35);">
                                    <i class="bi bi-display-fill"></i>
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between mb-0.5">
                                        <span class="fw-bold text-warning small">Kiosk Absensi ID Card (Tap Masuk /
                                            Keluar)</span>
                                        <i class="bi bi-box-arrow-up-right text-warning ms-1"
                                            style="font-size: 0.75rem;"></i>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.72rem; line-height: 1.3;">Layar Penuh
                                        Tap Kartu RFID Tanpa Perlu Login Akun</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Footer Badge -->
                <div class="pt-3 mt-3 border-top border-secondary border-opacity-10 d-flex align-items-center justify-content-between text-muted"
                    style="font-size: 0.75rem;">
                    <span><i class="bi bi-shield-lock me-1 text-warning"></i> Enkripsi Keamanan Aktif</span>
                    <span class="mono">v2.4 PWI-CI4</span>
                </div>
            </div>

            <!-- Right Side: Manual Login Form -->
            <div class="col-lg-6 form-section d-flex flex-column justify-content-center">
                <div class="mb-4">
                    <span class="badge mb-2 px-2.5 py-1 text-white"
                        style="background: rgba(249, 115, 22, 0.18); border: 1px solid rgba(249, 115, 22, 0.3); font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">
                        Autentikasi Akun
                    </span>
                    <h4 class="fw-bold text-white mb-1">Masuk dengan Kredensial</h4>
                    <p class="text-muted small mb-0">Masukkan username dan password akun terdaftar Anda.</p>
                </div>

                <!-- Flash Error Message -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2 py-2.5 px-3 mb-4 rounded-3 border-0"
                        style="background: rgba(239, 68, 68, 0.18); border: 1px solid rgba(239, 68, 68, 0.3) !important; color: #fca5a5; font-size: 0.83rem;">
                        <i class="bi bi-exclamation-octagon-fill fs-5 text-danger flex-shrink-0"></i>
                        <div><?= session()->getFlashdata('error') ?></div>
                    </div>
                <?php endif; ?>

                <!-- Flash Success Message -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success d-flex align-items-center gap-2 py-2.5 px-3 mb-4 rounded-3 border-0"
                        style="background: rgba(16, 185, 129, 0.18); border: 1px solid rgba(16, 185, 129, 0.3) !important; color: #6ee7b7; font-size: 0.83rem;">
                        <i class="bi bi-check-circle-fill fs-5 text-success flex-shrink-0"></i>
                        <div><?= session()->getFlashdata('success') ?></div>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('login/attempt') ?>" method="POST">
                    <?= csrf_field() ?>

                    <!-- Username Input -->
                    <div class="custom-input-group">
                        <label for="usernameInput">USERNAME</label>
                        <div class="input-box-wrapper">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" id="usernameInput" name="username" class="custom-form-input mono"
                                placeholder="admin / trainer / guest" value="<?= old('username') ?>"
                                autocomplete="username" required>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="custom-input-group mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="passwordInput">PASSWORD</label>
                            <span class="text-muted" style="font-size: 0.72rem;">Min. 4 karakter</span>
                        </div>
                        <div class="input-box-wrapper">
                            <i class="bi bi-key input-icon"></i>
                            <input type="password" id="passwordInput" name="password" class="custom-form-input mono"
                                placeholder="••••••••" autocomplete="current-password" required>
                            <button type="button" class="password-toggle-btn" id="btnTogglePassword"
                                title="Lihat/Sembunyikan Password">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit-login mb-4">
                        <i class="bi bi-box-arrow-in-right fs-5"></i>
                        <span>Masuk ke Dashboard</span>
                    </button>

                    <!-- 1-Click Fill Helper Chips -->

            </div>
        </div>
        </form>
    </div>
    </div>
    </div>

    <script>
        // Toggle Password Visibility
        const btnToggle = document.getElementById('btnTogglePassword');
        const pwdInput = document.getElementById('passwordInput');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (btnToggle && pwdInput) {
            btnToggle.addEventListener('click', function () {
                if (pwdInput.type === 'password') {
                    pwdInput.type = 'text';
                    toggleIcon.classList.remove('bi-eye');
                    toggleIcon.classList.add('bi-eye-slash');
                } else {
                    pwdInput.type = 'password';
                    toggleIcon.classList.remove('bi-eye-slash');
                    toggleIcon.classList.add('bi-eye');
                }
            });
        }

        // 1-Click Auto Fill
        function fillCredentials(user, pass) {
            const userInput = document.getElementById('usernameInput');
            const passInput = document.getElementById('passwordInput');
            if (userInput && passInput) {
                userInput.value = user;
                passInput.value = pass;
                userInput.focus();
            }
        }
    </script>

</body>

</html>