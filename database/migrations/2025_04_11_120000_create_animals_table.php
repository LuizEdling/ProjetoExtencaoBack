<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('raca');
            $table->string('data_ficha', 20);
            $table->string('especie');
            $table->enum('sexo', ['Macho', 'Fêmea']);
            $table->string('idade');
            $table->string('peso');
            $table->string('cor');
            $table->string('data_entrada', 20);
            $table->text('observacoes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
