<?php

namespace App\Models;

use CodeIgniter\Model;

class KamarModel extends Model
{
    protected $table            = 'kamar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_kamar',
        'lantai',
        'luas',
        'harga',
        'status',
        'deskripsi',
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

    public function getKamarKosong()
    {
        return $this->where('status', 'kosong')->findAll();
    }

    public function getKamarWithFasilitas($id)
    {
        $kamar = $this->find($id);
        if (!$kamar) return null;

        $fasilitasModel      = new FasilitasModel();
        $kamarFasilitasModel = new KamarFasilitasModel();

        $pivotRows  = $kamarFasilitasModel->where('kamar_id', $id)->findAll();
        $fasilitasIds = array_column($pivotRows, 'fasilitas_id');

        $kamar['fasilitas'] = !empty($fasilitasIds)
            ? $fasilitasModel->whereIn('id', $fasilitasIds)->findAll()
            : [];

        return $kamar;
    }

    public function getAllWithFasilitas()
    {
        $kamarList           = $this->findAll();
        $kamarFasilitasModel = new KamarFasilitasModel();
        $fasilitasModel      = new FasilitasModel();

        foreach ($kamarList as &$kamar) {
            $pivotRows       = $kamarFasilitasModel->where('kamar_id', $kamar['id'])->findAll();
            $fasilitasIds    = array_column($pivotRows, 'fasilitas_id');
            $kamar['fasilitas'] = !empty($fasilitasIds)
                ? $fasilitasModel->whereIn('id', $fasilitasIds)->findAll()
                : [];
        }

        return $kamarList;
    }
}
