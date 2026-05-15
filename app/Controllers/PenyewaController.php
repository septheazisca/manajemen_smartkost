<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KamarModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PenyewaModel;

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

    // =====================
    // INDEX - Admin lihat semua penyewa
    // =====================
    public function index()
    {
        $data['penyewa']      = $this->penyewaModel->getPenyewaLengkap();
        $data['kamar_kosong'] = $this->kamarModel->getKamarKosong();

        return view('admin/penyewa', $data);
    }

    // =====================
    // STORE - Admin tambah penyewa baru
    // =====================
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

        // 1. buat akun user
        $this->userModel->save([
            'name'                 => $this->request->getPost('name'),
            'email'                => $this->request->getPost('email'),
            'phone'                => $phone,
            'password'             => password_hash($phone, PASSWORD_DEFAULT),
            'role'                 => 'penyewa',
            'is_active'            => 1,
            'must_change_password' => 1,
        ]);

        $userId = $this->userModel->getInsertID();

        // 2. simpan data penyewa
        $this->penyewaModel->save([
            'user_id'           => $userId,
            'kamar_id'          => $this->request->getPost('kamar_id'),
            'tanggal_masuk'     => $this->request->getPost('tanggal_masuk'),
            'alamat'            => $this->request->getPost('alamat'),
            'asal_kota'         => $this->request->getPost('asal_kota'),
            'status_pekerjaan'   => $this->request->getPost('status_pekerjaan') ?: null,
            'status_pernikahan'  => $this->request->getPost('status_pernikahan') ?: null,
            'nomor_darurat'     => $this->request->getPost('nomor_darurat'),
        ]);

        // 3. update status kamar jadi terisi
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

    // =====================
    // UPDATE - Admin edit data penyewa
    // =====================
    public function update($id)
    {
        $penyewa = $this->penyewaModel->find($id);

        if (!$penyewa) {
            return redirect()->back()
                ->with('error', 'Data penyewa tidak ditemukan.');
        }

        $user = $this->userModel->find($penyewa['user_id']);

        $rules = [
            'name'  => 'required|min_length[3]',
            'phone' => 'required',
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

        $db->transBegin();

        $updateUser = $this->userModel->update($penyewa['user_id'], [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
        ]);

        if (!$updateUser) {

            $db->transRollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal update user');
        }

        $updatePenyewa = $this->penyewaModel->update($id, [

            'tanggal_masuk'     => $this->request->getPost('tanggal_masuk'),
            'alamat'            => $this->request->getPost('alamat'),
            'asal_kota'         => $this->request->getPost('asal_kota'),
            'status_pekerjaan'   => $this->request->getPost('status_pekerjaan') ?: null,
            'status_pernikahan'  => $this->request->getPost('status_pernikahan') ?: null,
            'nomor_darurat'     => $this->request->getPost('nomor_darurat'),

        ]);

        if (!$updatePenyewa) {

            $db->transRollback();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal update data penyewa');
        }

        $db->transCommit();

        return redirect()->to('/admin/penyewa')
            ->with('success', 'Data penyewa berhasil diupdate.');
    }

    // =====================
    // TOGGLE STATUS - Aktifkan / nonaktifkan akun penyewa
    // =====================
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

    // =====================
    // CHECKOUT - Penyewa keluar
    // =====================
    public function checkout($id)
    {
        $penyewa = $this->penyewaModel->find($id);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $user = $this->userModel->find($penyewa['user_id']);

        $db = \Config\Database::connect();
        $db->transStart();

        $this->penyewaModel->update($id, [
            'tanggal_keluar' => date('Y-m-d'),
        ]);

        $this->userModel->update($penyewa['user_id'], [
            'is_active' => 0,
            'email'     => $user['email'] . '_checkout_' . time(),
        ]);

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

    // =====================
    // RESET PASSWORD - Reset ke nomor HP
    // =====================
    public function resetPassword($id)
    {
        $penyewa = $this->penyewaModel->find($id);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $user = $this->userModel->find($penyewa['user_id']);

        $this->userModel->update($penyewa['user_id'], [
            'password'             => password_hash($user['phone'], PASSWORD_DEFAULT),
            'must_change_password' => 1,
        ]);

        return redirect()->to('/admin/penyewa')
            ->with('success', 'Password berhasil direset ke nomor HP penyewa.');
    }

    // =====================
    // PROFILE - Penyewa lihat & update data diri sendiri
    // =====================
    public function profile()
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->to('/tenant/dashboard')->with('error', 'Data profil tidak ditemukan.');
        }

        return view('tenant/profile', ['penyewa' => $penyewa]);
    }

    // =====================
    // UPDATE PROFILE - Penyewa update data diri sendiri
    // =====================
    public function updateProfile()
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // tambah validasi
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

        $this->userModel->update($userId, [
            'phone' => $this->request->getPost('phone'),
        ]);

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
}
