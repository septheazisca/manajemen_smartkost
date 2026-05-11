<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PenanggungJawabModel;
use App\Models\PengeluaranModel;
use CodeIgniter\HTTP\ResponseInterface;

class PengeluaranController extends BaseController
{
    protected $pengeluaranModel;
    protected $pjModel;

    public function __construct()
    {
        $this->pengeluaranModel = new PengeluaranModel();
        $this->pjModel          = new PenanggungJawabModel();
    }

    // =====================
    // INDEX - Admin lihat semua pengeluaran
    // =====================
    public function index()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data['pengeluaran']  = $this->pengeluaranModel->getPengeluaranLengkap($bulan, $tahun);
        $data['total']        = $this->pengeluaranModel->getTotalPengeluaran($bulan, $tahun);
        $data['bulan']        = $bulan;
        $data['tahun']        = $tahun;
        $data['pj_list']      = $this->pjModel->where('is_active', 1)->findAll();
        $data['list_bulan']   = $this->getListBulan();

        return view('admin/pengeluaran/index', $data);
    }

    // =====================
    // STORE - Admin tambah pengeluaran manual
    // =====================
    public function store()
    {
        $rules = [
            'keterangan' => 'required|min_length[3]',
            'kategori'   => 'required|in_list[maintenance,gaji,lainnya]',
            'jumlah'     => 'required|numeric|greater_than[0]',
            'bulan'      => 'required',
            'tahun'      => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
    

        $this->pengeluaranModel->save([
            'keterangan'     => $this->request->getPost('keterangan'),
            'kategori'       => $this->request->getPost('kategori'),
            'jumlah'         => $this->request->getPost('jumlah'),
            'bulan'          => $this->request->getPost('bulan'),
            'tahun'          => $this->request->getPost('tahun'),
            'pj_id'          => $this->request->getPost('pj_id') ?: null,
            'maintenance_id' => null,
        ]);

        return redirect()->to('/admin/pengeluaran')
            ->with('success', 'Pengeluaran berhasil dicatat.');
    }

    // =====================
    // UPDATE - Admin edit pengeluaran manual
    // =====================
    public function update($id)
    {
        $pengeluaran = $this->pengeluaranModel->find($id);

        if (!$pengeluaran) {
            return redirect()->back()->with('error', 'Data pengeluaran tidak ditemukan.');
        }

        // pengeluaran yang otomatis dari maintenance/gaji tidak boleh diedit manual
        if ($pengeluaran['maintenance_id'] !== null) {
            return redirect()->back()
                ->with('error', 'Pengeluaran dari maintenance tidak bisa diedit manual.');
        }

        $rules = [
            'keterangan' => 'required|min_length[3]',
            'kategori'   => 'required|in_list[maintenance,gaji,lainnya]',
            'jumlah'     => 'required|numeric|greater_than[0]',
            'bulan'      => 'required',
            'tahun'      => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->pengeluaranModel->update($id, [
            'keterangan' => $this->request->getPost('keterangan'),
            'kategori'   => $this->request->getPost('kategori'),
            'jumlah'     => $this->request->getPost('jumlah'),
            'bulan'      => $this->request->getPost('bulan'),
            'tahun'      => $this->request->getPost('tahun'),
            'pj_id'      => $this->request->getPost('pj_id') ?: null,
        ]);

        return redirect()->to('/admin/pengeluaran')
            ->with('success', 'Pengeluaran berhasil diupdate.');
    }

    // =====================
    // DELETE - Admin hapus pengeluaran manual
    // =====================
    public function delete($id)
    {
        $pengeluaran = $this->pengeluaranModel->find($id);

        if (!$pengeluaran) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // pengeluaran otomatis dari maintenance tidak boleh dihapus manual
        if ($pengeluaran['maintenance_id'] !== null) {
            return redirect()->back()
                ->with('error', 'Pengeluaran dari maintenance tidak bisa dihapus manual.');
        }

        $this->pengeluaranModel->delete($id);

        return redirect()->to('/admin/pengeluaran')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }

    // =====================
    // REKAP - Rekap pengeluaran per kategori
    // =====================
    public function rekap()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data['total_maintenance'] = $this->pengeluaranModel
            ->where('kategori', 'maintenance')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->selectSum('jumlah', 'total')
            ->first()['total'] ?? 0;

        $data['total_gaji'] = $this->pengeluaranModel
            ->where('kategori', 'gaji')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->selectSum('jumlah', 'total')
            ->first()['total'] ?? 0;

        $data['total_lainnya'] = $this->pengeluaranModel
            ->where('kategori', 'lainnya')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->selectSum('jumlah', 'total')
            ->first()['total'] ?? 0;

        $data['total_semua']  = $data['total_maintenance'] + $data['total_gaji'] + $data['total_lainnya'];
        $data['pengeluaran']  = $this->pengeluaranModel->getPengeluaranLengkap($bulan, $tahun);
        $data['bulan']        = $bulan;
        $data['tahun']        = $tahun;
        $data['list_bulan']   = $this->getListBulan();

        return view('admin/pengeluaran/rekap', $data);
    }

    // =====================
    // HELPER - List nama bulan
    // =====================
    private function getListBulan()
    {
        return [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
    }
}
