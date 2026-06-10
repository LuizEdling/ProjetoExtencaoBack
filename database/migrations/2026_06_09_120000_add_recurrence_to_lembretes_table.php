<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lembretes', function (Blueprint $table) {
            $table->string('tipo_recorrencia')->default('once')->after('data');
            $table->unsignedSmallInteger('intervalo_dias')->nullable()->after('tipo_recorrencia');
            $table->unsignedTinyInteger('dia_semana')->nullable()->after('intervalo_dias');
            $table->unsignedTinyInteger('dia_mes')->nullable()->after('dia_semana');
            $table->date('data_fim')->nullable()->after('dia_mes');
            $table->time('hora')->nullable()->after('data_fim');
            $table->boolean('ativo')->default(true)->after('hora');
        });
    }

    public function down(): void
    {
        Schema::table('lembretes', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_recorrencia',
                'intervalo_dias',
                'dia_semana',
                'dia_mes',
                'data_fim',
                'hora',
                'ativo',
            ]);
        });
    }
};
