<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PembayaranModel;
use App\Models\PenyewaModel;
use App\Models\TagihanModel;
use App\Models\UserModel;
use App\Libraries\FonnteService;
use CodeIgniter\HTTP\ResponseInterface;

class TagihanController extends BaseController
{
    // Semua model dideklarasikan sebagai property agar bisa dipakai di semua method
    protected $tagihanModel;
    protected $pembayaranModel;
    protected $penyewaModel;
    protected $userModel;
    protected $fonnteService;

    public function __construct()
    {
        $this->tagihanModel    = new TagihanModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->penyewaModel    = new PenyewaModel();
        $this->userModel       = new UserModel();
        $this->fonnteService   = new FonnteService();
    }

    // Tampilkan semua tagihan berdasarkan filter bulan & tahun
    // Juga tampilkan pembayaran yang menunggu konfirmasi admin
    public function index()
    {
        // Logika auto-update tunggakan DARI SINI TELAH DIHAPUS.
        // SEKARANG SEPENUHNYA DITANGANI OLEH CRON JOB (AutomasiTagihan.php)
        // agar notifikasi WA selalu terkirim bersamaan dengan perubahan status.

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

        $result = $this->tagihanModel->generateBulk($semuaPenyewa, $bulan, $tahun);

        if ($result === false) {
            return redirect()->back()->with('error', 'Gagal generate tagihan.');
        }

        $pesan = "Tagihan berhasil digenerate untuk {$result['berhasil']} penyewa.";
        if ($result['skip'] > 0) {
            $pesan .= " {$result['skip']} penyewa dilewati karena tagihan sudah ada.";
        }

        return redirect()->to('/admin/tagihan')->with('success', $pesan);
    }

    // Detail tagihan beserta riwayat pembayarannya
    // Pencarian dilakukan manual di array karena getTagihanLengkap() sudah include join
    // yang dibutuhkan untuk tampil di view (nama penyewa, nomor kamar, dll)
    public function show($id)
    {
        $tagihan = $this->tagihanModel->getTagihanLengkapById($id);

        if (!$tagihan) {
            return redirect()->to('/tagihan')->with('error', 'Tagihan tidak ditemukan');
        }

        $data['tagihan']    = $tagihan;
        $data['pembayaran'] = $this->pembayaranModel
            ->select('pembayaran.*, status_pembayaran.nama_status AS status, status_pembayaran.badge_class, status_pembayaran.icon')
            ->join('status_pembayaran', 'status_pembayaran.id = pembayaran.status_pembayaran_id')
            ->where('tagihan_id', $id)
            ->findAll();

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
            'status_pembayaran_id' => 2, // 2 is approved
            'catatan_admin'        => $this->request->getPost('catatan_admin'),
            'approved_at'          => date('Y-m-d H:i:s'),
            'approved_by'          => session()->get('user_id'),
        ]);

        // 2. Update status tagihan jadi lunas
        $this->tagihanModel->update($pembayaran['tagihan_id'], [
            'status_tagihan_id' => 3, // 3 is lunas
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal approve pembayaran.');
        }

        // Kirim WhatsApp trigger ke penyewa
        $tagihanLengkap = $this->tagihanModel->getTagihanLengkapById($pembayaran['tagihan_id']);
        if ($tagihanLengkap && !empty($tagihanLengkap['phone'])) {
            // PERBAIKAN: Ambil user_id manual dari PenyewaModel jika tidak ada di tagihanLengkap
            $penyewa = $this->penyewaModel->find($tagihanLengkap['penyewa_id']);
            $userId = $tagihanLengkap['user_id'] ?? ($penyewa['user_id'] ?? null);

            $totalBayar = $tagihanLengkap['jumlah'] + $tagihanLengkap['nominal_unik'];
            $namaBulan = $this->getListBulan()[$tagihanLengkap['bulan']] ?? $tagihanLengkap['bulan'];
            $pesan = "Halo *{$tagihanLengkap['nama']}*,\n\n";
            $pesan .= "🎉 Pembayaran sewa kost kamu telah *DISETUJUI* oleh Admin.\n\n";
            $pesan .= "📋 *Detail Kamar & Periode*\n";
            $pesan .= "Kamar  : {$tagihanLengkap['nama_kamar']}\n";
            $pesan .= "Periode: {$namaBulan} {$tagihanLengkap['tahun']}\n";
            $pesan .= "Jumlah : Rp " . number_format($totalBayar, 0, ',', '.') . "\n\n";
            $pesan .= "Status tagihan kamu saat ini sudah *LUNAS*. Terima kasih atas pembayarannya! 🙏";

            $this->fonnteService->sendAndLog($userId, $tagihanLengkap['phone'], $pesan, 'approved');
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
            'status_pembayaran_id' => 3, // 3 is ditolak
            'catatan_admin'        => $catatanAdmin,
        ]);

        // 2. Kembalikan tagihan ke pending agar penyewa bisa upload ulang bukti
        $this->tagihanModel->update($pembayaran['tagihan_id'], [
            'status_tagihan_id' => 1, // 1 is pending
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menolak pembayaran.');
        }

        // Kirim WhatsApp trigger ke penyewa
        $tagihanLengkap = $this->tagihanModel->getTagihanLengkapById($pembayaran['tagihan_id']);
        if ($tagihanLengkap && !empty($tagihanLengkap['phone'])) {
            
            // 🔍 AMBIL USER_ID SECARA MANUAL DARI PENYEWA MODEL
            $penyewa = $this->penyewaModel->find($tagihanLengkap['penyewa_id']);
            $userId = $tagihanLengkap['user_id'] ?? ($penyewa['user_id'] ?? null);

            $totalBayar = $tagihanLengkap['jumlah'] + $tagihanLengkap['nominal_unik'];
            $namaBulan = $this->getListBulan()[$tagihanLengkap['bulan']] ?? $tagihanLengkap['bulan'];
            $pesan = "Halo *{$tagihanLengkap['nama']}*,\n\n";
            $pesan .= "⚠️ Pembayaran sewa kost kamu *DITOLAK* oleh Admin.\n\n";
            $pesan .= "📋 *Detail Penolakan*\n";
            $pesan .= "Kamar            : {$tagihanLengkap['nama_kamar']}\n";
            $pesan .= "Periode          : {$namaBulan} {$tagihanLengkap['tahun']}\n";
            $pesan .= "Alasan Penolakan : *{$catatanAdmin}*\n\n";
            $pesan .= "Mohon lakukan upload ulang bukti transfer yang valid melalui aplikasi SmartKost. Terima kasih. 🙏";

            // Gunakan variabel $userId yang baru diambil
            $this->fonnteService->sendAndLog($userId, $tagihanLengkap['phone'], $pesan, 'ditolak');
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

        if ((int)$tagihan['status_tagihan_id'] === 3) { // 3 is lunas
            return redirect()->back()->with('error', 'Tagihan sudah lunas, tidak bisa ditandai menunggak.');
        }

        $this->tagihanModel->update($tagihanId, ['status_tagihan_id' => 4]); // 4 is menunggak

        // Kirim WhatsApp trigger ke penyewa
        $tagihanLengkap = $this->tagihanModel->getTagihanLengkapById($tagihanId);
        if ($tagihanLengkap && !empty($tagihanLengkap['phone'])) {
            // PERBAIKAN: Ambil user_id manual dari PenyewaModel jika tidak ada di tagihanLengkap
            $penyewa = $this->penyewaModel->find($tagihanLengkap['penyewa_id']);
            $userId = $tagihanLengkap['user_id'] ?? ($penyewa['user_id'] ?? null);

            $totalBayar = $tagihanLengkap['jumlah'] + $tagihanLengkap['nominal_unik'];
            $namaBulan = $this->getListBulan()[$tagihanLengkap['bulan']] ?? $tagihanLengkap['bulan'];
            $pesan = "Halo *{$tagihanLengkap['nama']}*,\n\n";
            $pesan .= "⚠️ Status tagihan sewa kost kamu untuk periode *{$namaBulan} {$tagihanLengkap['tahun']}* telah diubah menjadi *MENUNGGAK*.\n\n";
            $pesan .= "Mohon segera lakukan pembayaran sebesar *Rp " . number_format($totalBayar, 0, ',', '.') . "* dan upload bukti transfer melalui aplikasi SmartKost. Jika ada kendala, hubungi admin. Terima kasih. 🙏";

            $this->fonnteService->sendAndLog($userId, $tagihanLengkap['phone'], $pesan, 'tunggakan');
        }

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

        if ((int)$tagihan['status_tagihan_id'] === 3) { // 3 is lunas
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
            'tagihan_id'           => $tagihanId,
            'jumlah_bayar'         => $tagihan['jumlah'] + $tagihan['nominal_unik'],
            'bukti_transfer'       => $newName,
            'status_pembayaran_id' => 1, // 1 is pending
        ]);

        // Update status tagihan agar admin tahu ada pembayaran yang perlu dikonfirmasi
        $this->tagihanModel->update($tagihanId, [
            'status_tagihan_id' => 2, // 2 is menunggu_konfirmasi
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal upload bukti transfer.');
        }

        // Kirim WhatsApp trigger ke admin
        $admins = $this->userModel->where('role', 'admin')->where('is_active', 1)->findAll();
        $tagihanLengkap = $this->tagihanModel->getTagihanLengkapById($tagihanId);
        if ($tagihanLengkap && !empty($admins)) {
            $totalBayar = $tagihanLengkap['jumlah'] + $tagihanLengkap['nominal_unik'];
            $namaBulan = $this->getListBulan()[$tagihanLengkap['bulan']] ?? $tagihanLengkap['bulan'];

            foreach ($admins as $adm) {
                if (!empty($adm['phone'])) {
                    $pesan = "Halo Admin *{$adm['name']}*,\n\n";
                    $pesan .= "📥 Ada bukti pembayaran baru yang diupload oleh penyewa:\n\n";
                    $pesan .= "*   Penyewa : {$tagihanLengkap['nama']}\n";
                    $pesan .= "*   Kamar   : {$tagihanLengkap['nama_kamar']}\n";
                    $pesan .= "*   Periode : {$namaBulan} {$tagihanLengkap['tahun']}\n";
                    $pesan .= "*   Jumlah  : Rp " . number_format($totalBayar, 0, ',', '.') . "\n\n";
                    $pesan .= "Mohon segera login ke aplikasi SmartKost untuk melakukan verifikasi. Terima kasih.";

                    $this->fonnteService->sendAndLog($adm['id'], $adm['phone'], $pesan, 'upload_bukti');
                }
            }
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
    // detail satu tagihan beserta semua riwayat pembayaran penyewa tersebut
    public function detailTagihan($id)
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->to('/tenant/tagihan')->with('error', 'Data penyewa tidak ditemukan.');
        }

        $tagihan = $this->tagihanModel->getTagihanLengkapById($id);

        if (!$tagihan || $tagihan['penyewa_id'] !== $penyewa['id']) {
            return redirect()->to('/tenant/tagihan')->with('error', 'Tagihan tidak ditemukan.');
        }

        // PERBAIKAN QUERY: Ambil murni dari pembayaran yang join ke tagihan
        $data['pembayaran'] = $this->pembayaranModel
            ->select('
                pembayaran.id as pembayaran_id,
                pembayaran.jumlah_bayar,
                pembayaran.bukti_transfer,
                status_pembayaran.nama_status as status_pembayaran,
                status_pembayaran.badge_class,
                status_pembayaran.icon,
                pembayaran.catatan_admin,
                pembayaran.created_at,
                pembayaran.updated_at,
                tagihan.id as tagihan_id,
                status_tagihan.nama_status as status_tagihan
            ')
            ->join('status_pembayaran', 'status_pembayaran.id = pembayaran.status_pembayaran_id', 'left')
            ->join('tagihan', 'tagihan.id = pembayaran.tagihan_id', 'left')
            ->join('status_tagihan', 'status_tagihan.id = tagihan.status_tagihan_id', 'left')
            ->where('pembayaran.tagihan_id', $id)
            ->orderBy('pembayaran.created_at', 'DESC')
            ->findAll();

        $data['tagihan'] = $tagihan;
        $data['penyewa'] = $penyewa;

        return view('tenant/detail_tagihan', $data);
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

    // Export tagihan saya (tenant) ke Excel
    public function exportExcelSaya()
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->to('/tenant/dashboard')->with('error', 'Data penyewa tidak ditemukan.');
        }

        $tagihan = $this->tagihanModel->getTagihanByPenyewa($penyewa['id']);

        // HITUNG RINGKASAN
        $totalTagihan = 0;
        $totalLunas = 0;
        $totalMenunggak = 0;

        foreach ($tagihan as $t) {
            $jumlah = (int)$t['jumlah'];
            $totalTagihan += $jumlah;

            if (strtolower($t['status']) == 'lunas') {
                $totalLunas += $jumlah;
            } else {
                $totalMenunggak += $jumlah;
            }
        }

        // EXCEL
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tagihan Saya');

        // JUDUL
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'LAPORAN HISTORI TAGIHAN SAYA');

        $sheet->mergeCells('A2:E2');
        $sheet->setCellValue('A2', 'Nama Penyewa: ' . $penyewa['name']);

        $sheet->mergeCells('A3:E3');
        $sheet->setCellValue('A3', 'Kamar: Kamar ' . ($penyewa['nomor_kamar'] ?? '-'));

        $sheet->mergeCells('A4:E4');
        $sheet->setCellValue('A4', 'Dicetak pada: ' . date('d F Y H:i'));

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setBold(true);
        $sheet->getStyle('A4')->getFont()->setItalic(true);

        $sheet->getStyle('A1:E4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // RINGKASAN
        $sheet->setCellValue('A6', 'Total Tagihan');
        $sheet->setCellValue('B6', $totalTagihan);

        $sheet->setCellValue('A7', 'Total Lunas');
        $sheet->setCellValue('B7', $totalLunas);

        $sheet->setCellValue('A8', 'Total Menunggak/Pending');
        $sheet->setCellValue('B8', $totalMenunggak);

        // Format Rupiah Ringkasan
        foreach (['B6', 'B7', 'B8'] as $cell) {
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('#,##0');
        }

        $sheet->getStyle('A6:B8')->getFont()->setBold(true);

        // HEADER TABEL
        $headers = [
            'No',
            'Bulan & Tahun',
            'Jumlah Tagihan',
            'Jatuh Tempo',
            'Status'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '10', $header);
            $col++;
        }

        $sheet->getStyle('A10:E10')->applyFromArray([
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
        $row = 11;
        $no = 1;
        $listBulan = $this->getListBulan();

        foreach ($tagihan as $t) {
            $namaBulan = $listBulan[str_pad($t['bulan'], 2, '0', STR_PAD_LEFT)] ?? $t['bulan'];
            $periode = $namaBulan . ' ' . $t['tahun'];

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $periode);
            $sheet->setCellValue('C' . $row, (int)$t['jumlah']);
            $sheet->setCellValue('D' . $row, date('d/m/Y', strtotime($t['jatuh_tempo'])));
            $sheet->setCellValue('E' . $row, ucfirst($t['status']));

            // Format Rupiah
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0');

            // Warna Status
            if (strtolower($t['status']) == 'lunas') {
                $sheet->getStyle('E' . $row)->getFont()->getColor()->setRGB('198754');
            } else {
                $sheet->getStyle('E' . $row)->getFont()->getColor()->setRGB('DC3545');
            }

            // Zebra Table
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':E' . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8F9FA');
            }

            $row++;
        }

        // TOTAL AKHIR
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->setCellValue('A' . $row, 'TOTAL TAGIHAN');
        $sheet->setCellValue('C' . $row, $totalTagihan);

        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E9ECEF']
            ]
        ]);

        $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('#,##0');

        // BORDER
        $sheet->getStyle('A10:E' . $row)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $sheet->getStyle('A6:B8')->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // AUTO SIZE
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename = 'histori-tagihan-' . urlencode(strtolower(str_replace(' ', '-', $penyewa['name']))) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // Kirim notifikasi tagihan (pending) manual via WhatsApp
    public function kirimNotifTagihan($tagihanId)
    {
        $tagihanLengkap = $this->tagihanModel->getTagihanLengkapById($tagihanId);
        if (!$tagihanLengkap) {
            return redirect()->back()->with('error', 'Tagihan tidak ditemukan.');
        }

        if (empty($tagihanLengkap['phone'])) {
            return redirect()->back()->with('error', 'Nomor HP penyewa tidak terdaftar.');
        }

        $penyewa = $this->penyewaModel->find($tagihanLengkap['penyewa_id']);
        $userId = $tagihanLengkap['user_id'] ?? ($penyewa['user_id'] ?? null);

        $totalBayar = $tagihanLengkap['jumlah'] + $tagihanLengkap['nominal_unik'];
        $namaBulan = $this->getListBulan()[$tagihanLengkap['bulan']] ?? $tagihanLengkap['bulan'];
        $jatuhTempoFormated = date('d F Y', strtotime($tagihanLengkap['jatuh_tempo']));

        $pesan = "Halo *{$tagihanLengkap['nama']}*,\n\n📢 Ini adalah pengingat tagihan sewa kamar *Kamar {$tagihanLengkap['nama_kamar']}* untuk periode *{$namaBulan} {$tagihanLengkap['tahun']}*.\n\nTotal tagihan: *Rp " . number_format($totalBayar, 0, ',', '.') . "*\nJatuh Tempo: *{$jatuhTempoFormated}*\n\nMohon lakukan pembayaran dan unggah bukti transfer melalui aplikasi SmartKost. Terima kasih! 🙏";

        $status = $this->fonnteService->sendAndLog($userId, $tagihanLengkap['phone'], $pesan, 'tagihan');

        if ($status) {
            return redirect()->back()->with('success', 'Notifikasi pengingat tagihan berhasil dikirim ke WhatsApp.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengirim notifikasi WhatsApp.');
        }
    }

    // Kirim notifikasi menunggak manual via WhatsApp
    public function kirimNotifMenunggak($tagihanId)
    {
        $tagihanLengkap = $this->tagihanModel->getTagihanLengkapById($tagihanId);
        if (!$tagihanLengkap) {
            return redirect()->back()->with('error', 'Tagihan tidak ditemukan.');
        }

        if (empty($tagihanLengkap['phone'])) {
            return redirect()->back()->with('error', 'Nomor HP penyewa tidak terdaftar.');
        }

        $penyewa = $this->penyewaModel->find($tagihanLengkap['penyewa_id']);
        $userId = $tagihanLengkap['user_id'] ?? ($penyewa['user_id'] ?? null);

        $totalBayar = $tagihanLengkap['jumlah'] + $tagihanLengkap['nominal_unik'];
        $namaBulan = $this->getListBulan()[$tagihanLengkap['bulan']] ?? $tagihanLengkap['bulan'];
        $jatuhTempoFormated = date('d F Y', strtotime($tagihanLengkap['jatuh_tempo']));

        $pesan = "Halo *{$tagihanLengkap['nama']}*,\n\n⚠️ Tagihan sewa kamar *Kamar {$tagihanLengkap['nama_kamar']}* periode *{$namaBulan} {$tagihanLengkap['tahun']}* sebesar *Rp " . number_format($totalBayar, 0, ',', '.') . "* telah *MELEWATI JATUH TEMPO* ({$jatuhTempoFormated}).\n\nStatus tagihan saat ini: *MENUNGGAK*.\n\nMohon segera lakukan pelunasan pembayaran dan unggah bukti transfer melalui aplikasi SmartKost. Terima kasih atas pengertiannya. 🙏";

        $status = $this->fonnteService->sendAndLog($userId, $tagihanLengkap['phone'], $pesan, 'tunggakan');

        if ($status) {
            return redirect()->back()->with('success', 'Notifikasi tunggakan berhasil dikirim ke WhatsApp.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengirim notifikasi WhatsApp.');
        }
    }

    // Kirim notifikasi lunas manual via WhatsApp
    public function kirimNotifLunas($tagihanId)
    {
        $tagihanLengkap = $this->tagihanModel->getTagihanLengkapById($tagihanId);
        if (!$tagihanLengkap) {
            return redirect()->back()->with('error', 'Tagihan tidak ditemukan.');
        }

        if (empty($tagihanLengkap['phone'])) {
            return redirect()->back()->with('error', 'Nomor HP penyewa tidak terdaftar.');
        }

        $penyewa = $this->penyewaModel->find($tagihanLengkap['penyewa_id']);
        $userId = $tagihanLengkap['user_id'] ?? ($penyewa['user_id'] ?? null);

        $totalBayar = $tagihanLengkap['jumlah'] + $tagihanLengkap['nominal_unik'];
        $namaBulan = $this->getListBulan()[$tagihanLengkap['bulan']] ?? $tagihanLengkap['bulan'];

        $pesan = "Halo *{$tagihanLengkap['nama']}*,\n\n🎉 Terima kasih! Pembayaran sewa kamar *Kamar {$tagihanLengkap['nama_kamar']}* untuk periode *{$namaBulan} {$tagihanLengkap['tahun']}* sebesar *Rp " . number_format($totalBayar, 0, ',', '.') . "* telah kami terima dan berstatus *LUNAS*.\n\nTerima kasih atas kerja samanya! Semoga betah tinggal di SmartKost. 😊";

        $status = $this->fonnteService->sendAndLog($userId, $tagihanLengkap['phone'], $pesan, 'approved');

        if ($status) {
            return redirect()->back()->with('success', 'Notifikasi bukti pelunasan berhasil dikirim ke WhatsApp.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengirim notifikasi WhatsApp.');
        }
    }
}
