<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\RedirectResponse;

class SwitchLanguageController extends Controller
{
    // @desc Switch the application language (en_CA or fr_CA)
    // @route GET /language/{locale}
    public function setLocale(string $locale): RedirectResponse
    {
        if (in_array($locale, ['en_CA', 'fr_CA'])) {
            Cookie::queue('locale', $locale, 43200); // 30 days
        }

        return redirect()->back();
    }
}