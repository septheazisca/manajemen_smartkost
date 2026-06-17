<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MaintenanceModel;
use App\Models\PembayaranModel;
use App\Models\PengeluaranModel;
use App\Models\PenyewaModel;
use App\Models\TagihanModel;
use CodeIgniter\HTTP\ResponseInterface;

class LaporanController extends BaseController
{
    // Semua model yang dibutuhkan dideklarasikan sebagai property
    // agar bisa dipakai di semua method tanpa inisialisasi ulang
    protected $tagihanModel;
    protected $pembayaranModel;
    protected $pengeluaranModel;
    protected $penyewaModel;
    protected $maintenanceModel;

    public function __construct()
    {
        $this->tagihanModel     = new TagihanModel();
        $this->pembayaranModel  = new PembayaranModel();
        $this->pengeluaranModel = new PengeluaranModel();
        $this->penyewaModel     = new PenyewaModel();
        $this->maintenanceModel = new MaintenanceModel();
    }

    // Halaman utama laporan keuangan
    // Ambil bulan & tahun dari filter, default ke bulan & tahun sekarang
    public function index()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // getRingkasan() dipanggil sekali, hasilnya digabung dengan data lain
        // menggunakan array_merge agar tidak perlu assign satu per satu
        $data = array_merge($this->getRingkasan($bulan, $tahun), [
            'bulan'      => $bulan,
            'tahun'      => $tahun,
            'list_bulan' => $this->getListBulan(),
        ]);

        return view('admin/laporan/index', $data);
    }

    // Export laporan keuangan ke file PDF menggunakan library DomPDF
    public function exportPdf()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data = array_merge($this->getRingkasan($bulan, $tahun), [
            'bulan'      => $bulan,
            'tahun'      => $tahun,
            'list_bulan' => $this->getListBulan(),
            // Tambahkan waktu generate untuk ditampilkan di footer PDF
            'generated'  => date('d/m/Y H:i:s'),
        ]);

        // Inisialisasi DomPDF dengan opsi dasar
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->setOptions(new \Dompdf\Options([
            'defaultFont'          => 'sans-serif',
            'isRemoteEnabled'      => false, // nonaktifkan akses URL eksternal di PDF
            'isHtml5ParserEnabled' => true,
        ]));

        // Render view PHP menjadi string HTML, lalu load ke DomPDF
        $html = view('admin/laporan/pdf_template', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Stream file langsung ke browser sebagai download
        $filename = 'laporan-keuangan-' . $bulan . '-' . $tahun . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    // Export laporan keuangan ke file Excel menggunakan library PhpSpreadsheet
    public function exportExcel()
    {
        $bulan     = $this->request->getGet('bulan') ?? date('m');
        $tahun     = $this->request->getGet('tahun') ?? date('Y');
        $ringkasan = $this->getRingkasan($bulan, $tahun);
        $namaBulan = $this->getListBulan()[$bulan] ?? $bulan;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // =====================
        // SHEET 1 - Ringkasan keuangan bulan ini
        // =====================
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Ringkasan');

        // JUDUL
        $sheet1->mergeCells('A1:B1');
        $sheet1->setCellValue('A1', 'LAPORAN REKAP KEUANGAN SMARTKOST');

        $sheet1->mergeCells('A2:B2');
        $sheet1->setCellValue('A2', 'Periode : ' . $namaBulan . ' ' . $tahun);

        $sheet1->mergeCells('A3:B3');
        $sheet1->setCellValue('A3', 'Dicetak pada : ' . date('d F Y H:i'));

        $sheet1->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(14);

        $sheet1->getStyle('A2:A3')->getFont()
            ->setItalic(true);

        $sheet1->getStyle('A1:B3')->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );

        // SECTION PEMASUKAN
        $sheet1->mergeCells('A5:B5');
        $sheet1->setCellValue('A5', 'PEMASUKAN');
        $sheet1->getStyle('A5:B5')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '7F77DD']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);

        $sheet1->setCellValue('A6', 'Total Tagihan Lunas');
        $sheet1->setCellValue('B6', $ringkasan['total_pemasukan']);
        $sheet1->getStyle('A6:B6')->getFont()->setBold(true);

        // BORDER PEMASUKAN
        $sheet1->getStyle('A5:B6')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        // SECTION PENGELUARAN
        $sheet1->mergeCells('A8:B8');
        $sheet1->setCellValue('A8', 'PENGELUARAN');
        $sheet1->getStyle('A8:B8')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '7F77DD']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);

        $sheet1->setCellValue('A9', 'Biaya Maintenance');
        $sheet1->setCellValue('B9', $ringkasan['total_maintenance']);

        $sheet1->setCellValue('A10', 'Gaji Penanggung Jawab');
        $sheet1->setCellValue('B10', $ringkasan['total_gaji']);

        $sheet1->setCellValue('A11', 'Lainnya');
        $sheet1->setCellValue('B11', $ringkasan['total_lainnya']);

        $sheet1->setCellValue('A12', 'Total Pengeluaran');
        $sheet1->setCellValue('B12', $ringkasan['total_pengeluaran']);

        $sheet1->getStyle('A12:B12')->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9ECEF']
            ]
        ]);

        // BORDER PENGELUARAN
        $sheet1->getStyle('A8:B12')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        // SECTION SALDO BERSIH
        $sheet1->setCellValue('A14', 'SALDO BERSIH');
        $sheet1->setCellValue('B14', $ringkasan['saldo_bersih']);

        $sheet1->getStyle('A14:B14')->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9ECEF']
            ]
        ]);

        if ($ringkasan['saldo_bersih'] >= 0) {
            $sheet1->getStyle('B14')
                ->getFont()
                ->getColor()
                ->setRGB('198754');
        } else {
            $sheet1->getStyle('B14')
                ->getFont()
                ->getColor()
                ->setRGB('DC3545');
        }

        $sheet1->getStyle('A14:B14')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        // Format Rupiah Sheet 1
        $rupiahFormat = '#,##0';
        foreach (['B6', 'B9', 'B10', 'B11', 'B12', 'B14'] as $cell) {
            $sheet1->getStyle($cell)
                ->getNumberFormat()
                ->setFormatCode($rupiahFormat);
        }

        $sheet1->getColumnDimension('A')->setWidth(30);
        $sheet1->getColumnDimension('B')->setWidth(25);


        // =====================
        // SHEET 2 - Detail setiap pembayaran yang sudah approved
        // =====================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Pemasukan');

        // JUDUL
        $sheet2->mergeCells('A1:F1');
        $sheet2->setCellValue('A1', 'LAPORAN DETAIL PEMASUKAN KAMAR KOST');

        $sheet2->mergeCells('A2:F2');
        $sheet2->setCellValue('A2', 'Periode : ' . $namaBulan . ' ' . $tahun);

        $sheet2->mergeCells('A3:F3');
        $sheet2->setCellValue('A3', 'Dicetak pada : ' . date('d F Y H:i'));

        $sheet2->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(16);

        $sheet2->getStyle('A2:A3')->getFont()
            ->setItalic(true);

        $sheet2->getStyle('A1:F3')->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );

        // RINGKASAN PEMASUKAN
        $sheet2->setCellValue('A5', 'Total Pemasukan');
        $sheet2->setCellValue('B5', $ringkasan['total_pemasukan']);
        $sheet2->getStyle('A5:B5')->getFont()->setBold(true);
        $sheet2->getStyle('B5')
            ->getNumberFormat()
            ->setFormatCode($rupiahFormat);

        $sheet2->getStyle('A5:B5')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        // HEADER TABEL
        $headersPemasukan = [
            'No',
            'Nama Penyewa',
            'Nomor Kamar',
            'Bulan',
            'Jumlah',
            'Tanggal Bayar'
        ];

        $col = 'A';
        foreach ($headersPemasukan as $header) {
            $sheet2->setCellValue($col . '7', $header);
            $col++;
        }

        $sheet2->getStyle('A7:F7')->applyFromArray([
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
        $row = 8;
        $no  = 1;
        foreach ($ringkasan['detail_pemasukan'] as $item) {
            $sheet2->setCellValue('A' . $row, $no++);
            $sheet2->setCellValue('B' . $row, $item['name']);
            $sheet2->setCellValue('C' . $row, $item['nomor_kamar']);
            $sheet2->setCellValue('D' . $row, $item['bulan'] . '/' . $item['tahun']);
            $sheet2->setCellValue('E' . $row, (int)$item['jumlah']);

            $tglBayar = '-';
            if ($item['approved_at']) {
                $tglBayar = date('d/m/Y H:i', strtotime($item['approved_at']));
            }
            $sheet2->setCellValue('F' . $row, $tglBayar);

            // Format Rupiah
            $sheet2->getStyle('E' . $row)
                ->getNumberFormat()
                ->setFormatCode($rupiahFormat);

            // Zebra Table
            if ($row % 2 == 1) {
                $sheet2->getStyle('A' . $row . ':F' . $row)
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
        $sheet2->mergeCells('A' . $row . ':D' . $row);
        $sheet2->setCellValue('A' . $row, 'TOTAL PEMASUKAN');
        $sheet2->setCellValue('E' . $row, $ringkasan['total_pemasukan']);

        $sheet2->getStyle('A' . $row . ':E' . $row)->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9ECEF']
            ]
        ]);

        $sheet2->getStyle('E' . $row)
            ->getNumberFormat()
            ->setFormatCode($rupiahFormat);

        // BORDER TABEL
        $sheet2->getStyle('A7:F' . $row)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        // AUTO SIZE
        foreach (range('A', 'F') as $column) {
            $sheet2->getColumnDimension($column)
                ->setAutoSize(true);
        }

        // Freeze Pane
        $sheet2->freezePane('A8');


        // =====================
        // SHEET 3 - Detail setiap pengeluaran bulan ini
        // =====================
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Pengeluaran');

        // JUDUL
        $sheet3->mergeCells('A1:E1');
        $sheet3->setCellValue('A1', 'LAPORAN DETAIL PENGELUARAN KOST');

        $sheet3->mergeCells('A2:E2');
        $sheet3->setCellValue('A2', 'Periode : ' . $namaBulan . ' ' . $tahun);

        $sheet3->mergeCells('A3:E3');
        $sheet3->setCellValue('A3', 'Dicetak pada : ' . date('d F Y H:i'));

        $sheet3->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(16);

        $sheet3->getStyle('A2:A3')->getFont()
            ->setItalic(true);

        $sheet3->getStyle('A1:E3')->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );

        // RINGKASAN PENGELUARAN
        $sheet3->setCellValue('A5', 'Total Pengeluaran');
        $sheet3->setCellValue('B5', $ringkasan['total_pengeluaran']);
        $sheet3->getStyle('A5:B5')->getFont()->setBold(true);
        $sheet3->getStyle('B5')
            ->getNumberFormat()
            ->setFormatCode($rupiahFormat);

        $sheet3->getStyle('A5:B5')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        // HEADER TABEL
        $headersPengeluaran = [
            'No',
            'Keterangan',
            'Kategori',
            'Jumlah',
            'Tanggal'
        ];

        $col = 'A';
        foreach ($headersPengeluaran as $header) {
            $sheet3->setCellValue($col . '7', $header);
            $col++;
        }

        $sheet3->getStyle('A7:E7')->applyFromArray([
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
        $row = 8;
        $no  = 1;
        foreach ($ringkasan['detail_pengeluaran'] as $item) {
            $sheet3->setCellValue('A' . $row, $no++);
            $sheet3->setCellValue('B' . $row, $item['keterangan']);
            $sheet3->setCellValue('C' . $row, ucfirst($item['kategori']));
            $sheet3->setCellValue('D' . $row, (int)$item['jumlah']);
            $sheet3->setCellValue('E' . $row, date('d/m/Y H:i', strtotime($item['created_at'])));

            // Format Rupiah
            $sheet3->getStyle('D' . $row)
                ->getNumberFormat()
                ->setFormatCode($rupiahFormat);

            // Zebra Table
            if ($row % 2 == 1) {
                $sheet3->getStyle('A' . $row . ':E' . $row)
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
        $sheet3->mergeCells('A' . $row . ':C' . $row);
        $sheet3->setCellValue('A' . $row, 'TOTAL PENGELUARAN');
        $sheet3->setCellValue('D' . $row, $ringkasan['total_pengeluaran']);

        $sheet3->getStyle('A' . $row . ':D' . $row)->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9ECEF']
            ]
        ]);

        $sheet3->getStyle('D' . $row)
            ->getNumberFormat()
            ->setFormatCode($rupiahFormat);

        // BORDER TABEL
        $sheet3->getStyle('A7:E' . $row)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        // AUTO SIZE
        foreach (range('A', 'E') as $column) {
            $sheet3->getColumnDimension($column)
                ->setAutoSize(true);
        }

        // Freeze Pane
        $sheet3->freezePane('A8');


        // Pastikan sheet pertama yang aktif saat file dibuka
        $spreadsheet->setActiveSheetIndex(0);

        // Set header HTTP agar browser tahu ini file Excel yang harus didownload
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'laporan-keuangan-' . $bulan . '-' . $tahun . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // Halaman rekap khusus tagihan, bisa difilter per bulan & tahun
    public function tagihan()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data['tagihan']         = $this->tagihanModel->getTagihanLengkap($bulan, $tahun);

        // Hitung jumlah tagihan per status untuk ditampilkan di stat card
        $data['total_lunas']     = $this->tagihanModel->where('bulan', $bulan)->where('tahun', $tahun)->where('status_tagihan_id', 3)->countAllResults();
        $data['total_pending']   = $this->tagihanModel->where('bulan', $bulan)->where('tahun', $tahun)->where('status_tagihan_id', 1)->countAllResults();
        $data['total_menunggak'] = $this->tagihanModel->where('bulan', $bulan)->where('tahun', $tahun)->where('status_tagihan_id', 4)->countAllResults();

        $data['bulan']      = $bulan;
        $data['tahun']      = $tahun;
        $data['list_bulan'] = $this->getListBulan();

        return view('admin/laporan/tagihan', $data);
    }

    // Halaman rekap khusus maintenance
    // Catatan: filter bulan & tahun diterima tapi belum dipakai di query maintenance
    public function maintenance()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data['maintenance']    = $this->maintenanceModel->getMaintenanceLengkap($bulan, $tahun);

        // Hitung per status khusus bulan & tahun yang dipilih
        $data['total_menunggu'] = $this->maintenanceModel
            ->where('MONTH(created_at)', $bulan)
            ->where('YEAR(created_at)', $tahun)
            ->where('status_maintenance_id', 1)
            ->countAllResults();
        $data['total_proses']   = $this->maintenanceModel
            ->where('MONTH(created_at)', $bulan)
            ->where('YEAR(created_at)', $tahun)
            ->where('status_maintenance_id', 2)
            ->countAllResults();
        $data['total_selesai']  = $this->maintenanceModel
            ->where('MONTH(created_at)', $bulan)
            ->where('YEAR(created_at)', $tahun)
            ->where('status_maintenance_id', 3)
            ->countAllResults();
        $data['total_biaya']    = $this->maintenanceModel
            ->selectSum('biaya', 'total')
            ->where('MONTH(created_at)', $bulan)
            ->where('YEAR(created_at)', $tahun)
            ->where('status_maintenance_id', 3)
            ->first()['total'] ?? 0;

        $data['bulan']      = $bulan;
        $data['tahun']      = $tahun;
        $data['list_bulan'] = $this->getListBulan();

        return view('admin/laporan/maintenance', $data);
    }

    // Helper private: hitung semua angka keuangan untuk satu periode bulan & tahun
    // Dipanggil oleh index(), exportPdf(), dan exportExcel() agar tidak ada duplikasi kode
    private function getRingkasan($bulan, $tahun)
    {
        // Ambil semua pembayaran yang sudah diapprove admin di bulan ini
        // Join ke beberapa tabel untuk dapat nama penyewa dan nomor kamar
        $detailPemasukan = $this->pembayaranModel
            ->select('
                pembayaran.jumlah_bayar as jumlah,
                pembayaran.approved_at,
                tagihan.bulan,
                tagihan.tahun,
                users.name,
                kamar.nomor_kamar
            ')
            ->join('tagihan', 'tagihan.id = pembayaran.tagihan_id')
            ->join('penyewa', 'penyewa.id = tagihan.penyewa_id')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id')
            ->where('pembayaran.status_pembayaran_id', 2)
            ->where('tagihan.bulan', $bulan)
            ->where('tagihan.tahun', $tahun)
            ->findAll();

        // Jumlahkan semua nominal pembayaran menggunakan array_sum + array_column
        $totalPemasukan = array_sum(array_column($detailPemasukan, 'jumlah'));

        // Ambil semua pengeluaran bulan ini lalu pisahkan per kategori
        $detailPengeluaran = $this->pengeluaranModel->getPengeluaranLengkap($bulan, $tahun);

        $totalMaintenance = 0;
        $totalGaji        = 0;
        $totalLainnya     = 0;

        // Klasifikasikan pengeluaran ke dalam 3 bucket menggunakan match
        foreach ($detailPengeluaran as $item) {
            match ($item['kategori']) {
                'maintenance' => $totalMaintenance += $item['jumlah'],
                'gaji'        => $totalGaji        += $item['jumlah'],
                default       => $totalLainnya     += $item['jumlah'],
            };
        }

        $totalPengeluaran = $totalMaintenance + $totalGaji + $totalLainnya;

        // Saldo bersih = pemasukan dikurangi pengeluaran
        // Bisa negatif kalau pengeluaran lebih besar dari pemasukan
        $saldoBersih = $totalPemasukan - $totalPengeluaran;

        return [
            'total_pemasukan'    => $totalPemasukan,
            'total_maintenance'  => $totalMaintenance,
            'total_gaji'         => $totalGaji,
            'total_lainnya'      => $totalLainnya,
            'total_pengeluaran'  => $totalPengeluaran,
            'saldo_bersih'       => $saldoBersih,
            'detail_pemasukan'   => $detailPemasukan,
            'detail_pengeluaran' => $detailPengeluaran,
        ];
    }

}
