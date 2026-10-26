<?php

namespace App\Notifications;

use App\Models\AdoptionApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdoptionApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AdoptionApplication $application, public string $event) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $application = $this->application->loadMissing('listing');
        $key = $this->event === 'submitted' ? 'submitted' : 'updated';

        return (new MailMessage)
            ->subject(__('mail.adoption_'.$key.'_subject', ['name' => $application->listing->name]))
            ->greeting(__('mail.adoption_greeting', ['name' => $notifiable->name]))
            ->line(__('mail.adoption_'.$key.'_line', [
                'name' => $application->listing->name,
                'status' => $application->status->label(),
            ]))
            ->action(__('mail.adoption_action'), route('adoption.show', $application->listing))
            ->line(__('mail.adoption_footer'));
    }

    public function toArray(object $notifiable): array
    {
        return ['application_id' => $this->application->id, 'status' => $this->application->status->value];
    }
}
