<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="dash-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
    <div>
        <h2 class="fw-bold text-white mb-1">
            <i class="bi bi-sliders me-2" style="color:var(--amber);"></i> Kelola Konten &amp; Tata Letak Website
        </h2>
        <p class="small text-muted mb-0">Pengaturan konten website, banner pengumuman dashboard, topik materi training, target cycle time, dan kurikulum induksi.</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('dashboard') ?>" class="btn btn-ghost" style="font-size:0.84rem;">
            ← Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- CMS Navigation Sub-Tabs -->
<div class="tabs mb-4" id="cmsTabNav" style="background:var(--panel);border:1px solid var(--border);border-radius:9px;padding:4px;display:flex;gap:4px;overflow-x:auto;">
    <button type="button" class="tab-btn active" data-target="tab-announcement">
        📢 Pengumuman Dashboard
    </button>
    <button type="button" class="tab-btn" data-target="tab-topics">
        📚 Topik Materi Training
    </button>
    <button type="button" class="tab-btn" data-target="tab-thresholds">
        ⏱️ Target &amp; Cycle Time
    </button>
    <button type="button" class="tab-btn" data-target="tab-curriculum">
        📋 Modul Induksi
    </button>
    <button type="button" class="tab-btn" data-target="tab-factories">
        🏭 Lokasi &amp; Line Pabrik
    </button>
    <button type="button" class="tab-btn" data-target="tab-locations">
        🏢 Struktur Lokasi Produksi
    </button>
    <button type="button" class="tab-btn" data-target="tab-users">
        👥 Manajemen User
    </button>
</div>

<!-- TAB 1: Announcement Banner -->
<div class="cms-pane" id="tab-announcement">
    <div class="pwi-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-white mb-0">
                <i class="bi bi-megaphone-fill me-2" style="color:var(--blue);"></i> Banner Pengumuman &amp; Informasi Dashboard
            </h5>
            <span class="badge slate font-monospace">Realtime Broadcast</span>
        </div>
        <p class="small text-muted mb-4">Pesan yang Anda atur di sini akan langsung ditampilkan di bagian paling atas layar Dashboard bagi seluruh pengguna.</p>

        <form action="<?= base_url('content-manager/save-announcement') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="form-check form-switch mb-4 p-3 rounded-2" style="background:var(--panel-2);border:1px solid var(--border);">
                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="announcementEnabled" name="announcement_enabled" value="1" <?= (($settings['announcement']['announcement_enabled'] ?? '1') === '1') ? 'checked' : '' ?>>
                <label class="form-check-label fw-bold text-white" for="announcementEnabled">
                    Aktifkan Banner Pengumuman di Dashboard
                </label>
                <div class="small text-muted ms-4 ps-2">Jika dinonaktifkan, banner tidak akan muncul di layar utama dashboard.</div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label small text-muted">Judul Pengumuman *</label>
                    <input type="text" name="announcement_title" class="w-100" value="<?= esc($settings['announcement']['announcement_title'] ?? 'Pengumuman Resmi Training Center') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Warna &amp; Tipe Banner</label>
                    <select name="announcement_type" class="w-100">
                        <option value="info" <?= (($settings['announcement']['announcement_type'] ?? 'info') === 'info') ? 'selected' : '' ?>>Info (Biru)</option>
                        <option value="warning" <?= (($settings['announcement']['announcement_type'] ?? 'info') === 'warning') ? 'selected' : '' ?>>Peringatan / Alert (Amber)</option>
                        <option value="success" <?= (($settings['announcement']['announcement_type'] ?? 'info') === 'success') ? 'selected' : '' ?>>Sukses / Panduan (Hijau)</option>
                        <option value="danger" <?= (($settings['announcement']['announcement_type'] ?? 'info') === 'danger') ? 'selected' : '' ?>>Kritis / Larangan (Merah)</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small text-muted">Teks Isi Pengumuman *</label>
                <textarea name="announcement_message" class="w-100" rows="3" required><?= esc($settings['announcement']['announcement_message'] ?? '') ?></textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Pengumuman Dashboard
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TAB 2: Training Topics Management (Full CRUD) -->
<div class="cms-pane" id="tab-topics" style="display:none;">
    <div class="pwi-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h5 class="fw-bold text-white mb-0">
                    <i class="bi bi-journal-text me-2" style="color:var(--amber);"></i> Manajemen Topik Materi Training (SOP &amp; Modul)
                </h5>
                <small class="text-muted">Kelola daftar topik, isi konten materi, dan urutan sidebar di halaman Materi Training.</small>
            </div>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddTopic">
                + Tambah Topik Baru
            </button>
        </div>

        <div class="table-responsive">
            <table class="table pwi-table align-middle mb-0" style="font-size:0.84rem;">
                <thead>
                    <tr style="color:var(--muted);font-size:0.75rem;">
                        <th style="width:60px;">URUT</th>
                        <th style="width:50px;">IKON</th>
                        <th>JUDUL TOPIK</th>
                        <th>KATEGORI</th>
                        <th>STATUS</th>
                        <th style="text-align:right;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($topics)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada topik materi yang dikonfigurasi.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($topics as $tp): ?>
                            <tr style="border-bottom:1px solid var(--border);">
                                <td class="mono fw-bold" style="color:var(--amber);"><?= esc($tp['sort_order']) ?></td>
                                <td style="font-size:1.2rem;"><?= \App\Helpers\IconHelper::renderTopicIcon($tp['topic_key'], $tp['icon']) ?></td>
                                <td>
                                    <div class="fw-bold text-white"><?= esc($tp['title']) ?></div>
                                    <div class="mono small" style="color:var(--muted);font-size:0.72rem;">Key: <?= esc($tp['topic_key']) ?></div>
                                </td>
                                <td>
                                    <span class="badge slate font-monospace" style="font-size:0.72rem;"><?= esc($tp['category']) ?></span>
                                </td>
                                <td>
                                    <?php if ($tp['is_active']): ?>
                                        <span class="badge bg-success" style="font-size:0.72rem;">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary" style="font-size:0.72rem;">Non-Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:right;">
                                    <button type="button" class="btn btn-ghost btn-sm me-1 btn-edit-topic" 
                                        data-id="<?= $tp['id'] ?>"
                                        data-title="<?= esc($tp['title']) ?>"
                                        data-key="<?= esc($tp['topic_key']) ?>"
                                        data-icon="<?= esc($tp['icon']) ?>"
                                        data-category="<?= esc($tp['category']) ?>"
                                        data-order="<?= esc($tp['sort_order']) ?>"
                                        data-subtitle="<?= esc($tp['sub_title']) ?>"
                                        data-quote="<?= esc($tp['quote']) ?>"
                                        data-content="<?= esc(\App\Helpers\ContentHelper::htmlToPlainText($tp['content_html'])) ?>"
                                        data-active="<?= esc($tp['is_active']) ?>"
                                        data-bs-toggle="modal" data-bs-target="#modalEditTopic">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <a href="<?= base_url('content-manager/delete-topic/' . $tp['id']) ?>" class="btn btn-ghost btn-sm del-btn" style="color:var(--red);" onclick="return confirm('Apakah Anda yakin ingin menghapus topik ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 3: Thresholds & Target Cycle Time -->
<div class="cms-pane" id="tab-thresholds" style="display:none;">
    <div class="pwi-card p-4">
        <h5 class="fw-bold text-white mb-1">
            <i class="bi bi-speedometer2 me-2" style="color:var(--violet);"></i> Standar Target Cycle Time &amp; Syarat Kelulusan
        </h5>
        <p class="small text-muted mb-4">Pengaturan batas waktu baku proses manufaktur alas kaki di PT Parkland World Indonesia.</p>

        <form action="<?= base_url('content-manager/save-thresholds') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="p-3 rounded-2" style="background:var(--panel-2);border:1px solid var(--border);">
                        <label class="form-label small fw-bold text-warning mb-1">Motoric Test Max (detik)</label>
                        <p class="small text-muted mb-2">Batas maksimum waktu motoric agar berhak memilih track Sewing.</p>
                        <div class="input-group">
                            <input type="number" step="0.1" name="cycle_time_motoric_max" class="form-control mono text-center fw-bold fs-5" value="<?= esc($settings['thresholds']['cycle_time_motoric_max'] ?? '40.0') ?>" required>
                            <span class="input-group-text bg-dark text-muted border-secondary">detik</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 rounded-2" style="background:var(--panel-2);border:1px solid var(--border);">
                        <label class="form-label small fw-bold text-primary mb-1">Target Hard Skill (detik)</label>
                        <p class="small text-muted mb-2">Standar cycle time harian pada proses Training Center Hard Skill.</p>
                        <div class="input-group">
                            <input type="number" step="0.1" name="cycle_time_hardskill_target" class="form-control mono text-center fw-bold fs-5" value="<?= esc($settings['thresholds']['cycle_time_hardskill_target'] ?? '35.0') ?>" required>
                            <span class="input-group-text bg-dark text-muted border-secondary">detik</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 rounded-2" style="background:var(--panel-2);border:1px solid var(--border);">
                        <label class="form-label small fw-bold text-success mb-1">Target OJT Lini (detik)</label>
                        <p class="small text-muted mb-2">Standar cycle time kecepatan penuh di On the Job Training (OJT).</p>
                        <div class="input-group">
                            <input type="number" step="0.1" name="cycle_time_ojt_target" class="form-control mono text-center fw-bold fs-5" value="<?= esc($settings['thresholds']['cycle_time_ojt_target'] ?? '30.0') ?>" required>
                            <span class="input-group-text bg-dark text-muted border-secondary">detik</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Parameter Cycle Time
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TAB 4: Induction Curriculum Modules -->
<div class="cms-pane" id="tab-curriculum" style="display:none;">
    <div class="pwi-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold text-white mb-0">
                    <i class="bi bi-mortarboard-fill me-2" style="color:var(--blue);"></i> Checklist Modul Induksi Karyawan Baru
                </h5>
                <small class="text-muted">Atur daftar kurikulum teori, K3, dan budaya mutu yang wajib diselesaikan sebelum masuk Motoric Test.</small>
            </div>
            <button type="button" class="btn btn-ghost btn-sm" id="btnAddInductionRow">
                + Tambah Baris Modul
            </button>
        </div>

        <form action="<?= base_url('content-manager/save-induction-modules') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="table-responsive mb-4">
                <table class="table pwi-table align-middle mb-0" id="inductionModulesTable">
                    <thead>
                        <tr style="color:var(--muted);font-size:0.75rem;">
                            <th style="width:140px;">KODE MODUL</th>
                            <th>NAMA &amp; DESKRIPSI MATERI INDUKSI</th>
                            <th style="width:70px;text-align:right;">HAPUS</th>
                        </tr>
                    </thead>
                    <tbody id="inductionModulesTbody">
                        <?php 
                        $indModules = $settings['curriculum']['induction_modules'] ?? [];
                        if (empty($indModules)) {
                            $indModules = [
                                'M-01' => 'Company Profile & Nilai-Nilai PT Parkland World Indonesia',
                                'M-02' => 'Keselamatan & Kesehatan Kerja (K3) serta Tanggap Darurat',
                                'M-03' => 'Budaya 5S (Seiri, Seiton, Seiso, Seiketsu, Shitsuke) & Lean Culture',
                                'M-04' => 'Peraturan Perusahaan (PP), Disiplin & Tata Tertib Kerja',
                                'M-05' => 'Pengenalan Fasilitas, Mesin & Peralatan Dasar Sepatu',
                                'M-06' => 'Standar Mutu Dasar (Quality Basic & No Defect Mindset)',
                            ];
                        }
                        ?>
                        <?php foreach ($indModules as $code => $name): ?>
                            <tr>
                                <td>
                                    <input type="text" name="module_code[]" class="w-100 mono text-center fw-bold" value="<?= esc($code) ?>" placeholder="M-01" required>
                                </td>
                                <td>
                                    <input type="text" name="module_title[]" class="w-100" value="<?= esc($name) ?>" placeholder="Nama modul pembelajaran" required>
                                </td>
                                <td style="text-align:right;">
                                    <button type="button" class="btn btn-ghost btn-sm del-btn btn-del-ind-row" style="color:var(--red);">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Daftar Modul Induksi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TAB 5: Factory Locations -->
<div class="cms-pane" id="tab-factories" style="display:none;">
    <div class="pwi-card p-4">
        <h5 class="fw-bold text-white mb-1">
            <i class="bi bi-buildings-fill me-2" style="color:var(--green);"></i> Daftar Lokasi &amp; Line Pabrik (Factory)
        </h5>
        <p class="small text-muted mb-4">Daftar ini akan muncul sebagai opsi pilihan lokasi pabrik pada modul Peminjaman Operator.</p>

        <form action="<?= base_url('content-manager/save-factories') ?>" method="POST">
            <?= csrf_field() ?>
            <?php
            $rawList = $settings['factories']['factory_list'] ?? [];
            if (is_array($rawList)) {
                $rawText = implode("\n", $rawList);
            } else {
                $rawText = "Factory 1 (F1)\nFactory 2 (F2)\nFactory 3 (F3)\nFactory 4 (F4)\nFactory 5 (F5)\nBottom 1\nBottom 2\nInhouse";
            }
            ?>
            <div class="mb-4">
                <label class="form-label small text-muted">Daftar Pabrik (Satu nama pabrik per baris) *</label>
                <textarea name="factory_list_raw" class="w-100 mono" rows="8" placeholder="Factory 1 (F1)&#10;Factory 2 (F2)" required><?= esc($rawText) ?></textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Daftar Pabrik
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TAB 6: Production Location Hierarchy Structure (Struktur Lokasi Produksi) -->
<div class="cms-pane" id="tab-locations" style="display:none;">
    <div class="pwi-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-white mb-0">
                <i class="bi bi-diagram-3-fill me-2" style="color:var(--teal);"></i> Struktur Lokasi Produksi (Scope Besar &amp; Scope Kecil)
            </h5>
            <span class="badge slate font-monospace">Halaman Info Program</span>
        </div>
        <p class="small text-muted mb-4">Pengaturan kartu referensi scope besar dan scope kecil yang ditampilkan di bagian <strong>Struktur Lokasi Produksi</strong> pada menu Info Program.</p>

        <form action="<?= base_url('content-manager/save-locations') ?>" method="POST">
            <?= csrf_field() ?>

            <?php
            $defaultLocs = [
                [
                    'title' => 'Factory 1 – 5',
                    'dot_color' => 'var(--blue)',
                    'items' => [
                        ['label' => 'Cell', 'value' => '1 – 22'],
                        ['label' => 'Proses per cell', 'value' => 'Cutting, Sewing, Assembly'],
                    ]
                ],
                [
                    'title' => 'Inhouse',
                    'dot_color' => 'var(--amber)',
                    'items' => [
                        ['label' => 'Nosew Line', 'value' => '1 – 60'],
                        ['label' => 'Screen Table Line', 'value' => '1 – 15'],
                        ['label' => 'Cutting', 'value' => '1 – 30'],
                        ['label' => 'HF Welding', 'value' => '1 – 45'],
                    ]
                ],
                [
                    'title' => 'Bottom 1 & Bottom 2',
                    'dot_color' => 'var(--green)',
                    'items' => [
                        ['label' => 'Press', 'value' => '1 – 30'],
                        ['label' => 'Stockfit', 'value' => '1 – 5'],
                        ['label' => 'Degreasing', 'value' => '1 – 2'],
                        ['label' => 'UV', 'value' => '1 – 2'],
                    ]
                ]
            ];
            $savedLocs = $settings['locations']['production_locations'] ?? $defaultLocs;
            if (is_string($savedLocs)) {
                $decoded = json_decode($savedLocs, true);
                if ($decoded) $savedLocs = $decoded;
            }
            ?>

            <div id="locationCardsContainer" class="d-flex flex-column gap-4 mb-4">
                <?php foreach ($savedLocs as $cIndex => $card): ?>
                    <div class="p-3 rounded-3 border loc-card-form" style="background:var(--panel-2);border-color:var(--border) !important;">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color:var(--border) !important;">
                            <h6 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-card-heading text-warning"></i> Kartu Lokasi #<?= $cIndex + 1 ?>
                            </h6>
                            <button type="button" class="btn btn-ghost btn-sm text-danger btn-remove-card" onclick="removeLocationCard(this)">
                                <i class="bi bi-trash me-1"></i> Hapus Kartu Ini
                            </button>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label small text-muted">Judul Scope Besar / Lokasi *</label>
                                <input type="text" name="locations[<?= $cIndex ?>][title]" class="w-100" value="<?= esc($card['title'] ?? '') ?>" placeholder="Contoh: Factory 1 – 5" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small text-muted">Warna Indikator Dot</label>
                                <select name="locations[<?= $cIndex ?>][dot_color]" class="w-100">
                                    <option value="var(--blue)" <?= (($card['dot_color'] ?? '') === 'var(--blue)') ? 'selected' : '' ?>>Biru (Factory)</option>
                                    <option value="var(--amber)" <?= (($card['dot_color'] ?? '') === 'var(--amber)') ? 'selected' : '' ?>>Amber (Inhouse)</option>
                                    <option value="var(--green)" <?= (($card['dot_color'] ?? '') === 'var(--green)') ? 'selected' : '' ?>>Hijau (Bottom)</option>
                                    <option value="var(--violet)" <?= (($card['dot_color'] ?? '') === 'var(--violet)') ? 'selected' : '' ?>>Ungu (Khusus)</option>
                                    <option value="var(--teal)" <?= (($card['dot_color'] ?? '') === 'var(--teal)') ? 'selected' : '' ?>>Teal</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small text-white fw-semibold mb-2">Rincian Scope Kecil / Line / Proses:</label>
                            <table class="table table-sm table-dark table-borderless align-middle m-0">
                                <thead>
                                    <tr style="font-size:0.75rem;color:var(--muted);">
                                        <th style="width:45%;">NAMA SCOPE KECIL (LABEL)</th>
                                        <th style="width:45%;">RENTANG / KETERANGAN (VALUE)</th>
                                        <th style="width:10%;text-align:right;">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="card-items-tbody">
                                    <?php foreach (($card['items'] ?? []) as $iIndex => $item): ?>
                                        <tr>
                                            <td>
                                                <input type="text" name="locations[<?= $cIndex ?>][items][<?= $iIndex ?>][label]" class="w-100" value="<?= esc($item['label'] ?? '') ?>" placeholder="Contoh: Cell" required>
                                            </td>
                                            <td>
                                                <input type="text" name="locations[<?= $cIndex ?>][items][<?= $iIndex ?>][value]" class="w-100 mono" value="<?= esc($item['value'] ?? '') ?>" placeholder="Contoh: 1 – 22" required>
                                            </td>
                                            <td style="text-align:right;">
                                                <button type="button" class="btn btn-ghost btn-sm text-danger" onclick="removeLocationItemRow(this)">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-ghost btn-sm mt-2 text-warning" onclick="addLocationItemRow(this, <?= $cIndex ?>)">
                                + Tambah Baris Scope Kecil
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-3 border-top" style="border-color:var(--border) !important;">
                <button type="button" class="btn btn-ghost text-primary" onclick="addLocationCard()">
                    + Tambah Kartu Lokasi Baru
                </button>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check2-circle me-1"></i> Simpan Struktur Lokasi Produksi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TAB 7: Manajemen User & Akun Login (Superadmin) -->
<div class="cms-pane" id="tab-users" style="display:none;">
    <div class="pwi-card p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h5 class="fw-bold mb-1" style="color:var(--text);">
                    <i class="bi bi-people-fill me-2" style="color:var(--blue);"></i> Manajemen User &amp; Akun Login
                </h5>
                <p class="small text-muted mb-0">Kelola kredensial akun pengguna, peranan hak akses (Superadmin, Trainer, Guest, Leader Lean), dan keamanan sistem.</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddUser">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah User Baru
            </button>
        </div>

        <div class="table-scroll">
            <table class="align-middle mb-0" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align:center;">NO</th>
                        <th>NAMA LENGKAP</th>
                        <th>USERNAME</th>
                        <th>ROLE / HAK AKSES</th>
                        <th>TERDAFTAR</th>
                        <th style="width: 130px; text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4" style="color:var(--muted);">
                                <i class="bi bi-person-x fs-3 d-block mb-1"></i>
                                Belum ada akun user terdaftar.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $idx => $u): ?>
                            <tr>
                                <td class="mono text-center" style="color:var(--muted); font-size:0.85rem;"><?= $idx + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; background: var(--blue-dim); color: var(--blue); font-size: 0.9rem; border: 1px solid rgba(59, 130, 246, 0.3); flex-shrink: 0;">
                                            <?= strtoupper(substr($u['name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="color:var(--text); font-size: 0.92rem;"><?= esc($u['name']) ?></div>
                                            <?php if (session()->get('username') === $u['username']): ?>
                                                <span class="badge lulus mt-1" style="font-size: 0.68rem; padding: 2px 7px;">
                                                    <span class="dot"></span>Akun Anda
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="mono fw-bold" style="color:var(--amber); font-size:0.92rem;"><?= esc($u['username']) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $roleBadge = match($u['role']) {
                                        'superadmin'  => '<span class="badge gagal"><span class="dot"></span>Superadmin</span>',
                                        'trainer'     => '<span class="badge ojt"><span class="dot"></span>Trainer</span>',
                                        'leader_lean' => '<span class="badge training"><span class="dot"></span>Leader Lean</span>',
                                        default       => '<span class="badge induction"><span class="dot"></span>Guest / Tamu</span>',
                                    };
                                    echo $roleBadge;
                                    ?>
                                </td>
                                <td class="small mono" style="color:var(--muted); font-size: 0.82rem;">
                                    <?= !empty($u['created_at']) ? date('d M Y, H:i', strtotime($u['created_at'])) : '-' ?>
                                </td>
                                <td style="text-align: center;">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-ghost btn-sm btn-edit-user"
                                                data-id="<?= $u['id'] ?>"
                                                data-username="<?= esc($u['username']) ?>"
                                                data-name="<?= esc($u['name']) ?>"
                                                data-role="<?= esc($u['role']) ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditUser"
                                                title="Edit User"
                                                style="border: 1px solid var(--border); padding: 4px 8px; border-radius: 6px;">
                                            <i class="bi bi-pencil-square" style="color:var(--amber); font-size: 1rem;"></i>
                                        </button>
                                        <?php if (session()->get('username') !== $u['username']): ?>
                                            <a href="<?= base_url('content-manager/delete-user/' . $u['id']) ?>"
                                               class="btn btn-ghost btn-sm"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus user <?= esc($u['username']) ?> (<?= esc($u['name']) ?>)? Aksi ini tidak dapat dibatalkan.');"
                                               title="Hapus User"
                                               style="border: 1px solid var(--border); padding: 4px 8px; border-radius: 6px;">
                                                <i class="bi bi-trash" style="color:var(--red); font-size: 1rem;"></i>
                                            </a>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-ghost btn-sm disabled opacity-25" title="Tidak dapat menghapus akun sendiri" style="border: 1px solid var(--border); padding: 4px 8px; border-radius: 6px;">
                                                <i class="bi bi-trash" style="font-size: 1rem;"></i>
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
    </div>
</div>

<!-- Modal Add User -->
<div class="modal fade" id="modalAddUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <form action="<?= base_url('content-manager/store-user') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-person-plus-fill me-2" style="color:var(--blue);"></i> Tambah Akun User Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Nama Lengkap *</label>
                        <input type="text" name="name" class="w-100" placeholder="Contoh: John Doe, S.T." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Username Login *</label>
                        <input type="text" name="username" class="w-100 mono" placeholder="Contoh: jdoe / trainer_01" required>
                        <div class="small text-muted mt-1" style="font-size:0.75rem;">Gunakan huruf kecil, angka, atau underscore tanpa spasi.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Password Awal *</label>
                        <input type="password" name="password" class="w-100 mono" placeholder="Minimal 4 karakter" required>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small text-muted">Peran / Role Hak Akses *</label>
                        <select name="role" class="w-100" required>
                            <option value="trainer">Trainer (Input Nilai &amp; View All)</option>
                            <option value="superadmin">Superadmin (Full Control &amp; Settings)</option>
                            <option value="guest">Guest / Tamu (Read-Only)</option>
                            <option value="leader_lean">Leader Lean</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="modalEditUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <form id="formEditUser" action="" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-pencil-square me-2" style="color:var(--amber);"></i> Edit Akun User
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Nama Lengkap *</label>
                        <input type="text" id="editUserName" name="name" class="w-100" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Username Login *</label>
                        <input type="text" id="editUserUsername" name="username" class="w-100 mono" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Password Baru (Opsional)</label>
                        <input type="password" name="password" class="w-100 mono" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <div class="small text-muted mt-1" style="font-size:0.75rem;">Biarkan kosong jika tetap menggunakan password saat ini.</div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small text-muted">Peran / Role Hak Akses *</label>
                        <select id="editUserRole" name="role" class="w-100" required>
                            <option value="trainer">Trainer (Input Nilai &amp; View All)</option>
                            <option value="superadmin">Superadmin (Full Control &amp; Settings)</option>
                            <option value="guest">Guest / Tamu (Read-Only)</option>
                            <option value="leader_lean">Leader Lean</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add Topic -->
<div class="modal fade" id="modalAddTopic" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-dark">
            <form action="<?= base_url('content-manager/store-topic') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title text-white">Tambah Topik Materi Training Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Judul Topik *</label>
                            <input type="text" name="title" class="w-100" placeholder="Contoh: Quality Inspection Standard" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Pilih Ikon</label>
                            <select name="icon" class="w-100" style="font-size:0.83rem;">
                                <?php foreach (\App\Helpers\IconHelper::getPresetOptions() as $icVal => $icLabel): ?>
                                    <option value="<?= esc($icVal) ?>"><?= esc($icLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Nomor Urut</label>
                            <input type="number" name="sort_order" class="w-100 mono text-center" value="11">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Kategori</label>
                            <select name="category" class="w-100">
                                <option value="Bahan Baku">Bahan Baku</option>
                                <option value="Peralatan">Peralatan</option>
                                <option value="Mesin">Mesin</option>
                                <option value="Proses">Proses</option>
                                <option value="Treatment">Treatment</option>
                                <option value="Standar Mutu">Standar Mutu</option>
                                <option value="K3 & 5S">K3 &amp; 5S</option>
                                <option value="Umum">Umum</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Sub-Judul / Ringkasan</label>
                            <input type="text" name="sub_title" class="w-100" placeholder="Ringkasan isi modul topik">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Quote / Pesan Kunci Trainer</label>
                        <input type="text" name="quote" class="w-100" placeholder="Pesan panduan atau prinsip penting bagi operator...">
                    </div>

                    <div class="mb-0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small text-muted mb-0 fw-semibold">Isi Poin-Poin Materi (Teks Biasa)</label>
                            <span class="badge slate font-monospace" style="font-size:0.7rem;">Otomatis Diformat ke Kartu Modern</span>
                        </div>
                        <p class="small text-muted mb-2" style="font-size:0.76rem;">
                            Tuliskan materi sebagai teks biasa (tanpa kode HTML). Cukup gunakan nomor poin, dan sistem akan otomatis menampilkannya sebagai kartu materi beranimasi.
                        </p>
                        <textarea name="content_text" class="w-100 p-3 rounded-2" rows="6" style="background:var(--panel-2);border:1px solid var(--border);color:var(--text);font-size:0.85rem;line-height:1.5;" placeholder="Contoh penulisan:&#10;1. Nylon Bonded (#20 - #30)&#10;Benang filamen berkekuatan tinggi berlapis resin pelindung untuk stitching heavy-duty.&#10;&#10;2. Spun Polyester (#40)&#10;Standar jahitan upper sepatu lari, kelenturan seimbang dan tahan gesekan."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Topik</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Topic -->
<div class="modal fade" id="modalEditTopic" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-dark">
            <form id="formEditTopic" action="" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title text-white">Edit Topik Materi Training</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Judul Topik *</label>
                            <input type="text" id="editTopicTitle" name="title" class="w-100" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Pilih Ikon</label>
                            <select id="editTopicIcon" name="icon" class="w-100" style="font-size:0.83rem;">
                                <?php foreach (\App\Helpers\IconHelper::getPresetOptions() as $icVal => $icLabel): ?>
                                    <option value="<?= esc($icVal) ?>"><?= esc($icLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Nomor Urut</label>
                            <input type="number" id="editTopicOrder" name="sort_order" class="w-100 mono text-center">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Topic Key (ID)</label>
                            <input type="text" id="editTopicKey" name="topic_key" class="w-100 mono" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Kategori</label>
                            <select id="editTopicCategory" name="category" class="w-100">
                                <option value="Bahan Baku">Bahan Baku</option>
                                <option value="Peralatan">Peralatan</option>
                                <option value="Mesin">Mesin</option>
                                <option value="Proses">Proses</option>
                                <option value="Treatment">Treatment</option>
                                <option value="Standar Mutu">Standar Mutu</option>
                                <option value="K3 & 5S">K3 &amp; 5S</option>
                                <option value="Umum">Umum</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Status</label>
                            <select id="editTopicActive" name="is_active" class="w-100">
                                <option value="1">Aktif (Tampil di Menu)</option>
                                <option value="0">Non-Aktif (Disembunyikan)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Sub-Judul / Ringkasan</label>
                        <input type="text" id="editTopicSubtitle" name="sub_title" class="w-100">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Quote / Pesan Kunci Trainer</label>
                        <input type="text" id="editTopicQuote" name="quote" class="w-100">
                    </div>

                    <div class="mb-0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small text-muted mb-0 fw-semibold">Isi Poin-Poin Materi (Teks Biasa)</label>
                            <span class="badge slate font-monospace" style="font-size:0.7rem;">Otomatis Diformat ke Kartu Modern</span>
                        </div>
                        <p class="small text-muted mb-2" style="font-size:0.76rem;">
                            Tuliskan materi sebagai teks biasa (tanpa kode HTML). Cukup gunakan format nomor poin (misal: <code>1. Judul Poin</code> lalu baris bawahnya isi keterangan).
                        </p>
                        <textarea id="editTopicContent" name="content_text" class="w-100 p-3 rounded-2" rows="7" style="background:var(--panel-2);border:1px solid var(--border);color:var(--text);font-size:0.85rem;line-height:1.5;" placeholder="1. Judul Poin&#10;Penjelasan materi..."></textarea>
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // CMS Sub-Tab Switching
    const tabBtns = document.querySelectorAll('#cmsTabNav .tab-btn');
    const panes   = document.querySelectorAll('.cms-pane');

    function switchTab(targetId) {
        tabBtns.forEach(b => {
            if (b.getAttribute('data-target') === targetId) {
                b.classList.add('active');
            } else {
                b.classList.remove('active');
            }
        });

        panes.forEach(p => {
            if (p.id === targetId) {
                p.style.display = 'block';
            } else {
                p.style.display = 'none';
            }
        });
    }

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const target = this.getAttribute('data-target');
            switchTab(target);
            history.replaceState(null, null, '#' + target);
        });
    });

    // Check URL Hash on load
    if (window.location.hash) {
        const hashTarget = window.location.hash.replace('#', '');
        if (document.getElementById(hashTarget)) {
            switchTab(hashTarget);
        }
    }

    // Modal Edit Topic Populator
    const editBtns = document.querySelectorAll('.btn-edit-topic');
    const formEdit = document.getElementById('formEditTopic');

    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            formEdit.action = '<?= base_url('content-manager/update-topic/') ?>' + id;

            document.getElementById('editTopicTitle').value    = this.getAttribute('data-title') || '';
            document.getElementById('editTopicKey').value      = this.getAttribute('data-key') || '';
            document.getElementById('editTopicIcon').value     = this.getAttribute('data-icon') || '';
            document.getElementById('editTopicCategory').value = this.getAttribute('data-category') || 'Umum';
            document.getElementById('editTopicOrder').value    = this.getAttribute('data-order') || 0;
            document.getElementById('editTopicSubtitle').value = this.getAttribute('data-subtitle') || '';
            document.getElementById('editTopicQuote').value    = this.getAttribute('data-quote') || '';
            document.getElementById('editTopicContent').value  = this.getAttribute('data-content') || '';
            document.getElementById('editTopicActive').value   = this.getAttribute('data-active') || '1';
        });
    });

    // Modal Edit User Populator
    const editUserBtns = document.querySelectorAll('.btn-edit-user');
    const formEditUser = document.getElementById('formEditUser');

    editUserBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            formEditUser.action = '<?= base_url('content-manager/update-user/') ?>' + id;

            document.getElementById('editUserName').value     = this.getAttribute('data-name') || '';
            document.getElementById('editUserUsername').value = this.getAttribute('data-username') || '';
            document.getElementById('editUserRole').value     = this.getAttribute('data-role') || 'trainer';
        });
    });

    // Induction Modules Dynamic Row Adder
    const btnAddInd = document.getElementById('btnAddInductionRow');
    const indTbody  = document.getElementById('inductionModulesTbody');

    if (btnAddInd && indTbody) {
        btnAddInd.addEventListener('click', function() {
            const rowCount = indTbody.querySelectorAll('tr').length + 1;
            const newCode  = 'M-' + String(rowCount).padStart(2, '0');

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <input type="text" name="module_code[]" class="w-100 mono text-center fw-bold" value="${newCode}" placeholder="M-01" required>
                </td>
                <td>
                    <input type="text" name="module_title[]" class="w-100" value="" placeholder="Nama modul pembelajaran baru" required>
                </td>
                <td style="text-align:right;">
                    <button type="button" class="btn btn-ghost btn-sm del-btn btn-del-ind-row" style="color:var(--red);">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            indTbody.appendChild(tr);
            attachDelEvents();
        });
    }

    function attachDelEvents() {
        document.querySelectorAll('.btn-del-ind-row').forEach(btn => {
            btn.onclick = function() {
                const tr = this.closest('tr');
                if (tr && indTbody.querySelectorAll('tr').length > 1) {
                    tr.remove();
                } else {
                    alert('Minimal harus ada 1 modul induksi.');
                }
            };
        });
    }
    attachDelEvents();
});

let locationCardCount = 10;

function addLocationItemRow(btn, cardIdx) {
    const tbody = btn.previousElementSibling.querySelector('tbody');
    if (!tbody) return;
    const rowIdx = tbody.children.length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <input type="text" name="locations[${cardIdx}][items][${rowIdx}][label]" class="w-100" placeholder="Nama Scope..." required>
        </td>
        <td>
            <input type="text" name="locations[${cardIdx}][items][${rowIdx}][value]" class="w-100 mono" placeholder="Rentang..." required>
        </td>
        <td style="text-align:right;">
            <button type="button" class="btn btn-ghost btn-sm text-danger" onclick="removeLocationItemRow(this)">
                <i class="bi bi-x-lg"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
}

function removeLocationItemRow(btn) {
    const tr = btn.closest('tr');
    if (tr) tr.remove();
}

function removeLocationCard(btn) {
    const card = btn.closest('.loc-card-form');
    if (card) {
        if (confirm('Apakah Anda yakin ingin menghapus kartu lokasi ini?')) {
            card.remove();
        }
    }
}

function addLocationCard() {
    const container = document.getElementById('locationCardsContainer');
    if (!container) return;

    const cardIdx = locationCardCount++;
    const cardDiv = document.createElement('div');
    cardDiv.className = 'p-3 rounded-3 border loc-card-form';
    cardDiv.style.cssText = 'background:var(--panel-2);border-color:var(--border) !important;';

    cardDiv.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom" style="border-color:var(--border) !important;">
            <h6 class="fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-card-heading text-warning"></i> Kartu Lokasi #${cardIdx + 1}
            </h6>
            <button type="button" class="btn btn-ghost btn-sm text-danger btn-remove-card" onclick="removeLocationCard(this)">
                <i class="bi bi-trash me-1"></i> Hapus Kartu Ini
            </button>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <label class="form-label small text-muted">Judul Scope Besar / Lokasi *</label>
                <input type="text" name="locations[${cardIdx}][title]" class="w-100" placeholder="Contoh: Factory Baru" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Warna Indikator Dot</label>
                <select name="locations[${cardIdx}][dot_color]" class="w-100">
                    <option value="var(--blue)">Biru (Factory)</option>
                    <option value="var(--amber)">Amber (Inhouse)</option>
                    <option value="var(--green)">Hijau (Bottom)</option>
                    <option value="var(--violet)">Ungu (Khusus)</option>
                    <option value="var(--teal)">Teal</option>
                </select>
            </div>
        </div>

        <div class="mb-2">
            <label class="form-label small text-white fw-semibold mb-2">Rincian Scope Kecil / Line / Proses:</label>
            <table class="table table-sm table-dark table-borderless align-middle m-0">
                <thead>
                    <tr style="font-size:0.75rem;color:var(--muted);">
                        <th style="width:45%;">NAMA SCOPE KECIL (LABEL)</th>
                        <th style="width:45%;">RENTANG / KETERANGAN (VALUE)</th>
                        <th style="width:10%;text-align:right;">AKSI</th>
                    </tr>
                </thead>
                <tbody class="card-items-tbody">
                    <tr>
                        <td>
                            <input type="text" name="locations[${cardIdx}][items][0][label]" class="w-100" placeholder="Contoh: Cell" required>
                        </td>
                        <td>
                            <input type="text" name="locations[${cardIdx}][items][0][value]" class="w-100 mono" placeholder="Contoh: 1 – 10" required>
                        </td>
                        <td style="text-align:right;">
                            <button type="button" class="btn btn-ghost btn-sm text-danger" onclick="removeLocationItemRow(this)">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-ghost btn-sm mt-2 text-warning" onclick="addLocationItemRow(this, ${cardIdx})">
                + Tambah Baris Scope Kecil
            </button>
        </div>
    `;

    container.appendChild(cardDiv);
}
</script>
<?= $this->endSection() ?>
