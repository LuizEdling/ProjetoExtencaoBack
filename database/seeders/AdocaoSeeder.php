<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdocaoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('adocao')->insert([
            [
                'animal_id' => 2,
                'adotante_id' => 1,
                'data_adocao' => '2026-04-05',
                'doc_adocao' => 'termo_adocao_001.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'animal_id' => 5,
                'adotante_id' => 3,
                'data_adocao' => '2026-04-12',
                'doc_adocao' => 'termo_adocao_002.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'animal_id' => 10,
                'adotante_id' => 5,
                'data_adocao' => '2026-04-18',
                'doc_adocao' => 'termo_adocao_003.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'animal_id' => 14,
                'adotante_id' => 7,
                'data_adocao' => '2026-04-25',
                'doc_adocao' => 'termo_adocao_004.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'animal_id' => 15,
                'adotante_id' => 9,
                'data_adocao' => '2026-05-02',
                'doc_adocao' => 'termo_adocao_005.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}