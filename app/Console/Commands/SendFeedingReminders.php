<?php

namespace App\Console\Commands;

use App\Models\FeedingEstimate;
use App\Notifications\FeedingReminderNotification;
use Illuminate\Console\Command;

class SendFeedingReminders extends Command
{
    protected $signature = 'pets:send-feeding-reminders';

    protected $description = 'Send reminders before feeding estimates run out';

    public function handle(): int
    {
        $count = 0;
        FeedingEstimate::query()->whereNull('reminded_at')->whereDate('runs_out_at', '<=', now()->addDays(3))->with(['pet.user', 'product'])->each(function (FeedingEstimate $estimate) use (&$count): void {
            if ($estimate->update(['reminded_at' => now()])) {
                $estimate->pet->user->notify(new FeedingReminderNotification($estimate));
                $count++;
            }
        });
        $this->info("Sent {$count} feeding reminder(s).");

        return self::SUCCESS;
    }
}
