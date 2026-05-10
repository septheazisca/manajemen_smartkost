<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FasilitasModel;
use App\Models\KamarFasilitasModel;
use App\Models\KamarModel;
use CodeIgniter\HTTP\ResponseInterface;

class KamarController extends BaseController
{
    public function index()
    {
        $kamarModel     = new KamarModel();
        $fasilitasModel = new FasilitasModel();
        $pivotModel     = new KamarFasilitasModel();
    
        $rooms = $kamarModel->findAll();
    
        // Ambil fasilitas per kamar: [kamar_id => [fasilitas_id, ...]]
        $roomFacilities = [];
        foreach ($rooms as $room) {
            $rows = $pivotModel->where('kamar_id', $room['id'])->findAll();
            $roomFacilities[$room['id']] = array_column($rows, 'fasilitas_id');
        }
    
        $data = [
            'rooms'          => $rooms,
            'facilities'     => $fasilitasModel->findAll(),
            'roomFacilities' => $roomFacilities,   // <-- ini yang kurang
        ];
    
        return view('admin/kamar', $data);
    }

    public function store()
    {
        $kamarModel = new KamarModel();
        $pivotModel = new KamarFasilitasModel();

        $kamarModel->save([
            'nomor_kamar' => $this->request->getPost('nomor_kamar'),
            'lantai' => $this->request->getPost('lantai'),
            'luas' => $this->request->getPost('luas'),
            'harga' => $this->request->getPost('harga'),
            'deskripsi' => $this->request->getPost('deskripsi'),
        
            // default
            'status' => 'kosong',
        ]);

        $kamar_id = $kamarModel->insertID();

        // fasilitas (array)
        $fasilitas = $this->request->getPost('fasilitas');
        // contoh: [1,2,3]

        if ($fasilitas) {
            foreach ($fasilitas as $f) {
                $pivotModel->save([
                    'kamar_id' => $kamar_id,
                    'fasilitas_id' => $f
                ]);
            }
        }

        return redirect()->back()->with('success', 'Kamar berhasil ditambah');
    }

    public function update($id)
    {
        $kamarModel = new KamarModel();
        $pivotModel = new KamarFasilitasModel();
    
        $kamarModel->update($id, [
            'nomor_kamar' => $this->request->getPost('nomor_kamar'),
            'lantai'      => $this->request->getPost('lantai'),
            'luas'        => $this->request->getPost('luas'),
            'harga'       => $this->request->getPost('harga'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
        ]);
    
        // ✅ Sync fasilitas: hapus lama, insert baru
        $pivotModel->where('kamar_id', $id)->delete();
    
        $fasilitas = $this->request->getPost('fasilitas');
        if ($fasilitas) {
            foreach ($fasilitas as $f) {
                $pivotModel->save([
                    'kamar_id'    => $id,
                    'fasilitas_id' => $f,
                ]);
            }
        }
    
        return redirect()->back()->with('success', 'Kamar berhasil diupdate');
    }

    public function delete($id)
    {
        $kamarModel = new KamarModel();
        $pivotModel = new KamarFasilitasModel();

        $pivotModel->where('kamar_id', $id)->delete();
        $kamarModel->delete($id);

        return redirect()->back();
    }
}
