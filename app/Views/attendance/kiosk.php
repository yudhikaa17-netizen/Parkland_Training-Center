<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kiosk Absensi RFID — PT Parkland World Indonesia') ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Theme CSS -->
    <link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
    <style>
        :root {
            --kiosk-bg: #0b1329;
            --kiosk-card: #151f38;
            --kiosk-card-border: #233154;
        }
        body {
            background-color: var(--kiosk-bg);
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .kiosk-header {
            background: rgba(15, 23, 42, 0.95);
            border-bottom: 2px solid #ea580c;
            padding: 16px 32px;
            backdrop-filter: blur(10px);
        }
        .kiosk-clock {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 1.8rem;
            font-weight: 700;
            color: #fbbf24;
            letter-spacing: 1px;
        }
        .kiosk-tap-box {
            background: var(--kiosk-card);
            border: 2px dashed #ea580c;
            border-radius: 16px;
            padding: 28px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(234, 88, 12, 0.15);
            transition: all 0.3s ease;
        }
        .kiosk-tap-box:focus-within {
            border-color: #34d399;
            box-shadow: 0 0 25px rgba(52, 211, 153, 0.3);
            background: #192745;
        }
        .tap-input {
            width: 100%;
            font-size: 1.4rem;
            padding: 18px 24px;
            border: 2px solid var(--kiosk-card-border);
            border-radius: 12px;
            font-family: 'IBM Plex Mono', monospace;
            text-align: center;
            background: #0d1527;
            color: #38bdf8;
            letter-spacing: 2px;
            outline: none;
            transition: border-color 0.2s;
        }
        .tap-input:focus {
            border-color: #fbbf24;
            box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.2);
        }
        .status-display {
            min-height: 140px;
            border-radius: 16px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            transition: all 0.3s ease;
            background: #111a2e;
            border: 1px solid var(--kiosk-card-border);
        }
        .status-idle {
            color: #94a3b8;
        }
        .status-success-in {
            background: rgba(16, 185, 129, 0.15);
            border: 2px solid #10b981;
            color: #34d399;
            animation: pulseSuccess 0.5s ease;
        }
        .status-success-out {
            background: rgba(59, 130, 246, 0.15);
            border: 2px solid #3b82f6;
            color: #60a5fa;
            animation: pulseSuccess 0.5s ease;
        }
        .status-error {
            background: rgba(239, 68, 68, 0.15);
            border: 2px solid #ef4444;
            color: #f87171;
            animation: shake 0.4s ease;
        }
        .status-info {
            background: rgba(245, 158, 11, 0.15);
            border: 2px solid #f59e0b;
            color: #fbbf24;
        }
        @keyframes pulseSuccess {
            0% { transform: scale(0.97); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-8px); }
            40%, 80% { transform: translateX(8px); }
        }
        .stat-card {
            background: var(--kiosk-card);
            border: 1px solid var(--kiosk-card-border);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
        }
        .stat-value {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 1.8rem;
            font-weight: 700;
        }
    </style>
</head>
<body>

    <!-- Kiosk Header -->
    <header class="kiosk-header d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <img src="<?= base_url('asset/parkland-logo.png') ?>" alt="Logo Parkland" style="height: 48px; width: auto;">
            <div>
                <h4 class="fw-bold mb-0 text-white">STANDALONE ABSENSI KIOSK RFID</h4>
                <small class="text-warning fw-semibold">Training Center — PT Parkland World Indonesia</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-4">
            <div class="text-end">
                <div class="kiosk-clock" id="kioskClock">00:00:00 WIB</div>
                <div class="small text-muted" id="kioskDate"><?= date('l, d F Y') ?></div>
            </div>
            <a href="<?= base_url('attendance') ?>" class="btn btn-outline-light btn-sm px-3 rounded-pill">
                <i class="bi bi-box-arrow-left me-1"></i> Kembali ke Portal
            </a>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container-fluid p-4 flex-grow-1">
        <div class="row g-4">
            
            <!-- Left Side: RFID Scanner & Tap Feedback -->
            <div class="col-lg-5 col-xl-4 d-flex flex-column gap-4">
                
                <!-- RFID Input Box -->
                <div class="kiosk-tap-box">
                    <div class="mb-3">
                        <i class="bi bi-qr-code-scan display-4 text-warning"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-2">Tempelkan ID Card / NIK</h5>
                    <p class="small text-muted mb-3">
                        Posisikan ID Card pada reader USB. Sistem otomatis memproses Masuk/Pulang dan sinkronisasi ke UNIS API.
                    </p>
                    
                    <input type="text" id="kioskRfidInput" class="tap-input" placeholder="READY — TAP ID CARD DI SINI..." autocomplete="off" autofocus>
                    
                    <div class="d-flex justify-content-center align-items-center gap-2 mt-3 text-muted small">
                        <span class="spinner-grow spinner-grow-sm text-success" role="status" aria-hidden="true"></span>
                        <span>Scanner Aktif &amp; Siap Menerima Input</span>
                    </div>
                </div>

                <!-- Status Feedback Box -->
                <div class="status-display status-idle" id="kioskStatusBox">
                    <div id="kioskStatusContent">
                        <i class="bi bi-card-checklist fs-1 d-block mb-2 text-muted"></i>
                        <div class="fw-semibold fs-5">Menunggu Tap ID Card Peserta...</div>
                        <div class="small text-muted mt-1">Silakan tap ID Card atau ketik NIK peserta</div>
                    </div>
                </div>

                <!-- Live Summary Stats with Explicit Labels -->
                <div class="row g-2">
                    <div class="col-6">
                        <div class="stat-card" style="border-left: 4px solid #10b981;">
                            <small class="text-white-50 fw-semibold d-block mb-1" style="font-size: 0.78rem;">Trainee Hadir Hari Ini</small>
                            <div class="stat-value text-success" id="statHadir"><?= $stats['hadir'] ?? 0 ?> <span class="fs-6 text-muted font-monospace">Orang</span></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
                            <small class="text-white-50 fw-semibold d-block mb-1" style="font-size: 0.78rem;">Total Trainee Aktif TC</small>
                            <div class="stat-value text-warning"><?= $total_trainees_active ?? 0 ?> <span class="fs-6 text-muted font-monospace">Orang</span></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card" style="border-left: 4px solid #3b82f6;">
                            <small class="text-white-50 fw-semibold d-block mb-1" style="font-size: 0.78rem;">Trainee Izin / Sakit / Alpha</small>
                            <div class="stat-value text-info"><?= ($stats['izin'] ?? 0) + ($stats['sakit'] ?? 0) + ($stats['alpha'] ?? 0) ?> <span class="fs-6 text-muted font-monospace">Orang</span></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card" style="border-left: 4px solid #a855f7;">
                            <small class="text-white-50 fw-semibold d-block mb-1" style="font-size: 0.78rem;">Persentase Kehadiran</small>
                            <div class="stat-value text-purple" style="color: #c084fc;"><?= $present_rate ?? 0 ?>%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Today's Real-time Tap Log Table -->
            <div class="col-lg-7 col-xl-8">
                <div class="card bg-dark border-secondary h-100 shadow-lg">
                    <div class="card-header bg-dark border-secondary d-flex justify-content-between align-items-center py-3">
                        <h5 class="fw-bold text-white mb-0">
                            <i class="bi bi-clock-history text-warning me-2"></i> Riwayat Tap Hari Ini (<?= date('d/m/Y') ?>)
                        </h5>
                        <span class="badge bg-warning text-dark px-3 py-2 fs-6 font-monospace" id="kioskTotalLog">
                            <?= count($attendances) ?> Data
                        </span>
                    </div>
                    <div class="card-body p-0 table-responsive" style="max-height: 65vh;">
                        <table class="table table-dark table-hover table-striped align-middle mb-0">
                            <thead class="table-secondary text-uppercase small sticky-top">
                                <tr>
                                    <th>Nama / NIK</th>
                                    <th style="width: 120px;">Jam Masuk</th>
                                    <th style="width: 120px;">Jam Keluar</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="kioskLogTbody">
                                <?php if (empty($attendances)): ?>
                                    <tr id="emptyRow">
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            Belum ada aktivitas absensi tercatat hari ini.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($attendances as $att): ?>
                                        <?php
                                        $badgeClass = match($att['status']) {
                                            'Hadir' => 'bg-success',
                                            'Ijin', 'Izin', 'Sakit' => 'bg-warning text-dark',
                                            default => 'bg-danger'
                                        };
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-white"><?= esc($att['trainee_name']) ?></div>
                                                <small class="text-muted font-monospace"><?= esc($att['nik']) ?></small>
                                            </td>
                                            <td class="font-monospace fw-bold text-success">
                                                <i class="bi bi-box-arrow-in-right text-success me-1"></i><?= esc($att['time_in'] ?: '-') ?>
                                            </td>
                                            <td class="font-monospace fw-bold text-info">
                                                <i class="bi bi-box-arrow-right text-info me-1"></i><?= esc($att['time_out'] ?: '-') ?>
                                            </td>
                                            <td>
                                                <span class="badge <?= $badgeClass ?> px-2 py-1">
                                                    <?= esc($att['status']) ?>
                                                </span>
                                            </td>
                                            <td class="small text-muted"><?= esc($att['notes'] ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('kioskRfidInput');
        const statusBox = document.getElementById('kioskStatusBox');
        const statusContent = document.getElementById('kioskStatusContent');
        const clockEl = document.getElementById('kioskClock');

        // Live Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false }) + ' WIB';
            if (clockEl) clockEl.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Keep Focus on RFID scanner input automatically even after idle
        function keepFocus() {
            const activeEl = document.activeElement;
            if (activeEl !== input && (!activeEl || (activeEl.tagName !== 'INPUT' && activeEl.tagName !== 'TEXTAREA' && activeEl.tagName !== 'SELECT'))) {
                input.focus();
            }
        }
        setInterval(keepFocus, 500);
        input.focus();

        // Auto refocus on click or keydown anywhere in body
        document.addEventListener('click', keepFocus);
        document.addEventListener('keydown', function(e) {
            if (document.activeElement !== input) {
                keepFocus();
            }
        });

        // Handle RFID Input Enter Key
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const cardVal = (input.value || '').trim();
                if (!cardVal) return;

                // Show processing status
                statusBox.className = 'status-display status-info';
                statusContent.innerHTML = `
                    <div class="spinner-border text-warning mb-2" role="status"></div>
                    <div class="fw-bold fs-5">Memproses Tap ID Card: [${cardVal}]</div>
                    <div class="small text-muted">Menghubungkan ke database & UNIS API...</div>
                `;

                fetch('<?= base_url('attendance/tap') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        'rfid_tag': cardVal,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success === true || data.status === 'success') {
                        const trainee = data.trainee || {};
                        const type = data.type || 'checkin';
                        
                        if (type === 'checkin') {
                            statusBox.className = 'status-display status-success-in';
                            statusContent.innerHTML = `
                                <i class="bi bi-check-circle-fill display-4 text-success d-block mb-2"></i>
                                <div class="fw-bold fs-4 text-white">${data.time_type || 'ABSENSI MASUK'}: ${trainee.name || ''}</div>
                                <div class="font-monospace text-warning fs-5">NIK: ${trainee.nik || ''} | Jam: ${data.time || ''}</div>
                                <div class="small text-light mt-1">${data.message}</div>
                            `;
                        } else if (type === 'checkout') {
                            statusBox.className = 'status-display status-success-out';
                            statusContent.innerHTML = `
                                <i class="bi bi-box-arrow-right display-4 text-info d-block mb-2"></i>
                                <div class="fw-bold fs-4 text-white">${data.time_type || 'ABSENSI PULANG'}: ${trainee.name || ''}</div>
                                <div class="font-monospace text-info fs-5">NIK: ${trainee.nik || ''} | Jam: ${data.time || ''}</div>
                                <div class="small text-light mt-1">${data.message}</div>
                            `;
                        } else {
                            statusBox.className = 'status-display status-info';
                            statusContent.innerHTML = `
                                <i class="bi bi-info-circle-fill display-4 text-warning d-block mb-2"></i>
                                <div class="fw-bold fs-4 text-white">${trainee.name || ''} (${trainee.nik || ''})</div>
                                <div class="small text-warning fs-6 mt-1">${data.message}</div>
                            `;
                        }

                        // Reload page or refresh list after 1.8 seconds to show updated table
                        setTimeout(() => {
                            location.reload();
                        }, 1800);
                    } else {
                        statusBox.className = 'status-display status-error';
                        statusContent.innerHTML = `
                            <i class="bi bi-x-circle-fill display-4 text-danger d-block mb-2"></i>
                            <div class="fw-bold fs-4 text-white">TAP GAGAL</div>
                            <div class="small text-white mt-1">${data.message || 'ID Card/NIK tidak terdaftar.'}</div>
                        `;
                    }
                    input.value = '';
                })
                .catch(err => {
                    statusBox.className = 'status-display status-error';
                    statusContent.innerHTML = `
                        <i class="bi bi-wifi-off display-4 text-danger d-block mb-2"></i>
                        <div class="fw-bold fs-4 text-white">KONEKSI TERPUTUS</div>
                        <div class="small text-white mt-1">Gagal terhubung ke server. Periksa koneksi jaringan.</div>
                    `;
                    input.value = '';
                });
            }
        });
    });
    </script>
</body>
</html>
