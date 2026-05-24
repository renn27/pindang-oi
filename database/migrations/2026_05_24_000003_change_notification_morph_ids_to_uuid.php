<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('push_subscriptions')) {
            Schema::table('push_subscriptions', function (Blueprint $table) {
                $table->dropIndex('push_subscriptions_subscribable_morph_idx');
            });

            Schema::table('push_subscriptions', function (Blueprint $table) {
                $table->uuid('subscribable_id')->change();
                $table->index(['subscribable_type', 'subscribable_id'], 'push_subscriptions_subscribable_morph_idx');
            });
        }

        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex('notifications_notifiable_type_notifiable_id_index');
            });

            Schema::table('notifications', function (Blueprint $table) {
                $table->uuid('notifiable_id')->change();
                $table->index(['notifiable_type', 'notifiable_id'], 'notifications_notifiable_type_notifiable_id_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('push_subscriptions')) {
            Schema::table('push_subscriptions', function (Blueprint $table) {
                $table->dropIndex('push_subscriptions_subscribable_morph_idx');
            });

            Schema::table('push_subscriptions', function (Blueprint $table) {
                $table->unsignedBigInteger('subscribable_id')->change();
                $table->index(['subscribable_type', 'subscribable_id'], 'push_subscriptions_subscribable_morph_idx');
            });
        }

        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex('notifications_notifiable_type_notifiable_id_index');
            });

            Schema::table('notifications', function (Blueprint $table) {
                $table->unsignedBigInteger('notifiable_id')->change();
                $table->index(['notifiable_type', 'notifiable_id'], 'notifications_notifiable_type_notifiable_id_index');
            });
        }
    }
};
