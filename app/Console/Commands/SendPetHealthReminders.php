<?php

namespace App\Console\Commands;

use App\Models\PetHealthRecord;
use App\Notifications\PetHealthReminderNotification;
use Illuminate\Console\Command;

class SendPetHealthReminders extends Command
{
    protected $signature = 'pets:send-health-reminders';

    protected $description = 'Send due and overdue pet health reminders';

    public function handle(): int
    {
        $count = 0;
        PetHealthRecord::query()
            ->whereNull('reminded_at')
            ->whereNotNull('next_due_date')
            ->whereDate('next_due_date', '<=', now()->addDays(7)->toDateString())
            ->with(['pet.user'])
            ->orderBy('id')
            ->each(function (PetHealthRecord $record) use (&$count): void {
                $claimed = PetHealthRecord::query()
                    ->whereKey($record->id)
                    ->whereNull('reminded_at')
                    ->update(['reminded_at' => now()]);

                if ($claimed !== 1) {
                    return;
                }

                $record->pet->user->notify(new PetHealthReminderNotification($record));
                $count++;
            });

        $this->info("Sent {$count} health reminder(s).");

        return self::SUCCESS;
    }
}
