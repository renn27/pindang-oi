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
        Schema::table('sidebar_links', function (Blueprint $table) {
            $table->dropColumn('font');
            $table->string('background_color', 100)->nullable()->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sidebar_links', function (Blueprint $table) {
            $table->string('font', 150)->nullable()->after('color');
            $table->dropColumn('background_color');
        });
    }
};
