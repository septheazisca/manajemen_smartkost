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

    // =====================
    // INDEX - Laporan keuangan utama
    // =====================
    public function index()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data = array_merge($this->getRingkasan($bulan, $tahun), [
            'bulan'      => $bulan,
            'tahun'      => $tahun,
            'list_bulan' => $this->getListBulan(),
        ]);

        return view('admin/laporan/index', $data);
    }

    // =====================
    // EXPORT PDF - Export laporan ke PDF
    // =====================
    public function exportPdf()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data = array_merge($this->getRingkasan($bulan, $tahun), [
            'bulan'      => $bulan,
            'tahun'      => $tahun,
            'list_bulan' => $this->getListBulan(),
            'generated'  => date('d/m/Y H:i:s'),
        ]);

        // load dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->setOptions(new \Dompdf\Options([
            'defaultFont'                => 'sans-serif',
            'isRemoteEnabled'            => false,
            'isHtml5ParserEnabled'       => true,
        ]));

        // render view jadi HTML
        $html = view('admin/laporan/pdf_template', $data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'laporan-keuangan-' . $bulan . '-' . $tahun . '.pdf';

        $dompdf->stream($filename, ['Attachment' => true]);
    }

    // =====================
    // EXPORT EXCEL - Export laporan ke Excel
    // =====================
    public function exportExcel()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $ringkasan    = $this->getRingkasan($bulan, $tahun);
        $namaBulan    = $this->getListBulan()[$bulan] ?? $bulan;

        $spreadsheet  = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // =====================
        // SHEET 1 - Ringkasan
        // =====================
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Ringkasan');

        $sheet1->setCellValue('A1', 'LAPORAN KEUANGAN SMARKOST');
        $sheet1->setCellValue('A2', 'Periode: ' . $namaBulan . ' ' . $tahun);
        $sheet1->setCellValue('A3', 'Digenerate: ' . date('d/m/Y H:i:s'));

        $sheet1->setCellValue('A5', 'PEMASUKAN');
        $sheet1->setCellValue('A6', 'Total Tagihan Lunas');
        $sheet1->setCellValue('B6', $ringkasan['total_pemasukan']);

        $sheet1->setCellValue('A8', 'PENGELUARAN');
        $sheet1->setCellValue('A9', 'Biaya Maintenance');
        $sheet1->setCellValue('B9', $ringkasan['total_maintenance']);
        $sheet1->setCellValue('A10', 'Gaji Penanggung Jawab');
        $sheet1->setCellValue('B10', $ringkasan['total_gaji']);
        $sheet1->setCellValue('A11', 'Lainnya');
        $sheet1->setCellValue('B11', $ringkasan['total_lainnya']);
        $sheet1->setCellValue('A12', 'Total Pengeluaran');
        $sheet1->setCellValue('B12', $ringkasan['total_pengeluaran']);

        $sheet1->setCellValue('A14', 'SALDO BERSIH');
        $sheet1->setCellValue('B14', $ringkasan['saldo_bersih']);

        // styling header
        $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet1->getStyle('A5')->getFont()->setBold(true);
        $sheet1->getStyle('A8')->getFont()->setBold(true);
        $sheet1->getStyle('A14')->getFont()->setBold(true);
        $sheet1->getStyle('B14')->getFont()->setBold(true);

        // format angka rupiah
        $rupiahFormat = '#,##0';
        foreach (['B6', 'B9', 'B10', 'B11', 'B12', 'B14'] as $cell) {
            $sheet1->getStyle($cell)
                ->getNumberFormat()
                ->setFormatCode($rupiahFormat);
        }

        $sheet1->getColumnDimension('A')->setWidth(30);
        $sheet1->getColumnDimension('B')->setWidth(20);

        // =====================
        // SHEET 2 - Detail Pemasukan
        // =====================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Pemasukan');

        $sheet2->setCellValue('A1', 'No');
        $sheet2->setCellValue('B1', 'Nama Penyewa');
        $sheet2->setCellValue('C1', 'Nomor Kamar');
        $sheet2->setCellValue('D1', 'Bulan');
        $sheet2->setCellValue('E1', 'Jumlah');
        $sheet2->setCellValue('F1', 'Tanggal Bayar');

        $sheet2->getStyle('A1:F1')->getFont()->setBold(true);

        $row     = 2;
        $no      = 1;
        foreach ($ringkasan['detail_pemasukan'] as $item) {
            $sheet2->setCellValue('A' . $row, $no++);
            $sheet2->setCellValue('B' . $row, $item['name']);
            $sheet2->setCellValue('C' . $row, $item['nomor_kamar']);
            $sheet2->setCellValue('D' . $row, $item['bulan'] . '/' . $item['tahun']);
            $sheet2->setCellValue('E' . $row, $item['jumlah']);
            $sheet2->setCellValue('F' . $row, $item['approved_at'] ?? '-');
            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        // =====================
        // SHEET 3 - Detail Pengeluaran
        // =====================
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Pengeluaran');

        $sheet3->setCellValue('A1', 'No');
        $sheet3->setCellValue('B1', 'Keterangan');
        $sheet3->setCellValue('C1', 'Kategori');
        $sheet3->setCellValue('D1', 'Jumlah');
        $sheet3->setCellValue('E1', 'Tanggal');

        $sheet3->getStyle('A1:E1')->getFont()->setBold(true);

        $row = 2;
        $no  = 1;
        foreach ($ringkasan['detail_pengeluaran'] as $item) {
            $sheet3->setCellValue('A' . $row, $no++);
            $sheet3->setCellValue('B' . $row, $item['keterangan']);
            $sheet3->setCellValue('C' . $row, ucfirst($item['kategori']));
            $sheet3->setCellValue('D' . $row, $item['jumlah']);
            $sheet3->setCellValue('E' . $row, $item['created_at']);
            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
            $sheet3->getColumnDimension($col)->setAutoSize(true);
        }

        // set active sheet ke sheet pertama
        $spreadsheet->setActiveSheetIndex(0);

        // output
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'laporan-keuangan-' . $bulan . '-' . $tahun . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // =====================
    // LAPORAN TAGIHAN - Khusus rekap tagihan
    // =====================
    public function tagihan()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data['tagihan']         = $this->tagihanModel->getTagihanLengkap($bulan, $tahun);
        $data['total_lunas']     = $this->tagihanModel
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', 'lunas')
            ->countAllResults();
        $data['total_pending']   = $this->tagihanModel
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', 'pending')
            ->countAllResults();
        $data['total_menunggak'] = $this->tagihanModel
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', 'menunggak')
            ->countAllResults();
        $data['bulan']           = $bulan;
        $data['tahun']           = $tahun;
        $data['list_bulan']      = $this->getListBulan();

        return view('admin/laporan/tagihan', $data);
    }

    // =====================
    // LAPORAN MAINTENANCE - Khusus rekap maintenance
    // =====================
    public function maintenance()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data['maintenance']      = $this->maintenanceModel->getMaintenanceLengkap();
        $data['total_menunggu']   = $this->maintenanceModel->where('status', 'menunggu')->countAllResults();
        $data['total_proses']     = $this->maintenanceModel->where('status', 'proses')->countAllResults();
        $data['total_selesai']    = $this->maintenanceModel->where('status', 'selesai')->countAllResults();
        $data['total_biaya']      = $this->maintenanceModel
            ->selectSum('biaya', 'total')
            ->where('status', 'selesai')
            ->first()['total'] ?? 0;
        $data['bulan']            = $bulan;
        $data['tahun']            = $tahun;
        $data['list_bulan']       = $this->getListBulan();

        return view('admin/laporan/maintenance', $data);
    }

    // =====================
    // HELPER - Ambil semua data ringkasan keuangan
    // =====================
    private function getRingkasan($bulan, $tahun)
    {
        // detail pemasukan = pembayaran approved bulan ini
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
            ->where('pembayaran.status', 'approved')
            ->where('tagihan.bulan', $bulan)
            ->where('tagihan.tahun', $tahun)
            ->findAll();

        $totalPemasukan = array_sum(array_column($detailPemasukan, 'jumlah'));

        // detail pengeluaran bulan ini
        $detailPengeluaran = $this->pengeluaranModel
            ->getPengeluaranLengkap($bulan, $tahun);

        $totalMaintenance = 0;
        $totalGaji        = 0;
        $totalLainnya     = 0;

        foreach ($detailPengeluaran as $item) {
            match ($item['kategori']) {
                'maintenance' => $totalMaintenance += $item['jumlah'],
                'gaji'        => $totalGaji        += $item['jumlah'],
                default       => $totalLainnya     += $item['jumlah'],
            };
        }

        $totalPengeluaran = $totalMaintenance + $totalGaji + $totalLainnya;
        $saldoBersih      = $totalPemasukan - $totalPengeluaran;

        return [
            'total_pemasukan'   => $totalPemasukan,
            'total_maintenance' => $totalMaintenance,
            'total_gaji'        => $totalGaji,
            'total_lainnya'     => $totalLainnya,
            'total_pengeluaran' => $totalPengeluaran,
            'saldo_bersih'      => $saldoBersih,
            'detail_pemasukan'  => $detailPemasukan,
            'detail_pengeluaran' => $detailPengeluaran,
        ];
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
