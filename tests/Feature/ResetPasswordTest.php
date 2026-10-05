<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use App\Notifications\AdminPasswordReset;
use App\Notifications\NewUserWelcome;
use App\Support\TemporaryPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('en_CA');

        $this->admin = User::factory()->create(['role' => Role::ADMIN]);
        $this->member = User::factory()->create(['role' => Role::MEMBER, 'password' => 'old-password']);
    }

    public function test_resetting_a_password_emails_the_user_the_password_that_was_saved(): void
    {
        Notification::fake();

        $this->actingAs($this->admin)
            ->post(route('users.reset-password', $this->member))
            ->assertSessionHas('status');

        Notification::assertSentTo($this->member, AdminPasswordReset::class, function (AdminPasswordReset $notification) {
            // The password read back from the email, as the user sees it, is the one saved
            preg_match('/password is: (\S+)/', $this->emailText($notification->toMail($this->member)), $match);

            return isset($match[1]) && Hash::check($match[1], $this->member->fresh()->password);
        });
    }

    public function test_temporary_passwords_have_ten_characters_of_every_kind(): void
    {
        for ($i = 0; $i < 200; $i++) {
            $password = TemporaryPassword::generate();

            $this->assertSame(10, strlen($password));
            $this->assertMatchesRegularExpression('/[a-z]/', $password);
            $this->assertMatchesRegularExpression('/[A-Z]/', $password);
            $this->assertMatchesRegularExpression('/[0-9]/', $password);
            $this->assertMatchesRegularExpression('/[' . preg_quote(TemporaryPassword::SYMBOLS, '/') . ']/', $password);
        }
    }

    public function test_special_characters_reach_the_user_unchanged_in_both_password_emails(): void
    {
        // Each of these is dropped or changed when printed as plain Markdown
        foreach (['a_b_c*d*e!', '_Ab1_cd_2!', '*x*Y-9=+#?', '__Ab__1&$@', TemporaryPassword::SYMBOLS] as $password) {
            $this->assertStringContainsString($password, $this->emailText((new AdminPasswordReset($password))->toMail($this->member)));
            $this->assertStringContainsString($password, $this->emailText((new NewUserWelcome($password))->toMail($this->member)));
        }
    }

    /**
     * The email's text as the user reads it.
     */
    private function emailText(MailMessage $mail): string
    {
        return html_entity_decode(strip_tags((string) $mail->render()), ENT_QUOTES | ENT_HTML5);
    }

    public function test_the_password_is_kept_when_the_email_cannot_be_sent(): void
    {
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1, 'mail.mailers.smtp.timeout' => 1]);

        $this->actingAs($this->admin)
            ->post(route('users.reset-password', $this->member))
            ->assertSessionHas('error');

        $this->assertTrue(Hash::check('old-password', $this->member->fresh()->password));
    }

    public function test_only_management_roles_may_reset_a_password(): void
    {
        Notification::fake();

        $this->actingAs($this->member)
            ->post(route('users.reset-password', $this->admin))
            ->assertForbidden();

        Notification::assertNothingSent();
    }
}
