<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KamarModel;
use App\Models\MaintenanceModel;
use App\Models\PembayaranModel;
use App\Models\PenyewaModel;
use App\Models\TagihanModel;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    // Satu pintu masuk untuk semua role
    // Cek role dari session lalu arahkan ke dashboard yang sesuai
    public function index()
    {
        $role = session()->get('role');

        return match ($role) {
            'admin'   => $this->adminDashboard(),
            'pj'      => $this->pjDashboard(),
            'penyewa' => $this->penyewaDashboard(),
            default   => redirect()->to('/login'),
        };
    }

    // Dashboard admin: kumpulkan semua data ringkasan operasional kost
    private function adminDashboard()
    {
        $kamarModel      = new KamarModel();
        $penyewaModel    = new PenyewaModel();
        $tagihanModel    = new TagihanModel();
        $pembayaranModel = new PembayaranModel();
        $maintenanceModel = new MaintenanceModel();

        $bulan = date('m');
        $tahun = date('Y');

        $data = [
            // Statistik kamar
            'total_kamar'        => $kamarModel->countAll(),
            'kamar_terisi'       => $kamarModel->where('status', 'terisi')->countAllResults(),
            'kamar_kosong'       => $kamarModel->where('status', 'kosong')->countAllResults(),

            // Hanya hitung penyewa yang belum checkout (tanggal_keluar masih null)
            'total_penyewa'      => $penyewaModel->where('tanggal_keluar', null)->countAllResults(),

            // Tagihan yang butuh perhatian admin
            'tagihan_pending'    => $tagihanModel->where('status', 'menunggu_konfirmasi')->countAllResults(),
            'tagihan_menunggak'  => $tagihanModel->where('status', 'menunggak')->countAllResults(),

            // Laporan kerusakan yang belum ditangani
            'maintenance_pending' => $maintenanceModel->where('status', 'menunggu')->countAllResults(),

            // Total uang masuk bulan ini dari pembayaran yang sudah di-approve
            'pemasukan_bulan_ini' => $pembayaranModel
                ->select('SUM(jumlah_bayar) as total')
                ->join('tagihan', 'tagihan.id = pembayaran.tagihan_id')
                ->where('pembayaran.status', 'approved')
                ->where('tagihan.bulan', $bulan)
                ->where('tagihan.tahun', $tahun)
                ->first()['total'] ?? 0,

            // Daftar pembayaran yang menunggu konfirmasi admin
            'pembayaran_pending'  => $pembayaranModel->getPembayaranPending(),

            // Semua laporan maintenance untuk ditampilkan di tabel dashboard
            'maintenance_terbaru' => $maintenanceModel
                ->getMaintenanceLengkap(),
        ];

        return view('admin/dashboard', $data);
    }

    // Dashboard PJ: tampilkan ringkasan tugas dan riwayat gaji milik PJ yang login
    private function pjDashboard()
    {
        $pjModel          = new \App\Models\PenanggungJawabModel();
        $maintenanceModel = new \App\Models\MaintenanceModel();
        $pengeluaranModel = new \App\Models\PengeluaranModel();

        // Ambil data PJ berdasarkan user yang sedang login
        $userId = session()->get('user_id');
        $pj     = $pjModel->getPjByUserId($userId);

        if (!$pj) {
            return redirect()->to('/login')->with('error', 'Data tidak ditemukan.');
        }

        $data['pj']            = $pj;

        // Hitung statistik tugas khusus PJ yang login saja
        $data['total_tugas']   = $maintenanceModel->where('pj_id', $pj['id'])->countAllResults();
        $data['tugas_proses']  = $maintenanceModel->where('pj_id', $pj['id'])->where('status', 'proses')->countAllResults();
        $data['tugas_selesai'] = $maintenanceModel->where('pj_id', $pj['id'])->where('status', 'selesai')->countAllResults();

        // Riwayat gaji diambil dari tabel pengeluaran kategori 'gaji', terbaru di atas
        $data['riwayat_gaji']  = $pengeluaranModel
            ->where('pj_id', $pj['id'])
            ->where('kategori', 'gaji')
            ->orderBy('tahun', 'DESC')
            ->orderBy('bulan', 'DESC')
            ->findAll();

        return view('pj/dashboard', $data);
    }

    // Dashboard penyewa: tampilkan tagihan aktif dan status maintenance milik penyewa yang login
    private function penyewaDashboard()
    {
        $penyewaModel    = new \App\Models\PenyewaModel();
        $tagihanModel    = new \App\Models\TagihanModel();
        $maintenanceModel = new \App\Models\MaintenanceModel();

        // Ambil data penyewa berdasarkan user yang sedang login
        $userId  = session()->get('user_id');
        $penyewa = $penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->to('/login')->with('error', 'Data tidak ditemukan.');
        }

        $data['penyewa']         = $penyewa;

        // Tagihan yang masih perlu dibayar atau menunggu konfirmasi
        $data['tagihan_aktif']   = $tagihanModel
            ->where('penyewa_id', $penyewa['id'])
            ->whereIn('status', ['pending', 'menunggu_konfirmasi', 'menunggak'])
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Hitung berapa tagihan yang sudah lunas (untuk info di dashboard)
        $data['tagihan_lunas']   = $tagihanModel
            ->where('penyewa_id', $penyewa['id'])
            ->where('status', 'lunas')
            ->countAllResults();

        // Total semua laporan kerusakan yang pernah dibuat penyewa ini
        $data['total_maintenance'] = $maintenanceModel
            ->where('penyewa_id', $penyewa['id'])
            ->countAllResults();
            
        // Laporan yang masih dalam proses penanganan
        $data['maintenance_proses'] = $maintenanceModel
            ->where('penyewa_id', $penyewa['id'])
            ->whereIn('status', ['menunggu', 'proses'])
            ->countAllResults();

        return view('tenant/dashboard', $data);
    }
}
