<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="dash-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 mb-2">
    <div>
        <h2 class="fw-bold text-white mb-1">Peminjaman Operator</h2>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- Date Filter -->
        <form method="GET" action="<?= base_url('loans') ?>" class="d-inline-flex align-items-center gap-2 m-0" id="dateFilterForm">
            <label class="text-muted small m-0 d-none d-sm-inline">Filter Tanggal:</label>
            <input type="date" name="date" id="loanDate" value="<?= esc($selected_date ?? '') ?>" onchange="document.getElementById('dateFilterForm').submit()" style="padding:6px 12px; border-radius:6px; background:var(--panel-2); border:1px solid var(--border); color:var(--text); font-size:0.88rem;">
            <?php if (!empty($selected_date)): ?>
                <a href="<?= base_url('loans') ?>" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1" style="font-size:0.82rem;">
                    <i class="bi bi-x-circle"></i> Semua Tanggal
                </a>
            <?php else: ?>
                <span class="badge" style="background:var(--panel-2);border:1px solid var(--border);color:var(--blue);font-size:0.78rem;padding:6px 10px;">
                    <i class="bi bi-calendar-check me-1"></i> Menampilkan Semua Tanggal
                </span>
            <?php endif; ?>
        </form>

        <?php if ($current_role === 'superadmin'): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddLoan">
                + Ajukan Peminjaman
            </button>
        <?php endif; ?>
    </div>
</div>

<p class="note-box" style="margin-top:0;margin-bottom:24px;">
    Peminjaman hanya berlaku untuk operator yang masih dalam tahap <strong>Induction, Motoric Test, atau Training Hard Skill</strong>. Begitu operator memasuki <strong>On Job Training</strong>, ia tidak bisa lagi dipinjamkan. Permintaan diajukan oleh Admin Training Center dan disetujui/ditolak oleh Leader Lean. Setiap kejadian peminjaman otomatis tercatat di riwayat operator terkait.
</p>

<!-- 1. Executive Status KPI Cards & Alokasi Per Gedung -->
<div class="sub-heading">
    Ringkasan Status Peminjaman &amp; Sisa Operator TC 
    <span style="font-size:0.8rem; font-weight:normal; color:var(--muted);">
        (<?= !empty($selected_date) ? 'Tanggal: ' . esc($selected_date) : 'Semua Tanggal / Real-time' ?>)
    </span>
</div>
<div class="row g-3 mb-4">
    <!-- Stat Item 1: Sisa Operator TC -->
    <div class="col-md-6 col-lg-6">
        <div class="stat h-100" style="border-left: 4px solid var(--green); background: var(--panel);">
            <div class="n mono" style="color: var(--green);"><?= $sisa_operator_tc ?? 0 ?> <span style="font-size:0.85rem;color:var(--muted);">orang</span></div>
            <div class="l font-semibold text-white" style="font-size:0.92rem;">Sisa Operator TC (Ready)</div>
            <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">
                Rumus: Operator Hadir (<?= $present_today_count ?? 0 ?>) - Total Dipinjam (<?= $factory_stats['total_active'] ?? 0 ?>)
            </div>
        </div>
    </div>

    <!-- Stat Item 2: Total Dipinjam -->
    <div class="col-md-6 col-lg-6">
        <div class="stat h-100" style="border-left: 4px solid var(--amber); background: var(--panel);">
            <div class="n mono" style="color: var(--amber);"><?= $factory_stats['total_active'] ?? 0 ?> <span style="font-size:0.85rem;color:var(--muted);">orang</span></div>
            <div class="l font-semibold text-white" style="font-size:0.92rem;">Total Sedang Dipinjam</div>
            <div style="font-size:0.75rem;color:var(--muted);margin-top:4px;">Aktif Bantuan Line Produksi di Seluruh Gedung</div>
        </div>
    </div>
</div>

<!-- Dynamic Building Cards (Daftar Pabrik / Lokasi Otomatis dari Kelola Konten) -->
<div class="sub-heading mb-3">Alokasi Operator Dipinjam Per Gedung / Pabrik</div>
<div class="row g-3 mb-4">
    <?php if (!empty($factory_stats['factory_counts'])): ?>
        <?php foreach ($factory_stats['factory_counts'] as $fac): ?>
            <div class="col-6 col-md-3">
                <div class="stat h-100" style="border-left: 4px solid <?= esc($fac['color']) ?>; background: var(--panel);">
                    <div class="n mono" style="color: <?= esc($fac['color']) ?>;"><?= $fac['active'] ?? 0 ?> <span style="font-size:0.75rem;color:var(--muted);">orang</span></div>
                    <div class="l font-semibold text-white"><?= esc($fac['name']) ?></div>
                    <div style="font-size:0.72rem;color:var(--muted);margin-top:4px;"><?= esc($fac['description']) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-muted small">Belum ada daftar pabrik yang dikonfigurasi.</div>
    <?php endif; ?>
</div>

<!-- 2. Peminjaman Aktif Table -->
<?php 
$activeLoans = array_filter($loans, fn($item) => ($item['status'] ?? '') === 'Dipinjam' || ($item['status'] ?? '') === 'Disetujui');
$historyLoans = array_filter($loans, fn($item) => ($item['status'] ?? '') === 'Selesai' || ($item['status'] ?? '') === 'Ditolak' || ($item['status'] ?? '') === 'Dibatalkan');
?>

<div class="sub-heading">
    Peminjaman Aktif <span class="count mono"><?= count($activeLoans) ?></span>
</div>

<div class="table-scroll mb-5">
    <table>
        <thead>
            <tr>
                <th>Operator Trainee</th>
                <th>Gedung / Factory</th>
                <th>Departemen &amp; Line</th>
                <th>Penanggung Jawab (Peminjam)</th>
                <th>Periode Pinjam</th>
                <th>Durasi</th>
                <th>Alasan / Tujuan</th>
                <th>Status</th>
                <?php if ($current_role === 'superadmin'): ?>
                    <th class="text-end">Aksi</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($activeLoans)): ?>
                <tr>
                    <td colspan="9" class="empty-state">Tidak ada operator yang sedang dipinjam saat ini.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($activeLoans as $l): ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-white"><?= esc($l['trainee_name']) ?></div>
                            <div class="mono" style="font-size:0.75rem;color:var(--muted);"><?= esc($l['nik']) ?> &bull; Track: <?= esc($l['track']) ?></div>
                        </td>
                        <td>
                            <span class="badge ojt font-monospace">
                                <i class="bi bi-building me-1"></i> <?= esc($l['factory'] ?? 'Factory 1 (F1)') ?>
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold text-white"><?= esc($l['borrowing_department']) ?></div>
                            <div style="font-size:0.75rem;color:var(--muted);"><?= esc($l['line_name']) ?></div>
                        </td>
                        <td>
                            <div class="fw-semibold text-warning" style="font-size:0.85rem;"><?= esc($l['person_in_charge'] ?? 'Supervisor Line') ?></div>
                        </td>
                        <td style="font-size:0.84rem;">
                            <div>Mulai: <strong class="text-white"><?= esc($l['start_date']) ?></strong></div>
                            <div style="color:var(--amber);">Sedang Berjalan</div>
                        </td>
                        <td class="mono"><?= esc($l['duration_days']) ?> Hari</td>
                        <td style="font-size:0.82rem;color:var(--muted);max-width:180px;"><?= esc($l['purpose'] ?? '-') ?></td>
                        <td>
                            <span class="badge menunggu"><span class="dot"></span>Dipinjam</span>
                        </td>
                        <?php if ($current_role === 'superadmin'): ?>
                            <td class="text-end">
                                <a href="<?= base_url('loans/return/' . $l['id']) ?>" class="btn btn-primary" style="font-size:0.75rem;padding:4px 10px;" onclick="return confirm('Kembalikan operator ini ke Training Center?')">
                                    <i class="bi bi-arrow-return-left me-1"></i> Kembalikan
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- 3. Riwayat Peminjaman Table -->
<div class="sub-heading">
    Riwayat Peminjaman (Selesai) <span class="count mono"><?= count($historyLoans) ?></span>
</div>

<div class="table-scroll mb-4">
    <table>
        <thead>
            <tr>
                <th>Operator Trainee</th>
                <th>Gedung / Factory</th>
                <th>Departemen &amp; Line</th>
                <th>Periode Pinjam</th>
                <th>Tanggal Selesai</th>
                <th>Alasan / Tujuan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($historyLoans)): ?>
                <tr>
                    <td colspan="7" class="empty-state">Belum ada riwayat peminjaman yang selesai.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($historyLoans as $l): ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-white"><?= esc($l['trainee_name']) ?></div>
                            <div class="mono" style="font-size:0.75rem;color:var(--muted);"><?= esc($l['nik']) ?></div>
                        </td>
                        <td>
                            <span class="badge slate">
                                <?= esc($l['factory'] ?? 'Factory 1 (F1)') ?>
                            </span>
                        </td>
                        <td>
                            <div class="text-white"><?= esc($l['borrowing_department']) ?></div>
                            <div style="font-size:0.75rem;color:var(--muted);"><?= esc($l['line_name']) ?></div>
                        </td>
                        <td style="font-size:0.84rem;color:var(--muted);"><?= esc($l['start_date']) ?></td>
                        <td class="mono" style="font-size:0.84rem;color:var(--text);"><?= esc($l['end_date'] ?: '-') ?></td>
                        <td style="font-size:0.82rem;color:var(--muted);max-width:200px;"><?= esc($l['purpose'] ?? '-') ?></td>
                        <td>
                            <span class="badge lulus"><span class="dot"></span>Selesai</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Ajukan Peminjaman dengan TAP RFID & Penanggung Jawab -->
<?php if ($current_role === 'superadmin'): ?>
<div class="modal fade" id="modalAddLoan" tabindex="-1" aria-labelledby="modalAddLoanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-dark">
            <form action="<?= base_url('loans/create') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="modalAddLoanLabel">
                        <i class="bi bi-plus-circle-fill me-2" style="color:var(--amber);"></i> Ajukan Peminjaman Operator Trainee
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    
                    <!-- Tap RFID Box for Instant Auto-Fill -->
                    <div class="p-3 rounded-2 mb-3" style="background:var(--panel-2);border:1px dashed var(--amber);">
                        <label class="form-label small text-warning mb-1 font-semibold">
                            <i class="bi bi-qr-code-scan me-1"></i> Tap ID Card / Scan NIK Operator (Otomatis Isi Data)
                        </label>
                        <input type="text" id="loanRfidInput" class="w-100 font-monospace text-center p-2" placeholder="Tap ID Card atau ketik NIK operator di sini..." autocomplete="off">
                        <div id="loanScanFeedback" class="small mt-1 text-muted" style="min-height:18px;"></div>
                    </div>

                    <!-- Auto-filled Trainee Info Card -->
                    <div class="p-3 rounded-2 mb-3 d-none" id="loanTraineeInfoCard" style="background:rgba(16,185,129,0.1);border:1px solid #10b981;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-bold text-white mb-0" id="cardTraineeName">Nama Trainee</h6>
                                <small class="text-warning font-monospace" id="cardTraineeNik">NIK: -</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success" id="cardTraineeStage">Stage</span>
                                <div class="small text-muted" id="cardTraineeTrack">Track: -</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Pilih Operator Trainee *</label>
                            <select name="trainee_id" id="loanTraineeSelect" class="w-100" required>
                                <option value="">-- Pilih Operator Tersedia --</option>
                                <?php foreach ($available_trainees as $t): ?>
                                    <option value="<?= $t['id'] ?>" data-nik="<?= esc($t['nik']) ?>" data-rfid="<?= esc($t['rfid_tag']) ?>" data-name="<?= esc($t['name']) ?>" data-stage="<?= esc($t['stage']) ?>" data-track="<?= esc($t['track']) ?>">
                                        <?= esc($t['name']) ?> (<?= esc($t['nik']) ?>) - <?= esc($t['stage']) ?> [<?= esc($t['track']) ?>]
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small style="color:var(--muted);font-size:0.72rem;">* Hanya operator tersedia yang tampil.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small text-muted">Lokasi Pabrik (Factory) *</label>
                            <select name="factory" class="w-100" required>
                                <?php if (!empty($factory_stats['factory_counts'])): ?>
                                    <?php foreach ($factory_stats['factory_counts'] as $fac): ?>
                                        <option value="<?= esc($fac['name']) ?>"><?= esc($fac['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="Factory 1 (F1)">Factory 1 (F1)</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Item 4: Departemen & Penanggung Jawab Peminjam -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Nama Departemen Peminjam *</label>
                            <input type="text" name="borrowing_department" class="w-100" placeholder="Contoh: Production Sewing / Assembly Line 03" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Penanggung Jawab Peminjam (Supervisor/Staff) *</label>
                            <input type="text" name="person_in_charge" class="w-100" placeholder="Contoh: Bpk. Ahmad (Supervisor Sewing Cell 2)" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Line / Cell Tujuan *</label>
                            <input type="text" name="line_name" class="w-100" placeholder="Contoh: Cell 14 / Line A3" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Tanggal Mulai Pinjam *</label>
                            <input type="date" name="start_date" class="w-100" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Durasi Pinjam (Hari) *</label>
                            <input type="number" name="duration_days" class="w-100 mono" min="1" max="30" value="3" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Tujuan / Alasan Peminjaman *</label>
                        <textarea name="purpose" class="w-100" rows="2" placeholder="Contoh: Bantuan lonjakan output line perakitan model baru..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan &amp; Pinjamkan Operator</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rfidInput   = document.getElementById('loanRfidInput');
    const selectEl    = document.getElementById('loanTraineeSelect');
    const feedback    = document.getElementById('loanScanFeedback');
    const infoCard    = document.getElementById('loanTraineeInfoCard');
    const cardName    = document.getElementById('cardTraineeName');
    const cardNik     = document.getElementById('cardTraineeNik');
    const cardStage   = document.getElementById('cardTraineeStage');
    const cardTrack   = document.getElementById('cardTraineeTrack');

    function showTraineeInfo(option) {
        if (!option || !option.value) {
            if (infoCard) infoCard.classList.add('d-none');
            return;
        }
        if (infoCard) {
            infoCard.classList.remove('d-none');
            if (cardName) cardName.textContent = option.getAttribute('data-name') || 'Trainee';
            if (cardNik) cardNik.textContent = 'NIK: ' + (option.getAttribute('data-nik') || '-');
            if (cardStage) cardStage.textContent = option.getAttribute('data-stage') || 'Stage';
            if (cardTrack) cardTrack.textContent = 'Track: ' + (option.getAttribute('data-track') || '-');
        }
    }

    if (selectEl) {
        selectEl.addEventListener('change', function() {
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            showTraineeInfo(selectedOption);
        });
    }

    if (rfidInput && selectEl) {
        rfidInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = (rfidInput.value || '').trim().toLowerCase();
                if (!query) return;

                let found = false;
                for (let i = 0; i < selectEl.options.length; i++) {
                    const opt = selectEl.options[i];
                    const rfid = (opt.getAttribute('data-rfid') || '').toLowerCase();
                    const nik = (opt.getAttribute('data-nik') || '').toLowerCase();
                    const name = (opt.getAttribute('data-name') || '').toLowerCase();

                    if (rfid === query || nik === query || name.includes(query)) {
                        selectEl.selectedIndex = i;
                        showTraineeInfo(opt);
                        found = true;
                        if (feedback) {
                            feedback.style.color = 'var(--green)';
                            feedback.textContent = `✓ Trainee ditemukan: ${opt.getAttribute('data-name')} (${opt.getAttribute('data-nik')})`;
                        }
                        break;
                    }
                }

                if (!found && feedback) {
                    feedback.style.color = 'var(--red)';
                    feedback.textContent = `✗ Operator dengan RFID/NIK [${query}] tidak tersedia atau sedang dipinjam.`;
                }
                rfidInput.value = '';
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
