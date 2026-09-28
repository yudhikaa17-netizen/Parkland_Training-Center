<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('is_logged_in') && session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            session()->set([
                'user_id'      => $user['id'],
                'username'     => $user['username'],
                'name'         => $user['name'],
                'role'         => $user['role'],
                'is_logged_in' => true,
            ]);

            return redirect()->to('/dashboard')->with('success', 'Selamat datang kembali, ' . $user['name'] . '!');
        }

        return redirect()->back()->withInput()->with('error', 'Username atau password salah. Silakan coba lagi.');
    }

    public function quickLogin($role)
    {
        $userModel = new UserModel();
        $user = $userModel->where('role', $role)->first();

        if ($user) {
            session()->set([
                'user_id'      => $user['id'],
                'username'     => $user['username'],
                'name'         => $user['name'],
                'role'         => $user['role'],
                'is_logged_in' => true,
            ]);

            return redirect()->to('/dashboard')->with('success', 'Berhasil masuk sebagai ' . $user['name']);
        }

        return redirect()->to('/login')->with('error', 'Akun demo untuk peran tersebut tidak ditemukan.');
    }

    public function switchRole()
    {
        $targetRole = $this->request->getPost('role');
        $userModel = new UserModel();
        $user = $userModel->where('role', $targetRole)->first();

        if ($user) {
            session()->set([
                'user_id'      => $user['id'],
                'username'     => $user['username'],
                'name'         => $user['name'],
                'role'         => $user['role'],
                'is_logged_in' => true,
            ]);

            return redirect()->to('/dashboard')->with('success', 'Beralih peran menjadi: ' . $user['name']);
        }

        return redirect()->back()->with('error', 'Gagal beralih peran.');
    }

    public function logout()
    {
        $session = session();
        $session->remove(['user_id', 'username', 'name', 'role', 'is_logged_in']);
        $session->destroy();

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }

        return redirect()->to('/login');
    }
}
