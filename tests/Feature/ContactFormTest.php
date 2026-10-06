<?php

namespace Tests\Feature;

use App\Enums\InquiryType;
use App\Models\Inquiry;
use App\Notifications\InquiryReceived;
use App\Notifications\InquirySubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private function submit(array $overrides = [])
    {
        // The form must have been on screen for a few seconds (bot check)
        return $this->withSession(['contact_form_rendered_at' => now()->timestamp - 10])
            ->post(route('contact.store'), array_merge([
                'name' => 'Marie Tremblay',
                'email' => 'marie@example.com',
                'inquiring_about' => InquiryType::WORSHIP->value,
                'message' => "First line\nSecond line",
            ], $overrides));
    }

    public function test_submitting_the_form_saves_the_inquiry_and_confirms_to_the_visitor_without_their_message(): void
    {
        Notification::fake();

        $this->submit()->assertSessionHas('status');

        $this->assertSame(InquiryType::WORSHIP, Inquiry::sole()->inquiry);

        Notification::assertSentOnDemand(
            InquiryReceived::class,
            function (InquiryReceived $notification, array $channels, AnonymousNotifiable $notifiable) {
                $mail = $notification->toMail($notifiable);

                return $notifiable->routes['mail'] === ['marie@example.com' => 'Marie Tremblay']
                    && $mail->cc === []
                    && $mail->subject === __('contact.email_subjects.worship')
                    && $mail->introLines === [__('contact.email_intros.worship')]
                    && !str_contains((string) $mail->render(), 'First line');
            }
        );
    }

    public function test_submitting_the_form_sends_the_full_message_to_the_church_with_the_visitor_as_reply_to(): void
    {
        Notification::fake();
        config(['mail.inquiries_address' => 'church@example.com']);

        $this->submit();

        Notification::assertSentOnDemand(
            InquirySubmitted::class,
            function (InquirySubmitted $notification, array $channels, AnonymousNotifiable $notifiable) {
                $mail = $notification->toMail($notifiable);

                return $notifiable->routes['mail'] === 'church@example.com'
                    && $mail->replyTo === [['marie@example.com', 'Marie Tremblay']]
                    && str_contains($mail->subject, InquiryType::WORSHIP->label())
                    && in_array('First line', $mail->introLines)
                    && in_array('Second line', $mail->introLines);
            }
        );
    }

    public function test_every_inquiry_heading_has_its_subject_and_opening_line_in_both_languages(): void
    {
        foreach (['en_CA', 'fr_CA'] as $locale) {
            foreach (InquiryType::cases() as $type) {
                foreach (['email_subjects', 'email_intros'] as $group) {
                    $key = "contact.$group.{$type->value}";
                    $this->assertNotSame($key, __($key, [], $locale), "$key is missing in $locale");
                }
            }
        }
    }

    public function test_a_bot_submission_sends_no_email(): void
    {
        Notification::fake();

        $this->submit(['website' => 'http://spam.example'])->assertSessionHas('status');

        Notification::assertNothingSent();
        $this->assertSame(0, Inquiry::count());
    }

    public function test_a_mail_failure_still_saves_the_inquiry_and_thanks_the_visitor(): void
    {
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1, 'mail.mailers.smtp.timeout' => 1]);

        $this->submit()->assertSessionHas('status')->assertSessionHasNoErrors();

        $this->assertSame(1, Inquiry::count());
    }
}
