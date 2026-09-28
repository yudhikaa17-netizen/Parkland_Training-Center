<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\CardHelper;
use App\Models\AttendanceModel;
use App\Models\TraineeModel;

class Attendance extends BaseController
{
    protected $attendanceModel;
    protected $traineeModel;

    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->traineeModel = new TraineeModel();
    }

    public function index()
    {
        $selectedDate = $this->request->getGet('date') ?: date('Y-m-d');
        $attendances = $this->attendanceModel->getTodayAttendances($selectedDate);
        $trainees = $this->traineeModel->where('status', 'Aktif')->findAll();

        $loanModel = new \App\Models\OperatorLoanModel();
        $activeLoanTraineeIds = array_column($loanModel->where('status', 'Dipinjam')->findAll(), 'trainee_id');

        $readyOperators = [];
        foreach ($attendances as $att) {
            $isHadir = strtolower($att['status'] ?? '') === 'hadir';
            $belumKeluar = empty($att['time_out']) || $att['time_out'] === '00:00:00';
            $belumOjt = !in_array($att['stage'] ?? '', ['OJT', 'Lulus']);
            $belumDipinjamkan = !in_array($att['trainee_id'] ?? 0, $activeLoanTraineeIds);

            if ($isHadir && $belumKeluar && $belumOjt && $belumDipinjamkan) {
                $readyOperators[] = $att;
            }
        }

        $stats = [
            'total' => count($attendances),
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpha' => 0,
        ];

        foreach ($attendances as $att) {
            $status = strtolower($att['status']);
            if (isset($stats[$status])) {
                $stats[$status]++;
            }
        }

        $totalTraineesActive = count($trainees);
        $presentRate = $totalTraineesActive > 0 ? round(($stats['hadir'] / $totalTraineesActive) * 100, 1) : 0;

        $data = [
            'title' => 'Manajemen Absensi Trainee',
            'current_role' => session()->get('role') ?? 'guest',
            'selected_date' => $selectedDate,
            'attendances' => $attendances,
            'trainees' => $trainees,
            'ready_operators' => $readyOperators,
            'stats' => $stats,
            'total_trainees_active' => $totalTraineesActive,
            'present_rate' => $presentRate,
        ];

        return view('attendance/index', $data);
    }

    public function kiosk()
    {
        $today = date('Y-m-d');
        $attendances = $this->attendanceModel->getTodayAttendances($today);
        $trainees = $this->traineeModel->where('status', 'Aktif')->findAll();

        $stats = [
            'total' => count($attendances),
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpha' => 0,
        ];

        foreach ($attendances as $att) {
            $status = strtolower($att['status']);
            if (isset($stats[$status])) {
                $stats[$status]++;
            }
        }

        $totalTraineesActive = count($trainees);
        $presentRate = $totalTraineesActive > 0 ? round(($stats['hadir'] / $totalTraineesActive) * 100, 1) : 0;

        $data = [
            'title' => 'Kiosk Tap ID Card — Training Center',
            'current_role' => session()->get('role') ?? 'guest',
            'selected_date' => $today,
            'attendances' => $attendances,
            'trainees' => $trainees,
            'stats' => $stats,
            'total_trainees_active' => $totalTraineesActive,
            'present_rate' => $presentRate,
        ];

        return view('attendance/kiosk', $data);
    }

    public function tapRfid()
    {
        $rawInput = trim($this->request->getPost('rfid_tag') ?? $this->request->getGet('rfid_tag') ?? '');
        if (empty($rawInput)) {
            return $this->response->setJSON(['success' => false, 'status' => 'error', 'message' => 'Silakan tap ID Card peserta.']);
        }

        $cardInfo = CardHelper::parseCard($rawInput);
        $cardDec = $cardInfo['card_dec'];
        $cardHex = $cardInfo['card_hex'];

        $trainee = $this->traineeModel->groupStart()
            ->where('rfid_tag', $rawInput)
            ->orWhere('rfid_tag', $cardHex)
            ->orWhere('rfid_tag', $cardDec)
            ->orWhere('nik', $rawInput)
            ->groupEnd()
            ->first();

        // If not found in local DB, attempt auto-sync from UNIS API
        if (!$trainee) {
            $unisApi = new \App\Libraries\UnisApiService();

            // Coba cari via Hex hasil konversi, lalu via rawInput, lalu NIK
            $unisRes = $unisApi->getUserByCard($cardHex);
            if (empty($unisRes['status']) || empty($unisRes['data'])) {
                $unisRes = $unisApi->getUserByCard($rawInput);
            }
            if (empty($unisRes['status']) || empty($unisRes['data'])) {
                $unisRes = $unisApi->getUserByNik($rawInput);
            }

            if (!empty($unisRes['status']) && !empty($unisRes['data'])) {
                $uData = $unisRes['data'];
                $newId = $this->traineeModel->insert([
                    'rfid_tag' => $uData['card_hex'] ?: $cardHex,
                    'nik' => $uData['nik'],
                    'name' => $uData['nama'],
                    'gender' => 'Laki-laki',
                    'department' => $uData['departemen'] ?? 'Production',
                    'track' => 'Belum Ditentukan',
                    'stage' => 'Induction',
                    'start_date' => date('Y-m-d'),
                    'status' => 'Aktif',
                    'notes' => 'Auto-sync dari UNIS API via RFID Tap',
                ]);
                $trainee = $this->traineeModel->find($newId);
            }
        }

        if (!$trainee) {
            return $this->response->setJSON(['success' => false, 'status' => 'error', 'message' => 'ID Card ' . $rawInput . ' (Hex: ' . $cardHex . ') tidak terdaftar pada sistem maupun UNIS API.']);
        }

        $today = date('Y-m-d');
        $currentTime = date('H:i:s');

        // Check if attendance already exists today
        $existing = $this->attendanceModel->where('trainee_id', $trainee['id'])
            ->where('attendance_date', $today)
            ->first();

        if ($existing) {
            if (empty($existing['time_out'])) {
                $this->attendanceModel->update($existing['id'], [
                    'time_out' => $currentTime,
                    'notes' => ($existing['notes'] ? $existing['notes'] . ' | ' : '') . 'Keluar ' . $currentTime,
                ]);

                return $this->response->setJSON([
                    'success' => true,
                    'status' => 'success',
                    'type' => 'checkout',
                    'time_type' => 'Jam Pulang',
                    'time' => $currentTime,
                    'message' => 'Absensi Pulang Tercatat: ' . $trainee['name'] . ' (' . $trainee['nik'] . ') jam ' . $currentTime,
                    'trainee' => $trainee,
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => true,
                    'status' => 'success',
                    'type' => 'already_done',
                    'time_type' => 'Selesai',
                    'time' => $currentTime,
                    'message' => $trainee['name'] . ' sudah melakukan absensi masuk & pulang hari ini.',
                    'trainee' => $trainee,
                ]);
            }
        }

        // Create new attendance check-in
        $this->attendanceModel->insert([
            'trainee_id' => $trainee['id'],
            'attendance_date' => $today,
            'time_in' => $currentTime,
            'time_out' => null,
            'status' => 'Hadir',
            'rfid_tapped_at' => date('Y-m-d H:i:s'),
            'notes' => 'Masuk di TC',
        ]);

        return $this->response->setJSON([
            'success' => true,
            'status' => 'success',
            'type' => 'checkin',
            'time_type' => 'Jam Masuk',
            'time' => $currentTime,
            'message' => 'Absensi Masuk Berhasil: ' . $trainee['name'] . ' (' . $trainee['nik'] . ') jam ' . $currentTime,
            'trainee' => $trainee,
        ]);
    }

    public function storeManual()
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/attendance')->with('error', 'Akses Ditolak.');
        }

        $traineeId = $this->request->getPost('trainee_id');
        $attendanceDate = $this->request->getPost('attendance_date') ?: date('Y-m-d');
        $timeIn = $this->request->getPost('time_in') ?: '07:00:00';
        $timeOut = $this->request->getPost('time_out') ?: null;
        $status = $this->request->getPost('status') ?: 'Hadir';
        $notes = $this->request->getPost('notes');

        // Check if exists
        $existing = $this->attendanceModel->where('trainee_id', $traineeId)
            ->where('attendance_date', $attendanceDate)
            ->first();

        if ($existing) {
            $this->attendanceModel->update($existing['id'], [
                'time_in' => $timeIn,
                'time_out' => $timeOut,
                'status' => $status,
                'notes' => $notes,
            ]);
            return redirect()->to('/attendance?date=' . $attendanceDate)->with('success', 'Data absensi berhasil diperbarui.');
        }

        $this->attendanceModel->insert([
            'trainee_id' => $traineeId,
            'attendance_date' => $attendanceDate,
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'status' => $status,
            'notes' => $notes ?: 'Input manual oleh Admin TC',
        ]);

        return redirect()->to('/attendance?date=' . $attendanceDate)->with('success', 'Data absensi manual berhasil dicatat.');
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/attendance')->with('error', 'Akses Ditolak.');
        }

        $this->attendanceModel->delete($id);
        return redirect()->back()->with('success', 'Data absensi berhasil dihapus.');
    }
}
