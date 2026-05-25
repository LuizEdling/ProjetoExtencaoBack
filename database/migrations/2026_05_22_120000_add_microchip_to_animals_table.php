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

        if (Schema::hasColumn('animals', 'microchip')) {
            return;
        }

        Schema::table('animals', function (Blueprint $table) {
            $table->string('microchip', 15)->nullable()->after('raca');
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
            return;
        }

        Schema::table('animals', function (Blueprint $table) {
            $table->dropColumn('microchip');
        });
    }
};
