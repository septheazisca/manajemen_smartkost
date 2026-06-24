<?php

namespace App\Models;

use CodeIgniter\Model;

class TagihanModel extends Model
{
    protected $table            = 'tagihan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'penyewa_id',
        'bulan',
        'tahun',
        'jumlah',
        'nominal_unik',
        'status_tagihan_id',
        'jatuh_tempo',
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

    public function generateNominalUnik($penyewaId)
    {
        // nominal unik berdasarkan penyewa_id, range 1-999
        return ($penyewaId % 999) + 1;
    }

    // ambil tagihan lengkap dengan info penyewa
    public function getTagihanLengkap($bulan = null, $tahun = null)
    {
        $builder = $this->select('
            tagihan.*,
            status_tagihan.nama_status AS status,
            status_tagihan.badge_class,
            status_tagihan.icon,
            users.name AS nama,
            users.phone,
            kamar.nomor_kamar AS nama_kamar
        ')
            ->join('status_tagihan', 'status_tagihan.id = tagihan.status_tagihan_id')
            ->join('penyewa', 'penyewa.id = tagihan.penyewa_id')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id');

        if ($bulan) $builder->where('tagihan.bulan', $bulan);
        if ($tahun) $builder->where('tagihan.tahun', $tahun);

        return $builder->orderBy('tagihan.created_at', 'DESC')->findAll();
    }

    // Ambil detail satu tagihan berdasarkan ID
    public function getTagihanLengkapById($id)
    {
        return $this->select('
            tagihan.*,
            status_tagihan.nama_status AS status,
            status_tagihan.badge_class,
            status_tagihan.icon,
            users.name AS nama,
            users.phone,
            kamar.nomor_kamar AS nama_kamar
        ')
            ->join('status_tagihan', 'status_tagihan.id = tagihan.status_tagihan_id')
            ->join('penyewa', 'penyewa.id = tagihan.penyewa_id')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id')
            ->where('tagihan.id', $id)
            ->first();
    }

    // Generate tagihan massal untuk semua penyewa aktif
    public function generateBulk($semuaPenyewa, $bulan, $tahun)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $berhasil = 0;
        $skip     = 0;

        foreach ($semuaPenyewa as $penyewa) {
            // Skip penyewa yang sudah punya tagihan di bulan & tahun yang sama
            $sudahAda = $this->isTagihanExist($penyewa['id'], $bulan, $tahun);

            if ($sudahAda) {
                $skip++;
                continue;
            }

            // Nominal unik berbeda tiap penyewa
            $nominalUnik = $this->generateNominalUnik($penyewa['id']);
            $jatuhTempo = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-10';

            $this->save([
                'penyewa_id'   => $penyewa['id'],
                'bulan'        => $bulan,
                'tahun'        => $tahun,
                'jumlah'       => $penyewa['harga'],
                'nominal_unik' => $nominalUnik,
                'status_tagihan_id' => 1, // 1 is pending
                'jatuh_tempo'  => $jatuhTempo,
            ]);

            $berhasil++;
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return false;
        }

        return ['berhasil' => $berhasil, 'skip' => $skip];
    }
    

    // ambil tagihan by penyewa_id (untuk dashboard penyewa)
    public function getTagihanByPenyewa($penyewaId)
    {
        return $this->select('tagihan.*, status_tagihan.nama_status AS status, status_tagihan.badge_class, status_tagihan.icon')
            ->join('status_tagihan', 'status_tagihan.id = tagihan.status_tagihan_id')
            ->where('penyewa_id', $penyewaId)
            ->orderBy('tahun', 'DESC')
            ->orderBy('bulan', 'DESC')
            ->findAll();
    }

    // cek tagihan bulan ini sudah ada atau belum
    public function isTagihanExist($penyewaId, $bulan, $tahun)
    {
        return $this->where('penyewa_id', $penyewaId)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();
    }

    // ambil yang menunggak
    public function getMenunggak()
    {
        return $this->select('
                tagihan.*,
                status_tagihan.nama_status AS status,
                status_tagihan.badge_class,
                status_tagihan.icon,
                users.name,
                users.phone,
                kamar.nomor_kamar,
                penyewa.user_id
            ')
            ->join('status_tagihan', 'status_tagihan.id = tagihan.status_tagihan_id')
            ->join('penyewa', 'penyewa.id = tagihan.penyewa_id')
            ->join('users', 'users.id = penyewa.user_id')
            ->join('kamar', 'kamar.id = penyewa.kamar_id')
            ->where('status_tagihan.nama_status', 'menunggak')
            ->findAll();
    }

    // Auto-update tagihan berstatus pending yang sudah melewati tanggal jatuh tempo menjadi menunggak
    public function checkAndUpdateOverdue()
    {
        return $this->where('status_tagihan_id', 1) // 1 = pending
            ->where('jatuh_tempo <', date('Y-m-d'))
            ->set(['status_tagihan_id' => 4]) // 4 = menunggak
            ->update();
    }
}
