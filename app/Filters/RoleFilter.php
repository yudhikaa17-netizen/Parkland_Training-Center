<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userRole = $session->get('role') ?? 'guest';

        if (!empty($arguments)) {
            // Check if current user role matches one of allowed arguments
            if (!in_array($userRole, $arguments)) {
                return redirect()->to('/dashboard')->with('error', 'Akses Ditolak: Peran Anda (' . ucfirst($userRole) . ') tidak memiliki izin untuk mengakses modul tersebut sesuai Matriks Akses Pengguna.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
