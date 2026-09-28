<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OperatorLoanModel;
use App\Models\TraineeModel;

class Loan extends BaseController
{
    protected $loanModel;
    protected $traineeModel;

    public function __construct()
    {
        $this->loanModel    = new OperatorLoanModel();
        $this->traineeModel = new TraineeModel();
    }

    public function index()
    {
        $selectedDate = $this->request->getGet('date');
        $loans        = $this->loanModel->getLoansWithTrainee($selectedDate);
        $trainees     = $this->traineeModel->whereIn('stage', ['Induction', 'Motoric Test', 'Hard Skill', 'OJT', 'Lulus'])->findAll();
        $factoryStats = $this->loanModel->getFactoryStats($selectedDate);

        // Count attendances (Hadir)
        $attendanceModel   = new \App\Models\AttendanceModel();
        $targetDate = !empty($selectedDate) ? $selectedDate : date('Y-m-d');
        
        $presentTodayCount = $attendanceModel->where('attendance_date', $targetDate)
            ->where('status', 'Hadir')
            ->countAllResults();

        // Fallback: If no attendance scanned on target date and no explicit date was requested, use latest attendance date or total active trainees
        if (empty($selectedDate) && $presentTodayCount === 0) {
            $latestAtt = $attendanceModel->selectMax('attendance_date')->where('status', 'Hadir')->first();
            $latestDate = $latestAtt['attendance_date'] ?? null;
            if ($latestDate) {
                $presentTodayCount = $attendanceModel->where('attendance_date', $latestDate)
                    ->where('status', 'Hadir')
                    ->countAllResults();
            }
            if ($presentTodayCount === 0) {
                $presentTodayCount = $this->traineeModel->whereIn('stage', ['Induction', 'Motoric Test', 'Hard Skill', 'OJT', 'Lulus'])->countAllResults();
            }
        }

        // Calculate Sisa Operator TC (Ready di Training Center)
        // Rumus: Operator Hadir - Total Operator Sedang Dipinjam
        $totalActiveLoans = $factoryStats['total_active'];
        $sisaOperatorTc   = max(0, $presentTodayCount - $totalActiveLoans);

        // Get IDs of trainees who currently have active loans
        $activeLoanTraineeIds = array_column($this->loanModel->where('status', 'Dipinjam')->findAll(), 'trainee_id');
        $availableTrainees = [];
        foreach ($trainees as $t) {
            if (!in_array($t['id'], $activeLoanTraineeIds)) {
                $availableTrainees[] = $t;
            }
        }

        $data = [
            'title'               => 'Peminjaman Operator Trainee',
            'current_role'        => session()->get('role'),
            'selected_date'       => $selectedDate,
            'loans'               => $loans,
            'trainees'            => $trainees,
            'available_trainees'  => $availableTrainees,
            'factory_stats'       => $factoryStats,
            'present_today_count' => $presentTodayCount,
            'sisa_operator_tc'    => $sisaOperatorTc,
        ];

        return view('loan/index', $data);
    }

    public function store()
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/loans')->with('error', 'Akses Ditolak.');
        }

        $startDate = $this->request->getPost('start_date') ?: date('Y-m-d');
        $endDate   = $this->request->getPost('end_date') ?: null;
        $duration  = (int) $this->request->getPost('duration_days') ?: 1;
        $factory   = $this->request->getPost('factory') ?: 'Factory 1 (F1)';

        $data = [
            'trainee_id'           => $this->request->getPost('trainee_id'),
            'factory'              => $factory,
            'borrowing_department' => $this->request->getPost('borrowing_department'),
            'person_in_charge'     => $this->request->getPost('person_in_charge') ?: 'Supervisor Line',
            'line_name'            => $this->request->getPost('line_name'),
            'start_date'           => $startDate,
            'end_date'             => $endDate,
            'duration_days'        => $duration,
            'status'               => 'Dipinjam',
            'purpose'              => $this->request->getPost('purpose'),
        ];

        $this->loanModel->insert($data);
        return redirect()->to('/loans')->with('success', 'Pengajuan peminjaman operator berhasil dibuat.');
    }

    public function returnLoan($id)
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/loans')->with('error', 'Akses Ditolak.');
        }

        $this->loanModel->update($id, [
            'status'   => 'Selesai',
            'end_date' => date('Y-m-d'),
        ]);

        return redirect()->to('/loans')->with('success', 'Operator telah berhasil dikembalikan ke Training Center.');
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/loans')->with('error', 'Akses Ditolak.');
        }

        $this->loanModel->delete($id);
        return redirect()->to('/loans')->with('success', 'Data peminjaman operator berhasil dihapus.');
    }
}
