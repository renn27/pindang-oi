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
            $table->string('color', 100)->nullable()->after('icon');
            $table->string('font', 150)->nullable()->after('color');
            $table->dropColumn('is_special_se');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sidebar_links', function (Blueprint $table) {
            $table->dropColumn(['color', 'font']);
            $table->boolean('is_special_se')->default(false)->after('is_special');
        });
    }
};
