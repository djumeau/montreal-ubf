<?php

namespace App\Support;

class TemporaryPassword
{
    public const LENGTH = 10;

    // Special characters found on every keyboard; none that are easily confused or hard to type
    public const SYMBOLS = '!@#$%&*_-+=?';

    /**
     * Random password emailed to a user (new account, password reset): LENGTH characters with at least
     * one lowercase letter, one uppercase letter, one number and one special character.
     * Usage: TemporaryPassword::generate()
     */
    public static function generate(): string
    {
        $sets = ['abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', '0123456789', self::SYMBOLS];
        $pick = fn (string $characters) => $characters[random_int(0, strlen($characters) - 1)];

        // One of each kind, the rest from all of them, then shuffled so the kinds are not in a fixed order
        $password = array_map($pick, $sets);

        while (count($password) < self::LENGTH) {
            $password[] = $pick(implode('', $sets));
        }

        for ($i = count($password) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$password[$i], $password[$j]] = [$password[$j], $password[$i]];
        }

        return implode('', $password);
    }
}
