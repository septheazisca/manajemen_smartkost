<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AdminProfileController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Tampilkan halaman profil admin
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $user   = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/admin/dashboard')->with('error', 'Data profil tidak ditemukan.');
        }

        return view('admin/profile', ['user' => $user]);
    }

    /**
     * Update profil admin (nama, phone, email)
     */
    public function update()
    {
        $userId = session()->get('user_id');
        $user   = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Validasi: email is_unique hanya kalau berubah
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

        // Update session data so sidebar/header reflect changes immediately
        session()->set('name', $this->request->getPost('name'));

        return redirect()->to('/admin/profile')
            ->with('success', 'Profil berhasil diupdate.');
    }
}
