<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotifikasiLogModel;
use App\Models\PenyewaModel;
use App\Models\TagihanModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class NotifikasiController extends BaseController
{
    protected $notifikasiLogModel;
    protected $penyewaModel;
    protected $tagihanModel;
    protected $userModel;

    // ganti dengan token Fonnte kamu
    protected $fonnteToken = 'ISI_TOKEN_FONNTE_KAMU';

    public function __construct()
    {
        $this->notifikasiLogModel = new NotifikasiLogModel();
        $this->penyewaModel       = new PenyewaModel();
        $this->tagihanModel       = new TagihanModel();
        $this->userModel          = new UserModel();
    }

    // =====================
    // INDEX - Halaman kirim notifikasi
    // =====================
    public function index()
    {
        $data['penyewa']      = $this->penyewaModel->getPenyewaLengkap();
        $data['log']          = $this->notifikasiLogModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('admin/notifikasi/index', $data);
    }

    // =====================
    // KIRIM CUSTOM - Admin kirim pesan bebas ke semua atau individu
    // =====================
    public function kirimCustom()
    {
        $rules = [
            'pesan'   => 'required|min_length[5]',
            'target'  => 'required|in_list[semua,individu]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $pesan  = $this->request->getPost('pesan');
        $target = $this->request->getPost('target');

        if ($target === 'semua') {
            $penyewaList = $this->penyewaModel->getPenyewaLengkap();
        } else {
            $userId  = $this->request->getPost('user_id');
            if (!$userId) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Pilih penyewa yang dituju.');
            }
            $penyewa = $this->penyewaModel->getPenyewaByUserId($userId);
            $penyewaList = $penyewa ? [$penyewa] : [];
        }

        if (empty($penyewaList)) {
            return redirect()->back()->with('error', 'Tidak ada penyewa yang ditemukan.');
        }

        $berhasil = 0;
        $gagal    = 0;

        foreach ($penyewaList as $penyewa) {
            $noHp  = $penyewa['phone'];
            $hasil = $this->kirimWA($noHp, $pesan);

            $this->notifikasiLogModel->save([
                'user_id'         => $penyewa['user_id'],
                'no_hp'           => $noHp,
                'pesan'           => $pesan,
                'jenis'           => 'custom',
                'status_kirim'    => $hasil['success'] ? 'terkirim' : 'gagal',
                'response_fonnte' => json_encode($hasil),
                'sent_at'         => date('Y-m-d H:i:s'),
            ]);

            $hasil['success'] ? $berhasil++ : $gagal++;
        }

        $pesan = "Notifikasi terkirim ke {$berhasil} penyewa.";
        if ($gagal > 0) {
            $pesan .= " {$gagal} gagal terkirim.";
        }

        return redirect()->to('/admin/notifikasi')
            ->with('success', $pesan);
    }

    // =====================
    // KIRIM REMINDER TAGIHAN - Reminder ke penyewa yang belum bayar
    // =====================
    public function kirimReminderTagihan()
    {
        $bulan = $this->request->getPost('bulan') ?? date('m');
        $tahun = $this->request->getPost('tahun') ?? date('Y');

        // ambil tagihan yang belum lunas
        $tagihanBelumLunas = $this->tagihanModel
            ->select('
                tagihan.*,
                users.name,
                users.phone,
                kamar.nomor_kamar
            ')
            ->join('penyewa', 'penyewa.id = tagihan.penyewa_id')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id')
            ->where('tagihan.bulan', $bulan)
            ->where('tagihan.tahun', $tahun)
            ->whereIn('tagihan.status', ['pending', 'menunggak'])
            ->findAll();

        if (empty($tagihanBelumLunas)) {
            return redirect()->back()
                ->with('info', 'Semua penyewa sudah membayar tagihan bulan ini.');
        }

        $namaBulan = $this->getListBulan()[$bulan] ?? $bulan;
        $berhasil  = 0;
        $gagal     = 0;

        foreach ($tagihanBelumLunas as $tagihan) {
            $totalBayar = $tagihan['jumlah'] + $tagihan['nominal_unik'];

            $pesan = "Halo *{$tagihan['name']}*,\n\n";
            $pesan .= "Ini adalah pengingat tagihan sewa kost kamu.\n\n";
            $pesan .= "📋 *Detail Tagihan*\n";
            $pesan .= "Kamar     : {$tagihan['nomor_kamar']}\n";
            $pesan .= "Periode   : {$namaBulan} {$tahun}\n";
            $pesan .= "Jumlah    : Rp " . number_format($tagihan['jumlah'], 0, ',', '.') . "\n";
            $pesan .= "Kode unik : Rp " . number_format($tagihan['nominal_unik'], 0, ',', '.') . "\n";
            $pesan .= "Total     : Rp " . number_format($totalBayar, 0, ',', '.') . "\n";
            $pesan .= "Jatuh tempo: " . date('d/m/Y', strtotime($tagihan['jatuh_tempo'])) . "\n\n";
            $pesan .= "Mohon segera lakukan pembayaran dan upload bukti transfer di aplikasi SmarKost.\n\n";
            $pesan .= "Terima kasih 🙏";

            $noHp  = $tagihan['phone'];
            $hasil = $this->kirimWA($noHp, $pesan);

            $this->notifikasiLogModel->save([
                'user_id'         => null,
                'no_hp'           => $noHp,
                'pesan'           => $pesan,
                'jenis'           => 'tagihan',
                'status_kirim'    => $hasil['success'] ? 'terkirim' : 'gagal',
                'response_fonnte' => json_encode($hasil),
                'sent_at'         => date('Y-m-d H:i:s'),
            ]);

            $hasil['success'] ? $berhasil++ : $gagal++;
        }

        $msg = "Reminder tagihan terkirim ke {$berhasil} penyewa.";
        if ($gagal > 0) {
            $msg .= " {$gagal} gagal terkirim.";
        }

        return redirect()->to('/admin/notifikasi')->with('success', $msg);
    }

    // =====================
    // KIRIM REMINDER TUNGGAKAN - Khusus yang menunggak
    // =====================
    public function kirimReminderTunggakan()
    {
        $menunggak = $this->tagihanModel->getMenunggak();

        if (empty($menunggak)) {
            return redirect()->back()
                ->with('info', 'Tidak ada penyewa yang menunggak saat ini.');
        }

        $berhasil = 0;
        $gagal    = 0;

        foreach ($menunggak as $tagihan) {
            $namaBulan  = $this->getListBulan()[$tagihan['bulan']] ?? $tagihan['bulan'];
            $totalBayar = $tagihan['jumlah'] + $tagihan['nominal_unik'];

            $pesan = "Halo *{$tagihan['name']}*,\n\n";
            $pesan .= "⚠️ *Pemberitahuan Tunggakan*\n\n";
            $pesan .= "Kamu memiliki tagihan yang belum dibayar:\n\n";
            $pesan .= "Kamar   : {$tagihan['nomor_kamar']}\n";
            $pesan .= "Periode : {$namaBulan} {$tagihan['tahun']}\n";
            $pesan .= "Total   : Rp " . number_format($totalBayar, 0, ',', '.') . "\n\n";
            $pesan .= "Mohon segera hubungi admin atau lakukan pembayaran secepatnya.\n\n";
            $pesan .= "Terima kasih 🙏";

            $noHp  = $tagihan['phone'];
            $hasil = $this->kirimWA($noHp, $pesan);

            $this->notifikasiLogModel->save([
                'user_id'         => null,
                'no_hp'           => $noHp,
                'pesan'           => $pesan,
                'jenis'           => 'tunggakan',
                'status_kirim'    => $hasil['success'] ? 'terkirim' : 'gagal',
                'response_fonnte' => json_encode($hasil),
                'sent_at'         => date('Y-m-d H:i:s'),
            ]);

            $hasil['success'] ? $berhasil++ : $gagal++;
        }

        $msg = "Notifikasi tunggakan terkirim ke {$berhasil} penyewa.";
        if ($gagal > 0) {
            $msg .= " {$gagal} gagal terkirim.";
        }

        return redirect()->to('/admin/notifikasi')->with('success', $msg);
    }

    // =====================
    // KIRIM INFO - Admin kirim info umum ke semua penyewa
    // =====================
    public function kirimInfo()
    {
        $rules = [
            'judul' => 'required',
            'pesan' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $judul       = $this->request->getPost('judul');
        $isiPesan    = $this->request->getPost('pesan');
        $penyewaList = $this->penyewaModel->getPenyewaLengkap();

        if (empty($penyewaList)) {
            return redirect()->back()->with('error', 'Belum ada penyewa terdaftar.');
        }

        $berhasil = 0;
        $gagal    = 0;

        foreach ($penyewaList as $penyewa) {
            $pesan = "📢 *{$judul}*\n\n";
            $pesan .= $isiPesan . "\n\n";
            $pesan .= "— Admin SmarKost";

            $noHp  = $penyewa['phone'];
            $hasil = $this->kirimWA($noHp, $pesan);

            $this->notifikasiLogModel->save([
                'user_id'         => $penyewa['user_id'],
                'no_hp'           => $noHp,
                'pesan'           => $pesan,
                'jenis'           => 'info',
                'status_kirim'    => $hasil['success'] ? 'terkirim' : 'gagal',
                'response_fonnte' => json_encode($hasil),
                'sent_at'         => date('Y-m-d H:i:s'),
            ]);

            $hasil['success'] ? $berhasil++ : $gagal++;
        }

        $msg = "Info berhasil dikirim ke {$berhasil} penyewa.";
        if ($gagal > 0) {
            $msg .= " {$gagal} gagal terkirim.";
        }

        return redirect()->to('/admin/notifikasi')->with('success', $msg);
    }

    // =====================
    // LOG - Lihat riwayat notifikasi
    // =====================
    public function log()
    {
        $data['log'] = $this->notifikasiLogModel
            ->select('notifikasi_log.*, users.name')
            ->join('users', 'users.id = notifikasi_log.user_id', 'left')
            ->orderBy('notifikasi_log.created_at', 'DESC')
            ->findAll();

        return view('admin/notifikasi/log', $data);
    }

    // =====================
    // HELPER - Kirim WA via Fonnte API
    // =====================
    private function kirimWA(string $noHp, string $pesan): array
    {
        // format nomor HP — pastikan diawali 62
        $noHp = $this->formatNoHp($noHp);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => [
                'target'  => $noHp,
                'message' => $pesan,
            ],
            CURLOPT_HTTPHEADER     => [
                'Authorization: ' . $this->fonnteToken,
            ],
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return ['success' => false, 'message' => $error];
        }

        $result = json_decode($response, true);

        return [
            'success'  => isset($result['status']) && $result['status'] === true,
            'message'  => $result['reason'] ?? 'OK',
            'response' => $result,
        ];
    }

    // =====================
    // HELPER - Format nomor HP ke format 62xxx
    // =====================
    private function formatNoHp(string $noHp): string
    {
        $noHp = preg_replace('/\D/', '', $noHp);

        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        } elseif (!str_starts_with($noHp, '62')) {
            $noHp = '62' . $noHp;
        }

        return $noHp;
    }

    // =====================
    // HELPER - List nama bulan
    // =====================
    private function getListBulan(): array
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
