<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="dash-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="<?= base_url('trainees') ?>" class="btn btn-ghost">
            ← Kembali ke Daftar
        </a>
        <div>
            <h2 class="fw-bold text-white mb-0"><?= esc($trainee['name']) ?></h2>
            <div class="mono" style="font-size:0.8rem;color:var(--muted);">NIK: <?= esc($trainee['nik']) ?> &bull; Tanggal Masuk: <?= esc($trainee['start_date']) ?></div>
        </div>
    </div>

    <?php if ($current_role === 'superadmin'): ?>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditTrainee">
            <i class="bi bi-pencil-square me-1"></i> Edit Data
        </button>
    <?php endif; ?>
</div>

<div class="row g-4">
    <!-- Left Column: Trainee Profile Card & Current Status -->
    <div class="col-lg-4">
        <div class="pwi-card mb-4">
            <div class="text-center pb-3 border-bottom mb-3" style="border-color:var(--border) !important;">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                    style="width:68px;height:68px;font-size:26px;background:var(--amber);color:#05070a;font-weight:700;">
                    <?= strtoupper(substr($trainee['name'], 0, 1)) ?>
                </div>
                <h4 class="fw-bold text-white mb-1"><?= esc($trainee['name']) ?></h4>
                <div class="badge slate mb-2"><?= esc($trainee['gender']) ?></div>

                <div class="p-2 rounded-3 text-center mono mt-2" style="background:var(--panel-2);border:1px solid var(--border);">
                    <span style="color:var(--muted);font-size:0.75rem;">RFID Tag:</span>
                    <span class="fw-bold text-white ms-1"><?= esc($trainee['rfid_tag'] ?? 'Belum ada RFID') ?></span>
                </div>
            </div>

            <?php
            $stageBadge = match($trainee['stage']) {
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
            ?>

            <?php
                $startTs = !empty($trainee['start_date']) ? strtotime($trainee['start_date']) : time();
                $daysTotal = max(1, (int)floor((time() - $startTs) / 86400));
            ?>

            <div class="d-flex flex-column gap-2" style="font-size:0.86rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <span style="color:var(--muted);">Tahap Saat Ini:</span>
                    <span class="<?= $stageBadge ?>"><span class="dot"></span><?= esc($trainee['stage']) ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span style="color:var(--muted);">Lama Training:</span>
                    <span class="mono fw-bold text-info"><?= $daysTotal ?> Hari <small class="text-muted" style="font-weight:normal;">(sejak <?= date('d/m/Y', $startTs) ?>)</small></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span style="color:var(--muted);">Track Spesialisasi:</span>
                    <span class="fw-semibold text-white"><?= esc($trainee['track'] ?: 'Belum ditentukan') ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span style="color:var(--muted);">Departemen:</span>
                    <span class="fw-semibold text-white"><?= esc($trainee['department']) ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span style="color:var(--muted);">Lokasi Penempatan:</span>
                    <span class="fw-semibold text-white"><?= esc($trainee['target_scope_big'] ?? 'TC') ?> <?= !empty($trainee['target_scope_small']) ? '(' . esc($trainee['target_scope_small']) . ')' : '' ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span style="color:var(--muted);">Status:</span>
                    <span class="badge <?= ($trainee['status'] === 'Lulus') ? 'lulus' : (($trainee['status'] === 'Aktif') ? 'ojt' : 'gagal') ?>">
                        <span class="dot"></span><?= esc($trainee['status']) ?>
                    </span>
                </div>
                <div class="d-flex justify-content-between">
                    <span style="color:var(--muted);">Cycle Time Motorik:</span>
                    <span class="mono fw-bold text-warning"><?= $trainee['cycle_time_motoric'] ? esc($trainee['cycle_time_motoric']) . 's' : '-' ?></span>
                </div>
            </div>

            <?php if (!empty($trainee['notes'])): ?>
                <div class="mt-3 pt-3 border-top" style="border-color:var(--border) !important;">
                    <div class="small fw-semibold text-white mb-1">Catatan:</div>
                    <div class="small p-2 rounded" style="background:var(--panel-2);color:var(--muted);border:1px solid var(--border);"><?= esc($trainee['notes']) ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Training Progress, Daily Evaluations & Attendances -->
    <div class="col-lg-8">
        <!-- 1. Induction Curriculum / Materi Induksi Summary -->
        <div class="pwi-card mb-4" style="border-left: 4px solid var(--blue);">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-mortarboard-fill" style="color:var(--blue);"></i>
                        Materi &amp; Modul Induksi yang Diterima
                    </h5>
                    <small style="color:var(--muted);">Tracking pemahaman teori dasar, K3 pabrik, dan budaya mutu Parkland.</small>
                </div>
                <?php if (in_array($current_role, ['superadmin', 'trainer'])): ?>
                    <button type="button" class="btn btn-ghost" style="font-size:0.8rem;padding:5px 10px;" data-bs-toggle="modal" data-bs-target="#modalInductionChecklist">
                        <i class="bi bi-pencil-square me-1"></i> Update Checklist
                    </button>
                <?php endif; ?>
            </div>

            <?php 
            $totalModules = count($induction_modules);
            $completedCount = count($completed_induction);
            $progressPercent = $totalModules > 0 ? round(($completedCount / $totalModules) * 100) : 0;
            ?>

            <div class="mb-3">
                <div class="d-flex justify-content-between small fw-semibold mb-1">
                    <span style="color:var(--muted);">Progress Kelengkapan Teori:</span>
                    <span class="mono" style="color:var(--blue);"><?= $completedCount ?> / <?= $totalModules ?> Modul (<?= $progressPercent ?>%)</span>
                </div>
                <div class="progress" style="height: 8px; background: var(--panel-2); border-radius: 4px; overflow:hidden;">
                    <div class="progress-bar" role="progressbar" style="width: <?= $progressPercent ?>%; background: var(--blue);"></div>
                </div>
            </div>

            <div class="row g-2">
                <?php foreach ($induction_modules as $code => $moduleTitle): ?>
                    <?php $isDone = in_array($code, $completed_induction) || in_array($moduleTitle, $completed_induction); ?>
                    <div class="col-md-6">
                        <div class="p-2 rounded-2 border d-flex align-items-center gap-2" 
                            style="background: <?= $isDone ? 'var(--blue-dim)' : 'var(--panel-2)' ?>; border-color: <?= $isDone ? 'rgba(91,157,245,0.3)' : 'var(--border)' ?> !important;">
                            <i class="bi <?= $isDone ? 'bi-check-circle-fill text-success fs-5' : 'bi-dash-circle text-muted fs-5' ?>"></i>
                            <div class="small lh-sm">
                                <div class="fw-bold <?= $isDone ? 'text-white' : 'text-muted' ?>"><?= esc($code) ?></div>
                                <div style="font-size: 0.76rem; color: var(--muted);"><?= esc($moduleTitle) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Topik Materi Training Selesai (Recorded via ID Card / Manual in Materi Training) -->
        <div class="pwi-card mb-4" style="border-left: 4px solid var(--amber);">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-book-half" style="color:var(--amber);"></i>
                        Topik Materi Training Selesai
                    </h5>
                    <small style="color:var(--muted);">Riwayat materi yang telah dipelajari &amp; ditandai via Tap ID Card / Trainer.</small>
                </div>
                <span class="badge rounded-pill mono" style="background:var(--panel-2);color:var(--amber);border:1px solid rgba(245,158,11,0.3);font-size:0.75rem;padding:4px 10px;">
                    <?= count($completed_topics ?? []) ?> Topik
                </span>
            </div>

            <?php if (empty($completed_topics)): ?>
                <div class="text-center py-3 text-muted rounded small" style="background:var(--panel-2);">
                    Peserta belum menandai penyelesaian materi topik training.
                </div>
            <?php else: ?>
                <div class="row g-2">
                    <?php foreach ($completed_topics as $ct): ?>
                        <div class="col-md-6">
                            <div class="p-2 rounded-2 border d-flex justify-content-between align-items-center" style="background:var(--panel-2);border-color:var(--border) !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <div>
                                        <div class="fw-bold text-white small"><?= esc($ct['topic_title']) ?></div>
                                        <div class="mono" style="font-size:0.72rem;color:var(--muted);"><?= date('d M Y, H:i', strtotime($ct['completed_at'])) ?> &bull; <?= esc($ct['marked_via']) ?></div>
                                    </div>
                                </div>
                                <span class="badge slate font-monospace" style="font-size:0.68rem;"><?= esc($ct['marked_by'] ?? 'Admin') ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 2. Motoric Test Summary -->
        <div class="pwi-card mb-4" style="border-left: 4px solid var(--violet);">
            <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-speedometer2" style="color: var(--violet);"></i>
                Hasil Motoric Test &amp; Keputusan Track
            </h5>

            <?php if ($trainee['cycle_time_motoric']): ?>
                <div class="row g-3 align-items-center">
                    <div class="col-md-4 text-center border-end" style="border-color:var(--border) !important;">
                        <div class="display-6 fw-bold mono text-warning"><?= esc($trainee['cycle_time_motoric']) ?><small class="fs-6 text-muted">s</small></div>
                        <div class="small" style="color:var(--muted);">Cycle Time Dicapai</div>
                    </div>
                    <div class="col-md-8">
                        <?php if ($trainee['cycle_time_motoric'] < 40): ?>
                            <div class="retry-note mb-2" style="background:var(--green-dim);border-color:rgba(52,211,153,0.3);color:var(--text);padding:10px 14px;">
                                <span class="ic" style="color:var(--green);">✓</span>
                                <div><strong>Memenuhi Syarat:</strong> Cycle time &lt; 40 detik. Berhak memilih track Sewing, Cutting, Assembly, atau Inhouse.</div>
                            </div>
                        <?php else: ?>
                            <div class="retry-note mb-2" style="padding:10px 14px;">
                                <span class="ic">!</span>
                                <div><strong>Keterangan:</strong> Cycle time &ge; 40 detik. Opsi Sewing tidak tersedia; diarahkan ke Cutting, Assembly, atau Inhouse.</div>
                            </div>
                        <?php endif; ?>
                        <div class="small" style="color:var(--muted);">Catatan: <?= esc($trainee['motoric_notes'] ?? 'Tidak ada catatan motoric.') ?></div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-3 text-muted rounded" style="background:var(--panel-2);">
                    Peserta belum melaksanakan Motoric Test.
                </div>
            <?php endif; ?>
        </div>

        <!-- 3. Daily Evaluation History (Item 11: Separated Hard Skill & OJT Results) -->
        <?php
        $hardSkillEvals = array_filter($evaluations, fn($e) => ($e['stage'] ?? '') === 'Hard Skill' || ($e['stage'] ?? '') !== 'OJT');
        $ojtEvals = array_filter($evaluations, fn($e) => ($e['stage'] ?? '') === 'OJT');
        ?>

        <!-- 3a. Hard Skill Evaluation Table -->
        <div class="pwi-card mb-4" style="border-left: 4px solid var(--amber);">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-tools" style="color: var(--amber);"></i> 
                    Riwayat Evaluasi Hard Skill (Training TC)
                </h5>
                <span class="badge training"><?= count($hardSkillEvals) ?> Evaluasi</span>
            </div>

            <?php if (empty($hardSkillEvals)): ?>
                <div class="text-center py-4 text-muted rounded" style="background:var(--panel-2);">
                    Belum ada riwayat evaluasi harian Hard Skill untuk peserta ini.
                </div>
            <?php else: ?>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>HARI KE</th>
                                <th>TANGGAL</th>
                                <th>TARGET CT</th>
                                <th>AKTUAL CT</th>
                                <th>PASANG CEK</th>
                                <th>CACAT</th>
                                <th>JML LANGKAH</th>
                                <th>TERLEWAT</th>
                                <th>SKOR MUTU</th>
                                <th>CATATAN / SOP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hardSkillEvals as $e): ?>
                                <tr>
                                    <td><span class="badge slate font-monospace">Hari ke-<?= esc($e['day_number'] ?? '1') ?></span></td>
                                    <td class="small fw-semibold"><?= esc($e['evaluation_date']) ?></td>
                                    <td class="mono small" style="color:var(--muted);">
                                        <?= !empty($e['target_cycle_time']) ? esc($e['target_cycle_time']) . 's' : '-' ?>
                                    </td>
                                    <td class="mono fw-bold <?= (!empty($e['target_cycle_time']) && $e['cycle_time'] <= $e['target_cycle_time']) ? 'text-success' : 'text-warning' ?>">
                                        <?= $e['cycle_time'] ? esc($e['cycle_time']) . 's' : '-' ?>
                                    </td>
                                    <td class="mono text-center"><?= esc($e['pairs_checked'] ?? '5') ?></td>
                                    <td class="mono text-center <?= (!empty($e['defect_count']) && $e['defect_count'] > 0) ? 'text-danger fw-bold' : 'text-muted' ?>">
                                        <?= esc($e['defect_count'] ?? '0') ?>
                                    </td>
                                    <td class="mono text-center"><?= esc($e['total_steps'] ?? '10') ?></td>
                                    <td class="mono text-center <?= (!empty($e['missed_steps']) && $e['missed_steps'] > 0) ? 'text-warning fw-bold' : 'text-muted' ?>">
                                        <?= esc($e['missed_steps'] ?? '0') ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= (($e['quality_score'] ?? 100) >= 85) ? 'lulus' : 'menunggu' ?>">
                                            <i class="bi bi-check2-circle me-1"></i><?= isset($e['quality_score']) ? esc($e['quality_score']) . '%' : '100%' ?>
                                        </span>
                                    </td>
                                    <td class="small" style="color:var(--muted);"><?= esc($e['notes'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- 3b. OJT Evaluation Table (Penilaian 5 Pasang) -->
        <div class="pwi-card mb-4" style="border-left: 4px solid var(--blue);">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-person-workspace" style="color: var(--blue);"></i> 
                    Riwayat Evaluasi OJT (Penilaian 5 Pasang Line Factory)
                </h5>
                <span class="badge ojt"><?= count($ojtEvals) ?> Evaluasi</span>
            </div>

            <?php if (empty($ojtEvals)): ?>
                <div class="text-center py-4 text-muted rounded" style="background:var(--panel-2);">
                    Belum ada riwayat evaluasi OJT 5-pasang untuk peserta ini.
                </div>
            <?php else: ?>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>TANGGAL</th>
                                <th>TARGET CT</th>
                                <th>AKTUAL CT</th>
                                <th>PASANG CEK</th>
                                <th>CACAT</th>
                                <th>JML LANGKAH</th>
                                <th>TERLEWAT</th>
                                <th>SKOR MUTU</th>
                                <th>PENILAIAN OJT &amp; CATATAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ojtEvals as $e): ?>
                                <tr>
                                    <td class="small fw-semibold"><?= esc($e['evaluation_date']) ?></td>
                                    <td class="mono small" style="color:var(--muted);">
                                        <?= !empty($e['target_cycle_time']) ? esc($e['target_cycle_time']) . 's' : '-' ?>
                                    </td>
                                    <td class="mono fw-bold <?= (!empty($e['target_cycle_time']) && $e['cycle_time'] <= $e['target_cycle_time']) ? 'text-success' : 'text-warning' ?>">
                                        <?= $e['cycle_time'] ? esc($e['cycle_time']) . 's' : '-' ?>
                                    </td>
                                    <td class="mono text-center"><?= esc($e['pairs_checked'] ?? '5') ?></td>
                                    <td class="mono text-center <?= (!empty($e['defect_count']) && $e['defect_count'] > 0) ? 'text-danger fw-bold' : 'text-muted' ?>">
                                        <?= esc($e['defect_count'] ?? '0') ?>
                                    </td>
                                    <td class="mono text-center"><?= esc($e['total_steps'] ?? '10') ?></td>
                                    <td class="mono text-center <?= (!empty($e['missed_steps']) && $e['missed_steps'] > 0) ? 'text-warning fw-bold' : 'text-muted' ?>">
                                        <?= esc($e['missed_steps'] ?? '0') ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= (($e['quality_score'] ?? 100) >= 85) ? 'lulus' : 'menunggu' ?>">
                                            <?= isset($e['quality_score']) ? esc($e['quality_score']) . '%' : '100%' ?>
                                        </span>
                                    </td>
                                    <td class="small" style="color:var(--muted);"><?= esc($e['notes'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Attendance Logs -->
        <div class="pwi-card" style="border-left: 4px solid var(--green);">
            <h5 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-calendar-check" style="color: var(--green);"></i> 
                Riwayat Kehadiran &amp; Tap ID Card
            </h5>

            <?php if (empty($attendances)): ?>
                <div class="text-center py-4 text-muted rounded" style="background:var(--panel-2);">
                    Belum ada riwayat absensi.
                </div>
            <?php else: ?>
                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>TANGGAL</th>
                                <th>JAM MASUK</th>
                                <th>JAM PULANG</th>
                                <th>STATUS</th>
                                <th>KETERANGAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendances as $att): ?>
                                <tr>
                                    <td class="small fw-semibold"><?= esc($att['attendance_date']) ?></td>
                                    <td class="mono small"><?= esc($att['time_in'] ?? '-') ?></td>
                                    <td class="mono small"><?= esc($att['time_out'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge <?= ($att['status'] === 'Hadir') ? 'lulus' : (($att['status'] === 'Ijin' || $att['status'] === 'Sakit') ? 'menunggu' : 'gagal') ?>">
                                            <span class="dot"></span><?= esc($att['status']) ?>
                                        </span>
                                    </td>
                                    <td class="small" style="color:var(--muted);"><?= esc($att['notes'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Edit Trainee (Superadmin) -->
<?php if ($current_role === 'superadmin'): ?>
<div class="modal fade" id="modalEditTrainee" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-dark">
            <form action="<?= base_url('trainees/update/' . $trainee['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title text-white">Edit Data Trainee</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">NIK</label>
                            <input type="text" name="nik" class="w-100 mono" value="<?= esc($trainee['nik']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Nama Lengkap</label>
                            <input type="text" name="name" class="w-100" value="<?= esc($trainee['name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Jenis Kelamin</label>
                            <select name="gender" class="w-100">
                                <option value="L" <?= ($trainee['gender'] === 'L' || $trainee['gender'] === 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= ($trainee['gender'] === 'P' || $trainee['gender'] === 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Departemen</label>
                            <input type="text" name="department" class="w-100" value="<?= esc($trainee['department']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">RFID Tag</label>
                            <input type="text" name="rfid_tag" class="w-100 mono" value="<?= esc($trainee['rfid_tag']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Tanggal Masuk</label>
                            <input type="date" name="start_date" class="w-100" value="<?= esc($trainee['start_date']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Tahap (Stage)</label>
                            <select name="stage" class="w-100">
                                <option value="Induction" <?= ($trainee['stage'] === 'Induction') ? 'selected' : '' ?>>Induction</option>
                                <option value="Motoric Test" <?= ($trainee['stage'] === 'Motoric Test') ? 'selected' : '' ?>>Motoric Test</option>
                                <option value="Hard Skill" <?= ($trainee['stage'] === 'Hard Skill') ? 'selected' : '' ?>>Hard Skill</option>
                                <option value="Upskill" <?= ($trainee['stage'] === 'Upskill') ? 'selected' : '' ?>>Upskill</option>
                                <option value="OJT" <?= ($trainee['stage'] === 'OJT') ? 'selected' : '' ?>>OJT</option>
                                <option value="Lulus" <?= ($trainee['stage'] === 'Lulus') ? 'selected' : '' ?>>Lulus</option>
                                <option value="Tidak Lolos" <?= ($trainee['stage'] === 'Tidak Lolos') ? 'selected' : '' ?>>Tidak Lolos</option>
                                <option value="Resign" <?= ($trainee['stage'] === 'Resign') ? 'selected' : '' ?>>Resign</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Track</label>
                            <select name="track" class="w-100">
                                <option value="Belum Ditentukan" <?= ($trainee['track'] === 'Belum Ditentukan' || empty($trainee['track'])) ? 'selected' : '' ?>>Belum Ditentukan</option>
                                <option value="Sewing" <?= ($trainee['track'] === 'Sewing') ? 'selected' : '' ?>>Sewing</option>
                                <option value="Cutting" <?= ($trainee['track'] === 'Cutting') ? 'selected' : '' ?>>Cutting</option>
                                <option value="Assembly" <?= ($trainee['track'] === 'Assembly') ? 'selected' : '' ?>>Assembly</option>
                                <option value="Inhouse" <?= ($trainee['track'] === 'Inhouse') ? 'selected' : '' ?>>Inhouse</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted">Catatan</label>
                            <textarea name="notes" class="w-100" rows="2"><?= esc($trainee['notes']) ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal Update Checklist Materi Induksi (Superadmin & Trainer) -->
<?php if (in_array($current_role, ['superadmin', 'trainer'])): ?>
<div class="modal fade" id="modalInductionChecklist" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <form action="<?= base_url('trainees/save-induction/' . $trainee['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title text-white"><i class="bi bi-mortarboard-fill me-2" style="color:var(--blue);"></i>Checklist Materi Induksi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small mb-3" style="color:var(--muted);">Centang materi induksi yang telah diselesaikan oleh peserta <strong><?= esc($trainee['name']) ?></strong>:</p>
                    
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($induction_modules as $code => $moduleTitle): ?>
                            <?php $isDone = in_array($code, $completed_induction) || in_array($moduleTitle, $completed_induction); ?>
                            <label class="p-2 border rounded d-flex align-items-center gap-3 cursor-pointer" 
                                style="background: <?= $isDone ? 'var(--blue-dim)' : 'var(--panel-2)' ?>; border-color: <?= $isDone ? 'rgba(91,157,245,0.3)' : 'var(--border)' ?> !important;">
                                <input type="checkbox" name="induction_materials[]" value="<?= esc($code) ?>" class="form-check-input mt-0" <?= $isDone ? 'checked' : '' ?>>
                                <div class="small">
                                    <span class="badge slate me-1"><?= esc($code) ?></span>
                                    <span class="fw-semibold text-white"><?= esc($moduleTitle) ?></span>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Checklist</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
