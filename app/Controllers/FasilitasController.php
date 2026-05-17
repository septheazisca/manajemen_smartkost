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
}
