<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PenanggungJawabModel;
use App\Models\PengeluaranModel;
use CodeIgniter\HTTP\ResponseInterface;

class PengeluaranController extends BaseController
{
    // Semua model dideklarasikan sebagai property agar bisa dipakai di semua method
    protected $pengeluaranModel;
    protected $pjModel;

    public function __construct()
    {
        $this->pengeluaranModel = new PengeluaranModel();
        $this->pjModel          = new PenanggungJawabModel();
    }

    // Tampilkan semua pengeluaran berdasarkan filter bulan & tahun
    // Default ke bulan & tahun sekarang kalau filter tidak dikirim
    public function index()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data['pengeluaran'] = $this->pengeluaranModel->getPengeluaranLengkap($bulan, $tahun);
        $data['total']       = $this->pengeluaranModel->getTotalPengeluaran($bulan, $tahun);
        $data['bulan']       = $bulan;
        $data['tahun']       = $tahun;
        $data['pj_list']     = $this->pjModel->where('is_active', 1)->findAll();
        $data['list_bulan']  = $this->getListBulan();

        return view('admin/pengeluaran/index', $data);
    }

    // Tambah pengeluaran manual oleh admin
    // maintenance_id diset null karena ini input manual, bukan dari proses maintenance
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
            'maintenance_id' => null, // null = input manual, bukan dari maintenance
        ]);

        return redirect()->to('/admin/pengeluaran')
            ->with('success', 'Pengeluaran berhasil dicatat.');
    }

    // Edit pengeluaran manual
    // Pengeluaran yang berasal dari maintenance atau kategori gaji tidak boleh diedit
    // karena sudah terhubung ke proses lain yang tidak boleh diubah sembarangan
    public function update($id)
    {
        $pengeluaran = $this->pengeluaranModel->find($id);

        if (!$pengeluaran) {
            return redirect()->back()->with('error', 'Data pengeluaran tidak ditemukan.');
        }

        // Blokir edit kalau pengeluaran ini otomatis dari maintenance atau kategori gaji
        if ($pengeluaran['maintenance_id'] !== null || $pengeluaran['kategori'] === 'gaji') {
            return redirect()->back()
                ->with('error', 'Pengeluaran ini tidak bisa diedit.');
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

    // Hapus pengeluaran manual
    // Pengeluaran dari maintenance atau kategori gaji tidak boleh dihapus
    // agar data keuangan tetap akurat dan tidak bisa dimanipulasi
    public function delete($id)
    {
        $pengeluaran = $this->pengeluaranModel->find($id);

        if (!$pengeluaran) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Blokir hapus kalau pengeluaran ini otomatis dari maintenance atau kategori gaji
        if ($pengeluaran['maintenance_id'] !== null || $pengeluaran['kategori'] === 'gaji') {
            return redirect()->back()
                ->with('error', 'Pengeluaran ini tidak bisa dihapus.');
        }

        $this->pengeluaranModel->delete($id);

        return redirect()->to('/admin/pengeluaran')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }

    // Rekap pengeluaran per kategori untuk bulan & tahun tertentu
    // Total per kategori dihitung terpisah lalu dijumlahkan untuk total keseluruhan
    public function rekap()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Hitung total per kategori menggunakan selectSum
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

        // Total semua kategori dijumlahkan di PHP, bukan di query
        // agar masing-masing nilai per kategori tetap tersedia untuk ditampilkan terpisah
        $data['total_semua'] = $data['total_maintenance'] + $data['total_gaji'] + $data['total_lainnya'];
        $data['pengeluaran'] = $this->pengeluaranModel->getPengeluaranLengkap($bulan, $tahun);
        $data['bulan']       = $bulan;
        $data['tahun']       = $tahun;
        $data['list_bulan']  = $this->getListBulan();

        return view('admin/pengeluaran/rekap', $data);
    }

    // Helper private: mapping nomor bulan ke nama bulan Bahasa Indonesia
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
