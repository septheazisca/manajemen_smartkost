<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotifikasiLogModel;

class NotifikasiController extends BaseController
{
    protected $notifikasiLogModel;

    public function __construct()
    {
        $this->notifikasiLogModel = new NotifikasiLogModel();
    }

    // Tampilkan halaman log notifikasi WA
    public function index()
    {
        $data['log'] = $this->notifikasiLogModel
            ->select('notifikasi_log.*, users.name')
            ->join('users', 'users.id = notifikasi_log.user_id', 'left')
            ->orderBy('notifikasi_log.created_at', 'DESC')
            ->findAll();

        return view('admin/notifikasi/index', $data);
    }
}
