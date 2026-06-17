<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FasilitasModel;
use CodeIgniter\HTTP\ResponseInterface;

class FasilitasController extends BaseController
{
    // Siapkan model fasilitas supaya bisa langsung dipakai di semua method/fungsi di bawah
    // Disimpan sebagai property agar bisa dipakai di semua method
    protected $facilityModel;

    public function __construct()
    {
        $this->facilityModel = new FasilitasModel();
    }

    // Tampilkan semua data fasilitas ke halaman admin
    public function index()
    {
        $data['facilities'] = $this->facilityModel->findAll();
        $data['shared_facilities'] = $this->getSharedFacilities();
        return view('admin/fasilitas', $data);
    }

    // Simpan fasilitas baru ke database
    public function store()
    {
        // Validasi: nama fasilitas wajib diisi
        if (!$this->request->getPost('nama_fasilitas')) {
            return redirect()->back()->with('error', 'Nama fasilitas wajib diisi.');
        }

        $this->facilityModel->save([
            'nama_fasilitas' => $this->request->getPost('nama_fasilitas'),
        ]);

        return redirect()->back()->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    // Update nama fasilitas berdasarkan ID
    public function update($id)
    {
        // Validasi: nama fasilitas wajib diisi
        if (!$this->request->getPost('nama_fasilitas')) {
            return redirect()->back()->with('error', 'Nama fasilitas wajib diisi.');
        }

        $this->facilityModel->update($id, [
            'nama_fasilitas' => $this->request->getPost('nama_fasilitas'),
        ]);

        return redirect()->back()->with('success', 'Fasilitas berhasil diperbarui.');
    }

    // Hapus fasilitas berdasarkan ID
    public function delete($id)
    {
        $this->facilityModel->delete($id);
        return redirect()->back()->with('success', 'Fasilitas berhasil dihapus.');
    }

    // ============================================
    // FASILITAS BERSAMA (JSON File CRUD)
    // ============================================

    // Simpan data fasilitas bersama ke file JSON
    protected function saveSharedFacilities($data)
    {
        $path = $this->getSharedFacilitiesPath();
        if (!is_dir(WRITEPATH)) {
            mkdir(WRITEPATH, 0777, true);
        }
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
    }

    // Simpan fasilitas bersama baru
    public function storeShared()
    {
        $nama = $this->request->getPost('nama_fasilitas');
        $icon = $this->request->getPost('icon') ?: 'fa-circle-check';

        if (!$nama) {
            return redirect()->back()->with('error_bersama', 'Nama fasilitas bersama wajib diisi.');
        }

        $shared = $this->getSharedFacilities();
        
        // Generate new ID (max ID + 1)
        $newId = 1;
        if (!empty($shared)) {
            $newId = max(array_column($shared, 'id')) + 1;
        }

        $shared[] = [
            'id' => $newId,
            'nama_fasilitas' => $nama,
            'icon' => $icon
        ];

        $this->saveSharedFacilities($shared);

        return redirect()->back()->with('success_bersama', 'Fasilitas bersama berhasil ditambahkan.');
    }

    // Update fasilitas bersama
    public function updateShared($id)
    {
        $nama = $this->request->getPost('nama_fasilitas');
        $icon = $this->request->getPost('icon') ?: 'fa-circle-check';

        if (!$nama) {
            return redirect()->back()->with('error_bersama', 'Nama fasilitas bersama wajib diisi.');
        }

        $shared = $this->getSharedFacilities();
        $found = false;

        foreach ($shared as &$item) {
            if ($item['id'] == $id) {
                $item['nama_fasilitas'] = $nama;
                $item['icon'] = $icon;
                $found = true;
                break;
            }
        }

        if (!$found) {
            return redirect()->back()->with('error_bersama', 'Fasilitas bersama tidak ditemukan.');
        }

        $this->saveSharedFacilities($shared);

        return redirect()->back()->with('success_bersama', 'Fasilitas bersama berhasil diperbarui.');
    }

    // Hapus fasilitas bersama
    public function deleteShared($id)
    {
        $shared = $this->getSharedFacilities();
        $filtered = array_filter($shared, function ($item) use ($id) {
            return $item['id'] != $id;
        });

        // Re-index array values to keep the JSON file neat
        $filtered = array_values($filtered);

        $this->saveSharedFacilities($filtered);

        return redirect()->back()->with('success_bersama', 'Fasilitas bersama berhasil dihapus.');
    }
}
