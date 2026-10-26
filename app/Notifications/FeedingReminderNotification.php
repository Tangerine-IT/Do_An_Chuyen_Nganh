<?php

namespace App\Notifications;

use App\Models\FeedingEstimate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FeedingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public FeedingEstimate $estimate) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('mail.feeding_subject', ['pet' => $this->estimate->pet->name]))
            ->greeting(__('mail.feeding_greeting', ['name' => $notifiable->name]))
            ->line(__('mail.feeding_line', ['pet' => $this->estimate->pet->name, 'product' => $this->estimate->product->name, 'date' => $this->estimate->runs_out_at->format('d/m/Y')]))
            ->action(__('mail.feeding_action'), route('feeding.reorder', $this->estimate))
            ->line(__('mail.feeding_footer'));
    }

    public function toArray(object $notifiable): array
    {
        return ['estimate_id' => $this->estimate->id, 'runs_out_at' => $this->estimate->runs_out_at->toDateString()];
    }
}
