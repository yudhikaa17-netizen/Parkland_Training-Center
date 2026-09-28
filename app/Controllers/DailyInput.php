<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\CardHelper;
use App\Models\TraineeModel;
use App\Models\DailyEvaluationModel;

class DailyInput extends BaseController
{
    protected $traineeModel;
    protected $evaluationModel;

    public function __construct()
    {
        $this->traineeModel    = new TraineeModel();
        $this->evaluationModel = new DailyEvaluationModel();
    }

    public function index()
    {
        $today = date('Y-m-d');
        $attendanceModel = new \App\Models\AttendanceModel();

        // Get today's attendance records (Status: Hadir)
        $todayAttendances = $attendanceModel->where('attendance_date', $today)
            ->where('status', 'Hadir')
            ->findAll();

        $attendanceMap = [];
        $attendedTraineeIds = [];
        foreach ($todayAttendances as $att) {
            $attendanceMap[$att['trainee_id']] = $att;
            $attendedTraineeIds[] = (int) $att['trainee_id'];
        }

        // 1. Motoric Test Trainees:
        // Candidates are trainees with stage in ('Motoric Test', 'Induction', 'Belum Ditentukan') OR who tapped attendance today (and not yet in Hard Skill/OJT/Lulus/Resign)
        $motoricCandidates = $this->traineeModel->groupStart()
                ->whereIn('stage', ['Motoric Test', 'Induction', 'Belum Ditentukan'])
                ->orWhere('stage', '')
                ->orWhere('stage IS NULL')
            ->groupEnd()
            ->whereNotIn('stage', ['Hard Skill', 'OJT', 'Lulus', 'Resign', 'Tidak Lolos'])
            ->where('status', 'Aktif')
            ->findAll();

        // Also add any trainee who tapped attendance today and is not yet in Hard Skill/OJT/Lulus
        if (!empty($attendedTraineeIds)) {
            $attendedTrainees = $this->traineeModel->whereIn('id', $attendedTraineeIds)
                ->whereNotIn('stage', ['Hard Skill', 'OJT', 'Lulus', 'Resign', 'Tidak Lolos'])
                ->findAll();

            $candidateIds = array_column($motoricCandidates, 'id');
            foreach ($attendedTrainees as $at) {
                if (!in_array($at['id'], $candidateIds)) {
                    $motoricCandidates[] = $at;
                }
            }
        }

        // Attach attendance info to each motoric trainee
        foreach ($motoricCandidates as &$t) {
            $t['has_attended_today'] = isset($attendanceMap[$t['id']]);
            $t['attendance_time_in'] = $attendanceMap[$t['id']]['time_in'] ?? null;
            $t['attendance_time_out'] = $attendanceMap[$t['id']]['time_out'] ?? null;
        }
        unset($t);

        // Sort: trainees who tapped attendance today appear at the top, then newest ID
        usort($motoricCandidates, function ($a, $b) {
            if ($a['has_attended_today'] !== $b['has_attended_today']) {
                return $b['has_attended_today'] <=> $a['has_attended_today'];
            }
            return $b['id'] <=> $a['id'];
        });

        $motoricTrainees = $motoricCandidates;

        // 2. Hard Skill Trainees
        $hardSkillTrainees = $this->traineeModel->where('stage', 'Hard Skill')->where('status', 'Aktif')->findAll();
        foreach ($hardSkillTrainees as &$t) {
            $t['has_attended_today'] = isset($attendanceMap[$t['id']]);
            $t['attendance_time_in'] = $attendanceMap[$t['id']]['time_in'] ?? null;
        }
        unset($t);

        // 3. OJT Trainees
        $ojtTrainees = $this->traineeModel->where('stage', 'OJT')->where('status', 'Aktif')->findAll();
        foreach ($ojtTrainees as &$t) {
            $t['has_attended_today'] = isset($attendanceMap[$t['id']]);
            $t['attendance_time_in'] = $attendanceMap[$t['id']]['time_in'] ?? null;
        }
        unset($t);

        $settingModel = new \App\Models\SiteSettingModel();
        $factoryList  = $settingModel->getSetting('factory_list', ['Factory 1 (F1)', 'Factory 2 (F2)', 'Factory 3 (F3)', 'Factory 4 (F4)', 'Factory 5 (F5)', 'Bottom 1', 'Bottom 2', 'Inhouse']);

        $data = [
            'title'               => 'Input Data Harian (Massal)',
            'current_role'        => session()->get('role'),
            'motoric_trainees'    => $motoricTrainees,
            'hardskill_trainees'  => $hardSkillTrainees,
            'ojt_trainees'        => $ojtTrainees,
            'today_date'          => $today,
            'factory_list'        => $factoryList,
        ];

        return view('daily_input/index', $data);
    }

    public function saveMotoric()
    {
        $userRole = session()->get('role');
        if (!in_array($userRole, ['superadmin', 'trainer'])) {
            return redirect()->to('/daily-input')->with('error', 'Akses Ditolak.');
        }

        $traineeId       = $this->request->getPost('trainee_id');
        $cycleTime       = (float) $this->request->getPost('cycle_time');
        $targetCycleTime = (float) ($this->request->getPost('target_cycle_time') ?: 40.00);
        $track           = $this->request->getPost('track');
        $notes           = $this->request->getPost('notes');
        $nextAction      = $this->request->getPost('next_action'); // 'advance' or 'stay'

        // Validation rule: Cycle time >= 40 seconds -> Sewing track is not allowed
        if ($cycleTime >= 40.0 && $track === 'Sewing') {
            return redirect()->back()->with('error', 'Validasi Gagal: Cycle time ≥ 40 detik tidak memenuhi syarat untuk memilih track Sewing (Sewing tidak tersedia). Silakan pilih Cutting, Assembly, atau Inhouse.');
        }

        $updateData = [
            'cycle_time_motoric' => $cycleTime,
            'motoric_notes'      => $notes,
            'track'              => $track,
        ];

        if ($nextAction === 'advance') {
            $updateData['stage'] = 'Hard Skill';
        } else {
            $updateData['stage'] = 'Motoric Test';
        }

        $this->traineeModel->update($traineeId, $updateData);

        // Record daily evaluation
        $this->evaluationModel->insert([
            'trainee_id'        => $traineeId,
            'evaluation_date'   => date('Y-m-d'),
            'stage'             => 'Motoric Test',
            'day_number'        => 1,
            'cycle_time'        => $cycleTime,
            'target_cycle_time' => $targetCycleTime,
            'work_method'       => 'Motoric Standard',
            'notes'             => $notes . ' (Target CT: ' . $targetCycleTime . 's, Track: ' . $track . ')',
            'evaluator_id'      => session()->get('user_id'),
        ]);

        return redirect()->to('/daily-input')->with('success', 'Hasil Motoric Test (Target CT: ' . $targetCycleTime . 's) berhasil disimpan.');
    }

    public function saveHardSkill()
    {
        $userRole = session()->get('role');
        if (!in_array($userRole, ['superadmin', 'trainer'])) {
            return redirect()->to('/daily-input')->with('error', 'Akses Ditolak.');
        }

        $traineeId        = $this->request->getPost('trainee_id');
        $evaluationDate   = $this->request->getPost('evaluation_date') ?: date('Y-m-d');
        $cycleTime        = (float) $this->request->getPost('cycle_time');
        $targetCycleTime  = (float) ($this->request->getPost('target_cycle_time') ?: 35.00);
        $pairsChecked     = (int) ($this->request->getPost('pairs_checked') ?: 5);
        $defectCount      = (int) ($this->request->getPost('defect_count') ?: 0);
        $totalSteps       = (int) ($this->request->getPost('total_steps') ?: 10);
        $missedSteps      = (int) ($this->request->getPost('missed_steps') ?: 0);
        
        $qualityScore     = (float) ($this->request->getPost('quality_score') ?? max(0, min(100, round((($pairsChecked - $defectCount) / max(1, $pairsChecked)) * 100))));
        $sopScore         = (float) ($this->request->getPost('sop_score') ?? max(0, min(100, round((($totalSteps - $missedSteps) / max(1, $totalSteps)) * 100))));
        $sopCompliance    = $this->request->getPost('sop_compliance') ?: 'SOP: ' . $sopScore . '%';
        $notes            = $this->request->getPost('notes');
        $advanceToOjt     = $this->request->getPost('advance_to_ojt');

        // Item 9: Auto Progress calculation (Day Number)
        $previousLogs = $this->evaluationModel->where('trainee_id', $traineeId)
            ->where('stage', 'Hard Skill')
            ->countAllResults();
        $dayNumber = $previousLogs + 1;

        // OJT Placement Modal Data (Item 13)
        $ojtFactory = $this->request->getPost('ojt_factory') ?: 'Factory 1 (F1)';
        $ojtCell    = $this->request->getPost('ojt_cell') ?: 'Line 01';
        $ojtProcess = $this->request->getPost('ojt_process') ?: 'Sewing Upper';

        $this->evaluationModel->insert([
            'trainee_id'        => $traineeId,
            'evaluation_date'   => $evaluationDate,
            'stage'             => 'Hard Skill',
            'day_number'        => $dayNumber,
            'cycle_time'        => $cycleTime,
            'target_cycle_time' => $targetCycleTime,
            'pairs_checked'     => $pairsChecked,
            'defect_count'      => $defectCount,
            'total_steps'       => $totalSteps,
            'missed_steps'      => $missedSteps,
            'sop_score'         => $sopScore,
            'quality_score'     => $qualityScore,
            'work_method'       => $sopCompliance,
            'notes'             => $notes . ' | Mutu: ' . $qualityScore . '% (Cek: ' . $pairsChecked . ', Cacat: ' . $defectCount . ') | SOP: ' . $sopScore . '% (Langkah: ' . $totalSteps . ', Terlewat: ' . $missedSteps . ')',
            'evaluator_id'      => session()->get('user_id'),
        ]);

        // Item 12 & 13: If promoted to OJT, save placement details & switch active tab directly to OJT
        if ($advanceToOjt === 'yes') {
            $this->traineeModel->update($traineeId, [
                'stage'      => 'OJT',
                'department' => $ojtFactory,
                'track'      => $ojtProcess,
                'notes'      => 'Penempatan OJT: ' . $ojtFactory . ', Line/Cell: ' . $ojtCell . ', Proses: ' . $ojtProcess,
            ]);
            return redirect()->to('/daily-input#tab-ojt')->with('success', 'Evaluasi Hard Skill dicatat (Progress Hari ke-' . $dayNumber . '). Peserta BERHASIL dilanjutkan ke OJT di ' . $ojtFactory . ' (' . $ojtCell . ' - ' . $ojtProcess . ')!');
        }

        return redirect()->to('/daily-input#tab-hardskill')->with('success', 'Evaluasi harian Hard Skill (Progress Hari ke-' . $dayNumber . ') berhasil dicatat.');
    }

    public function saveOjt()
    {
        $userRole = session()->get('role');
        if (!in_array($userRole, ['superadmin', 'trainer'])) {
            return redirect()->to('/daily-input')->with('error', 'Akses Ditolak.');
        }

        $traineeId       = $this->request->getPost('trainee_id');
        $evaluationDate  = $this->request->getPost('evaluation_date') ?: date('Y-m-d');
        $cycleTime       = (float) $this->request->getPost('cycle_time');
        $targetCycleTime = (float) ($this->request->getPost('target_cycle_time') ?: 30.00);
        $pairsChecked    = (int) ($this->request->getPost('pairs_checked') ?: 5);
        $defectCount     = (int) ($this->request->getPost('defect_count') ?: 0);
        $totalSteps      = (int) ($this->request->getPost('total_steps') ?: 10);
        $missedSteps     = (int) ($this->request->getPost('missed_steps') ?: 0);

        $qualityScore    = (float) ($this->request->getPost('quality_score') ?? max(0, min(100, round((($pairsChecked - $defectCount) / max(1, $pairsChecked)) * 100))));
        $sopScore        = (float) ($this->request->getPost('sop_score') ?? max(0, min(100, round((($totalSteps - $missedSteps) / max(1, $totalSteps)) * 100))));
        $workMethod      = $this->request->getPost('work_method') ?: 'SOP: ' . $sopScore . '%';
        $finalStatus     = $this->request->getPost('final_status'); // 'stay', 'Lulus', 'Tidak Lolos', 'Resign'
        $notes           = $this->request->getPost('notes');

        $this->evaluationModel->insert([
            'trainee_id'        => $traineeId,
            'evaluation_date'   => $evaluationDate,
            'stage'             => 'OJT',
            'cycle_time'        => $cycleTime,
            'target_cycle_time' => $targetCycleTime,
            'pairs_checked'     => $pairsChecked,
            'defect_count'      => $defectCount,
            'total_steps'       => $totalSteps,
            'missed_steps'      => $missedSteps,
            'sop_score'         => $sopScore,
            'quality_score'     => $qualityScore,
            'work_method'       => $workMethod,
            'notes'             => 'Mutu: ' . $qualityScore . '% (Cek: ' . $pairsChecked . ', Cacat: ' . $defectCount . ') | SOP: ' . $sopScore . '% (Langkah: ' . $totalSteps . ', Terlewat: ' . $missedSteps . ') ' . $notes,
            'evaluator_id'      => session()->get('user_id'),
        ]);

        if (in_array($finalStatus, ['Lulus', 'Tidak Lolos', 'Resign'])) {
            $this->traineeModel->update($traineeId, [
                'stage'       => $finalStatus,
                'status'      => $finalStatus,
                'skill_level' => ($finalStatus === 'Lulus') ? 'Level 2 (Competent)' : 'Level 1 (Basic)',
            ]);
        }

        return redirect()->to('/daily-input')->with('success', 'Penilaian OJT berhasil disimpan.');
    }

    public function findByRfid()
    {
        $rawInput = trim($this->request->getGet('rfid') ?? '');
        if (empty($rawInput)) {
            return $this->response->setJSON(['success' => false, 'message' => 'RFID tag kosong.']);
        }

        $cardInfo = CardHelper::parseCard($rawInput);
        $cardDec  = $cardInfo['card_dec'];
        $cardHex  = $cardInfo['card_hex'];

        $trainee = $this->traineeModel->groupStart()
            ->where('rfid_tag', $rawInput)
            ->orWhere('rfid_tag', $cardHex)
            ->orWhere('rfid_tag', $cardDec)
            ->orWhere('nik', $rawInput)
            ->groupEnd()
            ->first();

        if ($trainee) {
            return $this->response->setJSON([
                'success' => true,
                'source'  => 'local',
                'trainee' => $trainee,
            ]);
        }

        // Try UNIS API
        $unisApi = new \App\Libraries\UnisApiService();
        $unisRes = $unisApi->getUserByCard($cardHex);
        if (empty($unisRes['status']) || empty($unisRes['data'])) {
            $unisRes = $unisApi->getUserByCard($rawInput);
        }
        if (empty($unisRes['status']) || empty($unisRes['data'])) {
            $unisRes = $unisApi->getUserByNik($rawInput);
        }

        if (!empty($unisRes['status']) && !empty($unisRes['data'])) {
            return $this->response->setJSON([
                'success'   => true,
                'source'    => 'unis_api',
                'unis_data' => $unisRes['data'],
                'message'   => 'Ditemukan di UNIS API: ' . $unisRes['data']['nama'] . ' (' . $unisRes['data']['nik'] . ')',
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Kartu RFID/NIK ' . $rawInput . ' (Hex: ' . $cardHex . ') tidak ditemukan dalam database lokal maupun UNIS API.']);
    }
}
