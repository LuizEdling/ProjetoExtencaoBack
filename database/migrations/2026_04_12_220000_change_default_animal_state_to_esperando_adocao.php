<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $defaultId = (int) DB::table('animal_states')
            ->where('nome', 'Esperando adoção')
            ->value('id');

        if ($defaultId === 0 || ! Schema::hasTable('animals')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement(
                'ALTER TABLE animals MODIFY animal_state_id BIGINT UNSIGNED NOT NULL DEFAULT '.$defaultId
            );

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE animals ALTER COLUMN animal_state_id SET DEFAULT '.$defaultId
            );

            return;
        }

        Schema::table('animals', function (Blueprint $table) use ($defaultId) {
            $table->unsignedBigInteger('animal_state_id')->default($defaultId)->change();
        });
    }

    public function down(): void
    {
        $defaultId = (int) DB::table('animal_states')
            ->where('nome', 'Esperando consulta')
            ->value('id');

        if ($defaultId === 0 || ! Schema::hasTable('animals')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement(
                'ALTER TABLE animals MODIFY animal_state_id BIGINT UNSIGNED NOT NULL DEFAULT '.$defaultId
            );

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE animals ALTER COLUMN animal_state_id SET DEFAULT '.$defaultId
            );

            return;
        }

        Schema::table('animals', function (Blueprint $table) use ($defaultId) {
            $table->unsignedBigInteger('animal_state_id')->default($defaultId)->change();
        });
    }
};
