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
        $rooms = $this->kamarModel->getKamarLengkap();

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
            'tipe'        => 'required',
        ];

        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $rules['foto'] = 'max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $fotoName = null;
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $fotoName = $foto->getRandomName();
            // Create target folder if it doesn't exist
            if (!is_dir(FCPATH . 'uploads/kamar')) {
                mkdir(FCPATH . 'uploads/kamar', 0777, true);
            }
            $foto->move(FCPATH . 'uploads/kamar', $fotoName);
        }

        // Simpan data kamar, status default 'kosong' karena belum ada penyewa
        $this->kamarModel->save([
            'nomor_kamar' => $this->request->getPost('nomor_kamar'),
            'tipe'        => $this->request->getPost('tipe'),
            'lantai'      => $this->request->getPost('lantai'),
            'luas'        => $this->request->getPost('luas'),
            'harga'       => $this->request->getPost('harga'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
            'foto'        => $fotoName,
            'status_kamar_id' => 1, // 1 is kosong
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
            'tipe'        => 'required',
        ];

        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $rules['foto'] = 'max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'nomor_kamar' => $this->request->getPost('nomor_kamar'),
            'tipe'        => $this->request->getPost('tipe'),
            'lantai'      => $this->request->getPost('lantai'),
            'luas'        => $this->request->getPost('luas'),
            'harga'       => $this->request->getPost('harga'),
            'deskripsi'   => $this->request->getPost('deskripsi'),
        ];

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $fotoName = $foto->getRandomName();
            if (!is_dir(FCPATH . 'uploads/kamar')) {
                mkdir(FCPATH . 'uploads/kamar', 0777, true);
            }
            $foto->move(FCPATH . 'uploads/kamar', $fotoName);

            // Hapus foto lama dari server
            $oldKamar = $this->kamarModel->find($id);
            if (!empty($oldKamar['foto']) && file_exists(FCPATH . 'uploads/kamar/' . $oldKamar['foto'])) {
                unlink(FCPATH . 'uploads/kamar/' . $oldKamar['foto']);
            }

            $updateData['foto'] = $fotoName;
        }

        // Update data utama kamar
        $this->kamarModel->update($id, $updateData);

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
        if ((int)$kamar['status_kamar_id'] === 2) { // 2 is terisi
            return redirect()->back()->with('error', 'Kamar tidak bisa dihapus karena masih ada penyewa.');
        }

        $this->pivotModel->where('kamar_id', $id)->delete();
        
        // Hapus file foto dari disk jika ada
        if (!empty($kamar['foto'])) {
            $fotoPath = FCPATH . 'uploads/kamar/' . $kamar['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
        }

        $this->kamarModel->delete($id);

        return redirect()->back()->with('success', 'Kamar berhasil dihapus.');
    }
}
