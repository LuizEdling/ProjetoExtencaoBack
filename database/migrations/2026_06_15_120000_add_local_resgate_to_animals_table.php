<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->string('bairro_resgate', 120)->nullable()->after('observacoes');
            $table->string('rua_resgate', 200)->nullable()->after('bairro_resgate');
        });
    }

    public function down(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->dropColumn(['bairro_resgate', 'rua_resgate']);
        });
    }
};
