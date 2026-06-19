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
            $table->string('controller_path')->nullable();
            $table->string('model_path')->nullable();
            $table->string('view_path')->nullable();
            $table->string('route_definition')->nullable();
            $table->string('policy_gate')->nullable();
            $table->string('middleware')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panduan_fiturs', function (Blueprint $table) {
            $table->dropColumn([
                'controller_path',
                'model_path',
                'view_path',
                'route_definition',
                'policy_gate',
                'middleware'
            ]);
        });
    }
};
