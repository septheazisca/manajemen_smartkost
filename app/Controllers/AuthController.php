<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    // Tampilkan halaman login
    public function login()
    {
        // kalau sudah login, langsung redirect
        if (session()->get('logged_in')) {
            return $this->redirectByRole(session()->get('role'));
        }

        return view('auth/login');
    }

    // Proses login saat user klik tombol masuk
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

        // 2. Cari user berdasarkan email
        $user = $model->where('email', $email)->first();

        // 2. cek user ada atau tidak
        if (!$user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email atau password salah.');
        }

        // 3. Verifikasi password dengan hash di database
        if (!password_verify($password, $user['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email atau password salah.');
        }

        // 4. Cek apakah akun aktif (bisa dinonaktifkan oleh admin)
        if ($user['is_active'] != 1) {
            return redirect()->back()
                ->with('error', 'Akun kamu dinonaktifkan. Hubungi admin.');
        }

        // 5. Simpan data user ke session agar bisa diakses di seluruh aplikasi
        session()->set([
            'user_id'  => $user['id'],
            'name'     => $user['name'],
            'role'     => $user['role'],
            'logged_in' => true,
        ]);

        // 6. Jika akun baru atau password baru di-reset admin, paksa ganti password dulu
        if ($user['must_change_password'] == 1) {
            return redirect()->to('/change-password')
                ->with('info', 'Kamu harus mengganti password sebelum melanjutkan.');
        }

        // 7. Redirect ke dashboard sesuai role
        return $this->redirectByRole($user['role']);
    }

    // Hapus semua session dan redirect ke halaman login
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Berhasil logout.');
    }

    // Tampilkan halaman unauthorized (akses ditolak)
    public function unauthorized()
    {
        return view('errors/unauthorized');
    }

    // Tampilkan form ganti password
    public function changePassword()
    {
        return view('auth/change_password');
    }

    // Proses ganti password
    public function updatePassword()
    {
        // Validasi: minimal 8 karakter, harus ada huruf besar, huruf kecil, dan angka
        $rules = [
            'password_baru' => [
                'rules'  => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/]',
                'errors' => [
                    'required'    => 'Password baru wajib diisi.',
                    'min_length'  => 'Password minimal 8 karakter.',
                    'regex_match' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
                ],
            ],
            // Konfirmasi harus sama persis dengan password baru
            'konfirmasi_password' => [
                'rules'  => 'required|matches[password_baru]',
                'errors' => [
                    'required' => 'Konfirmasi password wajib diisi.',
                    'matches'  => 'Konfirmasi password tidak cocok.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model = new UserModel();
        $userId = session()->get('user_id');

        // Hash password baru sebelum disimpan, lalu tandai sudah ganti password
        $model->update($userId, [
            'password'             => password_hash($this->request->getPost('password_baru'), PASSWORD_DEFAULT),
            'must_change_password' => 0,
        ]);

        return redirect()->to('/change-password')
            ->with('success', 'Password berhasil diubah.');
    }

    // Helper: tentukan tujuan redirect berdasarkan role user
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
