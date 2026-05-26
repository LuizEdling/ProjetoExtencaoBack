<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('adotantes')) {
            return;
        }

        Schema::table('adotantes', function (Blueprint $table) {
            if (! Schema::hasColumn('adotantes', 'rg')) {
                $table->string('rg', 20)->nullable()->after('telefone');
            }
            if (! Schema::hasColumn('adotantes', 'endereco')) {
                $table->string('endereco', 255)->nullable()->after('rg');
            }
            if (! Schema::hasColumn('adotantes', 'bairro')) {
                $table->string('bairro', 120)->nullable()->after('endereco');
            }
            if (! Schema::hasColumn('adotantes', 'cidade')) {
                $table->string('cidade', 120)->nullable()->after('bairro');
            }
            if (! Schema::hasColumn('adotantes', 'uf')) {
                $table->string('uf', 2)->nullable()->after('cidade');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('adotantes')) {
            return;
        }

        $cols = collect(['rg', 'endereco', 'bairro', 'cidade', 'uf'])
            ->filter(fn (string $c) => Schema::hasColumn('adotantes', $c))
            ->values()
            ->all();

        if ($cols === []) {
            return;
        }

        Schema::table('adotantes', function (Blueprint $table) use ($cols) {
            $table->dropColumn($cols);
        });
    }
};
