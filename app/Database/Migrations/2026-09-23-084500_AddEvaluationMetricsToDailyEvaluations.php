<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEvaluationMetricsToDailyEvaluations extends Migration
{
    public function up()
    {
        $fields = [
            'pairs_checked' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 5,
                'null'       => true,
                'after'      => 'target_cycle_time',
            ],
            'defect_count' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
                'null'       => true,
                'after'      => 'pairs_checked',
            ],
            'total_steps' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 10,
                'null'       => true,
                'after'      => 'defect_count',
            ],
            'missed_steps' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
                'null'       => true,
                'after'      => 'total_steps',
            ],
            'sop_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 100.00,
                'null'       => true,
                'after'      => 'missed_steps',
            ],
        ];

        // Check if columns exist before adding
        $db = \Config\Database::connect();
        $existingFields = $db->getFieldNames('daily_evaluations');

        $fieldsToAdd = [];
        foreach ($fields as $fieldName => $fieldDef) {
            if (!in_array($fieldName, $existingFields)) {
                $fieldsToAdd[$fieldName] = $fieldDef;
            }
        }

        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('daily_evaluations', $fieldsToAdd);
        }
    }

    public function down()
    {
        $fields = ['pairs_checked', 'defect_count', 'total_steps', 'missed_steps', 'sop_score'];
        $this->forge->dropColumn('daily_evaluations', $fields);
    }
}
