<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="dash-toolbar mb-2">
    <h2 class="fw-bold text-white mb-1">Input Data Harian (Massal)</h2>
</div>
<p class="note-box" style="margin-top:0;margin-bottom:26px;">
    Data seperti cycle time tetap unik per orang dan harus diisi satu per satu — tapi di sini semua peserta pada tahap yang sama ditampilkan dalam satu tabel, jadi tidak perlu bolak-balik membuka detail masing-masing orang.
</p>

<!-- ID Card Scanner / Jumper Box matching training-tracker (1).html -->
<div style="background:var(--panel);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-sm);padding:20px;margin-bottom:30px;">
    <label for="rfidScannerInput" style="font-size:0.82rem;color:var(--muted);display:block;margin-bottom:8px;">
        Tap ID Card peserta untuk langsung lompat &amp; menyorot barisnya di tabel yang sesuai (Motoric Test / Training Hard Skill / OJT), tanpa perlu scroll manual.
    </label>
    <input type="text" id="rfidScannerInput" placeholder="Klik di sini, lalu tap ID Card peserta…" autocomplete="off"
        style="width:100%;font-size:1rem;padding:14px;border:2px dashed var(--border);border-radius:8px;font-family:'IBM Plex Mono',monospace;text-align:center;background:var(--panel-2);">
    <div id="rfidScanFeedback" style="margin-top:10px;min-height:20px;font-size:0.86rem;font-weight:500;color:var(--amber);"></div>
</div>

<!-- TABEL 1: MOTORIC TEST -->
<div class="sub-heading">
    <span class="dot" style="background:var(--violet);width:8px;height:8px;border-radius:50%;display:inline-block;"></span>
    Motoric Test <span class="count mono" id="miCountMotoric"><?= count($motoric_trainees) ?></span>
</div>

<div class="table-scroll mb-4">
    <table>
        <thead>
            <tr>
                <th style="width: 22%;">Nama / ID</th>
                <th style="width: 14%;">Target CT (detik)</th>
                <th style="width: 16%;">Aktual CT (detik)</th>
                <th style="width: 18%;">Keterangan</th>
                <th style="width: 15%;">Pilih Track</th>
                <th style="width: 15%;" class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($motoric_trainees)): ?>
                <tr>
                    <td colspan="6" class="empty-state">Tidak ada peserta di tahap Motoric Test saat ini.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($motoric_trainees as $t): ?>
                    <tr id="row-trainee-<?= $t['id'] ?>" class="daily-input-row" data-rfid="<?= esc($t['rfid_tag'] ?? '') ?>" data-nik="<?= esc($t['nik']) ?>">
                        <form action="<?= base_url('daily-input/motoric') ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="trainee_id" value="<?= $t['id'] ?>">

                            <td>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-bold text-white"><?= esc($t['name']) ?></span>
                                    <?php if (!empty($t['has_attended_today'])): ?>
                                        <span class="badge" style="background:rgba(16,185,129,0.15);color:#10b981;border:1px solid rgba(16,185,129,0.4);font-size:0.68rem;padding:2px 6px;" title="Tercatat Hadir Jam <?= esc($t['attendance_time_in'] ?? '') ?>">
                                            <i class="bi bi-check-circle-fill me-1"></i>Hadir (<?= esc(substr($t['attendance_time_in'] ?? '', 0, 5)) ?>)
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="mono" style="font-size:0.75rem;color:var(--muted);">
                                    <?= esc($t['nik']) ?> &bull; <span style="color:var(--amber);"><?= esc($t['rfid_tag'] ?: '-') ?></span>
                                </div>
                            </td>
                            <!-- Item 7: Target CT Motoric Test -->
                            <td>
                                <input type="number" step="0.1" name="target_cycle_time" class="w-100 mono text-center text-muted" value="40.0" title="Target Benchmark (< 40.0s untuk Sewing)" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="cycle_time" id="ct_<?= $t['id'] ?>" class="w-100 mono text-warning fw-bold" placeholder="Detik.." value="<?= esc($t['cycle_time_motoric'] ?? '') ?>" required oninput="handleMotoricTimeChange(<?= $t['id'] ?>)">
                            </td>
                            <td>
                                <input type="text" name="notes" class="w-100" placeholder="Catatan motorik..." value="<?= esc($t['motoric_notes'] ?? '') ?>">
                            </td>
                            <td>
                                <select name="track" id="track_<?= $t['id'] ?>" class="w-100" required>
                                    <option value="Sewing" <?= ($t['track'] === 'Sewing') ? 'selected' : '' ?>>Sewing</option>
                                    <option value="Cutting" <?= ($t['track'] === 'Cutting') ? 'selected' : '' ?>>Cutting</option>
                                    <option value="Assembly" <?= ($t['track'] === 'Assembly') ? 'selected' : '' ?>>Assembly</option>
                                    <option value="Inhouse" <?= ($t['track'] === 'Inhouse') ? 'selected' : '' ?>>Inhouse</option>
                                </select>
                                <small id="warn_<?= $t['id'] ?>" class="text-danger d-none" style="font-size: 0.72rem;">* &ge;40s Sewing tidak tersedia</small>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <button type="submit" name="next_action" value="stay" class="btn btn-ghost" style="padding:6px 10px;font-size:0.78rem;" title="Simpan hasil tes">
                                        Simpan
                                    </button>
                                    <button type="submit" name="next_action" value="advance" class="btn btn-primary" style="padding:6px 12px;font-size:0.78rem;" title="Simpan & Lanjutkan ke Hard Skill">
                                        Ke Hard Skill →
                                    </button>
                                </div>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- TABEL 2: TRAINING HARD SKILL -->
<div class="sub-heading mt-5" id="tab-hardskill">
    <span class="dot" style="background:var(--amber);width:8px;height:8px;border-radius:50%;display:inline-block;"></span>
    Training Hard Skill — Evaluasi Harian &amp; Kepatuhan SOP <span class="count mono" id="miCountTraining"><?= count($hardskill_trainees) ?></span>
</div>

<div class="table-scroll mb-4">
    <table>
        <thead>
            <tr>
                <th style="min-width: 170px;">Nama / ID</th>
                <th style="min-width: 90px;">Track</th>
                <th style="min-width: 90px;">Progres</th>
                <th style="min-width: 100px;">Target CT (s)</th>
                <th style="min-width: 100px;">Aktual CT (s)</th>
                <th style="min-width: 85px;">Pasang Cek</th>
                <th style="min-width: 75px;">Cacat</th>
                <th style="min-width: 100px;">Jumlah Langkah</th>
                <th style="min-width: 105px;">Langkah Terlewat</th>
                <th style="min-width: 85px;">Skor Mutu</th>
                <th style="min-width: 150px;" class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($hardskill_trainees)): ?>
                <tr>
                    <td colspan="11" class="empty-state">Tidak ada peserta di tahap Training Hard Skill saat ini.</td>
                </tr>
            <?php else: ?>
                <?php
                $evalModel = new \App\Models\DailyEvaluationModel();
                ?>
                <?php foreach ($hardskill_trainees as $t): ?>
                    <?php
                    // Item 9: Auto Progress calculation (Day Number)
                    $prevLogs = $evalModel->where('trainee_id', $t['id'])->where('stage', 'Hard Skill')->countAllResults();
                    $autoDayProgress = $prevLogs + 1;
                    ?>
                    <tr id="row-trainee-<?= $t['id'] ?>" class="daily-input-row" data-rfid="<?= esc($t['rfid_tag'] ?? '') ?>" data-nik="<?= esc($t['nik']) ?>">
                        <form action="<?= base_url('daily-input/hardskill') ?>" method="POST" id="form-hardskill-<?= $t['id'] ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="trainee_id" value="<?= $t['id'] ?>">
                            <input type="hidden" name="advance_to_ojt" id="advance_to_ojt_<?= $t['id'] ?>" value="no">
                            <input type="hidden" name="sop_score" id="sop_score_hs_<?= $t['id'] ?>" value="100">
                            <input type="hidden" name="sop_compliance" id="sop_compliance_hs_<?= $t['id'] ?>" value="Sesuai SOP (100%)">
                            
                            <!-- Item 13: Hidden OJT placement data set by modal -->
                            <input type="hidden" name="ojt_factory" id="ojt_factory_<?= $t['id'] ?>" value="Factory 1 (F1)">
                            <input type="hidden" name="ojt_cell" id="ojt_cell_<?= $t['id'] ?>" value="Line 01">
                            <input type="hidden" name="ojt_process" id="ojt_process_<?= $t['id'] ?>" value="<?= esc($t['track']) ?>">

                            <td>
                                <div class="fw-bold text-white"><?= esc($t['name']) ?></div>
                                <div class="mono" style="font-size:0.75rem;color:var(--muted);"><?= esc($t['nik']) ?></div>
                            </td>
                            <td>
                                <span class="badge upskill"><span class="dot"></span><?= esc($t['track']) ?></span>
                            </td>
                            <!-- Item 9: Auto Progress Ke-N -->
                            <td>
                                <span class="badge bg-secondary font-monospace" title="Progres terhitung otomatis dari riwayat">Hari ke-<?= $autoDayProgress ?></span>
                            </td>
                            <td>
                                <input type="number" step="0.1" name="target_cycle_time" class="w-100 mono text-center text-muted" value="35.0" title="Target Standar" style="padding:6px 4px;" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="cycle_time" class="w-100 mono text-warning fw-bold text-center" placeholder="Detik.." style="padding:6px 4px;" required>
                            </td>
                            <td>
                                <input type="number" name="pairs_checked" id="pairs_checked_hs_<?= $t['id'] ?>" class="w-100 mono text-center" min="1" max="100" value="5" title="Standar 5 pasang" style="padding:6px 4px;" oninput="calculateEvalScore('hs', <?= $t['id'] ?>)" required>
                            </td>
                            <td>
                                <input type="number" name="defect_count" id="defect_count_hs_<?= $t['id'] ?>" class="w-100 mono text-center text-danger" min="0" max="100" value="0" title="Jumlah Cacat" style="padding:6px 4px;" oninput="calculateEvalScore('hs', <?= $t['id'] ?>)" required>
                            </td>
                            <td>
                                <input type="number" name="total_steps" id="total_steps_hs_<?= $t['id'] ?>" class="w-100 mono text-center" min="1" max="100" value="10" title="Jumlah Langkah SOP" style="padding:6px 4px;" oninput="calculateEvalScore('hs', <?= $t['id'] ?>)" required>
                            </td>
                            <td>
                                <input type="number" name="missed_steps" id="missed_steps_hs_<?= $t['id'] ?>" class="w-100 mono text-center text-warning" min="0" max="100" value="0" title="Langkah SOP Terlewat" style="padding:6px 4px;" oninput="calculateEvalScore('hs', <?= $t['id'] ?>)" required>
                            </td>
                            <td>
                                <input type="number" name="quality_score" id="quality_score_hs_<?= $t['id'] ?>" class="w-100 mono text-center text-success fw-bold" min="0" max="100" value="100" title="Skor Mutu / SOP" style="padding:6px 4px; background:var(--panel-2);" readonly required>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <button type="submit" class="btn btn-ghost" style="padding:5px 8px;font-size:0.78rem;" title="Simpan evaluasi hari ini">
                                        Simpan
                                    </button>
                                    <!-- Item 12 & 13: Promosikan ke OJT dengan Modal Pop-up Penempatan -->
                                    <button type="button" class="btn btn-primary" style="padding:5px 10px;font-size:0.78rem;" onclick="openPromoteOjtModal(<?= $t['id'] ?>, '<?= esc($t['name']) ?>', '<?= esc($t['nik']) ?>', '<?= esc($t['track']) ?>')">
                                        Mulai OJT →
                                    </button>
                                </div>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- TABEL 3: ON JOB TRAINING (OJT) -->
<div class="sub-heading mt-5" id="tab-ojt">
    <span class="dot" style="background:var(--blue);width:8px;height:8px;border-radius:50%;display:inline-block;"></span>
    On Job Training — Evaluasi Hari Ini <span class="count mono" id="miCountOjt"><?= count($ojt_trainees) ?></span>
</div>

<div class="table-scroll mb-4">
    <table>
        <thead>
            <tr>
                <th style="min-width: 170px;">Nama / ID</th>
                <th style="min-width: 90px;">Progres</th>
                <th style="min-width: 100px;">Target CT (s)</th>
                <th style="min-width: 100px;">Aktual CT (s)</th>
                <th style="min-width: 85px;">Pasang Cek</th>
                <th style="min-width: 75px;">Cacat</th>
                <th style="min-width: 100px;">Jumlah Langkah</th>
                <th style="min-width: 105px;">Langkah Terlewat</th>
                <th style="min-width: 85px;">Skor Mutu</th>
                <th style="min-width: 150px;" class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ojt_trainees)): ?>
                <tr>
                    <td colspan="10" class="empty-state">Tidak ada peserta di tahap On Job Training saat ini.</td>
                </tr>
            <?php else: ?>
                <?php
                $evalModel = new \App\Models\DailyEvaluationModel();
                ?>
                <?php foreach ($ojt_trainees as $t): ?>
                    <?php
                    $prevOjtLogs = $evalModel->where('trainee_id', $t['id'])->where('stage', 'OJT')->countAllResults();
                    $autoOjtProgress = $prevOjtLogs + 1;
                    ?>
                    <tr id="row-trainee-<?= $t['id'] ?>" class="daily-input-row" data-rfid="<?= esc($t['rfid_tag'] ?? '') ?>" data-nik="<?= esc($t['nik']) ?>">
                        <form action="<?= base_url('daily-input/ojt') ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="trainee_id" value="<?= $t['id'] ?>">
                            <input type="hidden" name="sop_score" id="sop_score_ojt_<?= $t['id'] ?>" value="100">
                            <input type="hidden" name="work_method" id="work_method_ojt_<?= $t['id'] ?>" value="Sesuai SOP (100%)">

                            <td>
                                <div class="fw-bold text-white"><?= esc($t['name']) ?></div>
                                <div class="mono" style="font-size:0.75rem;color:var(--muted);"><?= esc($t['nik']) ?> &bull; Track: <?= esc($t['track']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark font-monospace">Hari ke-<?= $autoOjtProgress ?></span>
                            </td>
                            <td>
                                <input type="number" step="0.1" name="target_cycle_time" class="w-100 mono text-center text-muted" value="35.0" title="Target Standar" style="padding:6px 4px;" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="cycle_time" class="w-100 mono text-warning fw-bold text-center" placeholder="Aktual (s)" style="padding:6px 4px;" required>
                            </td>
                            <td>
                                <input type="number" name="pairs_checked" id="pairs_checked_ojt_<?= $t['id'] ?>" class="w-100 mono text-center" min="1" max="100" value="5" title="Standar 5 pasang" style="padding:6px 4px;" oninput="calculateEvalScore('ojt', <?= $t['id'] ?>)" required>
                            </td>
                            <td>
                                <input type="number" name="defect_count" id="defect_count_ojt_<?= $t['id'] ?>" class="w-100 mono text-center text-danger" min="0" max="100" value="0" title="Jumlah Cacat" style="padding:6px 4px;" oninput="calculateEvalScore('ojt', <?= $t['id'] ?>)" required>
                            </td>
                            <td>
                                <input type="number" name="total_steps" id="total_steps_ojt_<?= $t['id'] ?>" class="w-100 mono text-center" min="1" max="100" value="10" title="Jumlah Langkah SOP" style="padding:6px 4px;" oninput="calculateEvalScore('ojt', <?= $t['id'] ?>)" required>
                            </td>
                            <td>
                                <input type="number" name="missed_steps" id="missed_steps_ojt_<?= $t['id'] ?>" class="w-100 mono text-center text-warning" min="0" max="100" value="0" title="Langkah SOP Terlewat" style="padding:6px 4px;" oninput="calculateEvalScore('ojt', <?= $t['id'] ?>)" required>
                            </td>
                            <td>
                                <input type="number" name="quality_score" id="quality_score_ojt_<?= $t['id'] ?>" class="w-100 mono text-center text-success fw-bold" min="0" max="100" value="100" title="Skor Mutu" style="padding:6px 4px; background:var(--panel-2);" readonly required>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <button type="submit" name="final_status" value="" class="btn btn-ghost" style="padding:6px 10px;font-size:0.78rem;">
                                        Simpan
                                    </button>
                                    <button type="submit" name="final_status" value="Lulus" class="btn btn-primary" style="padding:6px 12px;font-size:0.78rem;background:var(--green);color:#05070a;" title="Luluskan OJT ke Penempatan">
                                        Lulus OJT ✓
                                    </button>
                                </div>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Item 13 Modal Pop-up: Penempatan OJT (Factory, Cell, Process) -->
<div class="modal fade" id="modalPromoteOjt" tabindex="-1" aria-labelledby="modalPromoteOjtLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="modalPromoteOjtLabel">
                    <i class="bi bi-box-arrow-up-right text-warning me-2"></i> Penempatan OJT Operator Trainee
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 rounded-2 mb-3" style="background:var(--panel-2);border:1px solid var(--border);">
                    <h6 class="fw-bold text-white mb-0" id="promoteModalTraineeName">Nama Trainee</h6>
                    <small class="text-warning font-monospace" id="promoteModalTraineeNik">NIK: -</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">Lokasi Pabrik OJT (Factory) *</label>
                    <select id="modalOjtFactory" class="w-100" required>
                        <?php if (!empty($factory_list)): ?>
                            <?php foreach ($factory_list as $fItem): ?>
                                <option value="<?= esc($fItem) ?>"><?= esc($fItem) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="Factory 1 (F1)">Factory 1 (F1)</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">Cell / Line Tujuan *</label>
                    <input type="text" id="modalOjtCell" class="w-100" placeholder="Contoh: Line 02 / Sewing Cell 1" value="Line 01" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted">Proses / Skill Line OJT *</label>
                    <input type="text" id="modalOjtProcess" class="w-100" placeholder="Contoh: Upper Sewing, Stitching, Back Counter" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="confirmPromoteOjt()">
                    <i class="bi bi-check-circle me-1"></i> Lanjutkan Ke OJT
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let activePromoteTraineeId = null;

function calculateEvalScore(prefix, id) {
    const checkedInput = document.getElementById('pairs_checked_' + prefix + '_' + id);
    const defectInput = document.getElementById('defect_count_' + prefix + '_' + id);
    const totalStepsInput = document.getElementById('total_steps_' + prefix + '_' + id);
    const missedStepsInput = document.getElementById('missed_steps_' + prefix + '_' + id);
    const qualityInput = document.getElementById('quality_score_' + prefix + '_' + id);
    const sopScoreInput = document.getElementById('sop_score_' + prefix + '_' + id);
    const sopComplianceInput = document.getElementById('sop_compliance_' + prefix + '_' + id);
    const workMethodInput = document.getElementById('work_method_' + prefix + '_' + id);

    const checked = parseFloat(checkedInput ? checkedInput.value : 5) || 5;
    const defect = parseFloat(defectInput ? defectInput.value : 0) || 0;
    const totalSteps = parseFloat(totalStepsInput ? totalStepsInput.value : 10) || 10;
    const missedSteps = parseFloat(missedStepsInput ? missedStepsInput.value : 0) || 0;

    // 1. Mutu Produk (Pasang Cek & Cacat)
    const qualityScore = Math.max(0, Math.min(100, Math.round(((checked - defect) / Math.max(1, checked)) * 100)));
    if (qualityInput) {
        qualityInput.value = qualityScore;
    }

    // 2. Kepatuhan SOP (Jumlah Langkah & Langkah Terlewat)
    const sopScore = Math.max(0, Math.min(100, Math.round(((totalSteps - missedSteps) / Math.max(1, totalSteps)) * 100)));
    if (sopScoreInput) {
        sopScoreInput.value = sopScore;
    }

    let sopText = 'Sesuai SOP (100%)';
    if (sopScore < 100 && sopScore >= 80) {
        sopText = 'Ada Penyesuaian (' + sopScore + '%)';
    } else if (sopScore < 80) {
        sopText = 'Kurang Sesuai SOP (' + sopScore + '%)';
    }

    if (sopComplianceInput) {
        sopComplianceInput.value = sopText;
    }
    if (workMethodInput) {
        workMethodInput.value = sopText;
    }
}

function calculateOjtQuality(id) {
    calculateEvalScore('ojt', id);
}

function handleMotoricTimeChange(id) {
    const ctInput = document.getElementById('ct_' + id);
    const trackSelect = document.getElementById('track_' + id);
    const warnSpan = document.getElementById('warn_' + id);

    if (!ctInput || !trackSelect) return;
    const timeVal = parseFloat(ctInput.value);

    if (timeVal >= 40) {
        if (trackSelect.value === 'Sewing') {
            trackSelect.value = 'Cutting';
        }
        Array.from(trackSelect.options).forEach(opt => {
            if (opt.value === 'Sewing') opt.disabled = true;
        });
        if (warnSpan) warnSpan.classList.remove('d-none');
    } else {
        Array.from(trackSelect.options).forEach(opt => opt.disabled = false);
        if (warnSpan) warnSpan.classList.add('d-none');
    }
}

function openPromoteOjtModal(id, name, nik, track) {
    activePromoteTraineeId = id;
    const nameEl = document.getElementById('promoteModalTraineeName');
    const nikEl = document.getElementById('promoteModalTraineeNik');
    const procEl = document.getElementById('modalOjtProcess');

    if (nameEl) nameEl.textContent = name;
    if (nikEl) nikEl.textContent = 'NIK: ' + nik;
    if (procEl) procEl.value = track || 'Sewing Upper';

    const modal = new bootstrap.Modal(document.getElementById('modalPromoteOjt'));
    modal.show();
}

function confirmPromoteOjt() {
    if (!activePromoteTraineeId) return;

    const factory = document.getElementById('modalOjtFactory').value;
    const cell = document.getElementById('modalOjtCell').value;
    const process = document.getElementById('modalOjtProcess').value;

    document.getElementById('advance_to_ojt_' + activePromoteTraineeId).value = 'yes';
    document.getElementById('ojt_factory_' + activePromoteTraineeId).value = factory;
    document.getElementById('ojt_cell_' + activePromoteTraineeId).value = cell;
    document.getElementById('ojt_process_' + activePromoteTraineeId).value = process;

    const form = document.getElementById('form-hardskill-' + activePromoteTraineeId);
    if (form) form.submit();
}

document.addEventListener('DOMContentLoaded', function() {
    // Item 12: Hash check to focus OJT tab automatically
    if (window.location.hash === '#tab-ojt') {
        const ojtHeading = document.getElementById('tab-ojt');
        if (ojtHeading) {
            ojtHeading.scrollIntoView({ behavior: 'smooth' });
        }
    }

    const rfidInput = document.getElementById('rfidScannerInput');
    const rfidFeedback = document.getElementById('rfidScanFeedback');
    const rows = document.querySelectorAll('.daily-input-row');

    if (rfidInput) {
        rfidInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const code = (rfidInput.value || '').trim();
                if (!code) return;

                let foundRow = null;
                rows.forEach(r => {
                    const rfid = r.getAttribute('data-rfid') || '';
                    const nik = r.getAttribute('data-nik') || '';
                    if (rfid.toLowerCase() === code.toLowerCase() || nik.toLowerCase() === code.toLowerCase()) {
                        foundRow = r;
                    }
                });

                if (foundRow) {
                    foundRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    foundRow.style.outline = '2px solid var(--amber)';
                    foundRow.style.background = 'var(--panel-2)';
                    setTimeout(() => {
                        foundRow.style.outline = '';
                    }, 4000);

                    if (rfidFeedback) {
                        rfidFeedback.textContent = `✓ Ditemukan baris untuk peserta [${code}]! Fokus diarahkan.`;
                    }
                } else {
                    if (rfidFeedback) {
                        rfidFeedback.textContent = `Peserta dengan ID Card [${code}] tidak ditemukan di tabel Motoric/Hard Skill/OJT.`;
                    }
                }
                rfidInput.value = '';
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
