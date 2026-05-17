<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotifikasiLogModel;
use App\Models\PenyewaModel;
use App\Models\TagihanModel;
use App\Models\UserModel;

class NotifikasiController extends BaseController
{
    // Semua model dideklarasikan sebagai property agar bisa dipakai di semua method
    protected $notifikasiLogModel;
    protected $penyewaModel;
    protected $tagihanModel;
    protected $userModel;

    // Token Fonnte diambil dari file .env agar tidak hardcode di kode
    // Lebih aman karena file .env tidak ikut ke git repository
    protected $fonnteToken;

    public function __construct()
    {
        $this->notifikasiLogModel = new NotifikasiLogModel();
        $this->penyewaModel       = new PenyewaModel();
        $this->tagihanModel       = new TagihanModel();
        $this->userModel          = new UserModel();
        $this->fonnteToken        = env('FONNTE_TOKEN');
    }

    // Tampilkan halaman notifikasi beserta log 
    // terbaru untuk referensi admin
    public function index()
    {
        $data['penyewa'] = $this->penyewaModel->getPenyewaLengkap();
        $data['log']     = $this->notifikasiLogModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('admin/notifikasi/index', $data);
    }

    // Admin kirim pesan bebas ke semua penyewa atau satu penyewa tertentu
    public function kirimCustom()
    {
        $rules = [
            'pesan'  => 'required|min_length[5]',
            'target' => 'required|in_list[semua,individu]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $pesan  = $this->request->getPost('pesan');
        $target = $this->request->getPost('target');

        // Tentukan penerima berdasarkan target yang dipilih admin
        if ($target === 'semua') {
            $penyewaList = $this->penyewaModel->getPenyewaLengkap();
        } else {
            $userId = $this->request->getPost('user_id');
            if (!$userId) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Pilih penyewa yang dituju.');
            }
            $penyewa     = $this->penyewaModel->getPenyewaByUserId($userId);
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

            // Catat setiap pengiriman ke log, baik berhasil maupun gagal
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

        $msg = "Notifikasi terkirim ke {$berhasil} penyewa.";
        if ($gagal > 0) {
            $msg .= " {$gagal} gagal terkirim.";
        }

        return redirect()->to('/admin/notifikasi')->with('success', $msg);
    }

    // Kirim reminder otomatis ke penyewa yang belum bayar tagihan di bulan tertentu
    // Pesan berisi detail tagihan lengkap termasuk nominal unik untuk memudahkan transfer
    public function kirimReminderTagihan()
    {
        $bulan = $this->request->getPost('bulan') ?? date('m');
        $tahun = $this->request->getPost('tahun') ?? date('Y');

        // Ambil tagihan yang statusnya masih pending atau menunggak
        $tagihanBelumLunas = $this->tagihanModel
            ->select('tagihan.*, users.name, users.phone, kamar.nomor_kamar')
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

            // Susun pesan dengan format yang mudah dibaca di WhatsApp
            $pesan  = "Halo *{$tagihan['name']}*,\n\n";
            $pesan .= "Ini adalah pengingat tagihan sewa kost kamu.\n\n";
            $pesan .= "📋 *Detail Tagihan*\n";
            $pesan .= "Kamar      : {$tagihan['nomor_kamar']}\n";
            $pesan .= "Periode    : {$namaBulan} {$tahun}\n";
            $pesan .= "Jumlah     : Rp " . number_format($tagihan['jumlah'], 0, ',', '.') . "\n";
            $pesan .= "Kode unik  : Rp " . number_format($tagihan['nominal_unik'], 0, ',', '.') . "\n";
            $pesan .= "Total      : Rp " . number_format($totalBayar, 0, ',', '.') . "\n";
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

    // Kirim peringatan khusus ke penyewa yang statusnya menunggak
    // Berbeda dengan reminder tagihan yang bisa untuk semua yang belum bayar
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

            $pesan  = "Halo *{$tagihan['name']}*,\n\n";
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

    // Tampilkan semua riwayat pengiriman notifikasi
    // Join ke tabel users untuk dapat nama penyewa
    public function log()
    {
        $data['log'] = $this->notifikasiLogModel
            ->select('notifikasi_log.*, users.name')
            ->join('users', 'users.id = notifikasi_log.user_id', 'left')
            ->orderBy('notifikasi_log.created_at', 'DESC')
            ->findAll();

        return view('admin/notifikasi/log', $data);
    }

    // Helper private: kirim pesan WhatsApp via Fonnte API menggunakan cURL
    // Return array berisi status berhasil/gagal dan response dari Fonnte
    private function kirimWA(string $noHp, string $pesan): array
    {
        // Format nomor HP ke format internasional 62xxx sebelum dikirim
        $noHp = $this->formatNoHp($noHp);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 30,         // batas waktu tunggu response
            CURLOPT_CONNECTTIMEOUT => 10,         // batas waktu koneksi
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
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

        // Log response untuk memudahkan debugging jika ada masalah pengiriman
        // log_message('debug', 'Fonnte raw response: ' . $response);
        // log_message('debug', 'Fonnte curl error: ' . $error);

        if ($error) {
            return ['success' => false, 'message' => $error];
        }

        $result = json_decode($response, true);

        return [
            // Fonnte kadang return status true atau string 'true', keduanya ditangani
            'success'  => isset($result['status']) && ($result['status'] === true || $result['status'] === 'true'),
            'message'  => $result['reason'] ?? $result['message'] ?? 'OK',
            'response' => $result,
        ];
    }

    // Helper private: ubah nomor HP ke format internasional 62xxx
    // Contoh: 08123456789 → 628123456789
    private function formatNoHp(string $noHp): string
    {
        // Hapus semua karakter selain angka (strip spasi, strip, dll)
        $noHp = preg_replace('/\D/', '', $noHp);

        if (str_starts_with($noHp, '0')) {
            $noHp = '62' . substr($noHp, 1);
        } elseif (!str_starts_with($noHp, '62')) {
            $noHp = '62' . $noHp;
        }

        return $noHp;
    }

    // Helper private: mapping nomor bulan ke nama bulan Bahasa Indonesia
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
