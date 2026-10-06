<?php

namespace App\Notifications;

use App\Support\MailText;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewUserWelcome extends Notification
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
            ->subject(__('dashboard/manage-users/index.new_user_email_subject'))
            ->greeting(__('dashboard/manage-users/index.new_user_email_greeting', ['name' => $notifiable->name]))
            ->line(__('dashboard/manage-users/index.new_user_email_line'))
            ->line(MailText::exact('dashboard/manage-users/index.new_user_email_credentials', [
                'email' => $notifiable->email,
                'password' => $this->password,
            ]))
            ->line(__('dashboard/manage-users/index.new_user_email_action'));
    }
}
