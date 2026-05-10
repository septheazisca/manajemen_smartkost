<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;

class Laporan extends BaseController
{
    public function index()
    {
        $dompdf = new Dompdf();

        $html = "<h1>Laporan Kost</h1><p>Ini PDF pertama kamu</p>";

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("laporan_kost.pdf");
    }
}
