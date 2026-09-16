<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminPasswordReset extends Notification
{
    use Queueable;

    public function __construct(protected string $password)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('dashboard/index.password_reset_email_subject'))
            ->greeting(__('dashboard/index.password_reset_email_greeting', ['name' => $notifiable->name]))
            ->line(__('dashboard/index.password_reset_email_line'))
            ->line(__('dashboard/index.password_reset_email_password', ['password' => $this->password]))
            ->line(__('dashboard/index.password_reset_email_action'));
    }
}
