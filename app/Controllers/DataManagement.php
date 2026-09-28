<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Database\Database;

class DataManagement extends BaseController
{
    public function resetAll()
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak: Hanya Superadmin yang memiliki izin mereset data.');
        }

        $db = \Config\Database::connect();
        
        // Truncate tables
        $db->table('training_materials')->emptyTable();
        $db->table('operator_loans')->emptyTable();
        $db->table('attendances')->emptyTable();
        $db->table('daily_evaluations')->emptyTable();
        $db->table('trainees')->emptyTable();
        $db->table('users')->emptyTable();

        // Run seeder
        $seeder = \Config\Database::seeder();
        $seeder->call('TrainingCenterSeeder');

        return redirect()->to('/dashboard')->with('success', 'Semua data berhasil direset dan diisi kembali dengan data standar Training Center!');
    }
}
