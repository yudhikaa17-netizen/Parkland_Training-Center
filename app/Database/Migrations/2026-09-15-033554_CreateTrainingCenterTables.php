<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrainingCenterTables extends Migration
{
    public function up()
    {
        // 1. Users Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'default'    => 'guest', // superadmin, trainer, guest
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true);

        // 2. Trainees Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'rfid_tag' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
                'null'       => true,
            ],
            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'unique'     => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'gender' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'Laki-laki',
            ],
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'default'    => 'Production',
            ],
            'track' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Belum Ditentukan', // Sewing, Cutting, Assembly, Inhouse, Belum Ditentukan
            ],
            'stage' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Induction', // Induction, Motoric Test, Hard Skill, Upskill, OJT, Lulus, Tidak Lolos, Resign
            ],
            'cycle_time_motoric' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'null'       => true,
            ],
            'motoric_notes' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'default'    => 'Aktif', // Aktif, Lulus, Tidak Lolos, Resign
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('trainees', true);

        // 3. Daily Evaluations Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'trainee_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'evaluation_date' => [
                'type' => 'DATE',
            ],
            'stage' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'day_number' => [
                'type'       => 'INT',
                'constraint' => 4,
                'default'    => 1,
            ],
            'cycle_time' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'null'       => true,
            ],
            'target_cycle_time' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'null'       => true,
            ],
            'quality_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            'work_method' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'evaluator_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('daily_evaluations', true);

        // 4. Attendances Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'trainee_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'attendance_date' => [
                'type' => 'DATE',
            ],
            'time_in' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'time_out' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'Hadir', // Hadir, Izin, Sakit, Alpha
            ],
            'rfid_tapped_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'notes' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('attendances', true);

        // 5. Operator Loans Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'trainee_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'borrowing_department' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'line_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'duration_days' => [
                'type'       => 'INT',
                'constraint' => 4,
                'default'    => 1,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'default'    => 'Dipinjam', // Dipinjam, Selesai, Dibatalkan
            ],
            'purpose' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('operator_loans', true);

        // 6. Training Materials Table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'stage' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'All Stages',
            ],
            'track' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'All Tracks',
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'SOP', // SOP, Materi Teori, Video, Panduan Praktik
            ],
            'file_url' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'uploaded_by' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'default'    => 'Admin Training Center',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('training_materials', true);
    }

    public function down()
    {
        $this->forge->dropTable('training_materials', true);
        $this->forge->dropTable('operator_loans', true);
        $this->forge->dropTable('attendances', true);
        $this->forge->dropTable('daily_evaluations', true);
        $this->forge->dropTable('trainees', true);
        $this->forge->dropTable('users', true);
    }
}
