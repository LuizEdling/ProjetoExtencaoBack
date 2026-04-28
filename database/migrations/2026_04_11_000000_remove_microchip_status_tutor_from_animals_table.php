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
        if (! Schema::hasTable('animals')) {
            return;
        }

        $toDrop = collect(['microchip', 'status', 'tutor_nome', 'tutor_telefone'])
            ->filter(fn (string $column) => Schema::hasColumn('animals', $column))
            ->values()
            ->all();

        if ($toDrop === []) {
            return;
        }

        Schema::table('animals', function (Blueprint $table) use ($toDrop) {
            $table->dropColumn($toDrop);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('animals')) {
            return;
        }

        if (! Schema::hasColumn('animals', 'microchip')) {
            Schema::table('animals', fn (Blueprint $table) => $table->string('microchip')->nullable());
        }
        if (! Schema::hasColumn('animals', 'status')) {
            Schema::table('animals', fn (Blueprint $table) => $table->string('status')->index());
        }
        if (! Schema::hasColumn('animals', 'tutor_nome')) {
            Schema::table('animals', fn (Blueprint $table) => $table->string('tutor_nome')->nullable());
        }
        if (! Schema::hasColumn('animals', 'tutor_telefone')) {
            Schema::table('animals', fn (Blueprint $table) => $table->string('tutor_telefone')->nullable());
        }
    }
};
