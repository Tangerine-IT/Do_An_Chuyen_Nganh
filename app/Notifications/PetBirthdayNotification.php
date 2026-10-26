<?php

namespace App\Notifications;

use App\Models\Coupon;
use App\Models\Pet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PetBirthdayNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Pet $pet, public Coupon $coupon) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('mail.birthday_subject', ['pet' => $this->pet->name]))
            ->view('emails.birthday', [
                'pet' => $this->pet,
                'coupon' => $this->coupon,
            ]);
    }
}
