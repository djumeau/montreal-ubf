<?php

namespace App\Notifications;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InquirySubmitted extends Notification
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
     * Copy sent to the church's address with the full message; replying to it writes to the visitor.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->replyTo($this->inquiry->email, $this->inquiry->name)
            ->subject(__('contact.email_church_subject', [
                'inquiry' => $this->inquiry->inquiry->label(),
                'name' => $this->inquiry->name,
            ]))
            ->greeting(__('contact.email_church_greeting'))
            ->line(__('contact.email_church_from', ['name' => $this->inquiry->name, 'email' => $this->inquiry->email]))
            ->line(__('contact.email_church_message', ['inquiry' => $this->inquiry->inquiry->label()]));

        // One paragraph per line of the message, so its line breaks are kept
        foreach (preg_split('/\R+/', trim($this->inquiry->message)) as $line) {
            $mail->line($line);
        }

        return $mail->action(__('contact.email_church_action'), route('manage-inquiries'));
    }
}
