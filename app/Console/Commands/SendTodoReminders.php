<?php

namespace App\Console\Commands;

use App\Services\TodoReminderService;
use Illuminate\Console\Command;

class SendTodoReminders extends Command
{
    protected $signature = 'notifications:send-todo-reminders';

    protected $description = 'Send scheduled To Do List reminder notifications.';

    public function handle(TodoReminderService $reminders): int
    {
        $sent = $reminders->sendScheduledReminders();

        $this->info("Queued {$sent} To Do List reminder notifications.");

        return self::SUCCESS;
    }
}
