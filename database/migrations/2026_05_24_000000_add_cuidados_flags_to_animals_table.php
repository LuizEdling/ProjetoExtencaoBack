<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('animals', function (Blueprint $table): void {
            $table->boolean('vermifugado')->default(false)->after('observacoes');
            $table->boolean('vacinado')->default(false)->after('vermifugado');
            $table->boolean('castrado')->default(false)->after('vacinado');
        });
    }

    public function down(): void
    {
        Schema::table('animals', function (Blueprint $table): void {
            $table->dropColumn(['vermifugado', 'vacinado', 'castrado']);
        });
    }
};
