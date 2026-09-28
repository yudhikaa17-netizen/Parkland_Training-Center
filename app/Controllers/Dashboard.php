<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TraineeModel;
use App\Models\AttendanceModel;
use App\Models\OperatorLoanModel;
use App\Models\MaterialModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $traineeModel    = new TraineeModel();
        $attendanceModel = new AttendanceModel();
        $loanModel       = new OperatorLoanModel();
        $materialModel   = new MaterialModel();

        $stageCounts = $traineeModel->getCountsByStage();
        $totalTrainees = array_sum($stageCounts);

        $today = date('Y-m-d');
        $todayAttendances = $attendanceModel->getTodayAttendances($today);
        $totalAttendanceToday = count($todayAttendances);

        $activeLoans = $loanModel->where('status', 'Dipinjam')->findAll();
        $totalActiveLoans = count($activeLoans);

        $totalMaterials = $materialModel->countAllResults();

        $allTrainees = $traineeModel->orderBy('id', 'DESC')->findAll();
        $evalModel   = new \App\Models\DailyEvaluationModel();
        $allLoans    = $loanModel->findAll();
        $factoryStats = $loanModel->getFactoryStats();

        // Index active loans by trainee_id
        $activeLoanByTrainee = [];
        foreach ($allLoans as $l) {
            if ($l['status'] === 'Dipinjam') {
                $activeLoanByTrainee[$l['trainee_id']] = $l;
            }
        }

        // Attach latest evaluation & readiness info to trainees
        foreach ($allTrainees as &$t) {
            $latestEval = $evalModel->where('trainee_id', $t['id'])->orderBy('id', 'DESC')->first();
            $t['latest_cycle_time']  = $latestEval['cycle_time'] ?? $t['cycle_time_motoric'] ?? null;
            $t['target_cycle_time']  = $latestEval['target_cycle_time'] ?? 35.0;
            $t['latest_quality']     = $latestEval['quality_score'] ?? null;
            $t['active_loan']        = $activeLoanByTrainee[$t['id']] ?? null;

            if ($t['active_loan']) {
                $t['readiness_status'] = 'Dipinjam (' . ($t['active_loan']['factory'] ?? 'F1') . ')';
                $t['readiness_class']  = 'warning';
            } elseif (in_array($t['stage'], ['Lulus', 'OJT'])) {
                $t['readiness_status'] = 'Siap Dipinjam (Ready to Deploy)';
                $t['readiness_class']  = 'success';
            } elseif (in_array($t['stage'], ['Hard Skill', 'Motoric Test', 'Induction'])) {
                $t['readiness_status'] = 'Sedang Training (' . $t['stage'] . ')';
                $t['readiness_class']  = 'primary';
            } else {
                $t['readiness_status'] = $t['stage'];
                $t['readiness_class']  = 'secondary';
            }
        }
        unset($t);

        $todayAttendanceSummary = [
            'hadir'   => 0,
            'ijin'    => 0,
            'sakit'   => 0,
            'mangkir' => 0,
            'resign'  => 0,
        ];
        foreach ($todayAttendances as $att) {
            $st = strtolower($att['status'] ?? '');
            if ($st === 'hadir') $todayAttendanceSummary['hadir']++;
            elseif ($st === 'ijin' || $st === 'izin') $todayAttendanceSummary['ijin']++;
            elseif ($st === 'sakit') $todayAttendanceSummary['sakit']++;
            elseif ($st === 'mangkir' || $st === 'alpha') $todayAttendanceSummary['mangkir']++;
            elseif ($st === 'resign') $todayAttendanceSummary['resign']++;
        }

        $settingModel = new \App\Models\SiteSettingModel();
        $announcement = [
            'enabled' => $settingModel->getSetting('announcement_enabled', '1') === '1',
            'title'   => $settingModel->getSetting('announcement_title', 'Pengumuman Resmi Training Center'),
            'message' => $settingModel->getSetting('announcement_message', ''),
            'type'    => $settingModel->getSetting('announcement_type', 'info'),
        ];

        $presentTodayCount = $attendanceModel->where('attendance_date', $today)
            ->where('status', 'Hadir')
            ->countAllResults();

        // Fallback: If no attendance scanned today, use latest attendance date or total active trainees
        if ($presentTodayCount === 0) {
            $latestAtt = $attendanceModel->selectMax('attendance_date')->where('status', 'Hadir')->first();
            $latestDate = $latestAtt['attendance_date'] ?? null;
            if ($latestDate) {
                $presentTodayCount = $attendanceModel->where('attendance_date', $latestDate)
                    ->where('status', 'Hadir')
                    ->countAllResults();
            }
            if ($presentTodayCount === 0) {
                $presentTodayCount = $traineeModel->whereIn('stage', ['Induction', 'Motoric Test', 'Hard Skill', 'OJT', 'Lulus'])->countAllResults();
            }
        }

        $sisaOperatorTc = max(0, $presentTodayCount - ($factoryStats['total_active'] ?? 0));

        $data = [
            'title'                => 'Dashboard Summary',
            'current_role'         => session()->get('role'),
            'user_name'            => session()->get('name'),
            'stage_counts'         => $stageCounts,
            'total_trainees'       => $totalTrainees,
            'today_date'           => $today,
            'today_attendances'    => $todayAttendances,
            'today_attendance'     => $todayAttendanceSummary,
            'total_attendance'     => $totalAttendanceToday,
            'present_today_count'  => $presentTodayCount,
            'sisa_operator_tc'     => $sisaOperatorTc,
            'active_loans_count'   => $totalActiveLoans,
            'factory_stats'        => $factoryStats,
            'total_materials'      => $totalMaterials,
            'all_trainees'         => $allTrainees,
            'announcement'         => $announcement,
        ];

        return view('dashboard/index', $data);
    }
}
