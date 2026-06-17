<?php

namespace App\Models;

use CodeIgniter\Model;

class PenyewaModel extends Model
{
    protected $table            = 'penyewa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'user_id',
        'kamar_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'alamat',
        'asal_kota',
        'status_pekerjaan_id',
        'status_pernikahan_id',
        'nomor_darurat',
        'rating',
        'testimoni',
        'tampilkan_testimoni',
        'created_at',
        'updated_at'
    ];
    
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
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

    // ambil data penyewa lengkap dengan join
    public function getPenyewaLengkap()
    {
        return $this->select('
                penyewa.*,
                status_pekerjaan.nama_status AS status_pekerjaan,
                status_pernikahan.nama_status AS status_pernikahan,
                users.name,
                users.email,
                users.phone,
                users.is_active,
                users.must_change_password,
                kamar.nomor_kamar,
                kamar.harga,
                kamar.lantai
            ')
            ->join('status_pekerjaan', 'status_pekerjaan.id = penyewa.status_pekerjaan_id', 'left')
            ->join('status_pernikahan', 'status_pernikahan.id = penyewa.status_pernikahan_id', 'left')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id')
            ->where('penyewa.tanggal_keluar', null)
            ->findAll();
    }

    // ambil penyewa by user_id (untuk dashboard penyewa)
    public function getPenyewaByUserId($userId)
    {
        return $this->select('
                penyewa.*,
                status_pekerjaan.nama_status AS status_pekerjaan,
                status_pernikahan.nama_status AS status_pernikahan,
                users.name,
                users.email,
                users.phone,
                kamar.nomor_kamar,
                kamar.harga,
                kamar.lantai,
                kamar.luas
            ')
            ->join('status_pekerjaan', 'status_pekerjaan.id = penyewa.status_pekerjaan_id', 'left')
            ->join('status_pernikahan', 'status_pernikahan.id = penyewa.status_pernikahan_id', 'left')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id')
            ->where('penyewa.user_id', $userId)
            ->first();
    }
}
