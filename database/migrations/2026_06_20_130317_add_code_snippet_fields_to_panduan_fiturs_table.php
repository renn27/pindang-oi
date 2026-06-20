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
        Schema::table('panduan_fiturs', function (Blueprint $table) {
            $table->string('migration_path')->nullable();
            $table->text('migration_code')->nullable();
            $table->text('model_code')->nullable();
            $table->text('controller_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panduan_fiturs', function (Blueprint $table) {
            $table->dropColumn([
                'migration_path',
                'migration_code',
                'model_code',
                'controller_code',
            ]);
        });
    }
};
