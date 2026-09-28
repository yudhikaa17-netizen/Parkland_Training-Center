<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\CardHelper;
use App\Libraries\UnisApiService;
use App\Models\TraineeModel;

class ApiController extends BaseController
{
    protected UnisApiService $unisApi;
    protected TraineeModel $traineeModel;

    public function __construct()
    {
        $this->unisApi = new UnisApiService();
        $this->traineeModel = new TraineeModel();
    }

    /**
     * Check UNIS API connection health
     */
    public function health()
    {
        $result = $this->unisApi->checkHealth();
        return $this->response->setJSON($result);
    }

    /**
     * Lookup employee by NIK (Query param or Path param)
     */
    public function lookupNik()
    {
        $nik = trim($this->request->getGet('nik') ?? '');
        if (empty($nik)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Parameter NIK diperlukan.']);
        }

        $result = $this->unisApi->getUserByNik($nik);
        return $this->response->setJSON($result);
    }

    /**
     * Direct Path Endpoint: /api/user/nik/{NIK}
     */
    public function userByNik($nik = null)
    {
        $nik = trim($nik ?? $this->request->getGet('nik') ?? '');
        if (empty($nik)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Parameter NIK tidak boleh kosong.']);
        }

        $result = $this->unisApi->getUserByNik($nik);
        return $this->response->setJSON($result);
    }

    /**
     * Lookup employee by RFID Card Hex (Query param)
     */
    public function lookupCard()
    {
        $card = trim($this->request->getGet('card') ?? '');
        if (empty($card)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Parameter Card Hex atau Desimal diperlukan.']);
        }

        $cardInfo = CardHelper::parseCard($card);
        $result = $this->unisApi->getUserByCard($cardInfo['card_hex']);
        if (empty($result['status']) || empty($result['data'])) {
            $result = $this->unisApi->getUserByCard($card);
        }
        return $this->response->setJSON($result);
    }

    /**
     * Direct Path Endpoint: /api/user/card/{HEXA}
     */
    public function userByCard($cardHex = null)
    {
        $cardHex = trim($cardHex ?? $this->request->getGet('card') ?? '');
        if (empty($cardHex)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Parameter Card Hex tidak boleh kosong.']);
        }

        $cardInfo = CardHelper::parseCard($cardHex);
        $result = $this->unisApi->getUserByCard($cardInfo['card_hex']);
        if (empty($result['status']) || empty($result['data'])) {
            $result = $this->unisApi->getUserByCard($cardHex);
        }
        return $this->response->setJSON($result);
    }

    /**
     * Get users list from UNIS
     */
    public function users()
    {
        $result = $this->unisApi->getAllUsers();
        return $this->response->setJSON($result);
    }

    /**
     * Import employee from UNIS directly into Trainee database
     */
    public function importTrainee()
    {
        if (session()->get('role') !== 'superadmin') {
            return $this->response->setJSON(['status' => false, 'message' => 'Akses Ditolak: Hanya Superadmin.']);
        }

        $nik = trim($this->request->getPost('nik'));
        $cardHex = trim($this->request->getPost('card_hex'));
        $stage = $this->request->getPost('stage') ?: 'Induction';
        $track = $this->request->getPost('track') ?: 'Belum Ditentukan';

        $unisData = null;
        if (!empty($nik)) {
            $res = $this->unisApi->getUserByNik($nik);
            if (!empty($res['status']) && !empty($res['data'])) {
                $unisData = $res['data'];
            }
        } elseif (!empty($cardHex)) {
            $res = $this->unisApi->getUserByCard($cardHex);
            if (!empty($res['status']) && !empty($res['data'])) {
                $unisData = $res['data'];
            }
        }

        if (!$unisData) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data karyawan tidak ditemukan di UNIS API.']);
        }

        // Check if trainee already exists in DB
        $existing = $this->traineeModel->where('nik', $unisData['nik'])
            ->orWhere('rfid_tag', $unisData['card_hex'])
            ->first();

        if ($existing) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Peserta dengan NIK ' . $unisData['nik'] . ' atau RFID ' . $unisData['card_hex'] . ' sudah terdaftar dalam Training Center (Nama: ' . $existing['name'] . ').',
                'trainee' => $existing,
            ]);
        }

        $newId = $this->traineeModel->insert([
            'rfid_tag' => $unisData['card_hex'],
            'nik' => $unisData['nik'],
            'name' => $unisData['nama'],

            'department' => $unisData['departemen'] ?? 'Production',
            'track' => $track,
            'stage' => $stage,
            'start_date' => date('Y-m-d'),
            'status' => 'Aktif',
            'notes' => 'Diimpor otomatis dari UNIS API (Access Group: ' . ($unisData['access_group'] ?? '-') . ')',
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Berhasil mengimpor ' . $unisData['nama'] . ' (' . $unisData['nik'] . ') dari UNIS API!',
            'trainee_id' => $newId,
        ]);
    }
}
