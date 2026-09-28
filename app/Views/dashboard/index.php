<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- 1. Hero Meta Header -->
<div class="dash-hero-meta">
    <div>
        <div class="dash-hero-eyebrow">PT PARKLAND WORLD INDONESIA — TRAINING CENTER</div>
        <h2>Ringkasan Training Center</h2>
        <p style="color:var(--muted);font-size:0.88rem;margin:4px 0 0;">Monitoring komprehensif peserta training, absensi harian, peminjaman operator, dan pencarian skill matrix.</p>
    </div>
    <div class="dash-hero-date">
        <i class="bi bi-clock-history" style="color:var(--amber);"></i>
        <span id="liveDateTimeString"><?= date('d F Y, H:i') ?> WIB</span>
    </div>
</div>

<!-- Dynamic Announcement Banner from CMS -->
<?php if (!empty($announcement['enabled'])): ?>
    <?php
    $typeColors = [
        'info'    => ['bg' => 'var(--blue-dim)', 'border' => 'rgba(91,157,245,0.3)', 'text' => 'var(--blue)', 'icon' => 'bi-info-circle-fill'],
        'warning' => ['bg' => 'var(--amber-dim)', 'border' => 'rgba(217,172,90,0.3)', 'text' => 'var(--amber)', 'icon' => 'bi-exclamation-triangle-fill'],
        'success' => ['bg' => 'var(--green-dim)', 'border' => 'rgba(52,211,153,0.3)', 'text' => 'var(--green)', 'icon' => 'bi-check-circle-fill'],
        'danger'  => ['bg' => 'var(--red-dim)', 'border' => 'rgba(248,113,113,0.3)', 'text' => 'var(--red)', 'icon' => 'bi-exclamation-octagon-fill'],
    ];
    $c = $typeColors[$announcement['type'] ?? 'info'] ?? $typeColors['info'];
    ?>
    <div class="p-3 rounded-2 mb-4 d-flex align-items-start gap-3" style="background: <?= $c['bg'] ?>; border: 1px solid <?= $c['border'] ?>; box-shadow: var(--shadow-sm);">
        <i class="bi <?= $c['icon'] ?> fs-4" style="color: <?= $c['text'] ?>; line-height: 1;"></i>
        <div class="flex-grow-1">
            <div class="fw-bold" style="color: <?= $c['text'] ?>; font-size: 0.95rem; margin-bottom: 2px;">
                <?= esc($announcement['title']) ?>
            </div>
            <div style="color: var(--text); font-size: 0.85rem; line-height: 1.45;">
                <?= nl2br(esc($announcement['message'])) ?>
            </div>
        </div>
        <?php if ($current_role === 'superadmin'): ?>
            <a href="<?= base_url('content-manager#tab-announcement') ?>" class="btn btn-ghost btn-sm" style="font-size:0.75rem;padding:3px 8px;" title="Edit Pengumuman ini di CMS">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- 2. Tracking Trainee Section with Modern Bar Chart -->
<div class="dash-section">
    <div class="dash-section-head">
        <div class="dash-section-title">
            <span class="dash-ic">👥</span> Tracking Trainee 
            <span class="count mono"><?= $total_trainees ?> total</span>
        </div>
        <?php if ($current_role !== 'guest'): ?>
            <a href="<?= base_url('trainees') ?>" class="btn btn-ghost">Lihat Semua Trainee →</a>
        <?php endif; ?>
    </div>

    <!-- Interactive Bar Chart matching training-tracker (1).html -->
    <div class="barchart-card">
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
                <?php 
                $heightPct = max(18, round(($conf['count'] / $maxTrainee) * 100)); 
                ?>
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
</div>

<!-- 3. Peminjaman Operator Executive Dashboard (Synchronized 10-Cards Layout) -->
<div class="dash-section">
    <div class="dash-section-head">
        <div class="dash-section-title">
            <span class="dash-ic">🔄</span> Peminjaman Operator &amp; Alokasi Gedung
            <span class="count mono"><?= $factory_stats['total_active'] ?? 0 ?> Aktif Dipinjam</span>
        </div>
        <?php if ($current_role !== 'guest'): ?>
            <a href="<?= base_url('loans') ?>" class="btn btn-ghost">Kelola Peminjaman →</a>
        <?php endif; ?>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-3">
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
    <div class="row g-3">
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
</div>

<!-- 4. Absensi Hari Ini Section with Bar Chart -->
<div class="dash-section">
    <div class="dash-section-head">
        <div class="dash-section-title">
            <span class="dash-ic">🗓️</span> Absensi Hari Ini 
            <span class="count mono"><?= date('d-m-Y') ?></span>
        </div>
        <?php if ($current_role !== 'guest'): ?>
            <a href="<?= base_url('attendance') ?>" class="btn btn-ghost">Kelola Absensi →</a>
        <?php endif; ?>
    </div>

    <div class="barchart-card">
        <?php
        $attValues = [$today_attendance['hadir'], $today_attendance['ijin'], $today_attendance['sakit'], $today_attendance['mangkir'], $today_attendance['resign']];
        $maxAtt = max(1, ...$attValues);
        $attConfig = [
            ['label' => 'Hadir', 'color' => 'var(--green)', 'count' => $today_attendance['hadir']],
            ['label' => 'Ijin', 'color' => 'var(--amber)', 'count' => $today_attendance['ijin']],
            ['label' => 'Sakit', 'color' => 'var(--blue)', 'count' => $today_attendance['sakit']],
            ['label' => 'Mangkir', 'color' => 'var(--red)', 'count' => $today_attendance['mangkir']],
            ['label' => 'Resign', 'color' => 'var(--orange)', 'count' => $today_attendance['resign']],
        ];
        ?>
        <div class="barchart-wrap">
            <?php foreach ($attConfig as $ac): ?>
                <?php $hPct = max(18, round(($ac['count'] / $maxAtt) * 100)); ?>
                <div class="bar-col">
                    <div class="bar-track">
                        <div class="bar-fill" style="height: <?= $hPct ?>%; background: <?= $ac['color'] ?>;">
                            <span class="bar-value"><?= $ac['count'] ?></span>
                        </div>
                    </div>
                    <span class="bar-label"><?= $ac['label'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- 5. Live Skill Operator Search & Matrix -->
<div class="dash-section">
    <div class="dash-section-head">
        <div class="dash-section-title">
            <span class="dash-ic">🔍</span> Pencarian Skill &amp; Lokasi Operator
        </div>
        <span class="mono" style="font-size:0.8rem;color:var(--muted);">Live Filter Skill Matrix</span>
    </div>

    <div class="pwi-card mb-3" style="padding: 16px;">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" id="skillSearchInput" placeholder="Ketik nama operator, NIK, skill/proses (misal: Sewing, Cutting, Cell 1)..." class="w-100" style="padding:10px 14px;">
            </div>
            <div class="col-md-3">
                <select id="skillTrackFilter" class="w-100" style="padding:10px 12px;">
                    <option value="">Semua Track / Keahlian</option>
                    <option value="Sewing">Sewing</option>
                    <option value="Cutting">Cutting</option>
                    <option value="Assembly">Assembly</option>
                    <option value="Inhouse">Inhouse</option>
                    <option value="Press">Press / Bottom</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="skillLocationFilter" class="w-100" style="padding:10px 12px;">
                    <option value="">Semua Lokasi / Factory</option>
                    <option value="Factory 1">Factory 1 (F1)</option>
                    <option value="Factory 2">Factory 2 (F2)</option>
                    <option value="Factory 3">Factory 3 (F3)</option>
                    <option value="Factory 4">Factory 4 (F4)</option>
                    <option value="Factory 5">Factory 5 (F5)</option>
                    <option value="Bottom">Bottom 1 &amp; 2</option>
                    <option value="Inhouse">Inhouse</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-ghost w-100" id="btnResetSkillFilter" title="Reset filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Skill Matrix Table -->
    <div class="table-scroll">
        <table id="skillMatrixTable">
            <thead>
                <tr>
                    <th>NIK / ID</th>
                    <th>Nama Operator</th>
                    <th>Track / Keahlian</th>
                    <th>Cycle Time Target</th>
                    <th>Lokasi Penempatan</th>
                    <th>Status Training</th>
                    <th>Status Peminjaman</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($active_trainees)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada data operator yang terdaftar.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($active_trainees as $op): ?>
                        <?php
                        $stageBadge = match ($op['current_stage']) {
                            'Induction' => 'badge induction',
                            'Motoric Test' => 'badge motoric',
                            'Hard Skill' => 'badge training',
                            'Upskill' => 'badge upskill',
                            'OJT' => 'badge ojt',
                            'Lulus' => 'badge lulus',
                            'Tidak Lolos' => 'badge gagal',
                            'Resign' => 'badge resign',
                            default => 'badge slate'
                        };

                        $isLoaned = ($op['is_loaned'] ?? 0) == 1;
                        $loanBadge = $isLoaned 
                            ? '<span class="badge menunggu"><span class="dot"></span>Dipinjam: ' . esc($op['loan_building'] ?? 'Line') . '</span>' 
                            : '<span class="badge disetujui"><span class="dot"></span>Tersedia di TC</span>';
                        ?>
                        <tr class="skill-row" 
                            data-name="<?= strtolower(esc($op['name'])) ?>" 
                            data-nik="<?= strtolower(esc($op['nik'] ?? '')) ?>" 
                            data-track="<?= strtolower(esc($op['track'] ?? '')) ?>"
                            data-loc="<?= strtolower(esc(($op['target_scope_big'] ?? '') . ' ' . ($op['target_scope_small'] ?? ''))) ?>">
                            <td class="mono fw-semibold"><?= esc($op['nik'] ?: ('TRN-' . str_pad($op['id'], 3, '0', STR_PAD_LEFT))) ?></td>
                            <td class="fw-semibold text-white"><?= esc($op['name']) ?></td>
                            <td>
                                <span class="badge upskill">
                                    <span class="dot"></span><?= esc($op['track'] ?: 'Belum ditentukan') ?>
                                </span>
                            </td>
                            <td class="mono text-warning">
                                <?= !empty($op['target_cycle_time']) ? esc($op['target_cycle_time']) . 's' : '-' ?>
                            </td>
                            <td>
                                <span style="font-size:0.84rem;color:var(--text);">
                                    <?= esc($op['target_scope_big'] ?? '-') ?>
                                    <?php if (!empty($op['target_scope_small'])): ?>
                                        <span style="color:var(--muted);">/ <?= esc($op['target_scope_small']) ?></span>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <span class="<?= $stageBadge ?>">
                                    <span class="dot"></span><?= esc($op['current_stage']) ?>
                                </span>
                            </td>
                            <td><?= $loanBadge ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live Skill Operator Filter
    const searchInput = document.getElementById('skillSearchInput');
    const trackFilter = document.getElementById('skillTrackFilter');
    const locFilter = document.getElementById('skillLocationFilter');
    const resetBtn = document.getElementById('btnResetSkillFilter');
    const rows = document.querySelectorAll('.skill-row');

    function filterSkills() {
        const query = (searchInput.value || '').toLowerCase().trim();
        const track = (trackFilter.value || '').toLowerCase().trim();
        const loc = (locFilter.value || '').toLowerCase().trim();

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const nik = row.getAttribute('data-nik') || '';
            const rTrack = row.getAttribute('data-track') || '';
            const rLoc = row.getAttribute('data-loc') || '';

            const matchQuery = !query || name.includes(query) || nik.includes(query) || rTrack.includes(query) || rLoc.includes(query);
            const matchTrack = !track || rTrack.includes(track);
            const matchLoc = !loc || rLoc.includes(loc);

            if (matchQuery && matchTrack && matchLoc) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterSkills);
    if (trackFilter) trackFilter.addEventListener('change', filterSkills);
    if (locFilter) locFilter.addEventListener('change', filterSkills);

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (trackFilter) trackFilter.value = '';
            if (locFilter) locFilter.value = '';
            filterSkills();
        });
    }

    // Live Clock Update
    function updateClock() {
        const now = new Date();
        const opts = { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        const el = document.getElementById('liveDateTimeString');
        if (el) {
            el.textContent = now.toLocaleDateString('id-ID', opts) + ' WIB';
        }
    }
    setInterval(updateClock, 1000);
    updateClock();
});
</script>
<?= $this->endSection() ?>
