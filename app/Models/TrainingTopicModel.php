<?php

namespace App\Models;

use CodeIgniter\Model;

class TrainingTopicModel extends Model
{
    protected $table            = 'training_topics';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'topic_key',
        'title',
        'icon',
        'category',
        'badge_color',
        'sort_order',
        'sub_title',
        'quote',
        'content_html',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get active topics ordered by sort_order
     */
    public function getActiveTopics()
    {
        return $this->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Get all topics ordered by sort_order
     */
    public function getAllOrdered()
    {
        return $this->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
