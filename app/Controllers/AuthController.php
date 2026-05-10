<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $model = new UserModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        // 1. cek user
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }

        // 2. cek password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Password salah');
        }

        // 3. cek aktif
        if ($user['is_active'] != 1) {
            return redirect()->back()->with('error', 'Akun nonaktif');
        }

        // 4. set session
        session()->set([
            'user_id' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role'],
            'logged_in' => true
        ]);

        // 5. redirect berdasarkan role
        return match ($user['role']) {
            'admin' => redirect()->to('/admin/dashboard'),
            'pj' => redirect()->to('/pj/dashboard'),
            'penyewa' => redirect()->to('/tenant/dashboard'),
            default => redirect()->to('/login')
        };
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function unauthorized()
    {
        return view('errors/unauthorized');
    }
}
