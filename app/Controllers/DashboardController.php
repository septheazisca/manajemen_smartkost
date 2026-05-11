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
        return view('pj/dashboard');
    }

    private function penyewaDashboard()
    {
        return view('tenant/dashboard');
    }
}
