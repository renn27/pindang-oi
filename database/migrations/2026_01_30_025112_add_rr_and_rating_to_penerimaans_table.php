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
        Schema::table('penerimaans', function (Blueprint $table) {
            $table->integer('rr_terima')
                ->nullable()
                ->default(0)
                ->after('catatan');

            $table->tinyInteger('rating_terima')
                ->nullable()
                ->default(0)
                ->after('rr_terima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penerimaans', function (Blueprint $table) {
            $table->dropColumn([
                'rr_terima',
                'rating_terima',
            ]);
        });
    }
};
