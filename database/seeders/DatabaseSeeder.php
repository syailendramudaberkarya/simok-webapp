<?php

namespace Database\Seeders;

use App\Models\KartuTemplate;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Admin SiMOK',
            'email' => 'admin@simok.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        KartuTemplate::create([
            'nama_template' => 'Template Default',
            'warna_utama' => '#1E40AF',
            'warna_sekunder' => '#3B82F6',
            'is_active' => true,
        ]);
    }
}
