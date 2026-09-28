<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TrainingCenterSeeder extends Seeder
{
    public function run()
    {
        // 1. Users
        $users = [
            [
                'username'   => 'admin',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'name'       => 'Admin Training Center (Lean & Head Trainer)',
                'role'       => 'superadmin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'trainer',
                'password'   => password_hash('trainer123', PASSWORD_DEFAULT),
                'name'       => 'Trainer Parkland',
                'role'       => 'trainer',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'guest',
                'password'   => password_hash('guest123', PASSWORD_DEFAULT),
                'name'       => 'Guest / Viewer',
                'role'       => 'guest',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('users')->insertBatch($users);

        // 2. Trainees (Matching the dashboard screenshot: 5 total: 1 Induction, 0 Motoric, 0 Hard Skill, 0 Upskill, 1 OJT, 3 Lulus)
        $trainees = [
            [
                'rfid_tag'           => 'RFID-00184920',
                'nik'                => 'PWI-2026-001',
                'name'               => 'Ahmad Fauzi',
                'gender'             => 'Laki-laki',
                'department'         => 'Production - Sewing',
                'track'              => 'Belum Ditentukan',
                'stage'              => 'Induction',
                'cycle_time_motoric' => null,
                'motoric_notes'      => 'Baru masuk hari pertama, menjalani induksi teori & pengenalan K3.',
                'start_date'         => date('Y-m-d'),
                'status'             => 'Aktif',
                'notes'              => 'Peserta baru batch September 2026',
                'created_at'         => date('Y-m-d H:i:s'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
            [
                'rfid_tag'           => 'RFID-00273819',
                'nik'                => 'PWI-2026-002',
                'name'               => 'Budi Santoso',
                'gender'             => 'Laki-laki',
                'department'         => 'Production - Cutting',
                'track'              => 'Cutting',
                'stage'              => 'OJT',
                'cycle_time_motoric' => 34.50,
                'motoric_notes'      => 'Lulus motoric test 34.5 detik. Memilih track Cutting.',
                'start_date'         => date('Y-m-d', strtotime('-18 days')),
                'status'             => 'Aktif',
                'notes'              => 'Sedang menjalani OJT minggu ke-2 di Line Cutting A.',
                'created_at'         => date('Y-m-d H:i:s', strtotime('-18 days')),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
            [
                'rfid_tag'           => 'RFID-00391823',
                'nik'                => 'PWI-2026-003',
                'name'               => 'Citra Lestari',
                'gender'             => 'Perempuan',
                'department'         => 'Production - Sewing',
                'track'              => 'Sewing',
                'stage'              => 'Lulus',
                'cycle_time_motoric' => 31.20,
                'motoric_notes'      => 'Hasil motoric sangat baik, lulus Sewing dan siap penempatan penuh.',
                'start_date'         => date('Y-m-d', strtotime('-35 days')),
                'status'             => 'Lulus',
                'notes'              => 'Lulus dengan predikat memuaskan, ditempatkan di Sewing Line 4.',
                'created_at'         => date('Y-m-d H:i:s', strtotime('-35 days')),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
            [
                'rfid_tag'           => 'RFID-00481729',
                'nik'                => 'PWI-2026-004',
                'name'               => 'Dewi Anggraini',
                'gender'             => 'Perempuan',
                'department'         => 'Production - Assembly',
                'track'              => 'Assembly',
                'stage'              => 'Lulus',
                'cycle_time_motoric' => 38.00,
                'motoric_notes'      => 'Lulus motoric test 38.0 detik. Memilih track Assembly.',
                'start_date'         => date('Y-m-d', strtotime('-40 days')),
                'status'             => 'Lulus',
                'notes'              => 'Lulus OJT dan siap penempatan.',
                'created_at'         => date('Y-m-d H:i:s', strtotime('-40 days')),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
            [
                'rfid_tag'           => 'RFID-00561928',
                'nik'                => 'PWI-2026-005',
                'name'               => 'Eko Prasetyo',
                'gender'             => 'Laki-laki',
                'department'         => 'Production - Inhouse',
                'track'              => 'Inhouse',
                'stage'              => 'Lulus',
                'cycle_time_motoric' => 42.10,
                'motoric_notes'      => 'Cycle time motoric 42.1 detik -> Mengambil track Inhouse.',
                'start_date'         => date('Y-m-d', strtotime('-42 days')),
                'status'             => 'Lulus',
                'notes'              => 'Lulus training inhouse dengan performa stabil.',
                'created_at'         => date('Y-m-d H:i:s', strtotime('-42 days')),
                'updated_at'         => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('trainees')->insertBatch($trainees);

        // 3. Attendances
        $attendances = [
            [
                'trainee_id'      => 1,
                'attendance_date' => date('Y-m-d'),
                'time_in'         => '07:15:00',
                'time_out'        => null,
                'status'          => 'Hadir',
                'rfid_tapped_at'  => date('Y-m-d 07:15:22'),
                'notes'           => 'Tap RFID Gate 1 Training Center',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'trainee_id'      => 2,
                'attendance_date' => date('Y-m-d'),
                'time_in'         => '07:18:00',
                'time_out'        => null,
                'status'          => 'Hadir',
                'rfid_tapped_at'  => date('Y-m-d 07:18:05'),
                'notes'           => 'Tap RFID OJT Line Cutting',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('attendances')->insertBatch($attendances);

        // 4. Operator Loans
        $loans = [
            [
                'trainee_id'           => 2,
                'borrowing_department' => 'Cutting Dept',
                'line_name'            => 'Line Cutting 02',
                'start_date'           => date('Y-m-d', strtotime('-2 days')),
                'end_date'             => date('Y-m-d', strtotime('+3 days')),
                'duration_days'        => 5,
                'status'               => 'Dipinjam',
                'purpose'              => 'Bantuan lonjakan output produksi sepatu model running.',
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('operator_loans')->insertBatch($loans);

        // 5. Training Materials
        $materials = [
            [
                'title'       => 'Modul Induksi K3 & Regulasi Pabrik PT Parkland World Indonesia',
                'stage'       => 'Induction',
                'track'       => 'All Tracks',
                'category'    => 'SOP',
                'file_url'    => 'https://example.com/materials/sop-induksi-k3.pdf',
                'description' => 'Standar operasional prosedur pengenalan pabrik, keselamatan kerja (K3), budaya 5S, dan etika kerja.',
                'uploaded_by' => 'Lean & Head Trainer',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Panduan Pengujian Motorik & Ketelitian Tangan (Motoric Test)',
                'stage'       => 'Motoric Test',
                'track'       => 'All Tracks',
                'category'    => 'Panduan Praktik',
                'file_url'    => 'https://example.com/materials/guide-motoric-test.pdf',
                'description' => 'Tata cara tes motorik, stopwatch cycle time benchmark (< 40s untuk Sewing).',
                'uploaded_by' => 'Trainer Parkland',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Modul Hard Skill: Teknik Jahit Upper Sepatu (Sewing)',
                'stage'       => 'Hard Skill',
                'track'       => 'Sewing',
                'category'    => 'Materi Teori',
                'file_url'    => 'https://example.com/materials/hard-skill-sewing.pdf',
                'description' => 'Pelatihan teknik jahit dasar hingga kurva jahitan rumit selama 3 minggu.',
                'uploaded_by' => 'Trainer Parkland',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Form & Panduan Penilaian On Job Training (OJT 5 Pasang)',
                'stage'       => 'OJT',
                'track'       => 'All Tracks',
                'category'    => 'SOP',
                'file_url'    => 'https://example.com/materials/sop-ojt-assessment.pdf',
                'description' => 'Matriks penilaian 5 pasang hasil proses operator baru mencakup Kualitas, Cycle Time, dan Sikap Kerja.',
                'uploaded_by' => 'Lean & Head Trainer',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('training_materials')->insertBatch($materials);
    }
}
