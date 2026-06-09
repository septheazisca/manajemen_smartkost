<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PembayaranModel;
use App\Models\PenyewaModel;
use App\Models\TagihanModel;
use CodeIgniter\HTTP\ResponseInterface;

class TagihanController extends BaseController
{
    // Semua model dideklarasikan sebagai property agar bisa dipakai di semua method
    protected $tagihanModel;
    protected $pembayaranModel;
    protected $penyewaModel;

    public function __construct()
    {
        $this->tagihanModel    = new TagihanModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->penyewaModel    = new PenyewaModel();
    }

    // Tampilkan semua tagihan berdasarkan filter bulan & tahun
    // Juga tampilkan pembayaran yang menunggu konfirmasi admin
    public function index()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data['tagihan']            = $this->tagihanModel->getTagihanLengkap($bulan, $tahun);
        $data['pembayaran_pending'] = $this->pembayaranModel->getPembayaranPending();
        $data['bulan']              = $bulan;
        $data['tahun']              = $tahun;
        $data['list_bulan']         = $this->getListBulan();

        return view('admin/tagihan/index', $data);
    }

    // Generate tagihan bulanan untuk semua penyewa aktif sekaligus
    // Kalau tagihan bulan yang sama sudah ada, penyewa tersebut dilewati (skip)
    // Nominal unik digenerate per penyewa agar transfer bisa diidentifikasi
    public function generate()
    {
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');

        if (!$bulan || !$tahun) {
            return redirect()->back()->with('error', 'Bulan dan tahun wajib diisi.');
        }

        $semuaPenyewa = $this->penyewaModel->getPenyewaLengkap();

        if (empty($semuaPenyewa)) {
            return redirect()->back()->with('error', 'Belum ada penyewa aktif.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $berhasil = 0;
        $skip     = 0;

        foreach ($semuaPenyewa as $penyewa) {
            // Skip penyewa yang sudah punya tagihan di bulan & tahun yang sama
            $sudahAda = $this->tagihanModel->isTagihanExist($penyewa['id'], $bulan, $tahun);

            if ($sudahAda) {
                $skip++;
                continue;
            }

            // Nominal unik berbeda tiap penyewa, dihitung dari penyewa_id
            // Tujuannya agar admin bisa identifikasi siapa yang bayar dari nominal transfer
            $nominalUnik = $this->tagihanModel->generateNominalUnik($penyewa['id']);

            // Jatuh tempo selalu tanggal 10 bulan tersebut
            $jatuhTempo = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-10';

            $this->tagihanModel->save([
                'penyewa_id'   => $penyewa['id'],
                'bulan'        => $bulan,
                'tahun'        => $tahun,
                'jumlah'       => $penyewa['harga'],
                'nominal_unik' => $nominalUnik,
                'status'       => 'pending',
                'jatuh_tempo'  => $jatuhTempo,
            ]);

            $berhasil++;
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal generate tagihan.');
        }

        $pesan = "Tagihan berhasil digenerate untuk {$berhasil} penyewa.";
        if ($skip > 0) {
            $pesan .= " {$skip} penyewa dilewati karena tagihan sudah ada.";
        }

        return redirect()->to('/admin/tagihan')->with('success', $pesan);
    }

    // Detail tagihan beserta riwayat pembayarannya
    // Pencarian dilakukan manual di array karena getTagihanLengkap() sudah include join
    // yang dibutuhkan untuk tampil di view (nama penyewa, nomor kamar, dll)
    public function show($id)
    {
        $tagihan = $this->tagihanModel->getTagihanLengkap();

        // Filter array untuk cari tagihan dengan id yang sesuai
        $tagihan = array_filter($tagihan, function ($t) use ($id) {
            return $t['id'] == $id;
        });

        $tagihan = reset($tagihan);

        if (!$tagihan) {
            return redirect()->to('/tagihan')->with('error', 'Tagihan tidak ditemukan');
        }

        $data['tagihan']    = $tagihan;
        $data['pembayaran'] = $this->pembayaranModel->where('tagihan_id', $id)->findAll();

        return view('admin/tagihan/detail', $data);
    }

    // Admin approve pembayaran: status pembayaran jadi approved, tagihan jadi lunas
    // Kedua update dilakukan dalam satu transaction agar tidak setengah-setengah
    public function approve($pembayaranId)
    {
        $pembayaran = $this->pembayaranModel->find($pembayaranId);

        if (!$pembayaran) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Tandai pembayaran sebagai approved, catat siapa dan kapan yang approve
        $this->pembayaranModel->update($pembayaranId, [
            'status'        => 'approved',
            'catatan_admin' => $this->request->getPost('catatan_admin'),
            'approved_at'   => date('Y-m-d H:i:s'),
            'approved_by'   => session()->get('user_id'),
        ]);

        // 2. Update status tagihan jadi lunas
        $this->tagihanModel->update($pembayaran['tagihan_id'], [
            'status' => 'lunas',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal approve pembayaran.');
        }

        return redirect()->to('/admin/tagihan')
            ->with('success', 'Pembayaran berhasil dikonfirmasi. Tagihan lunas.');
    }

    // Admin tolak pembayaran: status pembayaran jadi ditolak, tagihan kembali ke pending
    // Alasan penolakan wajib diisi agar penyewa tahu apa yang salah
    public function tolak($pembayaranId)
    {
        $pembayaran = $this->pembayaranModel->find($pembayaranId);

        if (!$pembayaran) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $catatanAdmin = $this->request->getPost('catatan_admin');

        if (!$catatanAdmin) {
            return redirect()->back()->with('error', 'Alasan penolakan wajib diisi.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Tandai pembayaran sebagai ditolak beserta alasannya
        $this->pembayaranModel->update($pembayaranId, [
            'status'        => 'ditolak',
            'catatan_admin' => $catatanAdmin,
        ]);

        // 2. Kembalikan tagihan ke pending agar penyewa bisa upload ulang bukti
        $this->tagihanModel->update($pembayaran['tagihan_id'], [
            'status' => 'pending',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menolak pembayaran.');
        }

        return redirect()->to('/admin/tagihan')
            ->with('success', 'Pembayaran ditolak. Penyewa perlu upload ulang bukti transfer.');
    }

    // Admin tandai tagihan sebagai menunggak secara manual
    // Tagihan yang sudah lunas tidak bisa ditandai menunggak
    public function tandaiMenunggak($tagihanId)
    {
        $tagihan = $this->tagihanModel->find($tagihanId);

        if (!$tagihan) {
            return redirect()->back()->with('error', 'Tagihan tidak ditemukan.');
        }

        if ($tagihan['status'] === 'lunas') {
            return redirect()->back()->with('error', 'Tagihan sudah lunas, tidak bisa ditandai menunggak.');
        }

        $this->tagihanModel->update($tagihanId, ['status' => 'menunggak']);

        return redirect()->to('/admin/tagihan')
            ->with('success', 'Tagihan berhasil ditandai sebagai menunggak.');
    }

    // Penyewa upload bukti transfer untuk satu tagihan
    // Ada pengecekan ketat: tagihan harus milik penyewa yang login
    // File disimpan di public/uploads/bukti_transfer agar bisa diakses browser
    public function uploadBukti($tagihanId)
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data penyewa tidak ditemukan.');
        }

        $tagihan = $this->tagihanModel->find($tagihanId);

        // Pastikan tagihan ada dan memang milik penyewa yang sedang login
        if (!$tagihan || $tagihan['penyewa_id'] !== $penyewa['id']) {
            return redirect()->back()->with('error', 'Tagihan tidak ditemukan.');
        }

        if ($tagihan['status'] === 'lunas') {
            return redirect()->back()->with('error', 'Tagihan ini sudah lunas.');
        }

        // Validasi file: wajib ada, tipe JPG/PNG, maksimal 2MB
        $file = $this->request->getFile('bukti_transfer');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File bukti transfer wajib diupload.');
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return redirect()->back()->with('error', 'Format file harus JPG atau PNG.');
        }

        if ($file->getSizeByUnit('mb') > 2) {
            return redirect()->back()->with('error', 'Ukuran file maksimal 2MB.');
        }

        // Generate nama acak agar tidak bentrok dengan file lain
        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads/bukti_transfer', $newName);

        $db = \Config\Database::connect();
        $db->transStart();

        // Simpan record pembayaran dengan status pending, menunggu konfirmasi admin
        $this->pembayaranModel->save([
            'tagihan_id'     => $tagihanId,
            'jumlah_bayar'   => $tagihan['jumlah'] + $tagihan['nominal_unik'],
            'bukti_transfer' => $newName,
            'status'         => 'pending',
        ]);

        // Update status tagihan agar admin tahu ada pembayaran yang perlu dikonfirmasi
        $this->tagihanModel->update($tagihanId, [
            'status' => 'menunggu_konfirmasi',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal upload bukti transfer.');
        }

        return redirect()->to('/tenant/tagihan')
            ->with('success', 'Bukti transfer berhasil diupload. Menunggu konfirmasi admin.');
    }

    // Tampilkan semua tagihan milik penyewa yang sedang login
    public function tagihanSaya()
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->to('/tenant/dashboard')->with('error', 'Data penyewa tidak ditemukan.');
        }

        $data['tagihan'] = $this->tagihanModel->getTagihanByPenyewa($penyewa['id']);
        $data['penyewa'] = $penyewa;

        return view('tenant/tagihan', $data);
    }

    // Detail satu tagihan beserta semua riwayat pembayaran penyewa tersebut
    // Riwayat diambil lintas tagihan agar penyewa bisa lihat semua histori pembayaran
    public function detailTagihan($id)
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->to('/tenant/tagihan')->with('error', 'Data penyewa tidak ditemukan.');
        }

        // Cari tagihan di array hasil getTagihanLengkap agar dapat data join-nya
        $semua   = $this->tagihanModel->getTagihanLengkap();
        $tagihan = null;
        foreach ($semua as $t) {
            if ($t['id'] == $id) {
                $tagihan = $t;
                break;
            }
        }

        // Keamanan: pastikan tagihan ini memang milik penyewa yang login
        if (!$tagihan || $tagihan['penyewa_id'] !== $penyewa['id']) {
            return redirect()->to('/tenant/tagihan')->with('error', 'Tagihan tidak ditemukan.');
        }

        // Ambil semua riwayat pembayaran penyewa ini dengan join ke tagihan
        // Menggunakan right join agar tagihan tanpa pembayaran pun tetap muncul
        $data['pembayaran'] = $this->pembayaranModel
            ->select('
                tagihan.id as tagihan_id,
                tagihan.bulan,
                tagihan.tahun,
                tagihan.status as status_tagihan,
                tagihan.jumlah,
                tagihan.nominal_unik,
                pembayaran.id as pembayaran_id,
                pembayaran.jumlah_bayar,
                pembayaran.bukti_transfer,
                pembayaran.status as status_pembayaran,
                pembayaran.catatan_admin,
                pembayaran.created_at
            ')
            ->join('tagihan', 'tagihan.id = pembayaran.tagihan_id', 'right')
            ->where('tagihan.penyewa_id', $penyewa['id'])
            ->orderBy('tagihan.tahun', 'DESC')
            ->orderBy('tagihan.bulan', 'DESC')
            ->findAll();

        $data['tagihan'] = $tagihan;
        $data['penyewa'] = $penyewa;

        return view('tenant/detail_tagihan', $data);
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


    public function exportExcel()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $tagihan = $this->tagihanModel->getTagihanLengkap($bulan, $tahun);

        $namaBulan = $this->getListBulan()[$bulan] ?? $bulan;

        // HITUNG RINGKASAN
        $totalTagihan = 0;
        $totalLunas = 0;
        $totalMenunggak = 0;

        $jumlahLunas = 0;
        $jumlahMenunggak = 0;

        foreach ($tagihan as $t) {

            $jumlah = (int)$t['jumlah'];

            $totalTagihan += $jumlah;

            if (strtolower($t['status']) == 'lunas') {
                $totalLunas += $jumlah;
                $jumlahLunas++;
            } else {
                $totalMenunggak += $jumlah;
                $jumlahMenunggak++;
            }
        }

        // EXCEL
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tagihan Kost');

        // JUDUL
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'LAPORAN TAGIHAN KAMAR KOST');

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue(
            'A2',
            'Periode : ' . $namaBulan . ' ' . $tahun
        );

        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue(
            'A3',
            'Dicetak pada : ' . date('d F Y H:i')
        );

        $sheet->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(16);

        $sheet->getStyle('A2:A3')->getFont()
            ->setItalic(true);

        $sheet->getStyle('A1:F3')->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );

        // RINGKASAN
        $sheet->setCellValue('A5', 'Total Tagihan');
        $sheet->setCellValue('B5', $totalTagihan);

        $sheet->setCellValue('A6', 'Total Lunas');
        $sheet->setCellValue('B6', $totalLunas);

        $sheet->setCellValue('A7', 'Total Menunggak');
        $sheet->setCellValue('B7', $totalMenunggak);

        $sheet->setCellValue('D5', 'Jumlah Lunas');
        $sheet->setCellValue('E5', $jumlahLunas . ' Penyewa');

        $sheet->setCellValue('D6', 'Jumlah Menunggak');
        $sheet->setCellValue('E6', $jumlahMenunggak . ' Penyewa');

        // Format Rupiah Ringkasan
        foreach (['B5', 'B6', 'B7'] as $cell) {
            $sheet->getStyle($cell)
                ->getNumberFormat()
                ->setFormatCode('#,##0');
        }

        $sheet->getStyle('A5:E7')->getFont()->setBold(true);

        // HEADER TABEL
        $headers = [
            'No',
            'Nama Penyewa',
            'Kamar',
            'Tagihan',
            'Jatuh Tempo',
            'Status'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '9', $header);
            $col++;
        }

        $sheet->getStyle('A9:F9')->applyFromArray([
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

        foreach ($tagihan as $t) {

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $t['nama']);
            $sheet->setCellValue('C' . $row, $t['nama_kamar']);
            $sheet->setCellValue('D' . $row, (int)$t['jumlah']);

            $sheet->setCellValue(
                'E' . $row,
                date('d/m/Y', strtotime($t['jatuh_tempo']))
            );

            $sheet->setCellValue(
                'F' . $row,
                ucfirst($t['status'])
            );

            // Format Rupiah
            $sheet->getStyle('D' . $row)
                ->getNumberFormat()
                ->setFormatCode('#,##0');

            // Warna Status
            if (strtolower($t['status']) == 'lunas') {

                $sheet->getStyle('F' . $row)
                    ->getFont()
                    ->getColor()
                    ->setRGB('198754');
            } else {

                $sheet->getStyle('F' . $row)
                    ->getFont()
                    ->getColor()
                    ->setRGB('DC3545');
            }

            // Zebra Table
            if ($row % 2 == 0) {

                $sheet->getStyle('A' . $row . ':F' . $row)
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
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL TAGIHAN');

        $sheet->setCellValue('D' . $row, $totalTagihan);

        $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9ECEF']
            ]
        ]);

        $sheet->getStyle('D' . $row)
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // BORDER
        $sheet->getStyle('A9:F' . $row)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        // Border Ringkasan
        $sheet->getStyle('A5:E7')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );

        // AUTO SIZE
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)
                ->setAutoSize(true);
        }

        // Freeze Header
        $sheet->freezePane('A10');

        // DOWNLOAD
        $filename = 'laporan-tagihan-' .
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
