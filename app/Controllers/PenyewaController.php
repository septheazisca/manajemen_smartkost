<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KamarModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class PenyewaController extends BaseController
{
    protected $penyewaModel;
    protected $userModel;
    protected $kamarModel;

    public function __construct()
    {
        $this->penyewaModel = new PenyewaModel();
        $this->userModel    = new UserModel();
        $this->kamarModel   = new KamarModel();
    }

    // INDEX
    public function index()
    {
        $data['penyewa'] = $this->penyewaModel
            ->select('penyewa.*, users.name, users.email, users.phone, users.is_active, kamar.nomor_kamar')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id')
            ->findAll();

        $data['kamar_kosong'] = $this->kamarModel
            ->where('status', 'kosong')
            ->findAll();

        return view('admin/penyewa/index', $data);
    }

    // STORE
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

        // 1. buat password default dari nomor HP
        $defaultPassword = $this->request->getPost('phone');

        // 2. simpan ke tabel users
        $this->userModel->save([
            'name'                 => $this->request->getPost('name'),
            'email'                => $this->request->getPost('email'),
            'phone'                => $this->request->getPost('phone'),
            'password'             => password_hash($defaultPassword, PASSWORD_DEFAULT),
            'role'                 => 'penyewa',
            'is_active'            => 1,
            'must_change_password' => 1, // wajib ganti password saat pertama login
        ]);

        $userId = $this->userModel->getInsertID();

        // 3. simpan ke tabel penyewa
        $this->penyewaModel->save([
            'user_id'           => $userId,
            'kamar_id'          => $this->request->getPost('kamar_id'),
            'tanggal_masuk'     => $this->request->getPost('tanggal_masuk'),
            'alamat'            => $this->request->getPost('alamat'),
            'asal_kota'         => $this->request->getPost('asal_kota'),
            'status_pekerjaan'  => $this->request->getPost('status_pekerjaan'),
            'status_pernikahan' => $this->request->getPost('status_pernikahan'),
            'nomor_darurat'     => $this->request->getPost('nomor_darurat'),
        ]);

        // 4. update status kamar jadi terisi
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

    // UPDATE
    public function update($id)
    {
        // $id di sini adalah penyewa.id
        $penyewa = $this->penyewaModel->find($id);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data penyewa tidak ditemukan.');
        }

        $rules = [
            'name'  => 'required|min_length[3]',
            'phone' => 'required',
        ];

        // cek email hanya kalau berubah
        $user = $this->userModel->find($penyewa['user_id']);
        if ($this->request->getPost('email') !== $user['email']) {
            $rules['email'] = 'required|valid_email|is_unique[users.email]';
        } else {
            $rules['email'] = 'required|valid_email';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // update tabel users
        $this->userModel->update($penyewa['user_id'], [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
        ]);

        // update tabel penyewa
        $this->penyewaModel->update($id, [
            'alamat'            => $this->request->getPost('alamat'),
            'asal_kota'         => $this->request->getPost('asal_kota'),
            'status_pekerjaan'  => $this->request->getPost('status_pekerjaan'),
            'status_pernikahan' => $this->request->getPost('status_pernikahan'),
            'nomor_darurat'     => $this->request->getPost('nomor_darurat'),
            'tanggal_masuk'     => $this->request->getPost('tanggal_masuk'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate data penyewa.');
        }

        return redirect()->to('/admin/penyewa')
            ->with('success', 'Data penyewa berhasil diupdate.');
    }

    // TOGGLE AKTIF / NONAKTIF
    public function toggleStatus($id)
    {
        $penyewa = $this->penyewaModel->find($id);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $user = $this->userModel->find($penyewa['user_id']);
        $newStatus = $user['is_active'] == 1 ? 0 : 1;

        $this->userModel->update($penyewa['user_id'], [
            'is_active' => $newStatus,
        ]);

        $status = $newStatus == 1 ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->to('/admin/penyewa')
            ->with('success', "Akun penyewa berhasil {$status}.");
    }

    // CHECKOUT (penyewa keluar)
    public function checkout($id)
    {
        $penyewa = $this->penyewaModel->find($id);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. set tanggal keluar
        $this->penyewaModel->update($id, [
            'tanggal_keluar' => date('Y-m-d'),
        ]);

        // 2. nonaktifkan akun
        $this->userModel->update($penyewa['user_id'], [
            'is_active' => 0,
        ]);

        // 3. set kamar kembali kosong
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

    // RESET PASSWORD
    public function resetPassword($id)
    {
        $penyewa = $this->penyewaModel->find($id);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $user = $this->userModel->find($penyewa['user_id']);

        // reset ke nomor HP
        $this->userModel->update($penyewa['user_id'], [
            'password'             => password_hash($user['phone'], PASSWORD_DEFAULT),
            'must_change_password' => 1,
        ]);

        return redirect()->to('/admin/penyewa')
            ->with('success', 'Password berhasil direset ke nomor HP penyewa.');
    }
}
