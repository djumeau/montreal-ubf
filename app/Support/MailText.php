<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

class MailText
{
    /**
     * A translated email line whose values are printed exactly as given. Notification emails are written in Markdown,
     * which drops or changes characters such as _ * < : a password holding them would arrive different from the one saved.
     * One exception remains: Laravel's mail renderer removes the backslash from "\[", so keep both out of generated values.
     * Usage: ->line(MailText::exact('dashboard/manage-users/index.password_reset_email_password', ['password' => $password]))
     */
    public static function exact(string $key, array $values): HtmlString
    {
        $tokens = [];
        $encoded = [];

        foreach ($values as $name => $value) {
            $tokens[$name] = "@@{$name}@@";
            // Every character as an HTML code (e.g. "_" -> "&#95;"): shown as is, never read as Markdown or HTML
            $encoded["@@{$name}@@"] = implode('', array_map(fn (string $character) => '&#' . mb_ord($character) . ';', mb_str_split((string) $value)));
        }

        return new HtmlString(strtr(e(__($key, $tokens)), $encoded));
    }
}
