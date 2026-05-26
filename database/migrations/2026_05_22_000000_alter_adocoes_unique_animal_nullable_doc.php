<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adocao', function (Blueprint $table): void {
            $table->unique('animal_id');
        });
    }

    public function down(): void
    {
        Schema::table('adocao', function (Blueprint $table): void {
            $table->dropUnique(['animal_id']);
        });
    }
};
