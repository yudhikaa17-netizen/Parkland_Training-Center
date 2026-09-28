<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TraineeModel;
use App\Models\DailyEvaluationModel;
use App\Models\AttendanceModel;

class Trainee extends BaseController
{
    protected $traineeModel;
    protected $evaluationModel;
    protected $attendanceModel;

    public function __construct()
    {
        $this->traineeModel    = new TraineeModel();
        $this->evaluationModel = new DailyEvaluationModel();
        $this->attendanceModel = new AttendanceModel();
    }

    public function index()
    {
        $stageFilter = $this->request->getGet('stage');
        $trackFilter = $this->request->getGet('track');
        $search      = $this->request->getGet('q');

        $query = $this->traineeModel;

        if (!empty($stageFilter) && $stageFilter !== 'all') {
            $query = $query->where('stage', $stageFilter);
        }
        if (!empty($trackFilter) && $trackFilter !== 'all') {
            $query = $query->where('track', $trackFilter);
        }
        if (!empty($search)) {
            $query = $query->groupStart()
                ->like('name', $search)
                ->orLike('nik', $search)
                ->orLike('rfid_tag', $search)
                ->groupEnd();
        }

        $trainees = $query->orderBy('id', 'DESC')->findAll();
        $stageCounts = $this->traineeModel->getCountsByStage();

        $settingModel = new \App\Models\SiteSettingModel();
        $factoryList  = $settingModel->getSetting('factory_list', ['Factory 1 (F1)', 'Factory 2 (F2)', 'Factory 3 (F3)', 'Factory 4 (F4)', 'Factory 5 (F5)', 'Bottom 1', 'Bottom 2', 'Inhouse']);

        $data = [
            'title'          => 'Tracking Trainee',
            'current_role'   => session()->get('role'),
            'trainees'       => $trainees,
            'stage_counts'   => $stageCounts,
            'selected_stage' => $stageFilter ?? 'all',
            'selected_track' => $trackFilter ?? 'all',
            'search'         => $search ?? '',
            'factory_list'   => $factoryList,
        ];

        return view('trainee/index', $data);
    }

    public function detail($id)
    {
        $trainee = $this->traineeModel->find($id);
        if (!$trainee) {
            return redirect()->to('/trainees')->with('error', 'Data Trainee tidak ditemukan.');
        }

        $evaluations = $this->evaluationModel->where('trainee_id', $id)->orderBy('evaluation_date', 'DESC')->findAll();
        $attendances = $this->attendanceModel->where('trainee_id', $id)->orderBy('attendance_date', 'DESC')->findAll();

        $defaultInductionModules = [
            'M-01' => 'Company Profile & Nilai-Nilai PT Parkland World Indonesia',
            'M-02' => 'Keselamatan & Kesehatan Kerja (K3) serta Tanggap Darurat',
            'M-03' => 'Budaya 5S (Seiri, Seiton, Seiso, Seiketsu, Shitsuke) & Lean Culture',
            'M-04' => 'Peraturan Perusahaan (PP), Disiplin & Tata Tertib Kerja',
            'M-05' => 'Pengenalan Fasilitas, Mesin & Peralatan Dasar Sepatu',
            'M-06' => 'Standar Mutu Dasar (Quality Basic & No Defect Mindset)',
        ];

        $completedInduction = json_decode($trainee['induction_materials'] ?? '[]', true) ?: [];

        $completionModel = new \App\Models\MaterialCompletionModel();
        $completedTopics = $completionModel->getCompletionsByTrainee($id);

        $data = [
            'title'                      => 'Detail Trainee — ' . $trainee['name'],
            'current_role'               => session()->get('role'),
            'trainee'                    => $trainee,
            'evaluations'                => $evaluations,
            'attendances'                => $attendances,
            'induction_modules'          => $defaultInductionModules,
            'completed_induction'        => $completedInduction,
            'completed_topics'           => $completedTopics,
        ];

        return view('trainee/detail', $data);
    }

    public function saveInduction($id)
    {
        $userRole = session()->get('role');
        if (!in_array($userRole, ['superadmin', 'trainer'])) {
            return redirect()->to('/trainees/detail/' . $id)->with('error', 'Akses Ditolak.');
        }

        $materials = $this->request->getPost('induction_materials') ?: [];
        $this->traineeModel->update($id, [
            'induction_materials' => json_encode(array_values($materials)),
        ]);

        return redirect()->to('/trainees/detail/' . $id)->with('success', 'Checklist materi induksi peserta berhasil diperbarui.');
    }

    public function store()
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/trainees')->with('error', 'Akses Ditolak: Hanya Superadmin yang memiliki izin menambah data.');
        }

        $data = [
            'rfid_tag'           => $this->request->getPost('rfid_tag') ?: 'RFID-' . strtoupper(substr(uniqid(), -8)),
            'nik'                => $this->request->getPost('nik'),
            'name'               => $this->request->getPost('name'),
            'gender'             => $this->request->getPost('gender') ?? 'Laki-laki',
            'department'         => $this->request->getPost('department') ?? 'Production',
            'track'              => $this->request->getPost('track') ?? 'Belum Ditentukan',
            'stage'              => $this->request->getPost('stage') ?? 'Induction',
            'cycle_time_motoric' => $this->request->getPost('cycle_time_motoric') ?: null,
            'motoric_notes'      => $this->request->getPost('motoric_notes') ?: null,
            'start_date'         => $this->request->getPost('start_date') ?: date('Y-m-d'),
            'status'             => $this->request->getPost('status') ?? 'Aktif',
            'notes'              => $this->request->getPost('notes'),
        ];

        $this->traineeModel->insert($data);
        return redirect()->to('/trainees')->with('success', 'Data Trainee baru berhasil ditambahkan.');
    }

    public function update($id)
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/trainees')->with('error', 'Akses Ditolak: Hanya Superadmin yang memiliki izin mengubah data.');
        }

        $data = [
            'rfid_tag'           => $this->request->getPost('rfid_tag'),
            'nik'                => $this->request->getPost('nik'),
            'name'               => $this->request->getPost('name'),
            'gender'             => $this->request->getPost('gender'),
            'department'         => $this->request->getPost('department'),
            'track'              => $this->request->getPost('track'),
            'stage'              => $this->request->getPost('stage'),
            'cycle_time_motoric' => $this->request->getPost('cycle_time_motoric') ?: null,
            'motoric_notes'      => $this->request->getPost('motoric_notes'),
            'start_date'         => $this->request->getPost('start_date'),
            'status'             => $this->request->getPost('status'),
            'notes'              => $this->request->getPost('notes'),
        ];

        $this->traineeModel->update($id, $data);
        return redirect()->to('/trainees/detail/' . $id)->with('success', 'Data Trainee berhasil diperbarui.');
    }

    public function updateStage($id)
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/trainees')->with('error', 'Akses Ditolak: Hanya Superadmin yang memiliki izin mengubah tahap.');
        }

        $newStage  = $this->request->getPost('stage');
        $newTrack  = $this->request->getPost('track');
        $newStatus = $this->request->getPost('status');

        $updateData = ['stage' => $newStage];
        if (!empty($newTrack)) {
            $updateData['track'] = $newTrack;
        }
        if (!empty($newStatus)) {
            $updateData['status'] = $newStatus;
        }

        $this->traineeModel->update($id, $updateData);
        return redirect()->back()->with('success', 'Tahap Trainee berhasil diubah ke: ' . $newStage);
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/trainees')->with('error', 'Akses Ditolak: Hanya Superadmin yang memiliki izin menghapus data.');
        }

        $this->traineeModel->delete($id);
        return redirect()->to('/trainees')->with('success', 'Data Trainee berhasil dihapus.');
    }
}
