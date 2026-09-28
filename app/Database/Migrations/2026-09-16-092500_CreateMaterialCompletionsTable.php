<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMaterialCompletionsTable extends Migration
{
    public function up()
    {
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
            'topic_key' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'topic_title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'marked_by' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'marked_via' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'manual', // manual, rfid
            ],
            'completed_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addKey(['trainee_id', 'topic_key']);
        $this->forge->createTable('material_completions', true);
    }

    public function down()
    {
        $this->forge->dropTable('material_completions', true);
    }
}
