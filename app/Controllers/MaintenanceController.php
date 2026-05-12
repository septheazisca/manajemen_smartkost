<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KamarModel;
use App\Models\MaintenanceModel;
use App\Models\PenanggungJawabModel;
use App\Models\PengeluaranModel;
use App\Models\PenyewaModel;
use CodeIgniter\HTTP\ResponseInterface;

class MaintenanceController extends BaseController
{
    protected $maintenanceModel;
    protected $penyewaModel;
    protected $pjModel;
    protected $pengeluaranModel;
    protected $kamarModel;

    public function __construct()
    {
        $this->maintenanceModel = new MaintenanceModel();
        $this->penyewaModel     = new PenyewaModel();
        $this->pjModel          = new PenanggungJawabModel();
        $this->pengeluaranModel = new PengeluaranModel();
        $this->kamarModel       = new KamarModel();
    }

    // =====================
    // INDEX - Admin lihat semua laporan maintenance
    // =====================
    public function index()
    {
        $data['maintenance'] = $this->maintenanceModel->getMaintenanceLengkap();
        $data['pj_list']     = $this->pjModel->where('is_active', 1)->findAll();

        return view('admin/maintenance/index', $data);
    }

    // =====================
    // ASSIGN - Admin assign maintenance ke PJ
    // =====================
    public function assign($id)
    {
        $maintenance = $this->maintenanceModel->find($id);

        if (!$maintenance) {
            return redirect()->back()->with('error', 'Data maintenance tidak ditemukan.');
        }

        $pjId = $this->request->getPost('pj_id');

        if (!$pjId) {
            return redirect()->back()->with('error', 'Penanggung jawab wajib dipilih.');
        }

        $this->maintenanceModel->update($id, [
            'pj_id'       => $pjId,
            'status'      => 'proses',
            'assigned_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/maintenance')
            ->with('success', 'Laporan berhasil di-assign ke penanggung jawab.');
    }

    // =====================
    // SELESAI - PJ tandai maintenance selesai + input biaya
    // =====================
    public function selesai($id)
    {
        $maintenance = $this->maintenanceModel->find($id);

        if (!$maintenance) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // pastikan yang akses adalah PJ yang di-assign
        $userId = session()->get('user_id');
        $pj     = $this->pjModel->getPjByUserId($userId);

        if (!$pj || $maintenance['pj_id'] != $pj['id']) {
            return redirect()->back()->with('error', 'Kamu tidak memiliki akses ke laporan ini.');
        }

        $rules = [
            'catatan_pj' => 'required',
            'biaya'      => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $biaya = $this->request->getPost('biaya');

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. update status maintenance
        $this->maintenanceModel->update($id, [
            'status'     => 'selesai',
            'catatan_pj' => $this->request->getPost('catatan_pj'),
            'biaya'      => $biaya,
            'selesai_at' => date('Y-m-d H:i:s'),
        ]);

        // 2. otomatis catat ke pengeluaran
        if ($biaya > 0) {
            $this->pengeluaranModel->save([
                'keterangan'     => 'Biaya maintenance: ' . $maintenance['deskripsi'],
                'kategori'       => 'maintenance',
                'jumlah'         => $biaya,
                'bulan'          => date('m'),
                'tahun'          => date('Y'),
                'pj_id'          => $pj['id'],
                'maintenance_id' => $id,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal update status maintenance.');
        }

        return redirect()->to('/pj/maintenance')
            ->with('success', 'Maintenance selesai. Biaya berhasil dicatat.');
    }

    // =====================
    // LAPOR - Penyewa lapor kerusakan
    // =====================
    public function lapor()
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data penyewa tidak ditemukan.');
        }

        $rules = [
            'deskripsi' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // handle upload foto (opsional)
        $fotoName = null;
        $foto     = $this->request->getFile('foto');

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

            if (!in_array($foto->getMimeType(), $allowedTypes)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Format foto harus JPG atau PNG.');
            }

            if ($foto->getSizeByUnit('mb') > 2) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ukuran foto maksimal 2MB.');
            }

            $fotoName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/maintenance', $fotoName);
        }

        $this->maintenanceModel->save([
            'penyewa_id' => $penyewa['id'],
            'kamar_id'   => $penyewa['kamar_id'],
            'deskripsi'  => $this->request->getPost('deskripsi'),
            'foto'       => $fotoName,
            'status'     => 'menunggu',
        ]);

        return redirect()->to('/tenant/maintenance')
            ->with('success', 'Laporan kerusakan berhasil dikirim. Menunggu tindakan admin.');
    }

    // =====================
    // INDEX PENYEWA - Penyewa lihat laporan milik sendiri
    // =====================
    public function laporanSaya()
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->to('/tenant/dashboard')->with('error', 'Data tidak ditemukan.');
        }

        $data['maintenance'] = $this->maintenanceModel
            ->select('maintenance.*, kamar.nomor_kamar')
            ->join('kamar', 'kamar.id = maintenance.kamar_id', 'left')
            ->where('maintenance.penyewa_id', $penyewa['id'])
            ->orderBy('maintenance.created_at', 'DESC')
            ->findAll();

        return view('tenant/maintenance', $data);
    }

    // =====================
    // INDEX PJ - PJ lihat maintenance yang di-assign ke dia
    // =====================
    public function indexPj()
    {
        $userId = session()->get('user_id');
        $pj     = $this->pjModel->getPjByUserId($userId);

        if (!$pj) {
            return redirect()->to('/pj/dashboard')->with('error', 'Data tidak ditemukan.');
        }

        $data['maintenance'] = $this->maintenanceModel->getMaintenanceByPj($pj['id']);
        $data['pj']          = $pj;

        return view('pj/maintenance', $data);
    }

    // =====================
    // DETAIL - Admin & PJ lihat detail maintenance
    // =====================
    public function detail($id)
    {
        $maintenance = $this->maintenanceModel
            ->select('
                maintenance.*,
                users.name as nama_penyewa,
                users.phone as phone_penyewa,
                kamar.nomor_kamar,
                penanggung_jawab.nama as nama_pj,
                penanggung_jawab.phone as phone_pj
            ')
            ->join('penyewa', 'penyewa.id = maintenance.penyewa_id', 'left')
            ->join('users', 'users.id = penyewa.user_id', 'left')
            ->join('kamar', 'kamar.id = maintenance.kamar_id', 'left')
            ->join('penanggung_jawab', 'penanggung_jawab.id = maintenance.pj_id', 'left')
            ->where('maintenance.id', $id)
            ->first();

        if (!$maintenance) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $role = session()->get('role');
        $view = $role === 'admin' ? 'admin/maintenance/detail' : 'pj/maintenance_detail';

        // return view($view, ['maintenance' => $maintenance]);
        return view($view, [
            'maintenance' => $maintenance,
            'pj_list'     => $this->pjModel->where('is_active', 1)->findAll(), // tambah ini
        ]);
    }

    public function ambil($id)
    {
        $maintenance = $this->maintenanceModel->find($id);

        if (!$maintenance) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        if ($maintenance['pj_id'] !== null) {
            return redirect()->back()->with('error', 'Laporan ini sudah diambil oleh PJ lain.');
        }

        $userId = session()->get('user_id');
        $pj     = $this->pjModel->getPjByUserId($userId);

        $this->maintenanceModel->update($id, [
            'pj_id'       => $pj['id'],
            'status'      => 'proses',
            'assigned_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/pj/maintenance')
            ->with('success', 'Laporan berhasil diambil, segera kerjakan.');
    }

    // =====================
    // DELETE - Admin hapus laporan maintenance
    // =====================
    public function delete($id)
    {
        $maintenance = $this->maintenanceModel->find($id);

        if (!$maintenance) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // hapus foto jika ada
        if ($maintenance['foto']) {
            $fotoPath = FCPATH . 'uploads/maintenance/' . $maintenance['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
        }

        $this->maintenanceModel->delete($id);

        return redirect()->to('/admin/maintenance')
            ->with('success', 'Laporan maintenance berhasil dihapus.');
    }

    public function detailTenant($id)
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->to('/tenant/dashboard')->with('error', 'Data tidak ditemukan.');
        }

        $maintenance = $this->maintenanceModel
            ->select('maintenance.*, kamar.nomor_kamar, penanggung_jawab.nama as nama_pj')
            ->join('kamar', 'kamar.id = maintenance.kamar_id', 'left')
            ->join('penanggung_jawab', 'penanggung_jawab.id = maintenance.pj_id', 'left')
            ->where('maintenance.id', $id)
            ->where('maintenance.penyewa_id', $penyewa['id'])
            ->first();

        if (!$maintenance) {
            return redirect()->to('/tenant/maintenance')->with('error', 'Laporan tidak ditemukan.');
        }

        return view('tenant/maintenance_detail', ['maintenance' => $maintenance]);
    }
}
