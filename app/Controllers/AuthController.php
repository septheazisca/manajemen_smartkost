<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    public function login()
    {
        // kalau sudah login, langsung redirect
        if (session()->get('logged_in')) {
            return $this->redirectByRole(session()->get('role'));
        }

        return view('auth/login');
    }

    public function attemptLogin()
    {
        // 1. validasi input dulu
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email dan password wajib diisi dengan benar.');
        }

        $model    = new UserModel();
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        // 2. cek user ada atau tidak
        if (!$user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email atau password salah.');
        }

        // 3. cek password
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email atau password salah.');
        }

        // 4. cek akun aktif
        if ($user['is_active'] != 1) {
            return redirect()->back()
                ->with('error', 'Akun kamu dinonaktifkan. Hubungi admin.');
        }

        // 5. set session
        session()->set([
            'user_id'  => $user['id'],
            'name'     => $user['name'],
            'role'     => $user['role'],
            'logged_in' => true,
        ]);

        // 6. cek must_change_password
        if ($user['must_change_password'] == 1) {
            return redirect()->to('/change-password')
                ->with('info', 'Kamu harus mengganti password sebelum melanjutkan.');
        }

        // 7. redirect sesuai role
        return $this->redirectByRole($user['role']);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Berhasil logout.');
    }

    public function unauthorized()
    {
        return view('errors/unauthorized');
    }

    public function changePassword()
    {
        return view('auth/change_password');
    }

    public function updatePassword()
    {
        $rules = [
            'password_baru'    => 'required|min_length[6]',
            'konfirmasi_password' => 'required|matches[password_baru]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model = new UserModel();
        $userId = session()->get('user_id');

        $model->update($userId, [
            'password'             => password_hash($this->request->getPost('password_baru'), PASSWORD_DEFAULT),
            'must_change_password' => 0,
        ]);

        return redirect()->to('/change-password')
            ->with('success', 'Password berhasil diubah.');
    }

    // helper redirect by role
    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin'   => redirect()->to('/admin/dashboard'),
            'pj'      => redirect()->to('/pj/dashboard'),
            'penyewa' => redirect()->to('/tenant/dashboard'),
            default   => redirect()->to('/login'),
        };
    }
}
