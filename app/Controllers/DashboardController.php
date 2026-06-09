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
        $kamarModel       = new KamarModel();
        $penyewaModel     = new PenyewaModel();
        $tagihanModel     = new TagihanModel();
        $pembayaranModel  = new PembayaranModel();
        $maintenanceModel = new MaintenanceModel();
        $pengeluaranModel = new \App\Models\PengeluaranModel();

        $bulan = date('m');
        $tahun = date('Y');

        // Data pemasukan 6 bulan terakhir untuk chart
        $pemasukanBulanan = [];
        $labelBulan = [];
        for ($i = 5; $i >= 0; $i--) {
            $b = date('m', strtotime("-$i months"));
            $y = date('Y', strtotime("-$i months"));
            $total = $pembayaranModel
                ->select('SUM(jumlah_bayar) as total')
                ->join('tagihan', 'tagihan.id = pembayaran.tagihan_id')
                ->where('pembayaran.status', 'approved')
                ->where('tagihan.bulan', $b)
                ->where('tagihan.tahun', $y)
                ->first()['total'] ?? 0;
            $pemasukanBulanan[] = (int) $total;
            $labelBulan[] = date('M Y', strtotime("-$i months"));
        }

        // Data pengeluaran 6 bulan terakhir untuk chart
        $pengeluaranBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $b = date('m', strtotime("-$i months"));
            $y = date('Y', strtotime("-$i months"));
            $total = $pengeluaranModel
                ->selectSum('jumlah', 'total')
                ->where('bulan', $b)
                ->where('tahun', $y)
                ->first()['total'] ?? 0;
            $pengeluaranBulanan[] = (int) $total;
        }

        // Penyewa yang paling sering menunggak
        $seringMenunggak = $tagihanModel
            ->select('users.name, kamar.nomor_kamar, COUNT(tagihan.id) as jumlah_tunggakan')
            ->join('penyewa', 'penyewa.id = tagihan.penyewa_id')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id')
            ->where('tagihan.status', 'menunggak')
            ->groupBy('tagihan.penyewa_id')
            ->orderBy('jumlah_tunggakan', 'DESC')
            ->findAll(5);

        // Status tagihan bulan ini untuk pie chart
        $statusTagihan = [
            'lunas'              => $tagihanModel->where('bulan', $bulan)->where('tahun', $tahun)->where('status', 'lunas')->countAllResults(),
            'pending'            => $tagihanModel->where('bulan', $bulan)->where('tahun', $tahun)->where('status', 'pending')->countAllResults(),
            'menunggu_konfirmasi' => $tagihanModel->where('bulan', $bulan)->where('tahun', $tahun)->where('status', 'menunggu_konfirmasi')->countAllResults(),
            'menunggak'          => $tagihanModel->where('bulan', $bulan)->where('tahun', $tahun)->where('status', 'menunggak')->countAllResults(),
        ];

        $data = [
            'total_kamar'         => $kamarModel->countAll(),
            'kamar_terisi'        => $kamarModel->where('status', 'terisi')->countAllResults(),
            'kamar_kosong'        => $kamarModel->where('status', 'kosong')->countAllResults(),
            'total_penyewa'       => $penyewaModel->where('tanggal_keluar', null)->countAllResults(),
            'tagihan_pending'     => $tagihanModel->where('status', 'menunggu_konfirmasi')->countAllResults(),
            'tagihan_menunggak'   => $tagihanModel->where('status', 'menunggak')->countAllResults(),
            'maintenance_pending' => $maintenanceModel->where('status', 'menunggu')->countAllResults(),
            'pemasukan_bulan_ini' => $pembayaranModel
                ->select('SUM(jumlah_bayar) as total')
                ->join('tagihan', 'tagihan.id = pembayaran.tagihan_id')
                ->where('pembayaran.status', 'approved')
                ->where('tagihan.bulan', $bulan)
                ->where('tagihan.tahun', $tahun)
                ->first()['total'] ?? 0,
            'pembayaran_pending'  => $pembayaranModel->getPembayaranPending(),
            'maintenance_terbaru' => $maintenanceModel->getMaintenanceLengkap(),

            // Data baru untuk chart
            'pemasukan_bulanan'   => $pemasukanBulanan,
            'pengeluaran_bulanan' => $pengeluaranBulanan,
            'label_bulan'         => $labelBulan,
            'sering_menunggak'    => $seringMenunggak,
            'status_tagihan'      => $statusTagihan,
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
