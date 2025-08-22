<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::updateOrCreate(
            // ['id' => 1],
            [
                'name' => 'Master Admin',
                'guard_name' => 'web' // Adjust if using a different guard
            ]
        );
    }
}


// run seeder
// php artisan db:seed
// php artisan db:seed --class=RoleSeeder