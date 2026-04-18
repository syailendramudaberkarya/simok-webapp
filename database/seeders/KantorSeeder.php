<?php

namespace Database\Seeders;

use App\Models\Kantor;
use Illuminate\Database\Seeder;

class KantorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add a primary national office
        Kantor::create([
            'nama_kantor' => 'Dewan Pimpinan Nasional (DPN)',
            'jenjang' => 'DPN',
            'telepon' => '021-12345678',
            'email' => 'pusat@simok.id',
            'alamat' => 'Jl. Jenderal Sudirman No. 1, Jakarta Pusat',
            'kode_pos' => '10110',
            'idpropinsi' => '31', // DKI Jakarta
            'provinsi' => 'DAERAH KHUSUS IBUKOTA JAKARTA',
            'status' => 'Aktif',
        ]);

        // Generate some random offices for other levels
        Kantor::factory()->count(10)->create();
    }
}
