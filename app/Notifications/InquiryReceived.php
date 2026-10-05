<?php

namespace App\Notifications;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InquiryReceived extends Notification
{
    use Queueable;

    public function __construct(protected Inquiry $inquiry)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Confirmation sent to the visitor: the subject and the opening line depend on the inquiry heading
     * (contact.email_subjects / contact.email_intros). Their own message is left out, so the form
     * cannot be used to send someone else's text to an address under the church's name.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $type = $this->inquiry->inquiry->value;

        return (new MailMessage)
            ->subject(__('contact.email_subjects.' . $type))
            ->greeting(__('contact.email_greeting', ['name' => $this->inquiry->name]))
            ->line(__('contact.email_intros.' . $type));
    }
}
