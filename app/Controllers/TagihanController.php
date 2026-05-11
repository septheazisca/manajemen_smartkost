<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PembayaranModel;
use App\Models\PenyewaModel;
use App\Models\TagihanModel;
use CodeIgniter\HTTP\ResponseInterface;

class TagihanController extends BaseController
{

    protected $tagihanModel;
    protected $pembayaranModel;
    protected $penyewaModel;

    public function __construct()
    {
        $this->tagihanModel   = new TagihanModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->penyewaModel   = new PenyewaModel();
    }

    // =====================
    // INDEX - Admin lihat semua tagihan
    // =====================
    public function index()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data['tagihan']          = $this->tagihanModel->getTagihanLengkap($bulan, $tahun);
        $data['pembayaran_pending'] = $this->pembayaranModel->getPembayaranPending();
        $data['bulan']            = $bulan;
        $data['tahun']            = $tahun;
        $data['list_bulan']       = $this->getListBulan();

        return view('admin/tagihan/index', $data);
    }

    // =====================
    // GENERATE - Admin generate tagihan bulanan
    // =====================
    public function generate()
    {
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');

        if (!$bulan || !$tahun) {
            return redirect()->back()->with('error', 'Bulan dan tahun wajib diisi.');
        }

        // ambil semua penyewa aktif
        $semuaPenyewa = $this->penyewaModel->getPenyewaLengkap();

        if (empty($semuaPenyewa)) {
            return redirect()->back()->with('error', 'Belum ada penyewa aktif.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $berhasil = 0;
        $skip     = 0;

        foreach ($semuaPenyewa as $penyewa) {
            // cek tagihan bulan ini sudah ada atau belum
            $sudahAda = $this->tagihanModel->isTagihanExist(
                $penyewa['id'],
                $bulan,
                $tahun
            );

            if ($sudahAda) {
                $skip++;
                continue;
            }

            // generate nominal unik per penyewa
            $nominalUnik = $this->tagihanModel->generateNominalUnik($penyewa['id']);

            // jatuh tempo tanggal 10 bulan tersebut
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

    // =====================
    // SHOW - Detail tagihan + riwayat pembayaran
    // =====================
    public function show($id)
    {
        $tagihan = $this->tagihanModel->getTagihanLengkap();

        // cari manual di array
        $tagihan = array_filter($tagihan, function ($t) use ($id) {
            return $t['id'] == $id;
        });

        $tagihan = reset($tagihan);

        if (!$tagihan) {
            return redirect()->to('/tagihan')
                ->with('error', 'Tagihan tidak ditemukan');
        }

        $data['tagihan'] = $tagihan;

        $data['pembayaran'] = $this->pembayaranModel
            ->where('tagihan_id', $id)
            ->findAll();

        return view('admin/tagihan/detail', $data);
    }

    // =====================
    // APPROVE - Admin approve pembayaran
    // =====================
    public function approve($pembayaranId)
    {
        $pembayaran = $this->pembayaranModel->find($pembayaranId);

        if (!$pembayaran) {
            return redirect()->back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. update status pembayaran
        $this->pembayaranModel->update($pembayaranId, [
            'status'       => 'approved',
            'catatan_admin' => $this->request->getPost('catatan_admin'),
            'approved_at'  => date('Y-m-d H:i:s'),
            'approved_by'  => session()->get('user_id'),
        ]);

        // 2. update status tagihan jadi lunas
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

    // =====================
    // TOLAK - Admin tolak pembayaran
    // =====================
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

        // 1. update status pembayaran jadi ditolak
        $this->pembayaranModel->update($pembayaranId, [
            'status'        => 'ditolak',
            'catatan_admin' => $catatanAdmin,
        ]);

        // 2. kembalikan status tagihan ke pending
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

    // =====================
    // TANDAI MENUNGGAK - Admin tandai tagihan sebagai menunggak
    // =====================
    public function tandaiMenunggak($tagihanId)
    {
        $tagihan = $this->tagihanModel->find($tagihanId);

        if (!$tagihan) {
            return redirect()->back()->with('error', 'Tagihan tidak ditemukan.');
        }

        if ($tagihan['status'] === 'lunas') {
            return redirect()->back()->with('error', 'Tagihan sudah lunas, tidak bisa ditandai menunggak.');
        }

        $this->tagihanModel->update($tagihanId, [
            'status' => 'menunggak',
        ]);

        return redirect()->to('/admin/tagihan')
            ->with('success', 'Tagihan berhasil ditandai sebagai menunggak.');
    }

    // =====================
    // UPLOAD BUKTI - Penyewa upload bukti transfer
    // =====================
    public function uploadBukti($tagihanId)
    {
        $userId  = session()->get('user_id');
        $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);

        if (!$penyewa) {
            return redirect()->back()->with('error', 'Data penyewa tidak ditemukan.');
        }

        $tagihan = $this->tagihanModel->find($tagihanId);

        if (!$tagihan || $tagihan['penyewa_id'] !== $penyewa['id']) {
            return redirect()->back()->with('error', 'Tagihan tidak ditemukan.');
        }

        if ($tagihan['status'] === 'lunas') {
            return redirect()->back()->with('error', 'Tagihan ini sudah lunas.');
        }

        // validasi file
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

        // simpan file
        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/bukti_transfer', $newName);

        $db = \Config\Database::connect();
        $db->transStart();

        // simpan pembayaran
        $this->pembayaranModel->save([
            'tagihan_id'     => $tagihanId,
            'jumlah_bayar'   => $tagihan['jumlah'] + $tagihan['nominal_unik'],
            'bukti_transfer' => $newName,
            'status'         => 'pending',
        ]);

        // update status tagihan jadi menunggu konfirmasi
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

    // =====================
    // TAGIHAN PENYEWA - Penyewa lihat tagihan milik sendiri
    // =====================
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
