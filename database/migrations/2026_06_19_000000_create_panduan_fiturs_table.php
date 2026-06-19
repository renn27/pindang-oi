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
        Schema::create('panduan_fiturs', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('user')->index();
            $table->string('role_tab')->index();
            $table->string('menu_name');
            $table->string('slug')->index();
            $table->string('title');
            $table->text('explanation')->nullable();
            $table->string('route_target')->nullable();
            $table->text('tutorial')->nullable();
            $table->json('roles_allowed')->nullable();
            $table->text('output')->nullable();
            $table->json('form_details')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panduan_fiturs');
    }
};
