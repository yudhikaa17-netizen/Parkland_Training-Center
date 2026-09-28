<?php

namespace App\Models;

use CodeIgniter\Model;

class TraineeModel extends Model
{
    protected $table            = 'trainees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'rfid_tag',
        'nik',
        'name',
        'gender',
        'department',
        'track',
        'skill_level',
        'stage',
        'cycle_time_motoric',
        'motoric_notes',
        'start_date',
        'status',
        'induction_materials',
        'notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getCountsByStage()
    {
        $stages = [
            'Induction'    => 0,
            'Motoric Test' => 0,
            'Hard Skill'   => 0,
            'Upskill'      => 0,
            'OJT'          => 0,
            'Lulus'        => 0,
            'Tidak Lolos'  => 0,
            'Resign'       => 0,
        ];

        $results = $this->select('stage, COUNT(*) as total')
            ->groupBy('stage')
            ->findAll();

        foreach ($results as $row) {
            if (isset($stages[$row['stage']])) {
                $stages[$row['stage']] = (int) $row['total'];
            }
        }

        return $stages;
    }
}
