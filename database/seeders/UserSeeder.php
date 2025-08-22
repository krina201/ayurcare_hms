<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'hms.admin@mailinator.com'],
            [
                'name' => 'HMS Admin',
                'password' => Hash::make('hms.admin'),
                'role_id' => 1,
                'is_active' => 1
            ]
        );
    }
}


// run seeder
// php artisan db:seed --class=UserSeeder