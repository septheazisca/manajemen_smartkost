<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DetailKostController extends BaseController
{
    public function index()
    {
        $data['kost_details'] = $this->getKostDetails();
        return view('admin/detail_kost', $data);
    }

    public function update()
    {
        $data = [
            'detail_kost'    => $this->request->getPost('detail_kost'),
            'link_instagram' => $this->request->getPost('link_instagram'),
            'link_tiktok'    => $this->request->getPost('link_tiktok'),
            'link_twitter'   => $this->request->getPost('link_twitter'),
            'link_whatsapp'  => $this->request->getPost('link_whatsapp'),
            'alamat'         => $this->request->getPost('alamat'),
            'no_telepon'     => $this->request->getPost('no_telepon'),
            'email'          => $this->request->getPost('email'),
            'jam_operasi'    => $this->request->getPost('jam_operasi'),
        ];

        // Validasi sederhana: detail kost wajib diisi
        if (empty($data['detail_kost'])) {
            return redirect()->back()->with('error', 'Deskripsi detail kost wajib diisi.');
        }

        $this->saveKostDetails($data);

        return redirect()->to('/admin/detail-kost')->with('success', 'Detail kost berhasil diperbarui.');
    }
}
