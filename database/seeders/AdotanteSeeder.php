<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdotanteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('adotantes')->insert([
            [
                'nome' => 'João Silva',
                'cpf' => '12345678901',
                'telefone' => '42999990001',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Maria Oliveira',
                'cpf' => '12345678902',
                'telefone' => '42999990002',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Pedro Santos',
                'cpf' => '12345678903',
                'telefone' => '42999990003',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Ana Costa',
                'cpf' => '12345678904',
                'telefone' => '42999990004',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Lucas Ferreira',
                'cpf' => '12345678905',
                'telefone' => '42999990005',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Juliana Martins',
                'cpf' => '12345678906',
                'telefone' => '42999990006',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Carlos Souza',
                'cpf' => '12345678907',
                'telefone' => '42999990007',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Fernanda Lima',
                'cpf' => '12345678908',
                'telefone' => '42999990008',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Ricardo Almeida',
                'cpf' => '12345678909',
                'telefone' => '42999990009',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nome' => 'Patrícia Rocha',
                'cpf' => '12345678910',
                'telefone' => '42999990010',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}