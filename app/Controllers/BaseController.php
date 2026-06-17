<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * Kembalikan mapping nomor bulan ke nama bulan dalam Bahasa Indonesia
     * @return array
     */
    protected function getListBulan()
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

    /**
     * Get Kost Details from Database table (row with id = 1) or return default values.
     * @return array
     */
    protected function getKostDetails()
    {
        $model = new \App\Models\KostDetailModel();
        $details = $model->find(1);
        if (!$details) {
            $default = [
                'id'             => 1,
                'detail_kost'    => 'Hunian modern dan nyaman yang didesain khusus untuk mendukung produktivitas mahasiswa serta profesional muda. Nikmati hidup bebas ribet dengan fasilitas super lengkap, lingkungan yang kondusif, dan harga sewa bersahabat yang pas di kantong generasi muda.',
                'link_instagram' => 'https://instagram.com/smartkost',
                'link_tiktok'    => 'https://tiktok.com/@smartkost',
                'link_twitter'   => 'https://twitter.com/smartkost',
                'link_whatsapp'  => 'https://wa.me/6281234567890',
                'alamat'         => 'Jl. Margonda Raya No. 42, Depok, Jawa Barat',
                'no_telepon'     => '+62 812-3456-7890',
                'email'          => 'halo@smartkost.id',
                'jam_operasi'    => "Senin – Sabtu: 08.00 – 20.00 WIB\nMinggu: 09.00 – 17.00 WIB"
            ];
            $model->insert($default);
            return $default;
        }
        return $details;
    }

    /**
     * Save Kost Details to Database (row with id = 1).
     * @param array $data
     * @return void
     */
    protected function saveKostDetails($data)
    {
        $model = new \App\Models\KostDetailModel();
        if ($model->find(1)) {
            $model->update(1, $data);
        } else {
            $data['id'] = 1;
            $model->insert($data);
        }
    }
}

