<?php

namespace App\Models;

use CodeIgniter\Model;

class MaintenanceModel extends Model
{
    protected $table            = 'maintenance';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'penyewa_id',
        'kamar_id',
        'pj_id',
        'deskripsi',
        'foto',
        'biaya',
        'catatan_pj',
        'status',
        'assigned_at',
        'selesai_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // ambil semua maintenance lengkap
    public function getMaintenanceLengkap()
    {
        return $this->select('
                maintenance.*,
                users.name as nama_penyewa,
                kamar.nomor_kamar,
                pj_users.name as nama_pj
            ')
            ->join('penyewa', 'penyewa.id = maintenance.penyewa_id', 'left')
            ->join('users', 'users.id = penyewa.user_id', 'left')
            ->join('kamar', 'kamar.id = maintenance.kamar_id', 'left')
            ->join('penanggung_jawab', 'penanggung_jawab.id = maintenance.pj_id', 'left')
            ->join('users as pj_users', 'pj_users.id = penanggung_jawab.user_id', 'left')
            ->orderBy('maintenance.created_at', 'DESC')
            ->findAll();
    }

    // ambil maintenance by pj_id (untuk dashboard PJ)
    public function getMaintenanceByPj($pjId)
    {
        return $this->select('maintenance.*, users.name as nama_penyewa, kamar.nomor_kamar')
            ->join('penyewa', 'penyewa.id = maintenance.penyewa_id', 'left')
            ->join('users', 'users.id = penyewa.user_id', 'left')
            ->join('kamar', 'kamar.id = maintenance.kamar_id', 'left')
            ->groupStart()
                ->where('maintenance.pj_id', $pjId)
                ->orWhere('maintenance.pj_id', null)
            ->groupEnd()
            ->whereIn('maintenance.status', ['menunggu', 'proses'])
            ->orderBy('maintenance.created_at', 'DESC')
            ->findAll();
    }
}
