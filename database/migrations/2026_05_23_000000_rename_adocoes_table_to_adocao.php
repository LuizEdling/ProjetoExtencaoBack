<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ambientes que já rodaram a migration antiga com tabela "adocoes".
     */
    public function up(): void
    {
        if (Schema::hasTable('adocoes') && ! Schema::hasTable('adocao')) {
            Schema::rename('adocoes', 'adocao');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('adocao') && ! Schema::hasTable('adocoes')) {
            Schema::rename('adocao', 'adocoes');
        }
    }
};
