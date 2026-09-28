<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CmsDefaultSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Seed Site Settings if not exists
        $settings = [
            [
                'setting_key'   => 'announcement_enabled',
                'setting_value' => '1',
                'setting_group' => 'announcement',
                'description'   => 'Status aktif banner pengumuman di Dashboard (1=Aktif, 0=Nonaktif)',
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'announcement_title',
                'setting_value' => 'Pengumuman Resmi Training Center Parkland World Indonesia',
                'setting_group' => 'announcement',
                'description'   => 'Judul banner pengumuman Dashboard',
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'announcement_message',
                'setting_value' => 'Seluruh proses training berpedoman pada standar Lean Kaizen dan Target Cycle Time. Pastikan melakukan tap RFID absensi harian dan pencatatan evaluasi berkala.',
                'setting_group' => 'announcement',
                'description'   => 'Teks isi pengumuman Dashboard',
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'announcement_type',
                'setting_value' => 'info',
                'setting_group' => 'announcement',
                'description'   => 'Warna tema banner: info (biru), warning (amber), success (hijau), danger (merah)',
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'cycle_time_motoric_max',
                'setting_value' => '40.0',
                'setting_group' => 'thresholds',
                'description'   => 'Batas maksimum cycle time motoric untuk dapat memilih track Sewing (detik)',
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'cycle_time_hardskill_target',
                'setting_value' => '35.0',
                'setting_group' => 'thresholds',
                'description'   => 'Standar target cycle time tahap Hard Skill (detik)',
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'cycle_time_ojt_target',
                'setting_value' => '30.0',
                'setting_group' => 'thresholds',
                'description'   => 'Standar target cycle time tahap OJT (detik)',
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'factory_list',
                'setting_value' => json_encode([
                    "Factory 1 (F1)",
                    "Factory 2 (F2)",
                    "Factory 3 (F3)",
                    "Factory 4 (F4)",
                    "Factory 5 (F5)",
                    "Bottom 1",
                    "Bottom 2",
                    "Inhouse"
                ]),
                'setting_group' => 'factories',
                'description'   => 'Daftar lokasi pabrik / lini produksi',
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'induction_modules',
                'setting_value' => json_encode([
                    'M-01' => 'Company Profile & Nilai-Nilai PT Parkland World Indonesia',
                    'M-02' => 'Keselamatan & Kesehatan Kerja (K3) serta Tanggap Darurat',
                    'M-03' => 'Budaya 5S (Seiri, Seiton, Seiso, Seiketsu, Shitsuke) & Lean Culture',
                    'M-04' => 'Peraturan Perusahaan (PP), Disiplin & Tata Tertib Kerja',
                    'M-05' => 'Pengenalan Fasilitas, Mesin & Peralatan Dasar Sepatu',
                    'M-06' => 'Standar Mutu Dasar (Quality Basic & No Defect Mindset)',
                ]),
                'setting_group' => 'curriculum',
                'description'   => 'Daftar materi modul kurikulum induksi',
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($settings as $s) {
            $exist = $db->table('site_settings')->where('setting_key', $s['setting_key'])->countAllResults();
            if ($exist == 0) {
                $db->table('site_settings')->insert($s);
            }
        }

        // 2. Seed Training Topics
        $topicCount = $db->table('training_topics')->countAllResults();
        if ($topicCount == 0) {
            $now = date('Y-m-d H:i:s');
            $topics = [
                [
                    'topic_key'    => 'benang_jahit',
                    'title'        => 'Benang Jahit',
                    'icon'         => 'bi bi-bezier2',
                    'category'     => 'Bahan Baku',
                    'badge_color'  => 'var(--amber)',
                    'sort_order'   => 1,
                    'sub_title'    => 'Karakteristik, jenis serat, ukuran ketebalan benang, dan toleransi tegangan jahitan.',
                    'quote'        => 'Pemilihan jenis dan ukuran benang yang tepat menjamin kekuatan jahitan upper saat penarikan toe lasting dan pemakaian ekspor.',
                    'content_html' => '<div class="part-grid"><div class="part-card"><span class="num">1</span><span class="t text-white">Nylon Bonded (#20 - #30)</span><p>Benang filamen berkekuatan tinggi berlapis resin pelindung, digunakan untuk stitching komponen heavy-duty dan zigzag toe.</p></div><div class="part-card"><span class="num">2</span><span class="t text-white">Spun Polyester (#40)</span><p>Standar jahitan umum upper sepatu lari, memiliki kelenturan seimbang dan tahan gesekan jarum berkecepatan tinggi.</p></div><div class="part-card"><span class="num">3</span><span class="t text-white">Continuous Filament (#60)</span><p>Benang halus untuk penjahitan lining (lapisan dalam) dan collar padding agar lembut saat bersentuhan dengan kaki.</p></div><div class="part-card"><span class="num">4</span><span class="t text-white">Waterproof Thread</span><p>Benang khusus berpelapis hydrophobic untuk sepatu outdoor dan waterproof membrane.</p></div></div>',
                    'is_active'    => 1,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'topic_key'    => 'jarum_jahit',
                    'title'        => 'Jarum Jahit',
                    'icon'         => 'bi bi-pin-angle-fill',
                    'category'     => 'Peralatan',
                    'badge_color'  => 'var(--blue)',
                    'sort_order'   => 2,
                    'sub_title'    => 'Sistem jarum, ukuran nomor jarum, tipe ujung (point), dan prosedur penanganan jarum patah.',
                    'quote'        => 'Penerapan SOP Needle Management 100% mencegah risiko jarum patah tertinggal di dalam sepatu konsumen.',
                    'content_html' => '<div class="part-grid"><div class="part-card"><span class="num">1</span><span class="t text-white">DPx5 / 134R (Nm 80 - 100)</span><p>Sistem jarum standar untuk mesin flat-bed single needle pada kain mesh dan synthetic upper.</p></div><div class="part-card"><span class="num">2</span><span class="t text-white">DPx17 / 135x17 (Nm 100 - 130)</span><p>Batang jarum tebal untuk mesin post-bed dan bahan kulit tebal, mampu menahan lenturan saat jahitan tumit.</p></div><div class="part-card"><span class="num">3</span><span class="t text-white">Point Type R (Round Point)</span><p>Ujung bulat normal untuk material tekstil dan mesh agar tidak memutus serat benang kain.</p></div><div class="part-card"><span class="num">4</span><span class="t text-white">Point Type LR/LL (Cutting Point)</span><p>Ujung pipih menyerong seperti pisau mini untuk menembus material kulit asli dan sintetis tebal.</p></div></div>',
                    'is_active'    => 1,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'topic_key'    => 'no_sew',
                    'title'        => 'No Sew Machine',
                    'icon'         => 'bi bi-fire',
                    'category'     => 'Mesin',
                    'badge_color'  => 'var(--amber)',
                    'sort_order'   => 3,
                    'sub_title'    => 'Teknologi pengeleman panas tanpa benang untuk upper ringan dan modern.',
                    'quote'        => 'Kombinasi akurat antara Suhu (Temperature), Tekanan (Pressure), dan Waktu (Dwell Time) adalah kunci no-sew bonding yang tahan cuci.',
                    'content_html' => '<div class="part-grid"><div class="part-card"><span class="num">1</span><span class="t text-white">Suhu Pemanasan (135°C – 165°C)</span><p>Diatur sesuai ketebalan film TPU. Terlalu panas membuat bahan terbakar/menguning, kurang panas lem tidak rekat.</p></div><div class="part-card"><span class="num">2</span><span class="t text-white">Tekanan Pneumatik (3.0 – 5.0 Bar)</span><p>Memastikan film TPU menyatu rata ke dalam pori-pori kain mesh tanpa membentuk kantung udara.</p></div><div class="part-card"><span class="num">3</span><span class="t text-white">Waktu Tekan (12 – 20 Detik)</span><p>Waktu pemanasan dan pressing stabil yang diatur otomatis dengan timer digital mesin.</p></div><div class="part-card"><span class="num">4</span><span class="t text-white">Uji Daya Rekat (Peeling Test)</span><p>Pemeriksaan rutin setiap awal shift untuk memastikan bonding tidak terkelupas saat penarikan.</p></div></div>',
                    'is_active'    => 1,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'topic_key'    => 'assembly',
                    'title'        => 'Assembly Process',
                    'icon'         => 'bi bi-gear-wide-connected',
                    'category'     => 'Proses',
                    'badge_color'  => 'var(--green)',
                    'sort_order'   => 4,
                    'sub_title'    => 'Alur penggabungan Upper dan Bottom sol dari lasting hingga box packaging.',
                    'quote'        => 'MC = proses yang memerlukan penggunaan mesin. MN = proses manual yang tidak memerlukan mesin.',
                    'content_html' => '<div class="chip-flow mb-3"><span class="chip">1. Toe Lasting [MC]</span><span class="arrow">→</span><span class="chip">2. Heel Lasting [MC]</span><span class="arrow">→</span><span class="chip">3. Roughing [MC/MN]</span><span class="arrow">→</span><span class="chip">4. Primer & Glue [MN]</span><span class="arrow">→</span><span class="chip">5. Heating Oven [MC]</span><span class="arrow">→</span><span class="chip">6. Sole Press [MC]</span><span class="arrow">→</span><span class="chip">7. Chilling [MC]</span><span class="arrow">→</span><span class="chip">8. Finishing & QC [MN]</span></div>',
                    'is_active'    => 1,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'topic_key'    => 'sewing_machine',
                    'title'        => 'Sewing Machine',
                    'icon'         => 'bi bi-tools',
                    'category'     => 'Mesin',
                    'badge_color'  => 'var(--cyan)',
                    'sort_order'   => 5,
                    'sub_title'    => 'Tipe mesin jahit industri alas kaki dan prosedur pemeliharaan harian (TPM).',
                    'quote'        => 'Pemeriksaan harian mesin jahit sebelum bekerja mencegah kerusakan jarum dan benang putus di tengah siklus.',
                    'content_html' => '<div class="part-grid"><div class="part-card"><span class="num">1</span><span class="t text-white">Flat Bed Single / Double Needle</span><p>Mesin jahit meja datar untuk menyatukan panel upper dasar, tongue, dan jahitan lurus.</p></div><div class="part-card"><span class="num">2</span><span class="t text-white">Post Bed Machine</span><p>Tiang vertikal memudahkan manuver jahitan 3 dimensi pada lengkungan tumit dan collar sepatu.</p></div><div class="part-card"><span class="num">3</span><span class="t text-white">Cylinder Bed Machine</span><p>Silinder horizontal untuk penjahitan toe box dan area tubular tertutup yang sempit.</p></div><div class="part-card"><span class="num">4</span><span class="t text-white">Automatic Pattern Sewer (CNC)</span><p>Mesin jahit berpola komputer untuk jahitan logo, velcro, dan pola dekoratif presisi tinggi.</p></div></div>',
                    'is_active'    => 1,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'topic_key'    => 'cutting_proses',
                    'title'        => 'Cutting Proses',
                    'icon'         => 'bi bi-scissors',
                    'category'     => 'Proses',
                    'badge_color'  => 'var(--red)',
                    'sort_order'   => 6,
                    'sub_title'    => 'Metode pemotongan pola upper, efisiensi bahan (nesting yield), dan arah serat kain.',
                    'quote'        => 'Ketelitian pola potong memastikan konsistensi dimensi sepatu saat penjahitan dan assembling.',
                    'content_html' => '<div class="part-grid"><div class="part-card"><span class="num">1</span><span class="t text-white">Mesin Pons Hidrolik (Clicker Press)</span><p>Pemotongan manual menggunakan pisau dies baja dengan penekanan hidrolik bertekanan tinggi.</p></div><div class="part-card"><span class="num">2</span><span class="t text-white">Auto Cutting Laser / CNC Blade</span><p>Pemotongan multi-layer terprogram otomatis dari file CAD untuk hasil potong bersih dan presisi.</p></div><div class="part-card"><span class="num">3</span><span class="t text-white">Arah Alur Bahan (Grain Line)</span><p>Pola toe cap dan quarter wajib dipotong mengikuti alur kemuluran bahan yang telah ditentukan.</p></div><div class="part-card"><span class="num">4</span><span class="t text-white">Nesting & Yield Optimization</span><p>Teknik penataan tata letak pola potong untuk meminimalkan sisa limbah kain/kulit mentah.</p></div></div>',
                    'is_active'    => 1,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'topic_key'    => 'cutting_tools',
                    'title'        => 'Cutting Tools',
                    'icon'         => 'bi bi-hammer',
                    'category'     => 'Peralatan',
                    'badge_color'  => 'var(--orange)',
                    'sort_order'   => 7,
                    'sub_title'    => 'Spesifikasi pisau potong, talenan cutting pad, dan K3 penggunaan alat tajam.',
                    'quote'        => 'Gunakan selalu sarung tangan kawat baja (chainmail glove) saat mengoperasikan alat potong.',
                    'content_html' => '<div class="d-flex flex-column gap-2 mb-3"><div class="p-3 rounded-2 border d-flex align-items-center gap-3" style="background:var(--panel-2);border-color:var(--border) !important;"><span class="badge slate font-monospace fw-bold" style="font-size:0.9rem;padding:6px 12px;">DIES</span><div><div class="fw-bold text-white">Cutting Dies Baja (Single & Double Edge)</div><div class="small" style="color:var(--muted);">Wajib diperiksa ketajaman dan kerataan bibir pisau sebelum digunakan pada awal shift.</div></div></div><div class="p-3 rounded-2 border d-flex align-items-center gap-3" style="background:var(--panel-2);border-color:var(--border) !important;"><span class="badge ojt font-monospace fw-bold" style="font-size:0.9rem;padding:6px 12px;">PAD</span><div><div class="fw-bold text-white">Cutting Board / Polyethylene Pad</div><div class="small" style="color:var(--muted);">Alas potong wajib diratakan secara berkala (shaving) agar kedalaman potong merata.</div></div></div></div>',
                    'is_active'    => 1,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'topic_key'    => 'inhouse',
                    'title'        => 'Inhouse Treatment',
                    'icon'         => 'bi bi-mortarboard-fill',
                    'category'     => 'Treatment',
                    'badge_color'  => 'var(--purple)',
                    'sort_order'   => 8,
                    'sub_title'    => 'Skiving, splitting, embossing, cementing, dan perlakuan khusus sebelum perakitan.',
                    'quote'        => 'Preparasi material yang sempurna memudahkan proses penjahitan panel dan penempelan sol.',
                    'content_html' => '<div class="part-grid"><div class="part-card"><span class="num">1</span><span class="t text-white">Skiving (Penipisan Pinggir)</span><p>Menipiskan pinggiran panel bahan agar sambungan jahitan tumpuk tidak tebal dan kaku.</p></div><div class="part-card"><span class="num">2</span><span class="t text-white">Splitting (Penyamaan Tebal)</span><p>Membelah lembaran kulit asli untuk mendapatkan ketebalan milimeter yang seragam.</p></div><div class="part-card"><span class="num">3</span><span class="t text-white">Embossing & Logo Printing</span><p>Pemberian motif tekstur, cap panas (hot stamp), dan sablon logo merek.</p></div><div class="part-card"><span class="num">4</span><span class="t text-white">UV & Plasma Primer Treatment</span><p>Aktivasi permukaan sol karet/EVA dengan radiasi UV agar lem menempel kuat secara molekuler.</p></div></div>',
                    'is_active'    => 1,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'topic_key'    => 'material_kulit',
                    'title'        => 'Material Kulit',
                    'icon'         => 'bi bi-layers-fill',
                    'category'     => 'Bahan Baku',
                    'badge_color'  => 'var(--amber)',
                    'sort_order'   => 9,
                    'sub_title'    => 'Pengenalan serat kulit sapi asli, PU sintetis, microfiber, dan mesh fabric.',
                    'quote'        => 'Kenali sifat kemuluran tiap jenis kulit untuk menghindari cacat kerut dan sobek saat lasting.',
                    'content_html' => '<div class="part-grid"><div class="part-card"><span class="num">1</span><span class="t text-white">Full Grain & Action Leather</span><p>Kulit sapi asli lapisan atas dengan ketahanan sobek tinggi, membutuhkan jarum LR dan benang bonded.</p></div><div class="part-card"><span class="num">2</span><span class="t text-white">Suede & Nubuck Leather</span><p>Kulit bertekstur beludru halus, sensitif terhadap noda lem dan membutuhkan penanganan bersih.</p></div><div class="part-card"><span class="num">3</span><span class="t text-white">Synthetic PU / Microfiber</span><p>Material sintetis pengganti kulit dengan elastisitas stabil dan ketahanan abrasi tinggi.</p></div><div class="part-card"><span class="num">4</span><span class="t text-white">Engineered Mesh Fabric</span><p>Kain rajut bernapas untuk upper sepatu olahraga modern, memerlukan jarum round point.</p></div></div>',
                    'is_active'    => 1,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
                [
                    'topic_key'    => 'skill_level',
                    'title'        => 'Skill Level',
                    'icon'         => 'bi bi-award-fill',
                    'category'     => 'Standar Mutu',
                    'badge_color'  => 'var(--green)',
                    'sort_order'   => 10,
                    'sub_title'    => 'Matriks jenjang kemahiran dan sertifikasi kompetensi operator di PT Parkland World Indonesia.',
                    'quote'        => 'Sertifikasi kompetensi berkala memastikan peningkatan karir dan stabilitas produktivitas line.',
                    'content_html' => '<div class="d-flex flex-column gap-2 mb-4"><div class="p-3 rounded-2 border d-flex align-items-center gap-3" style="background:var(--panel-2);border-color:var(--border) !important;"><span class="badge slate font-monospace fw-bold" style="font-size:0.9rem;padding:6px 12px;">SL 1</span><div><div class="fw-bold text-white">Trainee Baru / Dasar</div><div class="small" style="color:var(--muted);">Mampu mengerjakan proses sangat sederhana dengan pengawasan penuh trainer di Training Center.</div></div></div><div class="p-3 rounded-2 border d-flex align-items-center gap-3" style="background:var(--panel-2);border-color:var(--border) !important;"><span class="badge ojt font-monospace fw-bold" style="font-size:0.9rem;padding:6px 12px;">SL 2</span><div><div class="fw-bold text-white">Operator Mandiri Terbatas</div><div class="small" style="color:var(--muted);">Mampu mengerjakan 1 proses standar sesuai target cycle time tanpa cacat kritis di line OJT.</div></div></div><div class="p-3 rounded-2 border d-flex align-items-center gap-3" style="background:var(--panel-2);border-color:var(--border) !important;"><span class="badge training font-monospace fw-bold" style="font-size:0.9rem;padding:6px 12px;">SL 3</span><div><div class="fw-bold text-white">Operator Terampil (Multi-Skilled)</div><div class="small" style="color:var(--muted);">Mampu mengoperasikan 2-3 mesin atau proses berbeda dengan cycle time stabil dan kualitas prima.</div></div></div><div class="p-3 rounded-2 border d-flex align-items-center gap-3" style="background:var(--panel-2);border-color:var(--border) !important;"><span class="badge lulus font-monospace fw-bold" style="font-size:0.9rem;padding:6px 12px;">SL 4</span><div><div class="fw-bold text-white">Senior Operator / Trouble-shooter</div><div class="small" style="color:var(--muted);">Menguasai seluruh proses di cell dan mampu membantu operator lain mengatasi kendala teknis.</div></div></div><div class="p-3 rounded-2 border d-flex align-items-center gap-3" style="background:var(--panel-2);border-color:var(--border) !important;"><span class="badge upskill font-monospace fw-bold" style="font-size:0.9rem;padding:6px 12px;">SL 5</span><div><div class="fw-bold text-white">Trainer / Line Leader Specialist</div><div class="small" style="color:var(--muted);">Mampu melatih trainee baru, mengukur cycle time target, dan menerapkan perbaikan Lean Kaizen.</div></div></div></div>',
                    'is_active'    => 1,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ],
            ];

            $db->table('training_topics')->insertBatch($topics);
        }
    }
}
