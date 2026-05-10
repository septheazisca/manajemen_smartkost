<?php

namespace App\Models;

use CodeIgniter\Model;

class PengeluaranModel extends Model
{
    protected $table            = 'pengeluaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'keterangan',
        'kategori',
        'jumlah',
        'bulan',
        'tahun',
        'pj_id',
        'maintenance_id',
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

    // total pengeluaran per bulan/tahun
    public function getTotalPengeluaran($bulan = null, $tahun = null)
    {
        $builder = $this->selectSum('jumlah', 'total');
        if ($bulan) $builder->where('bulan', $bulan);
        if ($tahun) $builder->where('tahun', $tahun);
        $result = $builder->first();
        return $result['total'] ?? 0;
    }

    // ambil pengeluaran lengkap dengan nama PJ kalau ada
    public function getPengeluaranLengkap($bulan = null, $tahun = null)
    {
        $builder = $this->select('
                pengeluaran.*,
                penanggung_jawab.nama as nama_pj
            ')
            ->join('penanggung_jawab', 'penanggung_jawab.id = pengeluaran.pj_id', 'left');

        if ($bulan) $builder->where('pengeluaran.bulan', $bulan);
        if ($tahun) $builder->where('pengeluaran.tahun', $tahun);

        return $builder->orderBy('pengeluaran.created_at', 'DESC')->findAll();
    }
}
