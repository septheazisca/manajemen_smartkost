<?php

namespace App\Models;

use CodeIgniter\Model;

class PenanggungJawabModel extends Model
{
    protected $table            = 'penanggung_jawab';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'nama',
        'phone',
        'spesialisasi',
        'gaji_bulanan',
        'is_active',
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

    // ambil semua PJ lengkap dengan data user
    public function getPjLengkap()
    {
        return $this->select('
                penanggung_jawab.*,
                users.email,
                users.is_active as user_active
            ')
            ->join('users', 'users.id = penanggung_jawab.user_id')
            ->findAll();
    }

    // ambil PJ by user_id
    public function getPjByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }
}
