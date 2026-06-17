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
    protected $userModel;
    protected $fonnteService;

    public function __construct()
    {
        $this->maintenanceModel = new MaintenanceModel();
        $this->penyewaModel     = new PenyewaModel();
        $this->pjModel          = new PenanggungJawabModel();
        $this->pengeluaranModel = new PengeluaranModel();
        $this->kamarModel       = new KamarModel();
        $this->userModel        = new \App\Models\UserModel();
        $this->fonnteService    = new \App\Libraries\FonnteService();
    }

    // Helper untuk mengambil detail maintenance buat keperluan notifikasi
    private function getMaintenanceDetailForNotif($id)
    {
        return $this->maintenanceModel
            ->select('maintenance.*, penyewa.user_id as penyewa_user_id, users.name as nama_penyewa, users.phone as phone_penyewa, kamar.nomor_kamar')
            ->join('penyewa', 'penyewa.id = maintenance.penyewa_id', 'left')
            ->join('users', 'users.id = penyewa.user_id', 'left')
            ->join('kamar', 'kamar.id = maintenance.kamar_id', 'left')
            ->where('maintenance.id', $id)
            ->first();
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
            'pj_id'                 => $pjId,
            'status_maintenance_id' => 2, // 2 is proses
            'assigned_at'           => date('Y-m-d H:i:s'),
        ]);

        // Notifikasi ke PJ yang ditugaskan dan Penyewa
        $pj = $this->pjModel->select('penanggung_jawab.*, users.id as user_id')->join('users', 'users.id = penanggung_jawab.user_id', 'left')->where('penanggung_jawab.id', $pjId)->first();
        $detail = $this->getMaintenanceDetailForNotif($id);

        if ($pj && !empty($pj['phone']) && $detail) {
            $pesanPj = "🛠️ *Tugas Maintenance Baru*\n\n";
            $pesanPj .= "Admin telah menugaskan kamu untuk laporan kerusakan berikut:\n";
            $pesanPj .= "Kamar: {$detail['nomor_kamar']}\n";
            $pesanPj .= "Keluhan: {$detail['deskripsi']}\n\n";
            $pesanPj .= "Harap segera dikerjakan dan update status di aplikasi SmartKost jika sudah selesai.";

            $this->fonnteService->sendAndLog($pj['user_id'], $pj['phone'], $pesanPj, 'maintenance');
        }

        if ($detail && !empty($detail['phone_penyewa'])) {
            $pjNama = $pj['nama'] ?? 'Penanggung Jawab';
            $pesanPenyewa = "Halo *{$detail['nama_penyewa']}*,\n\n";
            $pesanPenyewa .= "Laporan kerusakan kamu untuk Kamar *{$detail['nomor_kamar']}* sedang ditugaskan ke teknisi (*{$pjNama}*) dan akan segera dikerjakan.\n\n";
            $pesanPenyewa .= "Terima kasih atas kesabarannya 🙏";
            $this->fonnteService->sendAndLog($detail['penyewa_user_id'], $detail['phone_penyewa'], $pesanPenyewa, 'maintenance');
        }

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
        $catatanPj = $this->request->getPost('catatan_pj');

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Update status maintenance jadi selesai
        $this->maintenanceModel->update($id, [
            'status_maintenance_id' => 3, // 3 is selesai
            'catatan_pj'            => $catatanPj,
            'biaya'                 => $biaya,
            'selesai_at'            => date('Y-m-d H:i:s'),
        ]);

        // 2. Kalau ada biaya, otomatis catat ke tabel pengeluaran kategori 'maintenance'
        // Ini yang membuat pengeluaran maintenance tidak perlu diinput manual oleh admin
        if ($biaya > 0) {
            $this->pengeluaranModel->save([
                'keterangan'              => 'Biaya maintenance: ' . $maintenance['deskripsi'],
                'kategori_pengeluaran_id' => 1, // 1 is maintenance
                'jumlah'                  => $biaya,
                'bulan'                   => date('m'),
                'tahun'                   => date('Y'),
                'pj_id'                   => $pj['id'],
                'maintenance_id'          => $id, // referensi ke laporan asal, dipakai untuk cegah edit/hapus manual
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal update status maintenance.');
        }

        // Notifikasi ke Admin & Penyewa
        $detail = $this->getMaintenanceDetailForNotif($id);
        $pjNama = $pj['nama'] ?? 'Penanggung Jawab';

        $admins = $this->userModel->where('role', 'admin')->where('is_active', 1)->findAll();
        $pesanAdmin = "✅ *Perbaikan Selesai*\n\n";
        $pesanAdmin .= "Laporan kerusakan Kamar *{$detail['nomor_kamar']}* telah diselesaikan oleh *{$pjNama}*.\n";
        $pesanAdmin .= "Catatan PJ: {$catatanPj}\n";
        $pesanAdmin .= "Biaya: Rp " . number_format($biaya, 0, ',', '.');
        
        foreach ($admins as $admin) {
            if (!empty($admin['phone'])) {
                $this->fonnteService->sendAndLog($admin['id'], $admin['phone'], $pesanAdmin, 'maintenance');
            }
        }

        if ($detail && !empty($detail['phone_penyewa'])) {
            $pesanPenyewa = "Halo *{$detail['nama_penyewa']}*,\n\n";
            $pesanPenyewa .= "Kabar gembira! Laporan kerusakan kamu untuk Kamar *{$detail['nomor_kamar']}* telah selesai diperbaiki oleh tim kami (*{$pjNama}*).\n\n";
            $pesanPenyewa .= "Catatan Perbaikan: {$catatanPj}\n\n";
            $pesanPenyewa .= "Terima kasih atas kesabarannya 🙏";
            $this->fonnteService->sendAndLog($detail['penyewa_user_id'], $detail['phone_penyewa'], $pesanPenyewa, 'maintenance');
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

        $deskripsi = $this->request->getPost('deskripsi');

        // Status awal selalu 'menunggu', menunggu admin assign ke PJ
        $this->maintenanceModel->save([
            'penyewa_id'            => $penyewa['id'],
            'kamar_id'              => $penyewa['kamar_id'],
            'deskripsi'             => $deskripsi,
            'foto'                  => $fotoName,
            'status_maintenance_id' => 1, // 1 is menunggu
        ]);

        // Notifikasi ke Admin & PJ
        $admins = $this->userModel->where('role', 'admin')->where('is_active', 1)->findAll();
        $pjs = $this->pjModel->select('penanggung_jawab.id, penanggung_jawab.phone, users.id as user_id')
                    ->join('users', 'users.id = penanggung_jawab.user_id', 'left')
                    ->where('penanggung_jawab.is_active', 1)->findAll();
        
        $kamar = $this->kamarModel->find($penyewa['kamar_id']);
        $nomorKamar = $kamar ? $kamar['nomor_kamar'] : '-';

        $pesanAdminPj = "🚨 *Laporan Kerusakan Baru*\n\n";
        $pesanAdminPj .= "Penyewa: {$penyewa['nama']}\n";
        $pesanAdminPj .= "Kamar: {$nomorKamar}\n";
        $pesanAdminPj .= "Keluhan: {$deskripsi}\n\n";
        $pesanAdminPj .= "Mohon segera ditindaklanjuti. Cek aplikasi SmartKost untuk detailnya.";

        foreach ($admins as $admin) {
            if (!empty($admin['phone'])) {
                $this->fonnteService->sendAndLog($admin['id'], $admin['phone'], $pesanAdminPj, 'maintenance');
            }
        }
        foreach ($pjs as $pjData) {
            if (!empty($pjData['phone'])) {
                $this->fonnteService->sendAndLog($pjData['user_id'], $pjData['phone'], $pesanAdminPj, 'maintenance');
            }
        }

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
            ->select('maintenance.*, status_maintenance.nama_status as status, status_maintenance.badge_class, status_maintenance.icon, kamar.nomor_kamar')
            ->join('status_maintenance', 'status_maintenance.id = maintenance.status_maintenance_id')
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
        $data['totalMenunggu'] = $this->maintenanceModel->where('status_maintenance_id', 1)->countAllResults(); // 1 is menunggu
        $data['totalProses']   = $this->maintenanceModel->where('status_maintenance_id', 2)->where('pj_id', $pj['id'])->countAllResults(); // 2 is proses
        $data['totalSelesai']  = $this->maintenanceModel->where('status_maintenance_id', 3)->where('pj_id', $pj['id'])->countAllResults(); // 3 is selesai

        return view('pj/maintenance', $data);
    }

    // Detail laporan, bisa diakses admin dan PJ
    // View yang ditampilkan berbeda tergantung role yang sedang login
    public function detail($id)
    {
        $maintenance = $this->maintenanceModel
            ->select('
                maintenance.*,
                status_maintenance.nama_status AS status,
                status_maintenance.badge_class,
                status_maintenance.icon,
                users.name as nama_penyewa,
                users.phone as phone_penyewa,
                kamar.nomor_kamar,
                penanggung_jawab.nama as nama_pj,
                penanggung_jawab.phone as phone_pj
            ')
            ->join('status_maintenance', 'status_maintenance.id = maintenance.status_maintenance_id')
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
            'pj_id'                 => $pj['id'],
            'status_maintenance_id' => 2, // 2 is proses
            'assigned_at'           => date('Y-m-d H:i:s'),
        ]);

        // Notifikasi ke Admin & Penyewa
        $detail = $this->getMaintenanceDetailForNotif($id);
        $pjNama = $pj['nama'] ?? 'Penanggung Jawab';

        $admins = $this->userModel->where('role', 'admin')->where('is_active', 1)->findAll();
        $pesanAdmin = "🔧 *Laporan Diambil PJ*\n\n";
        $pesanAdmin .= "Laporan kerusakan Kamar *{$detail['nomor_kamar']}* saat ini sedang ditangani oleh *{$pjNama}*.";
        
        foreach ($admins as $admin) {
            if (!empty($admin['phone'])) {
                $this->fonnteService->sendAndLog($admin['id'], $admin['phone'], $pesanAdmin, 'maintenance');
            }
        }

        if ($detail && !empty($detail['phone_penyewa'])) {
            $pesanPenyewa = "Halo *{$detail['nama_penyewa']}*,\n\n";
            $pesanPenyewa .= "Laporan kerusakan kamu untuk Kamar *{$detail['nomor_kamar']}* saat ini sedang dikerjakan oleh teknisi kami (*{$pjNama}*).\n\n";
            $pesanPenyewa .= "Mohon ditunggu sampai proses perbaikan selesai. Terima kasih 🙏";
            $this->fonnteService->sendAndLog($detail['penyewa_user_id'], $detail['phone_penyewa'], $pesanPenyewa, 'maintenance');
        }

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
            ->select('
                maintenance.*,
                status_maintenance.nama_status AS status,
                status_maintenance.badge_class,
                status_maintenance.icon,
                kamar.nomor_kamar,
                penanggung_jawab.nama as nama_pj
            ')
            ->join('status_maintenance', 'status_maintenance.id = maintenance.status_maintenance_id')
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
