<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("
                ALTER TABLE animals
                ALTER COLUMN data_ficha TYPE date USING (
                    CASE
                        WHEN data_ficha ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2}' THEN substring(data_ficha from 1 for 10)::date
                        WHEN data_ficha ~ '^[0-9]{2}/[0-9]{2}/[0-9]{4}' THEN to_date(data_ficha, 'DD/MM/YYYY')
                        ELSE CURRENT_DATE
                    END
                )
            ");
            DB::statement("
                ALTER TABLE animals
                ALTER COLUMN data_entrada TYPE date USING (
                    CASE
                        WHEN data_entrada ~ '^[0-9]{4}-[0-9]{2}-[0-9]{2}' THEN substring(data_entrada from 1 for 10)::date
                        WHEN data_entrada ~ '^[0-9]{2}/[0-9]{2}/[0-9]{4}' THEN to_date(data_entrada, 'DD/MM/YYYY')
                        ELSE CURRENT_DATE
                    END
                )
            ");
            DB::statement("
                ALTER TABLE animals
                ALTER COLUMN idade TYPE integer USING (
                    CASE WHEN trim(idade) ~ '^[0-9]+$' THEN trim(idade)::integer ELSE 0 END
                )
            ");
            DB::statement("
                ALTER TABLE animals
                ALTER COLUMN peso TYPE decimal(8,2) USING (
                    CASE
                        WHEN trim(peso) ~ '^[0-9]+(\\.[0-9]+)?$' THEN trim(peso)::decimal
                        WHEN trim(peso) ~ '^[0-9]+,[0-9]+$' THEN replace(trim(peso), ',', '.')::decimal
                        ELSE 0.01
                    END
                )
            ");

            return;
        }

        Schema::table('animals', function (Blueprint $table) {
            $table->date('data_ficha')->change();
            $table->unsignedInteger('idade')->change();
            $table->decimal('peso', 8, 2)->change();
            $table->date('data_entrada')->change();
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

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE animals ALTER COLUMN data_ficha TYPE varchar(20) USING data_ficha::text');
            DB::statement('ALTER TABLE animals ALTER COLUMN data_entrada TYPE varchar(20) USING data_entrada::text');
            DB::statement('ALTER TABLE animals ALTER COLUMN idade TYPE varchar(255) USING idade::text');
            DB::statement('ALTER TABLE animals ALTER COLUMN peso TYPE varchar(255) USING peso::text');

            return;
        }

        Schema::table('animals', function (Blueprint $table) {
            $table->string('data_ficha', 20)->change();
            $table->string('idade')->change();
            $table->string('peso')->change();
            $table->string('data_entrada', 20)->change();
        });
    }
};
