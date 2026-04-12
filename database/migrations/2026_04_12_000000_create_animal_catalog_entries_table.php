<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animal_catalog_entries', function (Blueprint $table) {
            $table->id();
            $table->enum('kind', ['raca', 'especie', 'cor']);
            $table->string('name');
            $table->timestamps();

            $table->unique(['kind', 'name']);
        });

        $now = now();
        foreach (['Gato', 'Cachorro'] as $name) {
            DB::table('animal_catalog_entries')->insert([
                'kind' => 'especie',
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('animal_catalog_entries');
    }
};
