<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PenanggungJawabModel;
use App\Models\PengeluaranModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class PenanggungJawabController extends BaseController
{
    protected $pjModel;
    protected $userModel;
    protected $pengeluaranModel;

    public function __construct()
    {
        $this->pjModel          = new PenanggungJawabModel();
        $this->userModel        = new UserModel();
        $this->pengeluaranModel = new PengeluaranModel();
    }

    // =====================
    // INDEX - Admin lihat semua PJ
    // =====================
    public function index()
    {
        $data['pj_list'] = $this->pjModel->getPjLengkap();

        return view('admin/pj/index', $data);
    }

    // =====================
    // STORE - Admin tambah PJ baru
    // =====================
    public function store()
    {
        $rules = [
            'nama'         => 'required|min_length[3]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'phone'        => 'required',
            'spesialisasi' => 'permit_empty',
            'gaji_bulanan' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $phone = $this->request->getPost('phone');

        // 1. buat akun user untuk PJ
        $this->userModel->save([
            'name'                 => $this->request->getPost('nama'),
            'email'                => $this->request->getPost('email'),
            'phone'                => $phone,
            'password'             => password_hash($phone, PASSWORD_DEFAULT),
            'role'                 => 'pj',
            'is_active'            => 1,
            'must_change_password' => 1,
        ]);

        $userId = $this->userModel->getInsertID();

        // 2. simpan data PJ
        $this->pjModel->save([
            'user_id'      => $userId,
            'nama'         => $this->request->getPost('nama'),
            'phone'        => $phone,
            'spesialisasi' => $this->request->getPost('spesialisasi'),
            'gaji_bulanan' => $this->request->getPost('gaji_bulanan'),
            'is_active'    => 1,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambah penanggung jawab.');
        }

        return redirect()->to('/admin/pj')
            ->with('success', 'Penanggung jawab berhasil ditambahkan. Password default: nomor HP.');
    }

    // =====================
    // UPDATE - Admin edit data PJ
    // =====================
    public function update($id)
    {
        $pj = $this->pjModel->find($id);

        if (!$pj) {
            return redirect()->back()->with('error', 'Data penanggung jawab tidak ditemukan.');
        }

        $user = $this->userModel->find($pj['user_id']);

        $rules = [
            'nama'         => 'required|min_length[3]',
            'phone'        => 'required',
            'spesialisasi' => 'permit_empty',
            'gaji_bulanan' => 'required|numeric',
            'email'        => $this->request->getPost('email') !== $user['email']
                ? 'required|valid_email|is_unique[users.email]'
                : 'required|valid_email',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // update tabel users
        $this->userModel->update($pj['user_id'], [
            'name'  => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
        ]);

        // update tabel penanggung_jawab
        $this->pjModel->update($id, [
            'nama'         => $this->request->getPost('nama'),
            'phone'        => $this->request->getPost('phone'),
            'spesialisasi' => $this->request->getPost('spesialisasi'),
            'gaji_bulanan' => $this->request->getPost('gaji_bulanan'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate data penanggung jawab.');
        }

        return redirect()->to('/admin/pj')
            ->with('success', 'Data penanggung jawab berhasil diupdate.');
    }

    // =====================
    // TOGGLE STATUS - Aktifkan / nonaktifkan PJ
    // =====================
    public function toggleStatus($id)
    {
        $pj = $this->pjModel->find($id);

        if (!$pj) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $newStatus = $pj['is_active'] == 1 ? 0 : 1;

        $db = \Config\Database::connect();
        $db->transStart();

        // nonaktifkan di tabel PJ
        $this->pjModel->update($id, [
            'is_active' => $newStatus,
        ]);

        // nonaktifkan juga di tabel users
        $this->userModel->update($pj['user_id'], [
            'is_active' => $newStatus,
        ]);

        $db->transComplete();

        $status = $newStatus == 1 ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->to('/admin/pj')
            ->with('success', "Penanggung jawab berhasil {$status}.");
    }

    // =====================
    // BAYAR GAJI - Admin input pembayaran gaji bulanan PJ
    // =====================
    public function bayarGaji($id)
    {
        $pj = $this->pjModel->find($id);

        if (!$pj) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'bulan' => 'required',
            'tahun' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');

        // cek apakah gaji bulan ini sudah dibayar
        $sudahBayar = $this->pengeluaranModel
            ->where('pj_id', $id)
            ->where('kategori', 'gaji')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        if ($sudahBayar) {
            return redirect()->back()
                ->with('error', "Gaji {$pj['nama']} untuk bulan {$bulan}/{$tahun} sudah dibayar.");
        }

        // nominal gaji bisa di-override atau pakai gaji_bulanan default
        $jumlah = $this->request->getPost('jumlah') ?: $pj['gaji_bulanan'];

        $this->pengeluaranModel->save([
            'keterangan' => "Gaji penanggung jawab: {$pj['nama']} ({$bulan}/{$tahun})",
            'kategori'   => 'gaji',
            'jumlah'     => $jumlah,
            'bulan'      => $bulan,
            'tahun'      => $tahun,
            'pj_id'      => $id,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/pj')
            ->with('success', "Gaji {$pj['nama']} bulan {$bulan}/{$tahun} berhasil dicatat.");
    }

    // =====================
    // RIWAYAT GAJI - Admin lihat riwayat pembayaran gaji PJ
    // =====================
    public function riwayatGaji($id)
    {
        $pj = $this->pjModel->getPjLengkap($id);
        
        if (!$pj) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $data['pj'] = $pj;
        $data['riwayat'] = $this->pengeluaranModel
            ->where('pj_id', $id)
            ->where('kategori', 'gaji')
            ->orderBy('tahun', 'DESC')
            ->orderBy('bulan', 'DESC')
            ->findAll();

        return view('admin/pj/riwayat_gaji', $data);
    }

    // =====================
    // RESET PASSWORD - Reset password PJ ke nomor HP
    // =====================
    public function resetPassword($id)
    {
        $pj = $this->pjModel->find($id);

        if (!$pj) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $this->userModel->update($pj['user_id'], [
            'password'             => password_hash($pj['phone'], PASSWORD_DEFAULT),
            'must_change_password' => 1,
        ]);

        return redirect()->to('/admin/pj')
            ->with('success', 'Password berhasil direset ke nomor HP penanggung jawab.');
    }

    // =====================
    // DASHBOARD PJ - PJ lihat info diri sendiri
    // =====================
    public function dashboardPj()
    {
        $userId = session()->get('user_id');
        $pj     = $this->pjModel->getPjByUserId($userId);

        if (!$pj) {
            return redirect()->to('/login')->with('error', 'Data tidak ditemukan.');
        }

        $maintenanceModel = new \App\Models\MaintenanceModel();

        $data['pj']              = $pj;
        $data['total_tugas']     = $maintenanceModel->where('pj_id', $pj['id'])->countAllResults();
        $data['tugas_proses']    = $maintenanceModel->where('pj_id', $pj['id'])->where('status', 'proses')->countAllResults();
        $data['tugas_selesai']   = $maintenanceModel->where('pj_id', $pj['id'])->where('status', 'selesai')->countAllResults();
        $data['riwayat_gaji']    = $this->pengeluaranModel
            ->where('pj_id', $pj['id'])
            ->where('kategori', 'gaji')
            ->orderBy('tahun', 'DESC')
            ->orderBy('bulan', 'DESC')
            ->findAll();

        return view('pj/dashboard', $data);
    }
}
