<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE penerimaans 
            MODIFY status ENUM('Sedang Diperiksa', 'Diterima', 'Revisi')
            DEFAULT 'Sedang Diperiksa'
        ");
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE penerimaans 
            MODIFY status ENUM('Diterima', 'Revisi')
        ");
    }
};
