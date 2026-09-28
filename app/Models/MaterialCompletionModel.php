<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialCompletionModel extends Model
{
    protected $table            = 'material_completions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'trainee_id',
        'topic_key',
        'topic_title',
        'marked_by',
        'marked_via',
        'completed_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get list of completions for a topic including trainee details
     */
    public function getCompletionsByTopic($topicKey)
    {
        return $this->select('material_completions.*, trainees.name as trainee_name, trainees.nik, trainees.rfid_tag, trainees.stage, trainees.track')
            ->join('trainees', 'trainees.id = material_completions.trainee_id')
            ->where('material_completions.topic_key', $topicKey)
            ->orderBy('material_completions.completed_at', 'DESC')
            ->findAll();
    }

    /**
     * Get all completions grouped by topic_key
     */
    public function getAllGroupedByTopic()
    {
        $all = $this->select('material_completions.*, trainees.name as trainee_name, trainees.nik, trainees.rfid_tag, trainees.stage, trainees.track')
            ->join('trainees', 'trainees.id = material_completions.trainee_id')
            ->orderBy('material_completions.completed_at', 'DESC')
            ->findAll();

        $grouped = [];
        foreach ($all as $item) {
            $grouped[$item['topic_key']][] = $item;
        }
        return $grouped;
    }

    /**
     * Get all completions for a specific trainee
     */
    public function getCompletionsByTrainee($traineeId)
    {
        return $this->where('trainee_id', $traineeId)
            ->orderBy('completed_at', 'DESC')
            ->findAll();
    }
}
