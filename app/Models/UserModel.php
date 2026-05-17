<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'must_change_password',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = false;

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
    protected $validationMessages   = [
        // 'name' => [
        //     'required'   => 'Nama wajib diisi.',
        //     'min_length' => 'Nama minimal 3 karakter.',
        // ],
        // 'email' => [
        //     'required'    => 'Email wajib diisi.',
        //     'valid_email' => 'Format email tidak valid.',
        //     'is_unique'   => 'Email sudah digunakan.',
        // ],
        // 'password' => [
        //     'required'   => 'Password wajib diisi.',
        //     'min_length' => 'Password minimal 6 karakter.',
        // ],
    ];
    protected $skipValidation       = true;
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

    // jangan pernah return field password di select biasa
    public function findAllSafe()
    {
        return $this->select('
                id,
                name,
                email,
                role,
                phone,
                is_active,
                must_change_password,
                created_at
            ')
            ->findAll();
    }
}
