<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function switch(string $locale): RedirectResponse
    {
        if (in_array($locale, SetLocale::LOCALES, true)) {
            session(['locale' => $locale]);
        }

        return back();
    }
}
