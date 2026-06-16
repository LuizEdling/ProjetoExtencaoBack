<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('animal_states')->where('nome', 'Comunitário')->exists();
        if ($exists) {
            return;
        }

        $now = now();
        DB::table('animal_states')->insert([
            'nome' => 'Comunitário',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('animal_states')->where('nome', 'Comunitário')->delete();
    }
};
