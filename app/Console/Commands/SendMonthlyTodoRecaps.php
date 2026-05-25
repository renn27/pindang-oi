<?php

namespace App\Console\Commands;

use App\Services\TodoReminderService;
use Illuminate\Console\Command;

class SendMonthlyTodoRecaps extends Command
{
    protected $signature = 'notifications:send-monthly-todo-recaps';

    protected $description = 'Send previous month To Do List recap notifications.';

    public function handle(TodoReminderService $reminders): int
    {
        $sent = $reminders->sendScheduledMonthlyRecaps();

        $this->info("Queued {$sent} monthly To Do List recap notifications.");

        return self::SUCCESS;
    }
}
