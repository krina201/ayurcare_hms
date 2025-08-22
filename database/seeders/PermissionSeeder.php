<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User permissions
            'view-user',
            'create-user',
            'edit-user',
            'delete-user',

            // Role permissions
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',

            // Permission management permissions
            'view-permission',
            'create-permission',
            'edit-permission',
            'delete-permission',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }
    }
}
