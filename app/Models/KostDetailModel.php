<?php

namespace App\Models;

use CodeIgniter\Model;

class KostDetailModel extends Model
{
    protected $table            = 'kost_details';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'detail_kost',
        'link_instagram',
        'link_tiktok',
        'link_twitter',
        'link_whatsapp',
        'alamat',
        'no_telepon',
        'email',
        'jam_operasi',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
