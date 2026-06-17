<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KamarModel;
use App\Models\PenyewaModel;
use App\Models\FasilitasModel;
use App\Models\KamarFasilitasModel;

class HomeController extends BaseController
{
    protected $kamarModel;
    protected $penyewaModel;

    public function __construct()
    {
        $this->kamarModel   = new KamarModel();
        $this->penyewaModel = new PenyewaModel();
    }

    // Tampilkan Landing Page
    public function index()
    {
        // 1. Ambil semua kamar yang kosong (tersedia) beserta fasilitasnya
        $rooms = $this->kamarModel->where('status_kamar_id', 1)->findAll();
        
        $roomFacilities = [];
        if (!empty($rooms)) {
            $roomIds = array_column($rooms, 'id');
            $pivotModel = new KamarFasilitasModel();
            $allPivot = $pivotModel->whereIn('kamar_id', $roomIds)->findAll();
            $fasilitasModel = new FasilitasModel();
            $allFasilitas = $fasilitasModel->findAll();
            
            // Map fasilitas berdasarkan id
            $fasilitasMap = [];
            foreach ($allFasilitas as $f) {
                $fasilitasMap[$f['id']] = $f;
            }
            
            // Satukan fasilitas ke kamar masing-masing
            foreach ($allPivot as $pivot) {
                if (isset($fasilitasMap[$pivot['fasilitas_id']])) {
                    $roomFacilities[$pivot['kamar_id']][] = $fasilitasMap[$pivot['fasilitas_id']];
                }
            }
            
            foreach ($rooms as &$r) {
                $r['fasilitas'] = $roomFacilities[$r['id']] ?? [];
            }
        }

        // 2. Ambil data statistik kost
        $totalKamar   = $this->kamarModel->countAllResults();
        $kamarTersedia = $this->kamarModel->where('status_kamar_id', 1)->countAllResults();
        $penghuniAktif = $this->kamarModel->where('status_kamar_id', 2)->countAllResults();
        
        // Rata-rata rating penyewa
        $avgRatingQuery = $this->penyewaModel->where('tampilkan_testimoni', 1)->selectAvg('rating')->first();
        $avgRating = ($avgRatingQuery && $avgRatingQuery['rating']) ? round($avgRatingQuery['rating'], 1) : 4.9;
        
        // Jumlah total ulasan
        $totalUlasan = $this->penyewaModel->where('tampilkan_testimoni', 1)->where('rating !=', null)->countAllResults();

        // 3. Ambil data ulasan/testimoni dari tabel penyewa
        $testimonials = $this->penyewaModel->select('penyewa.*, users.name, users.phone, kamar.nomor_kamar')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id', 'left')
            ->where('penyewa.rating !=', null)
            ->where('penyewa.tampilkan_testimoni', 1)
            ->orderBy('penyewa.id', 'DESC')
            ->findAll();

        $fasilitasModel = new FasilitasModel();
        $sharedFacilities = $fasilitasModel->where('tipe', 'bersama')->findAll();

        $data = [
            'rooms'             => $rooms,
            'totalKamar'        => $totalKamar,
            'kamarTersedia'     => $kamarTersedia,
            'penghuniAktif'     => $penghuniAktif,
            'avgRating'         => $avgRating,
            'totalUlasan'       => $totalUlasan,
            'testimonials'      => $testimonials,
            'shared_facilities' => $sharedFacilities,
            'kost_details'      => $this->getKostDetails(),
        ];

        return view('home', $data);
    }

    // Tampilkan Detail Kamar
    public function detail($id)
    {
        // 1. Ambil data kamar beserta fasilitasnya
        $kamar = $this->kamarModel->getKamarWithFasilitas($id);
        if (!$kamar) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Kamar dengan ID $id tidak ditemukan.");
        }

        // 2. Ambil ulasan/review untuk kamar ini. Jika kosong, ambil ulasan umum sebagai fallback
        $reviews = $this->penyewaModel->select('penyewa.*, users.name, users.created_at as tgl_daftar, kamar.nomor_kamar')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id', 'left')
            ->where('penyewa.kamar_id', $id)
            ->where('penyewa.rating !=', null)
            ->findAll();

        if (empty($reviews)) {
            $reviews = $this->penyewaModel->select('penyewa.*, users.name, users.created_at as tgl_daftar, kamar.nomor_kamar')
                ->join('users', 'users.id = penyewa.user_id')
                ->join('kamar', 'kamar.id = penyewa.kamar_id', 'left')
                ->where('penyewa.rating !=', null)
                ->orderBy('penyewa.id', 'DESC')
                ->limit(3)
                ->findAll();
        }

        // Hitung rata-rata rating kamar ini (atau global jika fallback)
        $totalRating = 0;
        foreach ($reviews as $rev) {
            $totalRating += $rev['rating'];
        }
        $roomAvgRating = !empty($reviews) ? round($totalRating / count($reviews), 1) : 5.0;
        $roomReviewsCount = count($reviews);

        // 3. Ambil rekomendasi kamar serupa (tipe sama dan status kosong, exclude id saat ini)
        $similarRooms = $this->kamarModel->where('id !=', $id)
            ->where('status_kamar_id', 1)
            ->where('tipe', $kamar['tipe'] ?? 'Standard')
            ->limit(3)
            ->findAll();

        // Jika tidak cukup 3 kamar serupa, tambahkan kamar kosong tipe lain
        if (count($similarRooms) < 3) {
            $needed = 3 - count($similarRooms);
            $excludeIds = array_merge([$id], array_column($similarRooms, 'id'));
            
            $extraRooms = $this->kamarModel->where('status_kamar_id', 1)
                ->whereNotIn('id', $excludeIds)
                ->limit($needed)
                ->findAll();
                
            $similarRooms = array_merge($similarRooms, $extraRooms);
        }

        $fasilitasModel = new FasilitasModel();
        $sharedFacilities = $fasilitasModel->where('tipe', 'bersama')->findAll();

        $data = [
            'kamar'             => $kamar,
            'reviews'           => $reviews,
            'roomAvgRating'     => $roomAvgRating,
            'roomReviewsCount'  => $roomReviewsCount,
            'similarRooms'      => $similarRooms,
            'shared_facilities' => $sharedFacilities,
            'kost_details'      => $this->getKostDetails(),
        ];

        return view('detail', $data);
    }
}
