<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table            = 'pembayaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tagihan_id',
        'jumlah_bayar',
        'bukti_transfer',
        'status',
        'catatan_admin',
        'approved_at',
        'approved_by',
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

    // ambil pembayaran lengkap dengan info tagihan + penyewa
    public function getPembayaranLengkap()
    {
        return $this->select('
                pembayaran.*,
                tagihan.bulan,
                tagihan.tahun,
                tagihan.jumlah,
                tagihan.nominal_unik,
                users.name,
                users.phone,
                kamar.nomor_kamar
            ')
            ->join('tagihan', 'tagihan.id = pembayaran.tagihan_id')
            ->join('penyewa', 'penyewa.id = tagihan.penyewa_id')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id')
            ->orderBy('pembayaran.created_at', 'DESC')
            ->findAll();
    }

    // ambil pembayaran pending (menunggu konfirmasi admin)
    public function getPembayaranPending()
    {
        return $this->select('
                pembayaran.*,
                tagihan.bulan,
                tagihan.tahun,
                tagihan.jumlah,
                tagihan.nominal_unik,
                users.name,
                users.phone,
                kamar.nomor_kamar
            ')
            ->join('tagihan', 'tagihan.id = pembayaran.tagihan_id')
            ->join('penyewa', 'penyewa.id = tagihan.penyewa_id')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id')
            ->where('pembayaran.status', 'pending')
            ->orderBy('pembayaran.created_at', 'ASC')
            ->findAll();
    }
}
