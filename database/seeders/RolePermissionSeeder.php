<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed the roles and permissions.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage_members',
            'approve_members',
            'reject_members',
            'generate_cards',
            'manage_templates',
            'view_activity_logs',
            'manage_users',
            'view_dashboard',
            'view_own_profile',
            'download_own_card',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $adminRole = Role::findOrCreate('admin');
        $adminRole->givePermissionTo(Permission::all());

        $anggotaRole = Role::findOrCreate('anggota');
        $anggotaRole->givePermissionTo([
            'view_own_profile',
            'download_own_card',
        ]);
    }
}
