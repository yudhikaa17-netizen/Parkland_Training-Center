<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="dash-toolbar d-flex justify-content-between align-items-center flex-wrap gap-3 mb-2">
    <div>
        <h2 class="fw-bold text-white mb-1">Materi Training &amp; Modul Standar</h2>
    </div>

    <?php if ($current_role === 'superadmin'): ?>
        <button type="button" class="btn btn-primary fw-semibold d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalAddMaterial">
            <i class="bi bi-file-earmark-plus"></i> + Tambah Dokumen
        </button>
    <?php endif; ?>
</div>

<p class="note-box" style="margin-top:0;margin-bottom:24px;">
    Materi referensi dan modul standar untuk ditampilkan ke trainee selama proses Training Center — pilih topik di sebelah kiri untuk membaca materi dan menandai peserta yang telah menyelesaikan topik.
</p>

<!-- Modular Materi Layout matching screenshot & reference -->
<div class="materi-layout mb-5">
    <!-- Sidebar Navigation -->
    <div class="materi-sidebar">
        <input type="text" id="materiSearch" class="materi-search" placeholder="Cari topik materi…">
        <div id="materiNavList">
            <?php 
            $i = 0;
            foreach ($topics as $key => $topic): 
                $i++;
                $isActive = ($key === 'skill_level' || ($i === 1 && empty($topics['skill_level'])));
            ?>
                <button type="button" class="materi-nav-btn <?= $isActive ? 'active' : '' ?>" data-topic="<?= $key ?>" data-title="<?= esc($topic['title']) ?>">
                    <span class="ic"><?= \App\Helpers\IconHelper::renderTopicIcon($key, $topic['icon'] ?? '') ?></span>
                    <span class="topic-label"><?= esc($topic['title']) ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="materi-main-col">
        <!-- Materi Content Pane -->
        <div class="materi-content mb-4" id="materiContent">
            
            <!-- Global Process Banner -->
            <div class="p-3 rounded-2 mb-4" style="background:var(--panel-2);border:1px solid var(--border);color:var(--text);font-size:0.88rem;">
                <strong>MC</strong> = proses yang memerlukan penggunaan mesin. &nbsp;&bull;&nbsp; <strong>MN</strong> = proses manual yang tidak memerlukan mesin.
            </div>

            <!-- Dynamic Topics Loaded from CMS / Database -->
            <?php 
            $tIdx = 0;
            foreach ($topics as $tKey => $topic): 
                $tIdx++;
                $isDefaultActive = ($tKey === 'skill_level' || ($tIdx === 1 && empty($topics['skill_level'])));
            ?>
                <div id="topic-<?= esc($tKey) ?>" class="topic-pane" style="<?= $isDefaultActive ? 'display:block;' : 'display:none;' ?>">
                    <?= $topic['content_html'] ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Completion Tracker Box (Exact Match to User Screenshot) -->
        <div class="pwi-card p-4 mb-4" id="completionTrackerCard" style="background:var(--panel);border:1px solid var(--border);border-radius:8px;">
            <div class="mb-3">
                <h5 class="fw-bold text-white mb-1" style="font-size:1.05rem;">
                    Tandai Peserta Telah Mempelajari: <span id="activeTopicTitleDisplay" style="color:var(--amber);">Skill Level</span>
                </h5>
                <p class="small mb-0" style="color:var(--muted);font-size:0.8rem;">
                    Setiap penandaan otomatis tercatat di riwayat training operator terkait.
                </p>
            </div>

            <!-- Tap ID Card Input Area -->
            <div class="mb-3">
                <label class="form-label small text-muted mb-1" style="font-size:0.75rem;">Tap ID Card peserta</label>
                <div class="rfid-tap-box p-3 rounded-2 text-center" id="rfidTapArea" style="background:#090d16;border:1px dashed var(--border);cursor:pointer;transition:all 0.2s ease;">
                    <span class="mono" id="rfidStatusText" style="color:var(--muted);font-size:0.85rem;">Klik di sini, lalu tap ID Card...</span>
                    <input type="text" id="rfidScanInput" style="opacity:0;position:absolute;pointer-events:none;" autocomplete="off">
                </div>
            </div>

            <!-- Manual Trainee Selection Dropdown & Button -->
            <div class="d-flex gap-2 align-items-center mb-4 flex-wrap flex-sm-nowrap">
                <select id="manualTraineeSelect" class="form-select flex-grow-1" style="background:var(--panel-2);border:1px solid var(--border);color:var(--text);font-size:0.85rem;padding:9px 12px;border-radius:6px;">
                    <?php if (empty($trainees)): ?>
                        <option value="">Belum ada data trainee</option>
                    <?php else: ?>
                        <option value="">-- Pilih Peserta Trainee --</option>
                        <?php foreach ($trainees as $t): ?>
                            <option value="<?= $t['id'] ?>">
                                <?= esc($t['name']) ?> (<?= esc($t['nik']) ?>) - <?= esc($t['stage']) ?> [<?= esc($t['track']) ?>]
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <button type="button" id="btnMarkManual" class="btn btn-ghost text-nowrap" style="padding:9px 18px;font-size:0.85rem;">
                    Tandai Selesai
                </button>
            </div>

            <!-- List / Table of Completed Trainees for Active Topic -->
            <div>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <h6 class="fw-bold text-white mb-0" style="font-size:0.9rem;">Sudah Mempelajari Topik Ini</h6>
                    <span id="topicCompletionCount" class="badge rounded-pill" style="background:var(--panel-3);color:var(--muted);font-size:0.75rem;padding:3px 8px;">0</span>
                </div>

                <div class="table-responsive">
                    <table class="table pwi-table align-middle mb-0" style="font-size:0.82rem;">
                        <thead>
                            <tr style="color:var(--muted);font-size:0.75rem;border-bottom:1px solid var(--border);">
                                <th style="padding:10px 12px;">NAMA / ID</th>
                                <th style="padding:10px 12px;">TANGGAL DITANDAI</th>
                                <?php if (in_array($current_role, ['superadmin', 'trainer'])): ?>
                                    <th style="padding:10px 12px;text-align:right;">AKSI</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="completionTableBody">
                            <tr id="emptyCompletionRow">
                                <td colspan="<?= in_array($current_role, ['superadmin', 'trainer']) ? 3 : 2 ?>" class="text-center py-4" style="color:var(--muted);font-size:0.82rem;">
                                    Belum ada peserta yang menandai topik ini selesai.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Repositori File & Modul Dokumen Terupload -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3 mt-4">
    <div class="sub-heading m-0">Dokumen &amp; Modul Terunggah di Database</div>
    
    <?php if ($current_role === 'superadmin'): ?>
        <button type="button" class="btn btn-primary btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAddMaterial">
            <i class="bi bi-file-earmark-plus me-1"></i> + Tambah Dokumen
        </button>
    <?php endif; ?>
</div>

<!-- Category Filter Pills -->
<div class="d-flex align-items-center gap-2 flex-wrap mb-4" id="docCategoryFilter">
    <button type="button" class="btn btn-sm filter-doc-btn active" data-cat="ALL" style="background:var(--panel-2);border:1px solid var(--border);color:var(--text);font-size:0.8rem;border-radius:20px;padding:4px 14px;">
        Semua Jenis (<?= count($materials) ?>)
    </button>
    <?php foreach ($categories as $cat): ?>
        <?php 
        $catCount = count(array_filter($materials, fn($m) => ($m['category'] ?? '') === $cat));
        ?>
        <button type="button" class="btn btn-sm filter-doc-btn" data-cat="<?= esc($cat) ?>" style="background:var(--panel-2);border:1px solid var(--border);color:var(--muted);font-size:0.8rem;border-radius:20px;padding:4px 14px;">
            <?= esc($cat) ?> (<?= $catCount ?>)
        </button>
    <?php endforeach; ?>
</div>

<div class="row g-3" id="materialsGrid">
    <?php if (empty($materials)): ?>
        <div class="col-12">
            <div class="empty-state">Belum ada file materi dokumen tambahan yang diunggah.</div>
        </div>
    <?php else: ?>
        <?php foreach ($materials as $m): ?>
            <div class="col-md-6 col-lg-4 doc-item-card" data-category="<?= esc($m['category']) ?>">
                <div class="pwi-card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge slate font-monospace" style="font-size:0.72rem;">
                                <?= esc($m['category']) ?>
                            </span>
                            <span class="badge ojt" style="font-size:0.72rem;">
                                <?= esc($m['stage']) ?>
                            </span>
                        </div>
                        <h5 class="fw-bold text-white mb-2"><?= esc($m['title']) ?></h5>
                        <p style="color:var(--muted);font-size:0.82rem;margin-bottom:12px;"><?= esc($m['description'] ?? 'Tidak ada deskripsi.') ?></p>
                    </div>

                    <div class="pt-3 border-top d-flex justify-content-between align-items-center" style="border-color:var(--border) !important;">
                        <small class="text-muted" style="font-size:0.75rem;">
                            Oleh: <?= esc($m['uploaded_by']) ?>
                        </small>
                        <div class="d-flex align-items-center gap-1">
                            <a href="<?= esc($m['file_url']) ?>" target="_blank" class="btn btn-ghost" style="padding:4px 8px;font-size:0.75rem;">
                                Buka / Unduh ↗
                            </a>
                            <?php if ($current_role === 'superadmin'): ?>
                                <a href="<?= base_url('materials/delete/' . $m['id']) ?>" class="btn btn-ghost del-btn" style="padding:4px 8px;font-size:0.75rem;" onclick="return confirm('Hapus materi ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Single: Add Material / Document (Superadmin Only) -->
<?php if ($current_role === 'superadmin'): ?>
<div class="modal fade" id="modalAddMaterial" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <form action="<?= base_url('materials/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-file-earmark-plus text-primary me-2"></i> Tambah / Upload Dokumen Training
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Judul Dokumen / Modul *</label>
                        <input type="text" name="title" class="w-100" placeholder="Contoh: Form & Panduan Penilaian OJT" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small text-muted">Jenis / Kategori Dokumen *</label>
                            <select name="category" id="materialCategorySelect" class="w-100" onchange="toggleNewCategoryInput(this.value)">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= esc($cat) ?>"><?= esc($cat) ?></option>
                                <?php endforeach; ?>
                                <option value="NEW" style="color:var(--amber);font-weight:bold;">+ Tambah Jenis Dokumen Baru...</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">Tahap yang Relevan</label>
                            <select name="stage" class="w-100">
                                <option value="All Stages">Semua Tahap</option>
                                <option value="Induction">Induction</option>
                                <option value="Motoric Test">Motoric Test</option>
                                <option value="Hard Skill">Hard Skill</option>
                                <option value="OJT">OJT</option>
                            </select>
                        </div>
                    </div>

                    <!-- Input field for Custom New Category -->
                    <div class="mb-3" id="newCategoryWrapper" style="display:none;background:var(--panel-2);padding:12px;border-radius:6px;border:1px dashed var(--amber);">
                        <label class="form-label small text-warning mb-1">
                            <i class="bi bi-folder-plus me-1"></i> Nama Jenis / Kategori Dokumen Baru *
                        </label>
                        <input type="text" name="new_category" id="newCategoryInput" class="w-100" placeholder="Contoh: Form Evaluasi, Standard Operation Chart, K3">
                    </div>

                    <!-- Direct File Upload (Klik & Pilih File dari Perangkat) -->
                    <div class="mb-3">
                        <label class="form-label small text-white fw-semibold">Pilih File Dokumen (Klik &amp; Pilih File) *</label>
                        <input type="file" name="document_file" class="form-control" style="background:var(--panel-2);border:1px solid var(--border);color:var(--text);padding:10px;border-radius:6px;" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.mp4,.png,.jpg,.jpeg">
                        <small class="text-muted mt-1 d-block" style="font-size:0.75rem;">Support: PDF, Word, Excel, PowerPoint, MP4, Gambar</small>
                    </div>

                    <div class="mb-3">
                        <a class="small text-muted text-decoration-none" data-bs-toggle="collapse" href="#externalUrlCollapse" role="button" aria-expanded="false">
                            <i class="bi bi-link-45deg"></i> Atau gunakan Link URL Eksternal (Opsional)
                        </a>
                        <div class="collapse mt-2" id="externalUrlCollapse">
                            <input type="text" name="file_url" class="w-100" placeholder="https://example.com/materials/file.pdf">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small text-muted">Deskripsi Singkat</label>
                        <textarea name="description" class="w-100" rows="2" placeholder="Ringkasan isi dokumen atau panduan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Simpan &amp; Upload Dokumen
                    </button>
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
    const navButtons   = document.querySelectorAll('.materi-nav-btn');
    const panes        = document.querySelectorAll('.topic-pane');
    const searchInput  = document.getElementById('materiSearch');
    const topicTitleEl = document.getElementById('activeTopicTitleDisplay');
    const countBadgeEl = document.getElementById('topicCompletionCount');
    const tableBodyEl  = document.getElementById('completionTableBody');
    const rfidTapArea  = document.getElementById('rfidTapArea');
    const rfidScanInp  = document.getElementById('rfidScanInput');
    const rfidStatus   = document.getElementById('rfidStatusText');
    const manualSelect = document.getElementById('manualTraineeSelect');
    const btnMarkMan   = document.getElementById('btnMarkManual');

    const currentUserRole = '<?= esc($current_role) ?>';
    const isTrainerOrAdmin = ['superadmin', 'trainer'].includes(currentUserRole);

    // Initial completions map from server
    let topicCompletionsMap = <?= json_encode($completions_by_topic ?? []) ?>;
    let currentTopicKey   = 'skill_level';
    let currentTopicTitle = 'Skill Level';

    // Set initial active state if topic button exists
    const activeBtn = document.querySelector('.materi-nav-btn.active');
    if (activeBtn) {
        currentTopicKey   = activeBtn.getAttribute('data-topic');
        currentTopicTitle = activeBtn.getAttribute('data-title') || activeBtn.textContent.trim();
    }

    function renderCompletionTable() {
        if (!topicTitleEl || !countBadgeEl || !tableBodyEl) return;

        topicTitleEl.textContent = currentTopicTitle;
        const list = topicCompletionsMap[currentTopicKey] || [];
        countBadgeEl.textContent = list.length;

        if (list.length === 0) {
            tableBodyEl.innerHTML = `
                <tr id="emptyCompletionRow">
                    <td colspan="${isTrainerOrAdmin ? 3 : 2}" class="text-center py-4" style="color:var(--muted);font-size:0.82rem;">
                        Belum ada peserta yang menandai topik ini selesai.
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        list.forEach(item => {
            const formattedDate = item.formatted_date || item.completed_at;
            html += `
                <tr id="row-completion-${item.id}" style="border-bottom:1px solid var(--border);">
                    <td style="padding:10px 12px;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar-ph" style="width:28px;height:28px;font-size:0.75rem;">👤</span>
                            <div>
                                <div class="fw-bold text-white">${escapeHtml(item.trainee_name || 'Trainee')}</div>
                                <div class="mono" style="font-size:0.75rem;color:var(--muted);">NIK: ${escapeHtml(item.nik || '-')} &bull; Tag: ${escapeHtml(item.rfid_tag || '-')}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:10px 12px;color:var(--text);font-size:0.82rem;" class="mono">
                        ${escapeHtml(formattedDate)}
                    </td>
                    ${isTrainerOrAdmin ? `
                        <td style="padding:10px 12px;text-align:right;">
                            <button type="button" class="btn btn-ghost del-completion-btn" data-id="${item.id}" style="padding:3px 8px;font-size:0.75rem;color:var(--red);" title="Hapus Tanda Selesai">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    ` : ''}
                </tr>
            `;
        });

        tableBodyEl.innerHTML = html;

        // Attach delete listeners
        document.querySelectorAll('.del-completion-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                if (confirm('Hapus tanda penyelesaian topik untuk peserta ini?')) {
                    deleteCompletion(id);
                }
            });
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Sidebar topic switching
    navButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            navButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            currentTopicKey   = this.getAttribute('data-topic');
            currentTopicTitle = this.getAttribute('data-title') || this.textContent.trim();

            panes.forEach(pane => {
                if (pane.id === 'topic-' + currentTopicKey) {
                    pane.style.display = 'block';
                } else {
                    pane.style.display = 'none';
                }
            });

            renderCompletionTable();
        });
    });

    // Sidebar search filter
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = (searchInput.value || '').toLowerCase().trim();
            navButtons.forEach(btn => {
                const text = btn.textContent.toLowerCase();
                if (!query || text.includes(query)) {
                    btn.style.display = 'flex';
                } else {
                    btn.style.display = 'none';
                }
            });
        });
    }

    // RFID Tap Area Click & Scan
    if (rfidTapArea && rfidScanInp) {
        rfidTapArea.addEventListener('click', function() {
            rfidScanInp.focus();
            rfidTapArea.style.borderColor = 'var(--amber)';
            rfidTapArea.style.background = 'rgba(245,158,11,0.06)';
            rfidStatus.textContent = 'Menunggu scan ID Card peserta...';
            rfidStatus.style.color = 'var(--amber)';
        });

        let scanBuffer = '';
        let scanTimer = null;

        rfidScanInp.addEventListener('input', function(e) {
            clearTimeout(scanTimer);
            scanBuffer = this.value.trim();

            scanTimer = setTimeout(function() {
                if (scanBuffer.length >= 4) {
                    submitCompletion({ rfid_tag: scanBuffer });
                    rfidScanInp.value = '';
                    scanBuffer = '';
                }
            }, 300);
        });

        rfidScanInp.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(scanTimer);
                const tag = this.value.trim();
                if (tag.length > 0) {
                    submitCompletion({ rfid_tag: tag });
                    this.value = '';
                }
            }
        });
    }

    // Manual Trainee Mark Button
    if (btnMarkMan && manualSelect) {
        btnMarkMan.addEventListener('click', function() {
            const traineeId = manualSelect.value;
            if (!traineeId) {
                alert('Silakan pilih peserta trainee dari daftar dropdown.');
                return;
            }
            submitCompletion({ trainee_id: traineeId });
        });
    }

    // AJAX submit completion
    function submitCompletion(params) {
        const payload = new FormData();
        payload.append('topic_key', currentTopicKey);
        payload.append('topic_title', currentTopicTitle);
        if (params.rfid_tag) payload.append('rfid_tag', params.rfid_tag);
        if (params.trainee_id) payload.append('trainee_id', params.trainee_id);

        if (rfidStatus) {
            rfidStatus.textContent = 'Memproses penandaan...';
            rfidStatus.style.color = 'var(--blue)';
        }

        fetch('<?= base_url('materials/mark-completion') ?>', {
            method: 'POST',
            body: payload,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Add to local state
                if (!topicCompletionsMap[currentTopicKey]) {
                    topicCompletionsMap[currentTopicKey] = [];
                }
                topicCompletionsMap[currentTopicKey].unshift(data.record);
                renderCompletionTable();

                if (rfidStatus) {
                    rfidStatus.textContent = '✓ ' + (data.trainee ? data.trainee.name : 'Peserta') + ' berhasil ditandai!';
                    rfidStatus.style.color = 'var(--green)';
                    setTimeout(() => {
                        rfidStatus.textContent = 'Klik di sini, lalu tap ID Card...';
                        rfidStatus.style.color = 'var(--muted)';
                        if (rfidTapArea) {
                            rfidTapArea.style.borderColor = 'var(--border)';
                            rfidTapArea.style.background = '#090d16';
                        }
                    }, 3000);
                }
            } else {
                alert(data.message || 'Gagal menandai materi.');
                if (rfidStatus) {
                    rfidStatus.textContent = 'Klik di sini, lalu tap ID Card...';
                    rfidStatus.style.color = 'var(--muted)';
                    if (rfidTapArea) {
                        rfidTapArea.style.borderColor = 'var(--border)';
                        rfidTapArea.style.background = '#090d16';
                    }
                }
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi saat mengirim data.');
            if (rfidStatus) {
                rfidStatus.textContent = 'Klik di sini, lalu tap ID Card...';
                rfidStatus.style.color = 'var(--muted)';
            }
        });
    }

    // AJAX delete completion
    function deleteCompletion(id) {
        fetch('<?= base_url('materials/delete-completion/') ?>' + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Remove from local state
                if (topicCompletionsMap[currentTopicKey]) {
                    topicCompletionsMap[currentTopicKey] = topicCompletionsMap[currentTopicKey].filter(item => item.id != id);
                }
                renderCompletionTable();
            } else {
                alert(data.message || 'Gagal menghapus penandaan.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi.');
        });
    }

    // Initial render
    renderCompletionTable();

    // Document Category Filter Pills Logic
    const filterDocBtns = document.querySelectorAll('.filter-doc-btn');
    const docItemCards  = document.querySelectorAll('.doc-item-card');

    filterDocBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterDocBtns.forEach(b => {
                b.classList.remove('active');
                b.style.background = 'var(--panel-2)';
                b.style.color = 'var(--muted)';
            });
            this.classList.add('active');
            this.style.background = 'var(--amber)';
            this.style.color = '#000';

            const selectedCat = this.getAttribute('data-cat');
            docItemCards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                if (selectedCat === 'ALL' || cardCat === selectedCat) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});

// Toggle new category text input when "NEW" is selected in select dropdown
function toggleNewCategoryInput(val) {
    const wrapper = document.getElementById('newCategoryWrapper');
    const input   = document.getElementById('newCategoryInput');
    if (!wrapper) return;
    
    if (val === 'NEW') {
        wrapper.style.display = 'block';
        if (input) {
            input.focus();
            input.required = true;
        }
    } else {
        wrapper.style.display = 'none';
        if (input) input.required = false;
    }
}
</script>
<?= $this->endSection() ?>
