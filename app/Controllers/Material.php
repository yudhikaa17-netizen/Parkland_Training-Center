<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\CardHelper;
use App\Models\MaterialModel;
use App\Models\MaterialCompletionModel;
use App\Models\TraineeModel;

class Material extends BaseController
{
    protected $materialModel;
    protected $completionModel;
    protected $traineeModel;

    public function __construct()
    {
        $this->materialModel   = new MaterialModel();
        $this->completionModel = new MaterialCompletionModel();
        $this->traineeModel    = new TraineeModel();
    }

    public function index()
    {
        $materials = $this->materialModel->orderBy('id', 'DESC')->findAll();
        $trainees  = $this->traineeModel->where('status', 'Aktif')->orderBy('name', 'ASC')->findAll();

        $topicModel = new \App\Models\TrainingTopicModel();
        $dbTopics   = $topicModel->getActiveTopics();

        $topics = [];
        if (!empty($dbTopics)) {
            foreach ($dbTopics as $dt) {
                $topics[$dt['topic_key']] = [
                    'id'           => $dt['id'],
                    'title'        => $dt['title'],
                    'icon'         => $dt['icon'] ?: '📚',
                    'category'     => $dt['category'] ?? 'Umum',
                    'badge_color'  => $dt['badge_color'] ?: 'var(--amber)',
                    'sub_title'    => $dt['sub_title'] ?? '',
                    'quote'        => $dt['quote'] ?? '',
                    'content_html' => $dt['content_html'] ?? '',
                ];
            }
        }

        // Fetch completions grouped by topic
        $completionsByTopic = $this->completionModel->getAllGroupedByTopic();

        // Dynamic Document Categories
        $defaultCategories  = ['SOP', 'Materi Teori', 'Panduan Praktik', 'Video Tutorial'];
        $existingCategories = array_filter(array_unique(array_column($materials, 'category')));
        $categories         = array_values(array_unique(array_merge($defaultCategories, $existingCategories)));

        $data = [
            'title'                => 'Materi Training & Modul SOP',
            'current_role'         => session()->get('role') ?? 'guest',
            'materials'            => $materials,
            'categories'           => $categories,
            'trainees'             => $trainees,
            'topics'               => $topics,
            'completions_by_topic' => $completionsByTopic,
        ];

        return view('material/index', $data);
    }

    public function markCompletion()
    {
        $userRole = session()->get('role');
        if (!in_array($userRole, ['superadmin', 'trainer'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Akses Ditolak.']);
            }
            return redirect()->to('/materials')->with('error', 'Akses Ditolak.');
        }

        $topicKey   = $this->request->getPost('topic_key') ?? 'skill_level';
        $topicTitle = $this->request->getPost('topic_title') ?? 'Skill Level';
        $traineeId  = $this->request->getPost('trainee_id');
        $rawRfid    = trim($this->request->getPost('rfid_tag') ?? '');
        $markedVia  = 'manual';

        $trainee = null;

        // If RFID is provided
        if (!empty($rawRfid)) {
            $markedVia = 'rfid';
            $cardInfo  = CardHelper::parseCard($rawRfid);
            $cardDec   = $cardInfo['card_dec'];
            $cardHex   = $cardInfo['card_hex'];

            $trainee = $this->traineeModel->groupStart()
                ->where('rfid_tag', $rawRfid)
                ->orWhere('rfid_tag', $cardHex)
                ->orWhere('rfid_tag', $cardDec)
                ->orWhere('nik', $rawRfid)
                ->groupEnd()
                ->first();

            // Try UNIS API if not found in local DB
            if (!$trainee) {
                $unisApi = new \App\Libraries\UnisApiService();
                $unisRes = $unisApi->getUserByCard($cardHex);
                if (empty($unisRes['status']) || empty($unisRes['data'])) {
                    $unisRes = $unisApi->getUserByCard($rawRfid);
                }
                if (empty($unisRes['status']) || empty($unisRes['data'])) {
                    $unisRes = $unisApi->getUserByNik($rawRfid);
                }

                if (!empty($unisRes['status']) && !empty($unisRes['data'])) {
                    $uData = $unisRes['data'];
                    $newId = $this->traineeModel->insert([
                        'rfid_tag'   => $uData['card_hex'] ?: $cardHex,
                        'nik'        => $uData['nik'],
                        'name'       => $uData['nama'],
                        'gender'     => 'Laki-laki',
                        'department' => $uData['departemen'] ?? 'Production',
                        'track'      => 'Belum Ditentukan',
                        'stage'      => 'Induction',
                        'start_date' => date('Y-m-d'),
                        'status'     => 'Aktif',
                        'notes'      => 'Auto-sync dari UNIS API via RFID Tap Materi',
                    ]);
                    $trainee = $this->traineeModel->find($newId);
                }
            }

            if (!$trainee) {
                $msg = 'Kartu RFID/NIK ' . $rawRfid . ' (Hex: ' . $cardHex . ') tidak ditemukan di database atau UNIS API.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $msg]);
                }
                return redirect()->back()->with('error', $msg);
            }
        } elseif (!empty($traineeId)) {
            $trainee = $this->traineeModel->find($traineeId);
        }

        if (!$trainee) {
            $msg = 'Silakan pilih atau tap kartu peserta trainee terlebih dahulu.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Check if already completed this topic
        $existing = $this->completionModel->where('trainee_id', $trainee['id'])
            ->where('topic_key', $topicKey)
            ->first();

        if ($existing) {
            $msg = $trainee['name'] . ' (' . $trainee['nik'] . ') sudah pernah ditandai menyelesaikan topik "' . $topicTitle . '" pada ' . date('d-m-Y H:i', strtotime($existing['completed_at']));
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success'  => false,
                    'is_exist' => true,
                    'message'  => $msg,
                    'trainee'  => $trainee,
                ]);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Record completion
        $now = date('Y-m-d H:i:s');
        $completionId = $this->completionModel->insert([
            'trainee_id'   => $trainee['id'],
            'topic_key'    => $topicKey,
            'topic_title'  => $topicTitle,
            'marked_by'    => session()->get('name') ?? 'Admin TC',
            'marked_via'   => $markedVia,
            'completed_at' => $now,
        ]);

        $newRecord = [
            'id'           => $completionId,
            'trainee_id'   => $trainee['id'],
            'trainee_name' => $trainee['name'],
            'nik'          => $trainee['nik'],
            'rfid_tag'     => $trainee['rfid_tag'],
            'stage'        => $trainee['stage'],
            'track'        => $trainee['track'],
            'topic_key'    => $topicKey,
            'topic_title'  => $topicTitle,
            'marked_by'    => session()->get('name') ?? 'Admin TC',
            'marked_via'   => $markedVia,
            'completed_at' => $now,
            'formatted_date' => date('d M Y, H:i', strtotime($now)),
        ];

        $successMsg = 'Peserta ' . $trainee['name'] . ' (' . $trainee['nik'] . ') berhasil ditandai telah mempelajari topik "' . $topicTitle . '".';

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $successMsg,
                'record'  => $newRecord,
                'trainee' => $trainee,
            ]);
        }

        return redirect()->back()->with('success', $successMsg);
    }

    public function deleteCompletion($id)
    {
        $userRole = session()->get('role');
        if (!in_array($userRole, ['superadmin', 'trainer'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Akses Ditolak.']);
            }
            return redirect()->to('/materials')->with('error', 'Akses Ditolak.');
        }

        $this->completionModel->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Tanda penyelesaian materi berhasil dihapus.']);
        }

        return redirect()->back()->with('success', 'Tanda penyelesaian materi berhasil dihapus.');
    }

    public function store()
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/materials')->with('error', 'Akses Ditolak. Hanya Superadmin yang dapat menambah/mengunggah dokumen.');
        }

        $title       = trim($this->request->getPost('title') ?? '');
        $category    = trim($this->request->getPost('category') ?? '');
        $newCategory = trim($this->request->getPost('new_category') ?? '');

        // Use custom category if provided or selected NEW
        if ($category === 'NEW' || !empty($newCategory)) {
            $category = !empty($newCategory) ? $newCategory : 'Dokumen Umum';
        }

        if (empty($category)) {
            $category = 'SOP';
        }

        if (empty($title)) {
            return redirect()->back()->with('error', 'Judul dokumen wajib diisi.');
        }

        // Handle Direct File Upload (Klik & Pilih File)
        $fileUrl = $this->request->getPost('file_url') ?: '#';
        $file = $this->request->getFile('document_file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/materials/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);
            $fileUrl = base_url('uploads/materials/' . $newName);
        }

        $data = [
            'title'       => $title,
            'stage'       => $this->request->getPost('stage') ?? 'All Stages',
            'track'       => $this->request->getPost('track') ?? 'All Tracks',
            'category'    => $category,
            'file_url'    => $fileUrl,
            'description' => $this->request->getPost('description'),
            'uploaded_by' => session()->get('name') ?? 'Lean & Head Trainer',
        ];

        $this->materialModel->insert($data);
        return redirect()->to('/materials')->with('success', 'Dokumen / Modul (' . esc($category) . ') "' . esc($title) . '" berhasil ditambahkan.');
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/materials')->with('error', 'Akses Ditolak. Hanya Superadmin yang dapat menghapus dokumen.');
        }

        $this->materialModel->delete($id);
        return redirect()->to('/materials')->with('success', 'Materi training berhasil dihapus.');
    }
}
