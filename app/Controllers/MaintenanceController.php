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
    // Semua model dideklarasikan sebagai property agar bisa dipakai di semua method
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

    // Tampilkan semua laporan maintenance ke admin beserta daftar PJ aktif untuk form assign
    public function index()
    {
        $data['maintenance'] = $this->maintenanceModel->getMaintenanceLengkap();
        $data['pj_list']     = $this->pjModel->where('is_active', 1)->findAll();

        return view('admin/maintenance/index', $data);
    }

    // Admin assign laporan ke PJ, status otomatis berubah jadi 'proses'
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

    // PJ tandai laporan selesai dan input biaya yang dikeluarkan
    public function selesai($id)
    {
        $maintenance = $this->maintenanceModel->find($id);

        if (!$maintenance) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Keamanan: pastikan yang akses adalah PJ yang memang di-assign ke laporan ini
        // Mencegah PJ lain menandai selesai laporan yang bukan tugasnya
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

        // 1. Update status maintenance jadi selesai
        $this->maintenanceModel->update($id, [
            'status'     => 'selesai',
            'catatan_pj' => $this->request->getPost('catatan_pj'),
            'biaya'      => $biaya,
            'selesai_at' => date('Y-m-d H:i:s'),
        ]);

        // 2. Kalau ada biaya, otomatis catat ke tabel pengeluaran kategori 'maintenance'
        // Ini yang membuat pengeluaran maintenance tidak perlu diinput manual oleh admin
        if ($biaya > 0) {
            $this->pengeluaranModel->save([
                'keterangan'     => 'Biaya maintenance: ' . $maintenance['deskripsi'],
                'kategori'       => 'maintenance',
                'jumlah'         => $biaya,
                'bulan'          => date('m'),
                'tahun'          => date('Y'),
                'pj_id'          => $pj['id'],
                'maintenance_id' => $id, // referensi ke laporan asal, dipakai untuk cegah edit/hapus manual
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal update status maintenance.');
        }

        return redirect()->to('/pj/maintenance')
            ->with('success', 'Maintenance selesai. Biaya berhasil dicatat.');
    }

    // Penyewa lapor kerusakan, foto bersifat opsional
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

        // Proses upload foto jika ada, validasi tipe dan ukuran file
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

            // Generate nama acak agar tidak bentrok dengan file lain
            $fotoName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/maintenance', $fotoName);
        }

        // Status awal selalu 'menunggu', menunggu admin assign ke PJ
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

    // Penyewa lihat semua laporan milik sendiri saja
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

    // PJ lihat semua laporan yang di-assign ke dia atau belum di-assign siapapun
    public function indexPj()
    {
        $userId = session()->get('user_id');
        $pj     = $this->pjModel->getPjByUserId($userId);

        if (!$pj) {
            return redirect()->to('/pj/dashboard')->with('error', 'Data tidak ditemukan.');
        }

        // Data ini biarkan saja untuk mengisi tabel di bawah card
        $data['maintenance'] = $this->maintenanceModel->getMaintenanceByPj($pj['id']);
        $data['pj']          = $pj;

        // AMBIL HITUNGAN LANGSUNG DARI DATABASE (Abaikan filter array view)
        $data['totalMenunggu'] = $this->maintenanceModel->where('status', 'menunggu')->countAllResults();
        $data['totalProses']   = $this->maintenanceModel->where('status', 'proses')->where('pj_id', $pj['id'])->countAllResults();
        $data['totalSelesai']  = $this->maintenanceModel->where('status', 'selesai')->where('pj_id', $pj['id'])->countAllResults();

        return view('pj/maintenance', $data);
    }

    // Detail laporan, bisa diakses admin dan PJ
    // View yang ditampilkan berbeda tergantung role yang sedang login
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

        return view($view, [
            'maintenance' => $maintenance,
            // Daftar PJ aktif dikirim untuk form assign di halaman detail admin
            'pj_list'     => $this->pjModel->where('is_active', 1)->findAll(),
        ]);
    }

    // PJ ambil sendiri laporan yang belum di-assign, tanpa perlu tunggu admin
    // Mencegah dobel ambil: cek dulu apakah pj_id sudah terisi
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

        if (!$pj) {
            return redirect()->back()->with('error', 'Data PJ tidak ditemukan.');
        }

        $this->maintenanceModel->update($id, [
            'pj_id'       => $pj['id'],
            'status'      => 'proses',
            'assigned_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/pj/maintenance')
            ->with('success', 'Laporan berhasil diambil, segera kerjakan.');
    }

    // Admin hapus laporan maintenance
    // File foto di storage ikut dihapus agar tidak ada file sampah
    public function delete($id)
    {
        $maintenance = $this->maintenanceModel->find($id);

        if (!$maintenance) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        if ($maintenance['foto']) {
            $fotoPath = FCPATH . 'uploads/maintenance/' . $maintenance['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath); // hapus file fisik dari server
            }
        }

        $this->maintenanceModel->delete($id);

        return redirect()->to('/admin/maintenance')
            ->with('success', 'Laporan maintenance berhasil dihapus.');
    }

    // Penyewa lihat detail satu laporan milik sendiri
    // Ada pengecekan penyewa_id agar penyewa tidak bisa akses laporan milik orang lain
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
            ->where('maintenance.penyewa_id', $penyewa['id']) // filter ketat: hanya milik sendiri
            ->first();

        if (!$maintenance) {
            return redirect()->to('/tenant/maintenance')->with('error', 'Laporan tidak ditemukan.');
        }

        return view('tenant/maintenance_detail', ['maintenance' => $maintenance]);
    }
}
