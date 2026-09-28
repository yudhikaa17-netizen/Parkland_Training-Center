<?php

namespace App\Models;

use CodeIgniter\Model;

class DailyEvaluationModel extends Model
{
    protected $table            = 'daily_evaluations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'trainee_id',
        'evaluation_date',
        'stage',
        'day_number',
        'cycle_time',
        'target_cycle_time',
        'pairs_checked',
        'defect_count',
        'total_steps',
        'missed_steps',
        'sop_score',
        'quality_score',
        'work_method',
        'notes',
        'evaluator_id',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getEvaluationsByTrainee($traineeId)
    {
        return $this->where('trainee_id', $traineeId)
            ->orderBy('evaluation_date', 'ASC')
            ->findAll();
    }
}
