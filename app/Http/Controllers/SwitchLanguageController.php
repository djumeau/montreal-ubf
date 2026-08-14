<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;

class SwitchLanguageController extends Controller
{
    // @desc Switch the application language (en_CA or fr_CA)
    // @route GET /language/{locale}
    public function setLocale(string $locale): RedirectResponse
    {
        if (in_array($locale, ['en_CA', 'fr_CA'])) {
            Session::put('Locale', $locale);
        }

        return redirect()->back();
    }
}
