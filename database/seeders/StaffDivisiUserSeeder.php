<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffDivisiUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'staff_divisi@protek.test'],
            [
                'name' => 'Staff Divisi',
                'username' => 'staff_divisi',
                'password' => Hash::make('secret123'),
                'role' => 'staff_divisi',
            ]
        );
    }
}
