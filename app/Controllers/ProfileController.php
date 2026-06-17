<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PenyewaModel;
use App\Models\PenanggungJawabModel;

class ProfileController extends BaseController
{
    protected $userModel;
    protected $penyewaModel;
    protected $pjModel;

    public function __construct()
    {
        $this->userModel    = new UserModel();
        $this->penyewaModel = new PenyewaModel();
        $this->pjModel      = new PenanggungJawabModel();
    }

    // ==============================================
    // ADMIN PROFILE
    // ==============================================
    public function adminIndex()
    {
        $userId = session()->get('user_id');
        $user   = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/admin/dashboard')->with('error', 'Data profil tidak ditemukan.');
        }

        return view('admin/profile', ['user' => $user]);
    }

    public function adminUpdate()
    {
        $userId = session()->get('user_id');
        $user   = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $emailRule = ($this->request->getPost('email') != $user['email'])
            ? 'required|valid_email|is_unique[users.email]'
            : 'required|valid_email';

        $rules = [
            'name'  => 'required|min_length[3]',
            'email' => $emailRule,
            'phone' => 'required|min_length[10]|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->userModel->update($userId, [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
        ]);

        session()->set('name', $this->request->getPost('name'));

        return redirect()->to('/admin/profile')
            ->with('success', 'Profil berhasil diupdate.');
    }

    // ==============================================
    // TENANT (PENYEWA) PROFILE
    // ==============================================
    public function tenantIndex()
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->to('/tenant/dashboard')->with('error', 'Data profil tidak ditemukan.');
        }

        return view('tenant/profile', ['penyewa' => $penyewa]);
    }

    public function tenantUpdate()
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

        $this->userModel->update($userId, [
            'phone' => $this->request->getPost('phone'),
        ]);

        $this->penyewaModel->update($penyewa['id'], [
            'alamat'               => $this->request->getPost('alamat'),
            'asal_kota'            => $this->request->getPost('asal_kota'),
            'status_pekerjaan_id'  => $this->request->getPost('status_pekerjaan_id') ?: null,
            'status_pernikahan_id' => $this->request->getPost('status_pernikahan_id') ?: null,
            'nomor_darurat'        => $this->request->getPost('nomor_darurat'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal update profil.');
        }

        return redirect()->to('/tenant/profile')
            ->with('success', 'Profil berhasil diupdate.');
    }

    // ==============================================
    // PJ (PENANGGUNG JAWAB) PROFILE
    // ==============================================
    public function pjIndex()
    {
        $userId = session()->get('user_id');
        $pj     = $this->pjModel->getPjByUserId($userId);

        if (!$pj) {
            return redirect()->to('/pj/dashboard')->with('error', 'Data profil tidak ditemukan.');
        }

        $user = $this->userModel->find($userId);
        
        $data = [
            'pj' => array_merge($pj, ['email' => $user['email'] ?? ''])
        ];

        return view('pj/profile', $data);
    }

    public function pjUpdate()
    {
        $userId = session()->get('user_id');
        $pj     = $this->pjModel->getPjByUserId($userId);

        if (!$pj) {
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

        // Update data di tabel penanggung_jawab
        $this->pjModel->update($pj['id'], [
            'phone' => $this->request->getPost('phone'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal update profil.');
        }

        return redirect()->to('/pj/profile')
            ->with('success', 'Profil berhasil diupdate.');
    }
}
