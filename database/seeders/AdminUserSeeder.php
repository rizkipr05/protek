<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@protek.test'],
            [
                'name' => 'Admin Protek',
                'username' => 'admin',
                'password' => Hash::make('secret123'),
                'role' => 'admin',
            ]
        );
    }
}
