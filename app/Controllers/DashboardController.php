<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index()
    {
        $role = session()->get('role');

        return match ($role) {
            'admin' => view('admin/dashboard'),
            'pj' => view('pj/dashboard'),
            'penyewa' => view('tenant/dashboard'),
            default => redirect()->to('/login')
        };
    }
}
