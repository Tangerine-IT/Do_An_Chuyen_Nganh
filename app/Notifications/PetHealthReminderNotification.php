<?php

namespace App\Notifications;

use App\Models\PetHealthRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PetHealthReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PetHealthRecord $record) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $pet = $this->record->pet;

        return (new MailMessage)
            ->subject(__('mail.health_reminder_subject', ['pet' => $pet->name]))
            ->view('emails.health-reminder', [
                'notifiable' => $notifiable,
                'record' => $this->record,
            ]);
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'pet_id' => $this->record->pet_id,
            'pet_name' => $this->record->pet->name,
            'record_id' => $this->record->id,
            'type' => $this->record->type->value,
            'next_due_date' => $this->record->next_due_date->toDateString(),
            'booking_url' => route('services.index', ['pet' => $this->record->pet_id]),
        ];
    }
}
