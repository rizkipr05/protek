<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Web Developer',   'description' => 'Divisi pengembangan web',   'is_active' => true],
            ['name' => 'Mobile Developer','description' => 'Divisi pengembangan mobile','is_active' => true],
            ['name' => 'Tenesys',         'description' => 'Divisi sistem & keamanan',   'is_active' => true],
            ['name' => 'Bisnis TIK',      'description' => 'Divisi bisnis & pengembangan IT',   'is_active' => true],
            ['name' => 'Competitive Programming',  'description' => 'Divisi competitive programming',   'is_active' => true],
            ['name' => 'Artificial Intelligent',   'description' => 'Divisi Machine Learning & AI',   'is_active' => true],
            ['name' => 'UI UX',         'description' => 'Divisi Design Mobile & Web',   'is_active' => true],   
        ];

        foreach ($data as $row) {
            Division::firstOrCreate(['name' => $row['name']], $row);
        }
    }
}
