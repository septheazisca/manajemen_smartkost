<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FasilitasModel;
use App\Models\KamarFasilitasModel;
use App\Models\KamarModel;
use CodeIgniter\HTTP\ResponseInterface;

class KamarController extends BaseController
{
    // Siapkan model kamar dan fasilitas agar bisa langsung dipakai di semua fungsi
    // Disimpan sebagai property agar bisa dipakai di semua method
    protected $kamarModel;
    protected $fasilitasModel;
    protected $pivotModel;

    public function __construct()
    {
        $this->kamarModel     = new KamarModel();
        $this->fasilitasModel = new FasilitasModel();
        $this->pivotModel     = new KamarFasilitasModel();
    }

    // Tampilkan semua kamar beserta fasilitas yang dimiliki masing-masing
    public function index()
    {
        $rooms = $this->kamarModel->findAll();

        // Buat mapping fasilitas per kamar dalam bentuk array
        // Hasilnya: [kamar_id => [fasilitas_id, fasilitas_id, ...]]
        // Dipakai di view untuk tahu checkbox fasilitas mana yang harus dicentang
        $roomFacilities = [];
        if (!empty($rooms)) {
            $roomIds = array_column($rooms, 'id');
            // Optimasi N+1: Ambil semua data pivot sekaligus menggunakan whereIn
            $allPivot = $this->pivotModel->whereIn('kamar_id', $roomIds)->findAll();
            
            // Map berdasarkan kamar_id di memori PHP
            foreach ($allPivot as $pivot) {
                $roomFacilities[$pivot['kamar_id']][] = $pivot['fasilitas_id'];
            }
        }

        $data = [
            'rooms'          => $rooms,
            'facilities'     => $this->fasilitasModel->findAll(),
            'roomFacilities' => $roomFacilities,
        ];

        return view('admin/kamar', $data);
    }

    // Simpan kamar baru beserta fasilitas yang dipilih
    public function store()
    {
        // Validasi input wajib sebelum simpan ke database
        $rules = [
            'nomor_kamar' => 'required|is_unique[kamar.nomor_kamar]',
            'harga'       => 'required|numeric|greater_than[0]',
            'lantai'      => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        // Simpan data kamar, status default 'kosong' karena belum ada penyewa
        $this->kamarModel->save([
            'nomor_kamar' => $this->request->getPost('nomor_kamar'),
            'lantai'      => $this->request->getPost('lantai'),
            'luas'        => $this->request->getPost('luas'),
            'harga'       => $this->request->getPost('harga'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'status'      => 'kosong',
        ]);

        // Ambil ID kamar yang baru saja disimpan
        $kamar_id = $this->kamarModel->insertID();

        // Simpan relasi kamar-fasilitas ke tabel pivot (kamar_fasilitas)
        // Fasilitas dikirim sebagai array checkbox dari form, contoh: [1, 2, 3]
        $fasilitas = $this->request->getPost('fasilitas');
        if ($fasilitas) {
            foreach ($fasilitas as $f) {
                $this->pivotModel->save([
                    'kamar_id'     => $kamar_id,
                    'fasilitas_id' => $f,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Kamar berhasil ditambah');
    }

    // Update data kamar dan sinkronisasi fasilitas
    public function update($id)
    {
        // Validasi input wajib sebelum simpan ke database
        $rules = [
            'nomor_kamar' => "required|is_unique[kamar.nomor_kamar,id,{$id}]",
            'harga'       => 'required|numeric|greater_than[0]',
            'lantai'      => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Update data utama kamar
        $this->kamarModel->update($id, [
            'nomor_kamar' => $this->request->getPost('nomor_kamar'),
            'lantai'      => $this->request->getPost('lantai'),
            'luas'        => $this->request->getPost('luas'),
            'harga'       => $this->request->getPost('harga'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
        ]);

        // Sync fasilitas: hapus semua relasi lama lalu insert yang baru
        // Cara ini lebih simpel daripada membandingkan satu per satu mana yang berubah
        $this->pivotModel->where('kamar_id', $id)->delete();

        $fasilitas = $this->request->getPost('fasilitas');
        if ($fasilitas) {
            foreach ($fasilitas as $f) {
                $this->pivotModel->save([
                    'kamar_id'     => $id,
                    'fasilitas_id' => $f,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Kamar berhasil diupdate');
    }

    // Hapus kamar beserta semua relasi fasilitasnya
    // Relasi di tabel pivot harus dihapus dulu sebelum kamarnya,
    // agar tidak terjadi error foreign key constraint
    public function delete($id)
    {
        $kamar = $this->kamarModel->find($id);

        // Cegah error kalau ID tidak ditemukan
        if (!$kamar) {
            return redirect()->back()->with('error', 'Kamar tidak ditemukan.');
        }

        // Cegah hapus kamar yang masih terisi penyewa
        if ($kamar['status'] === 'terisi') {
            return redirect()->back()->with('error', 'Kamar tidak bisa dihapus karena masih ada penyewa.');
        }

        $this->pivotModel->where('kamar_id', $id)->delete();
        $this->kamarModel->delete($id);

        return redirect()->back()->with('success', 'Kamar berhasil dihapus.');
    }
}
