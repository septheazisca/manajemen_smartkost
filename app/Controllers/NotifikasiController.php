<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotifikasiLogModel;
use App\Models\PenyewaModel;
use App\Models\TagihanModel;
use App\Models\UserModel;
use App\Libraries\FonnteService;

class NotifikasiController extends BaseController
{
    protected $notifikasiLogModel;
    protected $penyewaModel;
    protected $tagihanModel;
    protected $userModel;
    protected $fonnteService;

    public function __construct()
    {
        $this->notifikasiLogModel = new NotifikasiLogModel();
        $this->penyewaModel        = new PenyewaModel();
        $this->tagihanModel        = new TagihanModel();
        $this->userModel           = new UserModel();
        $this->fonnteService       = new FonnteService();
    }

    // Tampilkan halaman log notifikasi WA & form kirim
    public function index()
    {
        $data['log'] = $this->notifikasiLogModel
            ->select('notifikasi_log.*, users.name')
            ->join('users', 'users.id = notifikasi_log.user_id', 'left')
            ->orderBy('notifikasi_log.created_at', 'DESC')
            ->findAll();

        $penyewaList = $this->penyewaModel->getPenyewaLengkap();
        $listBulan   = $this->getListBulan();
        
        $list_penyewa = [];
        foreach ($penyewaList as $p) {
            $unpaidBill = $this->tagihanModel->select('tagihan.*, status_tagihan.nama_status as status')
                ->join('status_tagihan', 'status_tagihan.id = tagihan.status_tagihan_id')
                ->where('penyewa_id', $p['id'])
                ->whereIn('status_tagihan_id', [1, 4]) // pending or menunggak
                ->orderBy('tagihan.created_at', 'DESC')
                ->first();

            $p['latest_bill'] = null;
            if ($unpaidBill) {
                $p['latest_bill'] = [
                    'periode' => ($listBulan[str_pad($unpaidBill['bulan'], 2, '0', STR_PAD_LEFT)] ?? $unpaidBill['bulan']) . ' ' . $unpaidBill['tahun'],
                    'total' => number_format($unpaidBill['jumlah'] + $unpaidBill['nominal_unik'], 0, ',', '.'),
                    'jatuh_tempo' => date('d F Y', strtotime($unpaidBill['jatuh_tempo'])),
                    'status' => $unpaidBill['status']
                ];
            } else {
                $p['latest_bill'] = [
                    'periode' => $listBulan[date('m')] . ' ' . date('Y'),
                    'total' => number_format($p['harga'], 0, ',', '.'),
                    'jatuh_tempo' => date('d F Y', strtotime(date('Y-m-10'))),
                    'status' => 'pending'
                ];
            }
            $list_penyewa[] = $p;
        }

        $data['list_penyewa'] = $list_penyewa;
        $data['list_bulan']   = $listBulan;

        return view('admin/notifikasi/index', $data);
    }

    // Kirim pesan WA custom ke user tertentu
    public function kirimCustom()
    {
        $userId  = $this->request->getPost('user_id');
        $message = $this->request->getPost('message');

        if (!$userId || !$message) {
            return redirect()->back()->with('error', 'Penyewa dan isi pesan wajib diisi.');
        }

        $user = $this->userModel->find($userId);

        if (!$user || empty($user['phone'])) {
            return redirect()->back()->with('error', 'User tidak ditemukan atau nomor HP tidak terdaftar.');
        }

        $status = $this->fonnteService->sendAndLog($userId, $user['phone'], $message, 'custom');

        if ($status) {
            return redirect()->back()->with('success', 'Pesan kustom berhasil dikirim ke WhatsApp.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengirim pesan kustom.');
        }
    }

    // Kirim pesan massal berdasarkan status tagihan pada bulan berjalan
    public function kirimMassal()
    {
        $statusTagihan = $this->request->getPost('status_tagihan'); // 'pending' atau 'menunggak'

        if (!in_array($statusTagihan, ['pending', 'menunggak'])) {
            return redirect()->back()->with('error', 'Status tagihan tidak valid.');
        }

        $bulan = date('m');
        $tahun = date('Y');
        $statusId = $statusTagihan === 'pending' ? 1 : 4;

        $tagihans = $this->tagihanModel->select('
                tagihan.*,
                users.id as user_id,
                users.name as nama,
                users.phone,
                kamar.nomor_kamar as nama_kamar
            ')
            ->join('penyewa', 'penyewa.id = tagihan.penyewa_id')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id')
            ->where('tagihan.status_tagihan_id', $statusId)
            ->where('tagihan.bulan', $bulan)
            ->where('tagihan.tahun', $tahun)
            ->findAll();

        if (empty($tagihans)) {
            return redirect()->back()->with('error', "Tidak ada tagihan aktif berstatus '{$statusTagihan}' untuk periode {$bulan}/{$tahun}.");
        }

        $listBulan = $this->getListBulan();
        $successCount = 0;
        $failCount = 0;

        foreach ($tagihans as $t) {
            if (empty($t['phone'])) {
                $failCount++;
                continue;
            }

            $totalBayar = $t['jumlah'] + $t['nominal_unik'];
            $namaBulan = $listBulan[$t['bulan']] ?? $t['bulan'];
            $jatuhTempoFormated = date('d F Y', strtotime($t['jatuh_tempo']));

            if ($statusTagihan === 'pending') {
                $pesan = "Halo *{$t['nama']}*,\n\n📢 Ini adalah pengingat tagihan sewa kamar *Kamar {$t['nama_kamar']}* untuk periode *{$namaBulan} {$t['tahun']}*.\n\nTotal tagihan: *Rp " . number_format($totalBayar, 0, ',', '.') . "*\nJatuh Tempo: *{$jatuhTempoFormated}*\n\nMohon lakukan pembayaran dan unggah bukti transfer melalui aplikasi SmartKost. Terima kasih! 🙏";
                $type = 'tagihan';
            } else {
                $pesan = "Halo *{$t['nama']}*,\n\n⚠️ Tagihan sewa kamar *Kamar {$t['nama_kamar']}* periode *{$namaBulan} {$t['tahun']}* sebesar *Rp " . number_format($totalBayar, 0, ',', '.') . "* telah *MELEWATI JATUH TEMPO* ({$jatuhTempoFormated}).\n\nStatus tagihan saat ini: *MENUNGGAK*.\n\nMohon segera lakukan pelunasan pembayaran dan unggah bukti transfer melalui aplikasi SmartKost. Terima kasih atas pengertiannya. 🙏";
                $type = 'tunggakan';
            }

            $status = $this->fonnteService->sendAndLog($t['user_id'], $t['phone'], $pesan, $type);
            if ($status) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        $msg = "Berhasil mengirim {$successCount} notifikasi.";
        if ($failCount > 0) {
            $msg .= " Gagal {$failCount} notifikasi.";
        }

        return redirect()->back()->with('success', $msg);
    }
}
