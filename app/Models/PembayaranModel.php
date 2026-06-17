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
        'status_pembayaran_id',
        'catatan_admin',
        'approved_at',
        'approved_by',
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

    // ambil pembayaran lengkap dengan info tagihan + penyewa
    public function getPembayaranLengkap()
    {
        return $this->select('
                pembayaran.*,
                status_pembayaran.nama_status AS status,
                status_pembayaran.badge_class,
                status_pembayaran.icon,
                tagihan.bulan,
                tagihan.tahun,
                tagihan.jumlah,
                tagihan.nominal_unik,
                users.name,
                users.phone,
                kamar.nomor_kamar
            ')
            ->join('status_pembayaran', 'status_pembayaran.id = pembayaran.status_pembayaran_id')
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
                status_pembayaran.nama_status AS status,
                status_pembayaran.badge_class,
                status_pembayaran.icon,
                tagihan.bulan,
                tagihan.tahun,
                tagihan.jumlah,
                tagihan.nominal_unik,
                users.name,
                users.phone,
                kamar.nomor_kamar
            ')
            ->join('status_pembayaran', 'status_pembayaran.id = pembayaran.status_pembayaran_id')
            ->join('tagihan', 'tagihan.id = pembayaran.tagihan_id')
            ->join('penyewa', 'penyewa.id = tagihan.penyewa_id')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id')
            ->where('status_pembayaran.nama_status', 'pending')
            ->orderBy('pembayaran.created_at', 'ASC')
            ->findAll();
    }
}
