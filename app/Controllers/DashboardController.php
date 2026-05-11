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
            'total_kamar'        => $kamarModel->countAll(),
            'kamar_terisi'       => $kamarModel->where('status', 'terisi')->countAllResults(),
            'kamar_kosong'       => $kamarModel->where('status', 'kosong')->countAllResults(),
            'total_penyewa'      => $penyewaModel->where('tanggal_keluar', null)->countAllResults(),
            'tagihan_pending'    => $tagihanModel->where('status', 'menunggu_konfirmasi')->countAllResults(),
            'tagihan_menunggak'  => $tagihanModel->where('status', 'menunggak')->countAllResults(),
            'maintenance_pending' => $maintenanceModel->where('status', 'menunggu')->countAllResults(),

            // pemasukan bulan ini
            'pemasukan_bulan_ini' => $pembayaranModel
                ->select('SUM(jumlah_bayar) as total')
                ->join('tagihan', 'tagihan.id = pembayaran.tagihan_id')
                ->where('pembayaran.status', 'approved')
                ->where('tagihan.bulan', $bulan)
                ->where('tagihan.tahun', $tahun)
                ->first()['total'] ?? 0,

            // tabel pembayaran pending
            'pembayaran_pending'  => $pembayaranModel->getPembayaranPending(),

            // maintenance terbaru (5 data)
            'maintenance_terbaru' => $maintenanceModel
                ->getMaintenanceLengkap(),
        ];

        return view('admin/dashboard', $data);
    }

    private function pjDashboard()
    {
        $pjModel          = new \App\Models\PenanggungJawabModel();
        $maintenanceModel = new \App\Models\MaintenanceModel();
        $pengeluaranModel = new \App\Models\PengeluaranModel();

        $userId = session()->get('user_id');
        $pj     = $pjModel->getPjByUserId($userId);

        if (!$pj) {
            return redirect()->to('/login')->with('error', 'Data tidak ditemukan.');
        }

        $data['pj']            = $pj;
        $data['total_tugas']   = $maintenanceModel->where('pj_id', $pj['id'])->countAllResults();
        $data['tugas_proses']  = $maintenanceModel->where('pj_id', $pj['id'])->where('status', 'proses')->countAllResults();
        $data['tugas_selesai'] = $maintenanceModel->where('pj_id', $pj['id'])->where('status', 'selesai')->countAllResults();
        $data['riwayat_gaji']  = $pengeluaranModel
            ->where('pj_id', $pj['id'])
            ->where('kategori', 'gaji')
            ->orderBy('tahun', 'DESC')
            ->orderBy('bulan', 'DESC')
            ->findAll();

        return view('pj/dashboard', $data);
    }

    private function penyewaDashboard()
    {
        $penyewaModel    = new \App\Models\PenyewaModel();
        $tagihanModel    = new \App\Models\TagihanModel();
        $maintenanceModel = new \App\Models\MaintenanceModel();

        $userId  = session()->get('user_id');
        $penyewa = $penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->to('/login')->with('error', 'Data tidak ditemukan.');
        }

        $data['penyewa']         = $penyewa;
        $data['tagihan_aktif']   = $tagihanModel
            ->where('penyewa_id', $penyewa['id'])
            ->whereIn('status', ['pending', 'menunggu_konfirmasi', 'menunggak'])
            ->orderBy('created_at', 'DESC')
            ->findAll();
        $data['tagihan_lunas']   = $tagihanModel
            ->where('penyewa_id', $penyewa['id'])
            ->where('status', 'lunas')
            ->countAllResults();
        $data['total_maintenance'] = $maintenanceModel
            ->where('penyewa_id', $penyewa['id'])
            ->countAllResults();
        $data['maintenance_proses'] = $maintenanceModel
            ->where('penyewa_id', $penyewa['id'])
            ->whereIn('status', ['menunggu', 'proses'])
            ->countAllResults();

        return view('tenant/dashboard', $data);
    }
}
