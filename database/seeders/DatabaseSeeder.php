<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        DB::table('personal_access_tokens')->delete();

        $this->call(UserSeeder::class);

        $this->call([
            AnimalSeeder::class,
            AdotanteSeeder::class,
            AdocaoSeeder::class,
            LembreteSeeder::class,
        ]);
    }
}
