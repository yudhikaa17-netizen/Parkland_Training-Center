<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="dash-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 mb-2">
    <div>
        <h2 class="fw-bold text-white mb-1">Absensi Harian Training Center</h2>
    </div>

    <div class="filter-row d-flex align-items-center gap-2">
        <a href="<?= base_url('attendance/kiosk') ?>" class="btn btn-pwi-orange fw-bold d-inline-flex align-items-center gap-2">
            <i class="bi bi-display"></i> Mode Kiosk Absensi (Halaman Khusus ID Card)
        </a>
        <!-- Date Filter -->
        <form method="GET" action="<?= base_url('attendance') ?>" class="d-inline-flex align-items-center gap-2 m-0" id="dateFilterForm">
            <input type="date" name="date" id="absensiDate" value="<?= esc($selected_date) ?>" onchange="document.getElementById('dateFilterForm').submit()">
        </form>
    </div>
</div>

<p class="note-box" style="margin-top:0;margin-bottom:24px;">
    Pilih tanggal, lalu tandai status kehadiran setiap peserta yang masih aktif di Training Center beserta alasannya. Jika status <strong>Resign</strong> dipilih, peserta otomatis keluar dari program dan statusnya berubah menjadi Resign.
</p>

<!-- 1. Ringkasan Absensi Tanggal Terpilih (Bar Chart) -->
<div class="sub-heading">Ringkasan Absensi Tanggal: <?= esc($selected_date) ?></div>
<div class="barchart-card mb-4">
    <?php
    $attSummary = [
        'hadir' => $stats['hadir'] ?? 0,
        'mangkir' => $stats['alpha'] ?? 0,
        'ijin' => $stats['izin'] ?? 0,
        'sakit' => $stats['sakit'] ?? 0,
        'resign' => 0
    ];
    $maxVal = max(1, ...array_values($attSummary));
    $chartConfig = [
        ['label' => 'Hadir', 'color' => 'var(--green)', 'count' => $attSummary['hadir']],
        ['label' => 'Mangkir', 'color' => 'var(--red)', 'count' => $attSummary['mangkir']],
        ['label' => 'Ijin', 'color' => 'var(--amber)', 'count' => $attSummary['ijin']],
        ['label' => 'Sakit', 'color' => 'var(--blue)', 'count' => $attSummary['sakit']],
        ['label' => 'Resign', 'color' => 'var(--orange)', 'count' => $attSummary['resign']],
    ];
    ?>
    <div class="barchart-wrap">
        <?php foreach ($chartConfig as $cc): ?>
            <?php $hPct = max(18, round(($cc['count'] / $maxVal) * 100)); ?>
            <div class="bar-col">
                <div class="bar-track">
                    <div class="bar-fill" style="height: <?= $hPct ?>%; background: <?= $cc['color'] ?>;">
                        <span class="bar-value"><?= $cc['count'] ?></span>
                    </div>
                </div>
                <span class="bar-label"><?= $cc['label'] ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- 2. Operator Ready di Training Center (Hadir, Belum Absen Keluar, Belum OJT & Belum Dipinjamkan) -->
<div class="sub-heading d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        Operator Ready di Training Center
        <span class="count mono" style="background:var(--green-dim);color:var(--green);border:1px solid rgba(52,211,153,0.3);margin-left:8px;padding:2px 8px;border-radius:12px;">
            <?= count($ready_operators ?? []) ?> Orang
        </span>
    </div>
    <span style="font-size:0.76rem;color:var(--muted);font-weight:normal;">
        <i class="bi bi-info-circle me-1"></i>Kriteria: Hadir • Belum Absen Keluar • Belum OJT • Belum Dipinjamkan
    </span>
</div>

<div class="table-scroll mb-4">
    <table>
        <thead>
            <tr>
                <th>Nama Operator / NIK</th>
                <th style="width:110px;">Jam Masuk</th>
                <th style="width:110px;">Jam Keluar</th>
                <th>Tahap Training</th>
                <th>Track / Peminatan</th>
                <th>Status Standby</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ready_operators)): ?>
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="bi bi-info-circle me-1"></i> Tidak ada operator standby (semua sudah absen keluar, OJT, dipinjamkan, atau belum hadir untuk tanggal terpilih).
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($ready_operators as $ro): ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-white"><?= esc($ro['trainee_name']) ?></div>
                            <div class="mono" style="font-size:0.75rem;color:var(--muted);"><?= esc($ro['nik']) ?></div>
                        </td>
                        <td class="mono fw-bold text-success"><?= esc($ro['time_in'] ?: '-') ?></td>
                        <td class="mono fw-bold text-info"><?= esc($ro['time_out'] ?: '-') ?></td>
                        <td>
                            <span class="badge" style="background:var(--blue-dim);color:var(--blue);border:1px solid rgba(91,157,245,0.3);font-size:0.78rem;padding:4px 8px;">
                                <?= esc($ro['stage']) ?>
                            </span>
                        </td>
                        <td style="color:var(--text);font-size:0.85rem;"><?= esc($ro['track'] ?? '-') ?></td>
                        <td>
                            <span class="badge lulus"><span class="dot"></span>Ready di TC</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- 2. Tap ID Card Box matching training-tracker (1).html -->
<div class="sub-heading">Tap ID Card</div>
<div style="background:var(--panel);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-sm);padding:22px;margin-bottom:12px;">
    <label for="attendanceRfidInput" style="font-size:0.82rem;color:var(--muted);display:block;margin-bottom:10px;">
        Tempelkan/tap ID Card peserta pada reader (reader USB standar bekerja seperti keyboard — cukup klik kolom ini sekali, lalu tap ID Card). Otomatis tercatat <strong>Hadir</strong> untuk tanggal yang dipilih di atas.
    </label>
    <input type="text" id="attendanceRfidInput" placeholder="Klik di sini, lalu tap ID Card…" autocomplete="off"
        style="width:100%;font-size:1.05rem;padding:16px;border:2px dashed var(--border);border-radius:8px;font-family:'IBM Plex Mono',monospace;text-align:center;background:var(--panel-2);">
    <div id="attendanceFeedback" style="margin-top:12px;min-height:22px;font-size:0.88rem;font-weight:500;color:var(--amber);"></div>
</div>
<p class="note-box" style="margin-top:0;margin-bottom:28px;">
    Kolom di atas bekerja dengan reader ID Card yang berperan sebagai keyboard (mode "keyboard wedge") dengan konversi Reverse Hex ke Decimal otomatis. Tap otomatis ini langsung mencatat waktu masuk/pulang di sistem.
</p>

<!-- 3. Riwayat Tap Hari Ini -->
<div class="sub-heading">
    Riwayat Tap Hari Ini <span class="count mono" id="rfidLogCount"><?= count($attendances) ?></span>
</div>

<div class="table-scroll mb-5">
    <table>
        <thead>
            <tr>
                <th>Nama Operator / NIK</th>
                <th style="width:110px;">Jam Masuk</th>
                <th style="width:110px;">Jam Keluar</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody id="rfidLogTbody">
            <?php if (empty($attendances)): ?>
                <tr>
                    <td colspan="5" class="empty-state">Belum ada aktivitas absensi untuk tanggal <?= esc($selected_date) ?>.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($attendances as $att): ?>
                    <?php
                    $attBadge = match($att['status']) {
                        'Hadir' => 'badge lulus',
                        'Ijin', 'Izin', 'Sakit' => 'badge menunggu',
                        default => 'badge gagal'
                    };
                    ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-white"><?= esc($att['trainee_name']) ?></div>
                            <div class="mono" style="font-size:0.75rem;color:var(--muted);"><?= esc($att['nik']) ?></div>
                        </td>
                        <td class="mono fw-bold text-success"><?= esc($att['time_in'] ?: '-') ?></td>
                        <td class="mono fw-bold text-info"><?= esc($att['time_out'] ?: '-') ?></td>
                        <td>
                            <span class="<?= $attBadge ?>"><span class="dot"></span><?= esc($att['status']) ?></span>
                        </td>
                        <td style="color:var(--muted);font-size:0.84rem;"><?= esc($att['notes'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- 4. Isi Manual / Koreksi Absensi -->
<?php if ($current_role === 'superadmin'): ?>
<div class="sub-heading">Isi Manual / Koreksi Absensi</div>
<div class="pwi-card">
    <form action="<?= base_url('attendance/manual') ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="attendance_date" value="<?= esc($selected_date) ?>">

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small text-muted">Pilih Trainee *</label>
                <select name="trainee_id" class="w-100" required>
                    <option value="">-- Pilih Trainee --</option>
                    <?php foreach ($trainees as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= esc($t['name']) ?> (<?= esc($t['nik']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Status Kehadiran *</label>
                <select name="status" class="w-100" required>
                    <option value="Hadir">Hadir</option>
                    <option value="Izin">Ijin / Permisi</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Alpha">Mangkir / Alpha</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">Keterangan / Alasan</label>
                <input type="text" name="notes" class="w-100" placeholder="Alasan jika izin/sakit...">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    Simpan
                </button>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rfidInput = document.getElementById('attendanceRfidInput');
    const feedback = document.getElementById('attendanceFeedback');

    if (rfidInput) {
        function autoFocusRfid() {
            const activeEl = document.activeElement;
            if (activeEl !== rfidInput && (!activeEl || (activeEl.tagName !== 'INPUT' && activeEl.tagName !== 'TEXTAREA' && activeEl.tagName !== 'SELECT'))) {
                rfidInput.focus();
            }
        }
        setInterval(autoFocusRfid, 1000);
        document.addEventListener('click', autoFocusRfid);

        rfidInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const cardVal = (rfidInput.value || '').trim();
                if (!cardVal) return;

                feedback.style.display = 'block';
                feedback.textContent = `Memproses tap ID Card: [${cardVal}]...`;

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
                        feedback.style.color = 'var(--green)';
                        feedback.textContent = `✓ Berhasil: ${data.message}`;
                        setTimeout(() => {
                            location.reload();
                        }, 1200);
                    } else {
                        feedback.style.color = 'var(--red)';
                        feedback.textContent = `✗ Gagal: ${data.message || 'ID Card tidak dikenali'}`;
                    }
                    rfidInput.value = '';
                })
                .catch(err => {
                    feedback.style.color = 'var(--red)';
                    feedback.textContent = `Error: Gagal menghubungkan ke server.`;
                    rfidInput.value = '';
                });
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
