<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\TagihanModel;
use App\Models\PenyewaModel;
use App\Libraries\FonnteService;

class AutomasiTagihan extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'SmartKost';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'automasi:tagihan';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Menjalankan pengecekan tagihan H-3 (Pengingat) dan Tunggakan secara otomatis.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'automasi:tagihan';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('=======================================', 'yellow');
        CLI::write(' MULAI PROSES OTOMATISASI TAGIHAN', 'green');
        CLI::write(' Waktu: ' . date('Y-m-d H:i:s'), 'yellow');
        CLI::write('=======================================', 'yellow');

        $tagihanModel  = new TagihanModel();
        $penyewaModel  = new PenyewaModel();
        $fonnteService = new FonnteService();
        $db            = \Config\Database::connect();

        // ==========================================
        // 1. PROSES H-3 PENGINGAT (REMINDER)
        // ==========================================
        CLI::write("\n[1/2] Mengecek tagihan H-3 (Jatuh tempo 3 hari lagi)...", 'cyan');
        
        // Ambil tanggal 3 hari dari sekarang
        $hMinus3 = date('Y-m-d', strtotime('+3 days'));

        $builderH3 = $db->table('tagihan');
        $builderH3->select('tagihan.*, users.id as user_id, users.name, users.phone, kamar.nomor_kamar');
        $builderH3->join('penyewa', 'penyewa.id = tagihan.penyewa_id');
        $builderH3->join('users', 'users.id = penyewa.user_id');
        $builderH3->join('kamar', 'kamar.id = penyewa.kamar_id');
        $builderH3->where('tagihan.status_tagihan_id', 1); // 1 = Pending
        $builderH3->where('DATE(tagihan.jatuh_tempo)', $hMinus3);
        
        $tagihanH3 = $builderH3->get()->getResultArray();

        if (empty($tagihanH3)) {
            CLI::write(' - Tidak ada tagihan jatuh tempo H-3 hari ini.', 'white');
        } else {
            foreach ($tagihanH3 as $t) {
                $totalBayar = $t['jumlah'] + $t['nominal_unik'];
                $namaBulan = $this->getNamaBulan($t['bulan']);
                
                $pesan = "Halo *{$t['name']}*,\n\n";
                $pesan .= "Pesan otomatis ini mengingatkan bahwa tagihan sewa kamar *{$t['nomor_kamar']}* kamu untuk periode *{$namaBulan} {$t['tahun']}* akan jatuh tempo dalam *3 HARI LAGI* (pada tanggal " . date('d M Y', strtotime($t['jatuh_tempo'])) . ").\n\n";
                $pesan .= "Total Tagihan: *Rp " . number_format($totalBayar, 0, ',', '.') . "*\n\n";
                $pesan .= "Mohon segera lakukan pembayaran melalui aplikasi SmartKost untuk menghindari denda/tunggakan. Terima kasih! 🙏";

                CLI::write(" - Mengirim WA Reminder ke: {$t['name']} ({$t['phone']})...", 'white');
                $fonnteService->sendAndLog($t['user_id'], $t['phone'], $pesan, 'pengingat_tagihan');
            }
            CLI::write(' - Selesai mengirim ' . count($tagihanH3) . ' pesan reminder.', 'green');
        }

        // ==========================================
        // 2. PROSES TUNGGAKAN (AUTO-OVERDUE)
        // ==========================================
        CLI::write("\n[2/2] Mengecek tagihan yang LEWAT jatuh tempo (Tunggakan)...", 'cyan');

        $hariIni = date('Y-m-d');

        $builderOverdue = $db->table('tagihan');
        $builderOverdue->select('tagihan.*, users.id as user_id, users.name, users.phone, kamar.nomor_kamar');
        $builderOverdue->join('penyewa', 'penyewa.id = tagihan.penyewa_id');
        $builderOverdue->join('users', 'users.id = penyewa.user_id');
        $builderOverdue->join('kamar', 'kamar.id = penyewa.kamar_id');
        $builderOverdue->where('tagihan.status_tagihan_id', 1); // 1 = Pending
        $builderOverdue->where('DATE(tagihan.jatuh_tempo) <', $hariIni); // Sudah lewat dari hari ini
        
        $tagihanOverdue = $builderOverdue->get()->getResultArray();

        if (empty($tagihanOverdue)) {
            CLI::write(' - Tidak ada tagihan baru yang menunggak hari ini.', 'white');
        } else {
            foreach ($tagihanOverdue as $t) {
                // Update status jadi menunggak (4)
                $tagihanModel->update($t['id'], ['status_tagihan_id' => 4]);

                $totalBayar = $t['jumlah'] + $t['nominal_unik'];
                $namaBulan = $this->getNamaBulan($t['bulan']);
                
                $pesan = "Halo *{$t['name']}*,\n\n";
                $pesan .= "⚠️ *PEMBERITAHUAN TUNGGAKAN* ⚠️\n\n";
                $pesan .= "Sistem mencatat bahwa tagihan sewa kamar *{$t['nomor_kamar']}* periode *{$namaBulan} {$t['tahun']}* telah MELEWATI batas waktu (Jatuh tempo: " . date('d M Y', strtotime($t['jatuh_tempo'])) . ").\n\n";
                $pesan .= "Status tagihan kamu saat ini otomatis berubah menjadi *MENUNGGAK*.\n";
                $pesan .= "Total Tagihan: *Rp " . number_format($totalBayar, 0, ',', '.') . "*\n\n";
                $pesan .= "Mohon segera selesaikan pembayaran untuk menghindari pemutusan fasilitas. Hubungi Admin jika ada kendala. Terima kasih.";

                CLI::write(" - Mengubah status jadi menunggak & kirim WA ke: {$t['name']} ({$t['phone']})...", 'red');
                $fonnteService->sendAndLog($t['user_id'], $t['phone'], $pesan, 'tunggakan');
            }
            CLI::write(' - Selesai memproses ' . count($tagihanOverdue) . ' tagihan menunggak.', 'green');
        }

        CLI::write("\n=======================================", 'yellow');
        CLI::write(' PROSES OTOMATISASI SELESAI', 'green');
        CLI::write('=======================================', 'yellow');
    }

    private function getNamaBulan($angkaBulan)
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulan[(int)$angkaBulan] ?? $angkaBulan;
    }
}
