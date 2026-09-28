<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="dash-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-white mb-1">Tracking Trainee</h2>
        <p style="color:var(--muted);font-size:0.86rem;margin:0;">Manajemen data peserta pelatihan, progres tahapan, lokasi tujuan, dan riwayat cycle time.</p>
    </div>

    <div class="filter-row">
        <!-- Stage Filter -->
        <form method="GET" action="<?= base_url('trainees') ?>" class="d-inline-flex align-items-center gap-2 m-0" id="filterForm">
            <select name="stage" id="filterStatus" onchange="document.getElementById('filterForm').submit()">
                <option value="all" <?= ($selected_stage === 'all') ? 'selected' : '' ?>>Semua status</option>
                <option value="Induction" <?= ($selected_stage === 'Induction') ? 'selected' : '' ?>>Induction / Teori (<?= $stage_counts['Induction'] ?? 0 ?>)</option>
                <option value="Motoric Test" <?= ($selected_stage === 'Motoric Test') ? 'selected' : '' ?>>Motoric Test (<?= $stage_counts['Motoric Test'] ?? 0 ?>)</option>
                <option value="Hard Skill" <?= ($selected_stage === 'Hard Skill') ? 'selected' : '' ?>>Training Hard Skill (<?= $stage_counts['Hard Skill'] ?? 0 ?>)</option>
                <option value="Upskill" <?= ($selected_stage === 'Upskill') ? 'selected' : '' ?>>Upskill Training (<?= $stage_counts['Upskill'] ?? 0 ?>)</option>
                <option value="OJT" <?= ($selected_stage === 'OJT') ? 'selected' : '' ?>>On Job Training (<?= $stage_counts['OJT'] ?? 0 ?>)</option>
                <option value="Lulus" <?= ($selected_stage === 'Lulus') ? 'selected' : '' ?>>Lulus / Aktif (<?= $stage_counts['Lulus'] ?? 0 ?>)</option>
                <option value="Tidak Lolos" <?= ($selected_stage === 'Tidak Lolos') ? 'selected' : '' ?>>Tidak Lolos Program (<?= $stage_counts['Tidak Lolos'] ?? 0 ?>)</option>
                <option value="Resign" <?= ($selected_stage === 'Resign') ? 'selected' : '' ?>>Resign (<?= $stage_counts['Resign'] ?? 0 ?>)</option>
            </select>
        </form>

        <?php if ($current_role === 'superadmin'): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddTrainee">
                + Tambah Trainee
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- 1. Barchart Summary of Stages -->
<div class="barchart-card mb-4">
    <?php
    $maxTrainee = max(1, ...array_values($stage_counts));
    $stagesConfig = [
        'Induction' => ['label' => 'Induction', 'color' => 'var(--slate)', 'count' => $stage_counts['Induction'] ?? 0],
        'Motoric Test' => ['label' => 'Motoric', 'color' => 'var(--violet)', 'count' => $stage_counts['Motoric Test'] ?? 0],
        'Hard Skill' => ['label' => 'Hard Skill', 'color' => 'var(--amber)', 'count' => $stage_counts['Hard Skill'] ?? 0],
        'Upskill' => ['label' => 'Upskill', 'color' => 'var(--teal)', 'count' => $stage_counts['Upskill'] ?? 0],
        'OJT' => ['label' => 'OJT', 'color' => 'var(--blue)', 'count' => $stage_counts['OJT'] ?? 0],
        'Lulus' => ['label' => 'Lulus / Aktif', 'color' => 'var(--green)', 'count' => $stage_counts['Lulus'] ?? 0],
        'Tidak Lolos' => ['label' => 'Tidak Lolos', 'color' => 'var(--red)', 'count' => $stage_counts['Tidak Lolos'] ?? 0],
        'Resign' => ['label' => 'Resign', 'color' => 'var(--orange)', 'count' => $stage_counts['Resign'] ?? 0],
    ];
    ?>
    <div class="barchart-wrap">
        <?php foreach ($stagesConfig as $key => $conf): ?>
            <?php $heightPct = max(18, round(($conf['count'] / $maxTrainee) * 100)); ?>
            <div class="bar-col">
                <div class="bar-track">
                    <div class="bar-fill" style="height: <?= $heightPct ?>%; background: <?= $conf['color'] ?>;">
                        <span class="bar-value"><?= $conf['count'] ?></span>
                    </div>
                </div>
                <span class="bar-label"><?= $conf['label'] ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- 2. ID Card Tap & Live Search Box matching training-tracker (1).html -->
<div style="background:var(--panel);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-sm);padding:14px 18px;margin-bottom:18px;">
    <label for="traineeRfidInput" style="font-size:0.78rem;color:var(--muted);display:block;margin-bottom:6px;">
        Tap ID Card untuk mencari atau menyorot peserta (atau ketik Nama / NIK untuk filter cepat tabel di bawah):
    </label>
    <input type="text" id="traineeRfidInput" placeholder="Klik di sini, lalu tap ID Card atau ketik pencarian…" autocomplete="off"
        style="width:100%;font-size:0.92rem;padding:12px;border:2px dashed var(--border);border-radius:8px;font-family:'IBM Plex Mono',monospace;text-align:center;background:var(--panel-2);">
    <div id="traineeRfidFeedback" style="min-height:18px;font-size:0.8rem;font-weight:500;margin-top:6px;color:var(--amber);"></div>
</div>

<!-- 3. Trainees Dark Table -->
<div class="table-scroll">
    <table id="traineeTable">
        <thead>
            <tr>
                <th style="width: 36px;"><input type="checkbox" id="traineeSelectAll" title="Pilih semua"></th>
                <th>Nama / NIK</th>
                <th>ID Card</th>
                <th>Track</th>
                <th>Lokasi Tujuan</th>
                <th>Tahap (Stage)</th>
                <th>Lama Training</th>
                <th>Status</th>
                <th class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($trainees)): ?>
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Belum ada data trainee yang sesuai filter.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($trainees as $t): ?>
                    <?php
                        $stageBadge = match($t['stage']) {
                            'Induction'    => 'badge induction',
                            'Motoric Test' => 'badge motoric',
                            'Hard Skill'   => 'badge training',
                            'Upskill'      => 'badge upskill',
                            'OJT'          => 'badge ojt',
                            'Lulus'        => 'badge lulus',
                            'Tidak Lolos'  => 'badge gagal',
                            'Resign'       => 'badge resign',
                            default        => 'badge slate',
                        };

                        $statusBadge = match($t['status']) {
                            'Lulus' => 'badge disetujui',
                            'Aktif' => 'badge ojt',
                            default => 'badge ditolak'
                        };

                        $startDateTs = !empty($t['start_date']) ? strtotime($t['start_date']) : time();
                        $daysInTraining = max(1, (int)floor((time() - $startDateTs) / 86400));
                    ?>
                    <tr class="trainee-row" id="trainee-row-<?= $t['id'] ?>" 
                        data-name="<?= strtolower(esc($t['name'])) ?>" 
                        data-nik="<?= strtolower(esc($t['nik'])) ?>" 
                        data-rfid="<?= strtolower(esc($t['rfid_tag'] ?? '')) ?>"
                        data-stage="<?= strtolower(esc($t['stage'])) ?>">
                        <td>
                            <input type="checkbox" class="trainee-checkbox" value="<?= $t['id'] ?>">
                        </td>
                        <td>
                            <div class="fw-bold text-white"><?= esc($t['name']) ?></div>
                            <div class="mono" style="font-size:0.75rem;color:var(--muted);"><?= esc($t['nik']) ?> &bull; <?= esc($t['gender']) ?></div>
                        </td>
                        <td>
                            <span class="mono" style="font-size:0.8rem;background:var(--panel-2);border:1px solid var(--border);padding:3px 8px;border-radius:5px;color:var(--muted);">
                                <?= esc($t['rfid_tag'] ?: 'Belum Ada') ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge upskill">
                                <span class="dot"></span><?= esc($t['track'] ?: 'Belum Dipilih') ?>
                            </span>
                        </td>
                        <td>
                            <span style="font-size:0.84rem;color:var(--text);">
                                <?= esc($t['target_scope_big'] ?? 'TC') ?>
                                <?php if (!empty($t['target_scope_small'])): ?>
                                    <span style="color:var(--muted);">/ <?= esc($t['target_scope_small']) ?></span>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td>
                            <span class="<?= $stageBadge ?>">
                                <span class="dot"></span><?= esc($t['stage']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="mono fw-bold text-info"><?= $daysInTraining ?> Hari</span>
                            <div class="mono" style="font-size:0.72rem;color:var(--muted);">Masuk: <?= !empty($t['start_date']) ? date('d/m/Y', strtotime($t['start_date'])) : '-' ?></div>
                        </td>
                        <td>
                            <span class="<?= $statusBadge ?>">
                                <span class="dot"></span><?= esc($t['status']) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <a href="<?= base_url('trainees/detail/' . $t['id']) ?>" class="btn btn-ghost" style="padding:4px 9px;font-size:0.78rem;" title="Lihat Detail & Evaluasi">
                                    <i class="bi bi-eye-fill"></i> Detail
                                </a>

                                <?php if ($current_role === 'superadmin'): ?>
                                    <button type="button" class="btn btn-ghost del-btn" style="padding:4px 8px;font-size:0.78rem;" 
                                        onclick="confirmDeleteTrainee(<?= $t['id'] ?>, '<?= esc($t['name']) ?>')" title="Hapus">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<p class="note-box" style="margin-top:16px;">
    <strong>Catatan Alur:</strong> Setiap data trainee yang ditambahkan akan otomatis memulai dari tahap <strong>Induction</strong>. Gunakan modul <strong>Input Data Harian</strong> untuk mengisi nilai Motoric Test, Cycle Time Hard Skill, dan Evaluasi OJT.
</p>

<!-- Modal: Tambah Trainee Baru -->
<?php if ($current_role === 'superadmin'): ?>
<div class="modal fade" id="modalAddTrainee" tabindex="-1" aria-labelledby="modalAddTraineeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <form action="<?= base_url('trainees/create') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="modalAddTraineeLabel">
                        <i class="bi bi-person-plus-fill me-2" style="color:var(--amber);"></i> Tambah Trainee Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Nama Lengkap Trainee *</label>
                        <input type="text" name="name" class="w-100" placeholder="Contoh: Muhammad Budi" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">NIK Karyawan *</label>
                            <input type="text" name="nik" class="w-100 font-monospace" placeholder="Contoh: 24080123" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Jenis Kelamin *</label>
                            <select name="gender" class="w-100" required>
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Kode ID Card (Tap / Scan)</label>
                        <input type="text" name="rfid_tag" class="w-100 font-monospace" placeholder="Tap ID Card atau ketik kode ID Card...">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Tanggal Masuk (Start Date) *</label>
                            <input type="date" name="start_date" class="w-100" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Departemen Tujuan</label>
                            <select name="department" class="w-100">
                                <option value="Production">Production</option>
                                <option value="Lean">Lean</option>
                                <option value="Quality">Quality</option>
                                <option value="Warehouse">Warehouse</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Scope Besar (Lokasi)</label>
                            <select name="target_scope_big" class="w-100">
                                <?php if (!empty($factory_list)): ?>
                                    <?php foreach ($factory_list as $fItem): ?>
                                        <option value="<?= esc($fItem) ?>"><?= esc($fItem) ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="Factory 1 (F1)">Factory 1 (F1)</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Scope Kecil (Cell / Line)</label>
                            <input type="text" name="target_scope_small" class="w-100" placeholder="Contoh: Cell 3 / Line 12">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Catatan Tambahan</label>
                        <textarea name="notes" rows="2" class="w-100" placeholder="Keterangan awal trainee..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Trainee</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form Hidden untuk Delete Trainee -->
<form id="formDeleteTrainee" action="" method="POST" style="display:none;">
    <?= csrf_field() ?>
</form>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rfidInput = document.getElementById('traineeRfidInput');
    const rfidFeedback = document.getElementById('traineeRfidFeedback');
    const selectAll = document.getElementById('traineeSelectAll');
    const checkboxes = document.querySelectorAll('.trainee-checkbox');
    const rows = document.querySelectorAll('.trainee-row');

    // Live search on RFID/Name input
    if (rfidInput) {
        rfidInput.addEventListener('input', function() {
            const val = (rfidInput.value || '').toLowerCase().trim();
            let matchCount = 0;

            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const nik = row.getAttribute('data-nik') || '';
                const rfid = row.getAttribute('data-rfid') || '';

                if (!val || name.includes(val) || nik.includes(val) || rfid.includes(val)) {
                    row.style.display = '';
                    matchCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (val && rfidFeedback) {
                rfidFeedback.textContent = `Ditemukan ${matchCount} peserta cocok dengan "${val}".`;
            } else if (rfidFeedback) {
                rfidFeedback.textContent = '';
            }
        });
    }

    // Select all checkboxes
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
        });
    }
});

function confirmDeleteTrainee(id, name) {
    if (confirm(`Apakah Anda yakin ingin menghapus data trainee "${name}"? Tindakan ini tidak dapat dibatalkan.`)) {
        const form = document.getElementById('formDeleteTrainee');
        form.action = `<?= base_url('trainees/delete/') ?>` + id;
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>
