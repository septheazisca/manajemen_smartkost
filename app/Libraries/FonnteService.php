<?php

namespace App\Libraries;

use App\Models\NotifikasiLogModel;

class FonnteService
{
    protected $fonnteToken;
    protected $notifikasiLogModel;

    public function __construct()
    {
        $this->fonnteToken        = env('FONNTE_TOKEN');
        $this->notifikasiLogModel = new NotifikasiLogModel();
    }

    /**
     * Kirim pesan WhatsApp dan catat hasilnya ke tabel log.
     *
     * @param int|null $userId ID user penerima (bisa null jika untuk nomor admin umum)
     * @param string $phone Nomor HP penerima
     * @param string $message Isi pesan WhatsApp
     * @param string $type Jenis notifikasi (e.g. tagihan, approved, ditolak, custom)
     * @return bool Status berhasil/gagal mengirim
     */
    public function sendAndLog($userId, string $phone, string $message, string $type): bool
    {
        $result = $this->send($phone, $message);

        $this->notifikasiLogModel->save([
            'user_id'         => $userId,
            'no_hp'           => $phone,
            'pesan'           => $message,
            'jenis'           => $type,
            'status_kirim'    => $result['success'] ? 'terkirim' : 'gagal',
            'response_fonnte' => json_encode($result),
            'sent_at'         => date('Y-m-d H:i:s'),
        ]);

        return $result['success'];
    }

    /**
     * Kirim WhatsApp via API Fonnte menggunakan cURL.
     *
     * @param string $phone Nomor HP penerima
     * @param string $message Isi pesan WhatsApp
     * @return array Status pengiriman dan detail response
     */
    public function send(string $phone, string $message): array
    {
        $formattedPhone = $this->formatPhone($phone);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POSTFIELDS     => [
                'target'  => $formattedPhone,
                'message' => $message,
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
            'success'  => isset($result['status']) && ($result['status'] === true || $result['status'] === 'true'),
            'message'  => $result['reason'] ?? $result['message'] ?? 'OK',
            'response' => $result,
        ];
    }

    /**
     * Format nomor HP ke standar internasional 62xxx.
     *
     * @param string $phone Nomor HP
     * @return string Nomor HP terformat
     */
    public function formatPhone(string $phone): string
    {
        // Hapus semua karakter non-digit
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
