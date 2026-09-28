<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="info-hero mb-4">
    <h2>Alur Training Karyawan Baru</h2>
    <p>Setiap karyawan baru menjalani training bertahap di Training Center sebelum ditempatkan, dengan evaluasi harian dan penilaian akhir sebelum dinyatakan lulus.</p>
</div>

<!-- 1. 3-Step Flow Diagram matching training-tracker (1).html -->
<div class="flow">
    <!-- TAHAP 1 -->
    <div class="flow-card">
        <span class="step-tag">TAHAP 1</span>
        <h3>Training Center</h3>
        <ul>
            <li><strong>Induction / Training Teori:</strong> 1 hari — track belum ditentukan</li>
            <li><strong>Motoric Test:</strong> cycle time &lt; 40 detik → boleh pilih track Sewing, Cutting, Assembly, atau Inhouse</li>
            <li><strong>Motoric Test:</strong> cycle time ≥ 40 detik → boleh pilih track Cutting, Assembly, atau Inhouse (Sewing tidak tersedia)</li>
            <li><strong>Training Hard Skill:</strong> Cutting: 1 minggu · Sewing/Assembly/Inhouse: 3 minggu</li>
            <li>Evaluasi cycle time terhadap target dilakukan setiap hari selama Training Hard Skill</li>
        </ul>
        <span class="dur mono">Induction 1 hari + Hard Skill 1–3 minggu</span>
    </div>

    <div class="flow-arrow">→</div>

    <!-- TAHAP 2 -->
    <div class="flow-card">
        <span class="step-tag">TAHAP 2</span>
        <h3>On Job Training</h3>
        <ul>
            <li>Durasi 2 minggu, langsung di lokasi kerja tujuan (Line Produksi)</li>
            <li>Dinilai dari 5 pasang hasil proses operator baru</li>
            <li>Aspek penilaian: kualitas, cycle time, dan metode kerja standar</li>
            <li>Evaluasi cycle time aktual vs target per pasang sepatu</li>
        </ul>
        <span class="dur mono">Durasi tetap: 2 minggu</span>
    </div>

    <div class="flow-arrow">→</div>

    <!-- TAHAP 3 -->
    <div class="flow-card">
        <span class="step-tag">TAHAP 3</span>
        <h3>Penempatan</h3>
        <ul>
            <li>Lulus OJT → aktif bekerja di lokasi yang ditentukan</li>
            <li>Lokasi mengikuti struktur scope besar &amp; scope kecil pabrik (Factory 1–5 / Inhouse / Bottom)</li>
            <li>Operator siap dipinjamkan via modul Peminjaman Operator jika line lain membutuhkan</li>
        </ul>
        <span class="dur mono">Status: Aktif Bekerja</span>
    </div>
</div>

<!-- 2. Gantt Chart Timeline Visualization -->
<div class="sub-heading">Gantt Chart Proses Training Center</div>
<p style="color:var(--muted);font-size:0.86rem;margin-bottom:18px;">Timeline dari Induction (Hari 1) sampai lolos penilaian OJT. Durasi berbeda tergantung track Training Hard Skill yang ditetapkan setelah Motoric Test.</p>

<!-- Track Cutting -->
<div class="gantt-card">
    <div class="gantt-title">Track Cutting</div>
    <div class="gantt-sub">Total ±17 hari kerja — Hard Skill 1 minggu</div>
    
    <div class="gantt-scale">
        <span style="left: 0%;">H1</span>
        <span style="left: 6%;">H2</span>
        <span style="left: 41%;">H7 (Akhir HS)</span>
        <span style="left: 100%;">H17 (Akhir OJT)</span>
    </div>
    
    <div class="gantt-row">
        <div class="gantt-label">Induction &amp; Motoric</div>
        <div class="gantt-track">
            <div class="gantt-bar" style="left: 0%; width: 6%; background: var(--slate);">1H</div>
        </div>
    </div>
    <div class="gantt-row">
        <div class="gantt-label">Training Hard Skill</div>
        <div class="gantt-track">
            <div class="gantt-bar" style="left: 6%; width: 35%; background: var(--amber);">6H (1 Mgg)</div>
        </div>
    </div>
    <div class="gantt-row">
        <div class="gantt-label">On Job Training (OJT)</div>
        <div class="gantt-track">
            <div class="gantt-bar" style="left: 41%; width: 59%; background: var(--blue);">10H (2 Mgg)</div>
        </div>
    </div>
</div>

<!-- Track Sewing / Assembly / Inhouse -->
<div class="gantt-card">
    <div class="gantt-title">Track Sewing / Assembly / Inhouse</div>
    <div class="gantt-sub">Total ±27 hari kerja — Hard Skill 3 minggu</div>
    
    <div class="gantt-scale">
        <span style="left: 0%;">H1</span>
        <span style="left: 4%;">H2</span>
        <span style="left: 63%;">H17 (Akhir HS)</span>
        <span style="left: 100%;">H27 (Akhir OJT)</span>
    </div>
    
    <div class="gantt-row">
        <div class="gantt-label">Induction &amp; Motoric</div>
        <div class="gantt-track">
            <div class="gantt-bar" style="left: 0%; width: 4%; background: var(--slate);">1H</div>
        </div>
    </div>
    <div class="gantt-row">
        <div class="gantt-label">Training Hard Skill</div>
        <div class="gantt-track">
            <div class="gantt-bar" style="left: 4%; width: 59%; background: var(--amber);">16H (3 Mgg)</div>
        </div>
    </div>
    <div class="gantt-row">
        <div class="gantt-label">On Job Training (OJT)</div>
        <div class="gantt-track">
            <div class="gantt-bar" style="left: 63%; width: 37%; background: var(--blue);">10H (2 Mgg)</div>
        </div>
    </div>
</div>

<!-- Retry Policy Note -->
<div class="retry-note">
    <span class="ic">!</span>
    <div>Jika operator baru <strong>tidak lolos On Job Training</strong>, ia dikembalikan ke Training Center untuk mengulang — dengan <strong>satu kali kesempatan tambahan</strong> saja. Jika pada percobaan kedua tetap tidak lolos OJT, status akhirnya adalah <strong>Tidak Lolos Program</strong>.</div>
</div>

<!-- 3. Production Location Hierarchy Structure -->
<div class="loc-section mb-5">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2>Struktur Lokasi Produksi</h2>
            <p style="color:var(--muted);font-size:0.88rem;margin:0;">Referensi scope besar dan scope kecil yang digunakan saat menempatkan trainee di area kerja.</p>
        </div>
        <?php if ($current_role === 'superadmin'): ?>
            <a href="<?= base_url('content-manager#tab-locations') ?>" class="btn btn-ghost btn-sm" style="font-size:0.8rem;" title="Edit Struktur Lokasi Produksi di CMS">
                <i class="bi bi-pencil-square me-1"></i> Edit Struktur Lokasi
            </a>
        <?php endif; ?>
    </div>

    <div class="loc-grid" style="margin-top:16px;">
        <?php foreach (($location_structure ?? []) as $card): ?>
            <div class="loc-card">
                <h4>
                    <span class="dot" style="background: <?= esc($card['dot_color'] ?? 'var(--blue)') ?>;"></span>
                    <?= esc($card['title']) ?>
                </h4>
                <?php if (!empty($card['items'])): ?>
                    <?php foreach ($card['items'] as $item): ?>
                        <div class="row">
                            <span><?= esc($item['label']) ?></span>
                            <span><?= esc($item['value']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- 4. End-to-End Workflow Diagram & System Benefits -->
<div class="pwi-card mb-4">
    <div class="sub-heading" style="margin-top:0;">Workflow Sistem &amp; Diagram Alur (TC Admin &amp; Trainer)</div>
    
    <div class="row g-4">
        <div class="col-lg-8 border-end" style="border-color: var(--border) !important;">
            <div class="d-flex flex-column gap-3">
                <div class="p-3 rounded-3" style="background:var(--panel-2);border-left:4px solid var(--blue);">
                    <div class="badge ojt mb-1">TC Admin</div>
                    <h6 class="fw-bold mb-1 text-white">1. Input Data Karyawan Baru &amp; Rekam Absensi Harian (Tap ID Card)</h6>
                    <p class="small mb-0" style="color:var(--muted);">Admin mendaftarkan NIK, Nama, dan ID Card. Absensi harian dicatat otomatis dengan tap ID Card di gate Training Center.</p>
                </div>

                <div class="p-3 rounded-3" style="background:var(--panel-2);border-left:4px solid var(--slate);">
                    <div class="badge induction mb-1">Trainer</div>
                    <h6 class="fw-bold mb-1 text-white">2. Induction &amp; Theoretical Training (Website Paperless &amp; Tap ID Card)</h6>
                    <p class="small mb-0" style="color:var(--muted);">Materi teori dan K3 diakses secara digital melalui modul Materi Training. Riwayat belajar terekam saat peserta tap ID Card.</p>
                </div>

                <div class="p-3 rounded-3" style="background:var(--panel-2);border-left:4px solid var(--violet);">
                    <div class="badge motoric mb-1">Trainer</div>
                    <h6 class="fw-bold mb-1 text-white">3. Motoric Test &amp; Pemilihan Track (Record Result → Web &amp; Tap ID Card)</h6>
                    <p class="small mb-0" style="color:var(--muted);">Pengukuran cycle time awal. Cycle time &lt; 40s membuka opsi Sewing. Nilai diinput pada modul Input Data Harian.</p>
                </div>

                <div class="p-3 rounded-3" style="background:var(--panel-2);border-left:4px solid var(--amber);">
                    <div class="badge training mb-1">Trainer</div>
                    <h6 class="fw-bold mb-1 text-white">4. Training Hard Skill (1 - 3 Minggu) (Record Result → Web &amp; Tap RFID)</h6>
                    <p class="small mb-0" style="color:var(--muted);">Pelatihan keterampilan teknis spesifik. Evaluasi cycle time dan skor kualitas harian dicatat massal di sistem.</p>
                </div>

                <div class="p-3 rounded-3" style="background:var(--panel-2);border-left:4px solid var(--green);">
                    <div class="badge lulus mb-1">Trainer</div>
                    <h6 class="fw-bold mb-1 text-white">5. On Job Training (2 Minggu) &amp; Final Assessment</h6>
                    <p class="small mb-0" style="color:var(--muted);">Penilaian 5 pasang hasil proses kerja di line nyata. Finalisasi status kelulusan peserta (Lulus/Tidak Lolos/Resign).</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <h5 class="fw-bold text-white mb-3">Keunggulan &amp; Manfaat Sistem</h5>
            <div class="d-flex flex-column gap-3">
                <div class="p-2">
                    <div class="fw-bold text-white mb-1"><i class="bi bi-database-check me-1" style="color:var(--amber);"></i> End-to-end Data Record</div>
                    <span class="small" style="color:var(--muted);">Semua tahapan dari pendaftaran hingga kelulusan tercatat dalam 1 platform terpadu.</span>
                </div>
                <div class="p-2">
                    <div class="fw-bold text-white mb-1"><i class="bi bi-file-earmark-check me-1" style="color:var(--green);"></i> Paperless Operation</div>
                    <span class="small" style="color:var(--muted);">Menggantikan buku absensi, lembar penilaian manual, dan form kertas yang rawan hilang.</span>
                </div>
                <div class="p-2">
                    <div class="fw-bold text-white mb-1"><i class="bi bi-upc-scan me-1" style="color:var(--blue);"></i> Realtime ID Card Reader</div>
                    <span class="small" style="color:var(--muted);">Kehadiran dan materi langsung tercatat dengan tap ID Card standar USB Keyboard Wedge.</span>
                </div>
                <div class="p-2">
                    <div class="fw-bold text-white mb-1"><i class="bi bi-clock-history me-1" style="color:var(--violet);"></i> Standarisasi Cycle Time</div>
                    <span class="small" style="color:var(--muted);">Pencatatan target vs aktual detik per proses untuk memastikan kesiapan ke line produksi.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
