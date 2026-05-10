<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FasilitasModel;
use CodeIgniter\HTTP\ResponseInterface;

class FasilitasController extends BaseController
{
    protected $facilityModel;

    public function __construct()
    {
        $this->facilityModel = new FasilitasModel();
    }

    public function index()
    {
        $data['facilities'] = $this->facilityModel->findAll();
        return view('admin/fasilitas', $data);
    }

    public function store()
    {
        $this->facilityModel->save([
            'nama_fasilitas' => $this->request->getPost('nama_fasilitas'),
        ]);

        return redirect()->back();
    }

    public function update($id)
    {
        $this->facilityModel->update($id, [
            'nama_fasilitas' => $this->request->getPost('nama_fasilitas'),
        ]);

        return redirect()->back();
    }

    public function delete($id)
    {
        $this->facilityModel->delete($id);
        return redirect()->back();
    }
}
