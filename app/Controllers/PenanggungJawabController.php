<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MaintenanceModel;
use App\Models\PenanggungJawabModel;
use App\Models\PengeluaranModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class PenanggungJawabController extends BaseController
{
    // Semua model dideklarasikan sebagai property agar bisa dipakai di semua method
    protected $pjModel;
    protected $userModel;
    protected $pengeluaranModel;

    public function __construct()
    {
        $this->pjModel          = new PenanggungJawabModel();
        $this->userModel        = new UserModel();
        $this->pengeluaranModel = new PengeluaranModel();
    }

    // Tampilkan semua data PJ ke halaman admin
    public function index()
    {
        $data['pj_list'] = $this->pjModel->getPjLengkap();
        return view('admin/pj/index', $data);
    }

    // Tambah PJ baru beserta akun login-nya
    // Data PJ disimpan di 2 tabel: users (untuk login) dan penanggung_jawab (untuk data kerja)
    public function store()
    {
        $rules = [
            'nama'         => 'required|min_length[3]',
            'email'        => 'required|valid_email|is_unique[users.email]',
            'phone'        => 'required',
            'spesialisasi' => 'permit_empty',
            'gaji_bulanan' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $phone = $this->request->getPost('phone');

        // 1. Buat akun user untuk PJ, password default = nomor HP
        // must_change_password = 1 agar PJ wajib ganti password saat pertama login
        $this->userModel->save([
            'name'                 => $this->request->getPost('nama'),
            'email'                => $this->request->getPost('email'),
            'phone'                => $phone,
            'password'             => password_hash($phone, PASSWORD_DEFAULT),
            'role'                 => 'pj',
            'is_active'            => 1,
            'must_change_password' => 1,
        ]);

        $userId = $this->userModel->getInsertID();

        // 2. Simpan data kerja PJ, dihubungkan ke akun user lewat user_id
        $this->pjModel->save([
            'user_id'      => $userId,
            'nama'         => $this->request->getPost('nama'),
            'phone'        => $phone,
            'spesialisasi' => $this->request->getPost('spesialisasi'),
            'gaji_bulanan' => $this->request->getPost('gaji_bulanan'),
            'is_active'    => 1,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambah penanggung jawab.');
        }

        return redirect()->to('/admin/pj')
            ->with('success', 'Penanggung jawab berhasil ditambahkan. Password default: nomor HP.');
    }

    // Update data PJ di 2 tabel sekaligus dalam satu transaction
    // Validasi email: kalau email tidak berubah, skip cek is_unique agar tidak error
    public function update($id)
    {
        $pj = $this->pjModel->find($id);

        if (!$pj) {
            return redirect()->back()->with('error', 'Data penanggung jawab tidak ditemukan.');
        }

        $user = $this->userModel->find($pj['user_id']);

        $rules = [
            'nama'         => 'required|min_length[3]',
            'phone'        => 'required',
            'spesialisasi' => 'permit_empty',
            'gaji_bulanan' => 'required|numeric',
            // Cek is_unique hanya kalau email memang berubah
            'email'        => $this->request->getPost('email') !== $user['email']
                ? 'required|valid_email|is_unique[users.email]'
                : 'required|valid_email',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Update data login di tabel users
        $this->userModel->update($pj['user_id'], [
            'name'  => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
        ]);

        // Update data kerja di tabel penanggung_jawab
        $this->pjModel->update($id, [
            'nama'         => $this->request->getPost('nama'),
            'phone'        => $this->request->getPost('phone'),
            'spesialisasi' => $this->request->getPost('spesialisasi'),
            'gaji_bulanan' => $this->request->getPost('gaji_bulanan'),
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate data penanggung jawab.');
        }

        return redirect()->to('/admin/pj')
            ->with('success', 'Data penanggung jawab berhasil diupdate.');
    }

    // Aktifkan atau nonaktifkan PJ
    // Status diupdate di 2 tabel agar konsisten: PJ nonaktif tidak bisa login
    public function toggleStatus($id)
    {
        $pj = $this->pjModel->find($id);

        if (!$pj) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Toggle: kalau aktif jadi nonaktif, kalau nonaktif jadi aktif
        $newStatus = $pj['is_active'] == 1 ? 0 : 1;

        $db = \Config\Database::connect();
        $db->transStart();

        $this->pjModel->update($id, ['is_active' => $newStatus]);
        $this->userModel->update($pj['user_id'], ['is_active' => $newStatus]);

        $db->transComplete();

        $status = $newStatus == 1 ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->to('/admin/pj')
            ->with('success', "Penanggung jawab berhasil {$status}.");
    }

    // Catat pembayaran gaji PJ ke tabel pengeluaran
    // Ada pengecekan dobel bayar: gaji bulan yang sama tidak bisa dibayar 2 kali
    public function bayarGaji($id)
    {
        $pj = $this->pjModel->find($id);

        if (!$pj) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'bulan' => 'required',
            'tahun' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');

        // Cegah dobel bayar: cek apakah sudah ada record gaji bulan ini
        $sudahBayar = $this->pengeluaranModel
            ->where('pj_id', $id)
            ->where('kategori_pengeluaran_id', 2)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        if ($sudahBayar) {
            return redirect()->back()
                ->with('error', "Gaji {$pj['nama']} untuk bulan {$bulan}/{$tahun} sudah dibayar.");
        }

        // Kalau admin input jumlah berbeda, pakai itu. Kalau tidak, pakai gaji default
        $jumlah = $this->request->getPost('jumlah') ?: $pj['gaji_bulanan'];

        $this->pengeluaranModel->save([
            'keterangan'              => "Gaji penanggung jawab: {$pj['nama']} ({$bulan}/{$tahun})",
            'kategori_pengeluaran_id' => 2,
            'jumlah'                  => $jumlah,
            'bulan'                   => $bulan,
            'tahun'                   => $tahun,
            'pj_id'                   => $id,
            'created_at'              => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/pj')
            ->with('success', "Gaji {$pj['nama']} bulan {$bulan}/{$tahun} berhasil dicatat.");
    }

    // Tampilkan riwayat semua pembayaran gaji untuk satu PJ
    public function riwayatGaji($id)
    {
        $pj = $this->pjModel->getPjLengkap($id);

        if (!$pj) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $data['pj']      = $pj;
        $data['riwayat'] = $this->pengeluaranModel
            ->where('pj_id', $id)
            ->where('kategori_pengeluaran_id', 2)
            ->orderBy('tahun', 'DESC')
            ->orderBy('bulan', 'DESC')
            ->findAll();

        return view('admin/pj/riwayat_gaji', $data);
    }

    // Export riwayat gaji ke file Excel (.xlsx)
    public function exportGaji($id)
    {
        $pj = $this->pjModel->getPjLengkap($id);

        if (!$pj) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $riwayat = $this->pengeluaranModel
            ->where('pj_id', $id)
            ->where('kategori_pengeluaran_id', 2)
            ->orderBy('tahun', 'DESC')
            ->orderBy('bulan', 'DESC')
            ->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Gaji PJ');

        // Header judul
        $sheet->setCellValue('A1', 'LAPORAN RIWAYAT GAJI PENANGGUNG JAWAB');
        $sheet->setCellValue('A2', 'Nama: ' . $pj['nama']);
        $sheet->setCellValue('A3', 'Spesialisasi: ' . ($pj['spesialisasi'] ?: 'Umum'));
        $sheet->setCellValue('A4', 'Gaji Pokok / Bulan: Rp ' . number_format($pj['gaji_bulanan'], 0, ',', '.'));
        $sheet->setCellValue('A5', 'Digenerate: ' . date('d/m/Y H:i:s'));

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A4')->getFont()->setBold(true);

        // Header tabel
        $headers = ['No', 'Bulan & Tahun', 'Keterangan', 'Nominal Gaji (Rp)', 'Tanggal Pembayaran'];
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue($col . '7', $header);
            $sheet->getStyle($col . '7')->getFont()->setBold(true);
            $sheet->getStyle($col . '7')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('7F77DD');
            $sheet->getStyle($col . '7')->getFont()->getColor()->setRGB('FFFFFF');
        }

        // Isi data
        $row = 8;
        $no  = 1;
        $total = 0;
        $listBulan = $this->getListBulan();
        foreach ($riwayat as $r) {
            $namaBulan = $listBulan[$r['bulan']] ?? $r['bulan'];
            $periode = $namaBulan . ' ' . $r['tahun'];

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $periode);
            $sheet->setCellValue('C' . $row, $r['keterangan']);
            $sheet->setCellValue('D' . $row, (int) $r['jumlah']);
            $sheet->setCellValue('E' . $row, date('d/m/Y', strtotime($r['created_at'])));

            // Format rupiah kolom D
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');

            // Warna baris selang-seling
            if ($no % 2 == 0) {
                $sheet->getStyle('A' . $row . ':E' . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F3F2FF');
            }

            $total += $r['jumlah'];
            $row++;
        }

        // Baris total
        $sheet->setCellValue('C' . $row, 'TOTAL GAJI DITERIMA');
        $sheet->setCellValue('D' . $row, (int) $total);
        $sheet->getStyle('C' . $row . ':D' . $row)->getFont()->setBold(true);
        $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('C' . $row . ':D' . $row)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('EEEDFE');

        // Auto size kolom
        foreach (['A', 'B', 'C', 'D', 'E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Border tabel
        $sheet->getStyle('A7:E' . ($row))->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Output
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'riwayat-gaji-' . urlencode(strtolower(str_replace(' ', '-', $pj['nama']))) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }


    // Reset password PJ kembali ke nomor HP
    // must_change_password = 1 agar PJ wajib ganti password lagi saat login
    public function resetPassword($id)
    {
        $pj = $this->pjModel->find($id);

        if (!$pj) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $this->userModel->update($pj['user_id'], [
            'password'             => password_hash((string) $pj['phone'], PASSWORD_DEFAULT),
            'must_change_password' => 1,
        ]);

        return redirect()->to('/admin/pj')
            ->with('success', 'Password berhasil direset ke nomor HP penanggung jawab.');
    }

    // Export riwayat gaji untuk PJ yang sedang login
    public function exportGajiSelf()
    {
        $userId = session()->get('user_id');
        $pj     = $this->pjModel->getPjByUserId($userId);

        if (!$pj) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        return $this->exportGaji($pj['id']);
    }
}
