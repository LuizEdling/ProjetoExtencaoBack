<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animal_states', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->timestamps();
        });

        $now = now();
        $names = [
            'Esperando consulta',
            'Consultado',
            'Em cirurgia',
            'Esperando adoção',
            'Adotado',
        ];

        foreach ($names as $nome) {
            DB::table('animal_states')->insert([
                'nome' => $nome,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $defaultId = (int) DB::table('animal_states')
            ->where('nome', 'Esperando adoção')
            ->value('id');

        Schema::table('animals', function (Blueprint $table) use ($defaultId) {
            $table->foreignId('animal_state_id')
                ->default($defaultId)
                ->constrained('animal_states')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->dropForeign(['animal_state_id']);
        });

        Schema::table('animals', function (Blueprint $table) {
            $table->dropColumn('animal_state_id');
        });

        Schema::dropIfExists('animal_states');
    }
};
