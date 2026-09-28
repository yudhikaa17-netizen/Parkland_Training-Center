<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SiteSettingModel;
use App\Models\TrainingTopicModel;
use App\Models\UserModel;

class ContentManager extends BaseController
{
    protected SiteSettingModel $settingModel;
    protected TrainingTopicModel $topicModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->settingModel = new SiteSettingModel();
        $this->topicModel   = new TrainingTopicModel();
        $this->userModel    = new UserModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak: Hanya Superadmin yang memiliki izin mengelola konten website.');
        }

        $allSettings = $this->settingModel->getAllGrouped();
        $allTopics   = $this->topicModel->getAllOrdered();
        $allUsers    = $this->userModel->orderBy('id', 'ASC')->findAll();

        $data = [
            'title'        => 'Kelola Konten & Tata Letak Website',
            'current_role' => session()->get('role'),
            'settings'     => $allSettings,
            'topics'       => $allTopics,
            'users'        => $allUsers,
        ];

        return view('content_manager/index', $data);
    }

    public function saveAnnouncement()
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak.');
        }

        $enabled = $this->request->getPost('announcement_enabled') ? '1' : '0';
        $title   = trim($this->request->getPost('announcement_title') ?? '');
        $message = trim($this->request->getPost('announcement_message') ?? '');
        $type    = $this->request->getPost('announcement_type') ?? 'info';

        $this->settingModel->setSetting('announcement_enabled', $enabled, 'announcement', 'Status aktif banner pengumuman');
        $this->settingModel->setSetting('announcement_title', $title, 'announcement', 'Judul banner pengumuman Dashboard');
        $this->settingModel->setSetting('announcement_message', $message, 'announcement', 'Isi teks pengumuman Dashboard');
        $this->settingModel->setSetting('announcement_type', $type, 'announcement', 'Tipe warna banner pengumuman');

        return redirect()->to('/content-manager#tab-announcement')->with('success', 'Pengaturan banner pengumuman Dashboard berhasil diperbarui.');
    }

    public function storeTopic()
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak.');
        }

        $title       = trim($this->request->getPost('title') ?? '');
        $topicKey    = trim($this->request->getPost('topic_key') ?? '');
        if (empty($topicKey)) {
            $topicKey = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $title));
        }

        $icon        = $this->request->getPost('icon') ?: 'bi bi-file-earmark-text';
        $category    = $this->request->getPost('category') ?: 'Umum';
        $badgeColor  = $this->request->getPost('badge_color') ?: 'var(--amber)';
        $sortOrder   = (int) $this->request->getPost('sort_order') ?: 0;
        $subTitle    = $this->request->getPost('sub_title');
        $quote       = $this->request->getPost('quote');
        $contentText = $this->request->getPost('content_text') ?? $this->request->getPost('content_html') ?? '';
        $finalHtml   = \App\Helpers\ContentHelper::formatForDisplay($contentText);

        $this->topicModel->insert([
            'topic_key'    => $topicKey,
            'title'        => $title,
            'icon'         => $icon,
            'category'     => $category,
            'badge_color'  => $badgeColor,
            'sort_order'   => $sortOrder,
            'sub_title'    => $subTitle,
            'quote'        => $quote,
            'content_html' => $finalHtml,
            'is_active'    => 1,
        ]);

        return redirect()->to('/content-manager#tab-topics')->with('success', 'Topik materi baru "' . $title . '" berhasil ditambahkan.');
    }

    public function updateTopic($id)
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak.');
        }

        $title       = trim($this->request->getPost('title') ?? '');
        $topicKey    = trim($this->request->getPost('topic_key') ?? '');
        $icon        = $this->request->getPost('icon') ?: 'bi bi-file-earmark-text';
        $category    = $this->request->getPost('category') ?: 'Umum';
        $badgeColor  = $this->request->getPost('badge_color') ?: 'var(--amber)';
        $sortOrder   = (int) $this->request->getPost('sort_order') ?: 0;
        $subTitle    = $this->request->getPost('sub_title');
        $quote       = $this->request->getPost('quote');
        $contentText = $this->request->getPost('content_text') ?? $this->request->getPost('content_html') ?? '';
        $finalHtml   = \App\Helpers\ContentHelper::formatForDisplay($contentText);
        $isActive    = $this->request->getPost('is_active') ? 1 : 0;

        $this->topicModel->update($id, [
            'topic_key'    => $topicKey,
            'title'        => $title,
            'icon'         => $icon,
            'category'     => $category,
            'badge_color'  => $badgeColor,
            'sort_order'   => $sortOrder,
            'sub_title'    => $subTitle,
            'quote'        => $quote,
            'content_html' => $finalHtml,
            'is_active'    => $isActive,
        ]);

        return redirect()->to('/content-manager#tab-topics')->with('success', 'Topik materi "' . $title . '" berhasil diperbarui.');
    }

    public function deleteTopic($id)
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak.');
        }

        $this->topicModel->delete($id);
        return redirect()->to('/content-manager#tab-topics')->with('success', 'Topik materi berhasil dihapus.');
    }

    public function saveThresholds()
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak.');
        }

        $motoricMax     = (float) $this->request->getPost('cycle_time_motoric_max');
        $hardSkillTarget= (float) $this->request->getPost('cycle_time_hardskill_target');
        $ojtTarget      = (float) $this->request->getPost('cycle_time_ojt_target');

        $this->settingModel->setSetting('cycle_time_motoric_max', (string)$motoricMax, 'thresholds', 'Batas max cycle time motoric');
        $this->settingModel->setSetting('cycle_time_hardskill_target', (string)$hardSkillTarget, 'thresholds', 'Target Hard Skill');
        $this->settingModel->setSetting('cycle_time_ojt_target', (string)$ojtTarget, 'thresholds', 'Target OJT');

        return redirect()->to('/content-manager#tab-thresholds')->with('success', 'Standar target cycle time & parameter berhasil diperbarui.');
    }

    public function saveInductionModules()
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak.');
        }

        $codes  = $this->request->getPost('module_code') ?: [];
        $titles = $this->request->getPost('module_title') ?: [];

        $modules = [];
        for ($i = 0; $i < count($codes); $i++) {
            $code  = trim($codes[$i]);
            $title = trim($titles[$i] ?? '');
            if (!empty($code) && !empty($title)) {
                $modules[$code] = $title;
            }
        }

        $this->settingModel->setSetting('induction_modules', $modules, 'curriculum', 'Daftar materi modul kurikulum induksi');

        return redirect()->to('/content-manager#tab-curriculum')->with('success', 'Daftar modul kurikulum induksi berhasil disimpan.');
    }

    public function saveFactories()
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak.');
        }

        $rawFactories = trim($this->request->getPost('factory_list_raw') ?? '');
        $lines = explode("\n", str_replace("\r", "", $rawFactories));
        $factories = [];
        foreach ($lines as $line) {
            $f = trim($line);
            if (!empty($f)) {
                $factories[] = $f;
            }
        }

        $this->settingModel->setSetting('factory_list', array_values($factories), 'factories', 'Daftar lokasi pabrik / lini produksi');

        return redirect()->to('/content-manager#tab-factories')->with('success', 'Daftar lokasi pabrik peminjaman operator berhasil diperbarui.');
    }

    public function saveLocationStructure()
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak.');
        }

        $cardsInput = $this->request->getPost('locations') ?: [];
        $cleanedCards = [];

        foreach ($cardsInput as $card) {
            $title = trim($card['title'] ?? '');
            $dotColor = trim($card['dot_color'] ?? 'var(--blue)');
            $rawItems = $card['items'] ?? [];

            if (empty($title)) continue;

            $items = [];
            foreach ($rawItems as $it) {
                $lbl = trim($it['label'] ?? '');
                $val = trim($it['value'] ?? '');
                if (!empty($lbl)) {
                    $items[] = ['label' => $lbl, 'value' => $val];
                }
            }

            $cleanedCards[] = [
                'title'     => $title,
                'dot_color' => $dotColor,
                'items'     => $items
            ];
        }

        $this->settingModel->setSetting('production_locations', $cleanedCards, 'locations', 'Struktur lokasi produksi scope besar dan kecil');

        return redirect()->to('/content-manager#tab-locations')->with('success', 'Struktur Lokasi Produksi berhasil diperbarui.');
    }

    public function storeUser()
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak.');
        }

        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'name'     => 'required|min_length[2]|max_length[100]',
            'password' => 'required|min_length[4]',
            'role'     => 'required|in_list[superadmin,trainer,guest,leader_lean]',
        ];

        $messages = [
            'username' => [
                'required'   => 'Username wajib diisi.',
                'min_length' => 'Username minimal 3 karakter.',
                'is_unique'  => 'Username sudah terdaftar, gunakan username lain.',
            ],
            'name' => [
                'required'   => 'Nama lengkap wajib diisi.',
                'min_length' => 'Nama minimal 2 karakter.',
            ],
            'password' => [
                'required'   => 'Password wajib diisi.',
                'min_length' => 'Password minimal 4 karakter.',
            ],
            'role' => [
                'required' => 'Role wajib dipilih.',
                'in_list'  => 'Pilihan role tidak valid.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            $errors = implode('<br>', $this->validator->getErrors());
            return redirect()->to('/content-manager#tab-users')->with('error', $errors)->withInput();
        }

        $userData = [
            'username' => trim($this->request->getPost('username')),
            'name'     => trim($this->request->getPost('name')),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->getPost('role'),
        ];

        $this->userModel->insert($userData);

        return redirect()->to('/content-manager#tab-users')->with('success', "User '{$userData['username']}' berhasil ditambahkan.");
    }

    public function updateUser($id)
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/content-manager#tab-users')->with('error', 'User tidak ditemukan.');
        }

        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'name'     => 'required|min_length[2]|max_length[100]',
            'role'     => 'required|in_list[superadmin,trainer,guest,leader_lean]',
        ];

        $messages = [
            'username' => [
                'required'   => 'Username wajib diisi.',
                'min_length' => 'Username minimal 3 karakter.',
                'is_unique'  => 'Username sudah digunakan oleh akun lain.',
            ],
            'name' => [
                'required'   => 'Nama lengkap wajib diisi.',
                'min_length' => 'Nama minimal 2 karakter.',
            ],
            'role' => [
                'required' => 'Role wajib dipilih.',
                'in_list'  => 'Pilihan role tidak valid.',
            ],
        ];

        $newPassword = trim($this->request->getPost('password') ?? '');
        if (!empty($newPassword)) {
            $rules['password'] = 'min_length[4]';
            $messages['password'] = ['min_length' => 'Password minimal 4 karakter.'];
        }

        if (!$this->validate($rules, $messages)) {
            $errors = implode('<br>', $this->validator->getErrors());
            return redirect()->to('/content-manager#tab-users')->with('error', $errors)->withInput();
        }

        $updateData = [
            'username' => trim($this->request->getPost('username')),
            'name'     => trim($this->request->getPost('name')),
            'role'     => $this->request->getPost('role'),
        ];

        if (!empty($newPassword)) {
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        // Check if downgrading role of the last superadmin
        if ($user['role'] === 'superadmin' && $updateData['role'] !== 'superadmin') {
            $superadminCount = $this->userModel->where('role', 'superadmin')->countAllResults();
            if ($superadminCount <= 1) {
                return redirect()->to('/content-manager#tab-users')->with('error', 'Gagal mengubah role: Harus ada minimal 1 akun Superadmin aktif.');
            }
        }

        $this->userModel->update($id, $updateData);

        // If updating the currently logged-in user, update session details
        if (session()->get('username') === $user['username']) {
            session()->set([
                'username' => $updateData['username'],
                'name'     => $updateData['name'],
                'role'     => $updateData['role'],
            ]);
        }

        return redirect()->to('/content-manager#tab-users')->with('success', "Akun '{$updateData['username']}' berhasil diperbarui.");
    }

    public function deleteUser($id)
    {
        if (session()->get('role') !== 'superadmin') {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/content-manager#tab-users')->with('error', 'User tidak ditemukan.');
        }

        // Prevent deleting own logged-in user
        if (session()->get('username') === $user['username'] || session()->get('user_id') == $id) {
            return redirect()->to('/content-manager#tab-users')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif login.');
        }

        // Prevent deleting the last superadmin
        if ($user['role'] === 'superadmin') {
            $superadminCount = $this->userModel->where('role', 'superadmin')->countAllResults();
            if ($superadminCount <= 1) {
                return redirect()->to('/content-manager#tab-users')->with('error', 'Tidak dapat menghapus satu-satunya akun Superadmin di sistem.');
            }
        }

        $this->userModel->delete($id);

        return redirect()->to('/content-manager#tab-users')->with('success', "Akun '{$user['username']}' ({$user['name']}) berhasil dihapus.");
    }
}
