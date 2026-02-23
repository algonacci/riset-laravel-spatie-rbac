<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Wajib clear cache permission supaya data role/permission terbaru langsung terbaca.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'tambah artikel',
            'edit artikel',
            'hapus artikel',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $roleAdmin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $roleAdmin->syncPermissions($permissions);

        $rolePenulis = Role::firstOrCreate([
            'name' => 'penulis',
            'guard_name' => 'web',
        ]);
        $rolePenulis->syncPermissions([
            'tambah artikel',
            'edit artikel',
        ]);

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Super Admin', 'password' => 'password']
        );
        $adminUser->syncRoles([$roleAdmin]);

        $penulisUser = User::firstOrCreate(
            ['email' => 'penulis@gmail.com'],
            ['name' => 'Penulis Biasa', 'password' => 'password']
        );
        $penulisUser->syncRoles([$rolePenulis]);

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => 'password']
        );
    }
}
