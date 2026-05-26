<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratacao', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('adocao_id')->unique()->constrained('adocao')->cascadeOnDelete();
            $table->longText('html_gerado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratacao');
    }
};
