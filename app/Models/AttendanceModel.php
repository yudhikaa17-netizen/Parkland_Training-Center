<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table            = 'attendances';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'trainee_id',
        'attendance_date',
        'time_in',
        'time_out',
        'status',
        'rfid_tapped_at',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getTodayAttendances($date = null)
    {
        $date = $date ?? date('Y-m-d');
        return $this->select('attendances.*, trainees.name as trainee_name, trainees.nik, trainees.stage, trainees.track, trainees.rfid_tag')
            ->join('trainees', 'trainees.id = attendances.trainee_id')
            ->where('attendances.attendance_date', $date)
            ->orderBy('attendances.time_in', 'ASC')
            ->findAll();
    }
}
