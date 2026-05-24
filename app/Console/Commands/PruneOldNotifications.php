<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneOldNotifications extends Command
{
    protected $signature = 'notifications:prune {--days=30 : Delete notifications older than this many days}';

    protected $description = 'Delete old database notifications.';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $deleted = DB::table('notifications')
            ->where('created_at', '<', now()->subDays($days))
            ->delete();

        $this->info("Deleted {$deleted} notifications older than {$days} days.");

        return self::SUCCESS;
    }
}
