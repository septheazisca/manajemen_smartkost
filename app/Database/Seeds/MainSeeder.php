<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        $this->call('UserSeeder');
        $this->call('KamarSeeder');
        $this->call('FasilitasSeeder');
        $this->call('KamarFasilitasSeeder');
        $this->call('PenanggungJawabSeeder');
        $this->call('PenyewaSeeder');
        $this->call('TagihanSeeder');
        $this->call('PembayaranSeeder');
        $this->call('MaintenanceSeeder');
        $this->call('PengeluaranSeeder');
        // $this->call('NotifikasiLogSeeder');
    }
}
