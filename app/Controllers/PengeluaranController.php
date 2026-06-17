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

        $db = \Config\Database::connect();
        $data['pengeluaran']   = $this->pengeluaranModel->getPengeluaranLengkap($bulan, $tahun);
        $data['total']         = $this->pengeluaranModel->getTotalPengeluaran($bulan, $tahun);
        $data['bulan']         = $bulan;
        $data['tahun']         = $tahun;
        $data['pj_list']       = $this->pjModel->where('is_active', 1)->findAll();
        $data['list_bulan']    = $this->getListBulan();
        $data['kategori_list'] = $db->table('kategori_pengeluaran')->get()->getResultArray();

        return view('admin/pengeluaran/index', $data);
    }

    // Tambah pengeluaran manual oleh admin
    // maintenance_id diset null karena ini input manual, bukan dari proses maintenance
    public function store()
    {
        $rules = [
            'keterangan'              => 'required|min_length[3]',
            'kategori_pengeluaran_id' => 'required|numeric',
            'jumlah'                  => 'required|numeric|greater_than[0]',
            'bulan'                   => 'required',
            'tahun'                   => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->pengeluaranModel->save([
            'keterangan'              => $this->request->getPost('keterangan'),
            'kategori_pengeluaran_id' => $this->request->getPost('kategori_pengeluaran_id'),
            'jumlah'                  => $this->request->getPost('jumlah'),
            'bulan'                   => $this->request->getPost('bulan'),
            'tahun'                   => $this->request->getPost('tahun'),
            'pj_id'                   => $this->request->getPost('pj_id') ?: null,
            'maintenance_id'          => null, // null = input manual, bukan dari maintenance
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
        if ($pengeluaran['maintenance_id'] !== null || $pengeluaran['kategori_pengeluaran_id'] == 2) {
            return redirect()->back()
                ->with('error', 'Pengeluaran ini tidak bisa diedit.');
        }

        $rules = [
            'keterangan'              => 'required|min_length[3]',
            'kategori_pengeluaran_id' => 'required|numeric',
            'jumlah'                  => 'required|numeric|greater_than[0]',
            'bulan'                   => 'required',
            'tahun'                   => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->pengeluaranModel->update($id, [
            'keterangan'              => $this->request->getPost('keterangan'),
            'kategori_pengeluaran_id' => $this->request->getPost('kategori_pengeluaran_id'),
            'jumlah'                  => $this->request->getPost('jumlah'),
            'bulan'                   => $this->request->getPost('bulan'),
            'tahun'                   => $this->request->getPost('tahun'),
            'pj_id'                   => $this->request->getPost('pj_id') ?: null,
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
        if ($pengeluaran['maintenance_id'] !== null || $pengeluaran['kategori_pengeluaran_id'] == 2) {
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
            ->where('kategori_pengeluaran_id', 1)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->selectSum('jumlah', 'total')
            ->first()['total'] ?? 0;

        $data['total_gaji'] = $this->pengeluaranModel
            ->where('kategori_pengeluaran_id', 2)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->selectSum('jumlah', 'total')
            ->first()['total'] ?? 0;

        $data['total_lainnya'] = $this->pengeluaranModel
            ->where('kategori_pengeluaran_id', 3)
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



    public function exportExcel()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $pengeluaran = $this->pengeluaranModel->getPengeluaranLengkap($bulan, $tahun);
        $namaBulan   = $this->getListBulan()[$bulan] ?? $bulan;

        // HITUNG RINGKASAN
        $total_maintenance = $this->pengeluaranModel
            ->where('kategori_pengeluaran_id', 1)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->selectSum('jumlah', 'total')
            ->first()['total'] ?? 0;

        $total_gaji = $this->pengeluaranModel
            ->where('kategori_pengeluaran_id', 2)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->selectSum('jumlah', 'total')
            ->first()['total'] ?? 0;

        $total_lainnya = $this->pengeluaranModel
            ->where('kategori_pengeluaran_id', 3)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->selectSum('jumlah', 'total')
            ->first()['total'] ?? 0;

        $total_semua = (int)$total_maintenance + (int)$total_gaji + (int)$total_lainnya;
        $jumlah_transaksi = count($pengeluaran);

        // EXCEL
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pengeluaran');

        // JUDUL
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'LAPORAN REKAP PENGELUARAN SMARTKOST');

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'Periode : ' . $namaBulan . ' ' . $tahun);

        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue('A3', 'Dicetak pada : ' . date('d F Y H:i'));

        $sheet->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(16);

        $sheet->getStyle('A2:A3')->getFont()
            ->setItalic(true);

        $sheet->getStyle('A1:G3')->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );

        // RINGKASAN
        $sheet->setCellValue('A5', 'Total Pengeluaran');
        $sheet->setCellValue('B5', $total_semua);

        $sheet->setCellValue('A6', 'Total Maintenance');
        $sheet->setCellValue('B6', (int)$total_maintenance);

        $sheet->setCellValue('A7', 'Total Gaji');
        $sheet->setCellValue('B7', (int)$total_gaji);

        $sheet->setCellValue('D5', 'Total Lainnya');
        $sheet->setCellValue('E5', (int)$total_lainnya);

        $sheet->setCellValue('D6', 'Jumlah Transaksi');
        $sheet->setCellValue('E6', $jumlah_transaksi . ' Transaksi');

        // Format Rupiah Ringkasan
        foreach (['B5', 'B6', 'B7', 'E5'] as $cell) {
            $sheet->getStyle($cell)
                ->getNumberFormat()
                ->setFormatCode('#,##0');
        }

        $sheet->getStyle('A5:E7')->getFont()->setBold(true);

        // Border Ringkasan
        $sheet->getStyle('A5:E7')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        // HEADER TABEL
        $headers = [
            'No',
            'Keterangan',
            'Kategori',
            'PJ Terkait',
            'Jumlah',
            'Sumber',
            'Tanggal'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '9', $header);
            $col++;
        }

        $sheet->getStyle('A9:G9')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '7F77DD']
            ]
        ]);

        // DATA TABEL
        $row = 10;
        $no = 1;

        foreach ($pengeluaran as $p) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $p['keterangan']);
            $sheet->setCellValue('C' . $row, ucfirst($p['kategori']));
            $sheet->setCellValue('D' . $row, $p['nama_pj'] ?? '-');
            $sheet->setCellValue('E' . $row, (int)$p['jumlah']);
            $sheet->setCellValue('F' . $row, $p['maintenance_id'] ? 'Otomatis' : 'Manual');
            $sheet->setCellValue('G' . $row, date('d/m/Y', strtotime($p['created_at'])));

            // Format Rupiah
            $sheet->getStyle('E' . $row)
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            // Zebra Table
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)
                    ->getFill()
                    ->setFillType(
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
                    )
                    ->getStartColor()
                    ->setRGB('F8F9FA');
            }

            $row++;
        }

        // TOTAL AKHIR
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL PENGELUARAN');
        $sheet->setCellValue('E' . $row, $total_semua);

        $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9ECEF']
            ]
        ]);

        $sheet->getStyle('E' . $row)
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // BORDER TABEL
        $sheet->getStyle('A9:G' . $row)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        // AUTO SIZE
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)
                ->setAutoSize(true);
        }

        // Freeze Header
        $sheet->freezePane('A10');

        // DOWNLOAD
        $filename = 'laporan-pengeluaran-' .
            strtolower($namaBulan) .
            '-' .
            $tahun .
            '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
