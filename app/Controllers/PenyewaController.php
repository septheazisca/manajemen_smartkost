<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KamarModel;
use App\Models\PenyewaModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class PenyewaController extends BaseController
{
    // Semua model dideklarasikan sebagai property agar bisa dipakai di semua method
    protected $penyewaModel;
    protected $userModel;
    protected $kamarModel;

    public function __construct()
    {
        $this->penyewaModel = new PenyewaModel();
        $this->userModel    = new UserModel();
        $this->kamarModel   = new KamarModel();
    }

    // Tampilkan semua penyewa aktif beserta daftar kamar kosong untuk form tambah
    public function index()
    {
        $data['penyewa']      = $this->penyewaModel->getPenyewaLengkap();
        $data['kamar_kosong'] = $this->kamarModel->getKamarKosong();
        
        // Ambil semua ulasan & rating kost untuk dipantau admin
        $data['all_ratings']  = $this->penyewaModel->select('penyewa.*, users.name, users.phone, kamar.nomor_kamar')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id', 'left')
            ->where('penyewa.rating !=', null)
            ->orderBy('penyewa.id', 'DESC')
            ->findAll();

        return view('admin/penyewa', $data);
    }

    // Tambah penyewa baru beserta akun login-nya
    // Data disimpan di 2 tabel: users (untuk login) dan penyewa (untuk data sewa)
    // Status kamar otomatis berubah jadi 'terisi' setelah penyewa ditambahkan
    public function store()
    {
        $rules = [
            'name'          => 'required|min_length[3]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'phone'         => 'required',
            'kamar_id'      => 'required',
            'tanggal_masuk' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $phone = $this->request->getPost('phone');

        // 1. Buat akun user untuk login, password default = nomor HP
        // must_change_password = 1 agar penyewa wajib ganti password saat pertama login
        $this->userModel->save([
            'name'                 => $this->request->getPost('name'),
            'email'                => $this->request->getPost('email'),
            'phone'                => $phone,
            'password'             => password_hash((string) $phone, PASSWORD_DEFAULT),
            'role'                 => 'penyewa',
            'is_active'            => 1,
            'must_change_password' => 1,
        ]);

        $userId = $this->userModel->getInsertID();

        // 2. Simpan data sewa penyewa, dihubungkan ke akun user lewat user_id
        $this->penyewaModel->save([
            'user_id'          => $userId,
            'kamar_id'         => $this->request->getPost('kamar_id'),
            'tanggal_masuk'    => $this->request->getPost('tanggal_masuk'),
            'alamat'           => $this->request->getPost('alamat'),
            'asal_kota'        => $this->request->getPost('asal_kota'),
            'status_pekerjaan' => $this->request->getPost('status_pekerjaan') ?: null,
            'status_pernikahan' => $this->request->getPost('status_pernikahan') ?: null,
            'nomor_darurat'    => $this->request->getPost('nomor_darurat'),
        ]);

        // 3. Update status kamar jadi terisi agar tidak bisa dipilih penyewa lain
        $this->kamarModel->update($this->request->getPost('kamar_id'), [
            'status' => 'terisi',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambah penyewa. Silakan coba lagi.');
        }

        return redirect()->to('/admin/penyewa')
            ->with('success', 'Penyewa berhasil ditambahkan. Password default: nomor HP penyewa.');
    }

    // Update data penyewa di 2 tabel sekaligus menggunakan transaction manual
    // Pakai transBegin/transCommit/transRollback agar bisa cek hasil tiap update secara terpisah
    // Validasi email: skip is_unique kalau email tidak berubah agar tidak error
    public function update($id)
    {
        $penyewa = $this->penyewaModel->find($id);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data penyewa tidak ditemukan.');
        }

        $user = $this->userModel->find($penyewa['user_id']);

        $rules = [
            'name'  => 'required|min_length[3]',
            'phone' => 'required',
            // Cek is_unique hanya kalau email memang berubah
            'email' => ($this->request->getPost('email') != $user['email'])
                ? 'required|valid_email|is_unique[users.email]'
                : 'required|valid_email',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Update data login di tabel users
        $this->userModel->update($penyewa['user_id'], [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
        ]);

        // Update data sewa di tabel penyewa
        $this->penyewaModel->update($id, [
            'tanggal_masuk'    => $this->request->getPost('tanggal_masuk'),
            'alamat'           => $this->request->getPost('alamat'),
            'asal_kota'        => $this->request->getPost('asal_kota'),
            'status_pekerjaan' => $this->request->getPost('status_pekerjaan') ?: null,
            'status_pernikahan' => $this->request->getPost('status_pernikahan') ?: null,
            'nomor_darurat'    => $this->request->getPost('nomor_darurat'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate data penyewa.');
        }

        return redirect()->to('/admin/penyewa')
            ->with('success', 'Data penyewa berhasil diupdate.');
    }

    // Aktifkan atau nonaktifkan akun penyewa
    // Hanya update tabel users karena login dikontrol lewat is_active di sana
    public function toggleStatus($id)
    {
        $penyewa = $this->penyewaModel->find($id);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $user      = $this->userModel->find($penyewa['user_id']);
        $newStatus = $user['is_active'] == 1 ? 0 : 1;

        $this->userModel->update($penyewa['user_id'], [
            'is_active' => $newStatus,
        ]);

        $status = $newStatus == 1 ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->to('/admin/penyewa')
            ->with('success', "Akun penyewa berhasil {$status}.");
    }

    // Proses checkout penyewa: tandai tanggal keluar, nonaktifkan akun, kosongkan kamar
    // Email di-rename dengan suffix _checkout_timestamp agar email bisa dipakai lagi
    // oleh penyewa baru tanpa bentrok dengan validasi is_unique
    public function checkout($id)
    {
        $penyewa = $this->penyewaModel->find($id);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $user = $this->userModel->find($penyewa['user_id']);

        $db = \Config\Database::connect();
        $db->transStart();

        // Catat tanggal keluar penyewa
        $this->penyewaModel->update($id, [
            'tanggal_keluar' => date('Y-m-d'),
        ]);

        // Nonaktifkan akun dan rename email agar bisa dipakai lagi di pendaftaran baru
        $this->userModel->update($penyewa['user_id'], [
            'is_active' => 0,
            'email'     => $user['email'] . '_checkout_' . time(),
        ]);

        // Kembalikan status kamar ke kosong agar bisa ditempati penyewa baru
        $this->kamarModel->update($penyewa['kamar_id'], [
            'status' => 'kosong',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal proses checkout.');
        }

        return redirect()->to('/admin/penyewa')
            ->with('success', 'Penyewa berhasil checkout. Kamar kembali tersedia.');
    }

    // Reset password penyewa kembali ke nomor HP terbaru
    // must_change_password = 1 agar penyewa wajib ganti password lagi saat login
    public function resetPassword($id)
    {
        $penyewa = $this->penyewaModel->find($id);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $user = $this->userModel->find($penyewa['user_id']);

        $this->userModel->update($penyewa['user_id'], [
            'password'             => password_hash((string) $user['phone'], PASSWORD_DEFAULT),
            'must_change_password' => 1,
        ]);

        return redirect()->to('/admin/penyewa')
            ->with('success', 'Password berhasil direset ke nomor HP penyewa.');
    }

    // Tampilkan halaman profil penyewa yang sedang login
    public function profile()
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->to('/tenant/dashboard')->with('error', 'Data profil tidak ditemukan.');
        }

        return view('tenant/profile', ['penyewa' => $penyewa]);
    }

    // Penyewa update data diri sendiri
    // Nama dan email tidak bisa diubah sendiri, harus minta admin
    // Yang bisa diupdate: nomor HP, alamat, asal kota, status pekerjaan, status pernikahan, nomor darurat
    public function updateProfile()
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'phone' => 'required|min_length[10]|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Update nomor HP di tabel users
        $this->userModel->update($userId, [
            'phone' => $this->request->getPost('phone'),
        ]);

        // Update data tambahan di tabel penyewa
        $this->penyewaModel->update($penyewa['id'], [
            'alamat'            => $this->request->getPost('alamat'),
            'asal_kota'         => $this->request->getPost('asal_kota'),
            'status_pekerjaan'  => $this->request->getPost('status_pekerjaan') ?: null,
            'status_pernikahan' => $this->request->getPost('status_pernikahan') ?: null,
            'nomor_darurat'     => $this->request->getPost('nomor_darurat'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal update profil.');
        }

        return redirect()->to('/tenant/profile')
            ->with('success', 'Profil berhasil diupdate.');
    }

    // Simpan atau edit rating & testimoni penyewa
    public function saveRating()
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data penyewa tidak ditemukan.');
        }

        $rules = [
            'rating'    => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
            'testimoni' => 'required|min_length[5]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors_rating', $this->validator->getErrors());
        }

        $this->penyewaModel->update($penyewa['id'], [
            'rating'              => $this->request->getPost('rating'),
            'testimoni'           => $this->request->getPost('testimoni'),
            'tampilkan_testimoni' => 1 // Selalu tampilkan secara default saat dikirim/diedit
        ]);

        return redirect()->back()->with('success_rating', 'Penilaian Anda berhasil disimpan.');
    }

    // Toggle sembunyikan/tampilkan testimoni
    public function toggleRatingVisibility()
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data penyewa tidak ditemukan.');
        }

        $newStatus = ($penyewa['tampilkan_testimoni'] == 1) ? 0 : 1;

        $this->penyewaModel->update($penyewa['id'], [
            'tampilkan_testimoni' => $newStatus
        ]);

        $message = ($newStatus == 1) ? 'Ulasan Anda kembali ditampilkan di halaman utama.' : 'Ulasan Anda berhasil disembunyikan dari halaman utama.';

        return redirect()->back()->with('success_rating', $message);
    }
}
