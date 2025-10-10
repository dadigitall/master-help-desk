<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Priorité 1: Langue dans l'URL (ex: /en/dashboard)
        if ($request->segment(1) && in_array($request->segment(1), array_keys(config('app.supported_locales', ['fr', 'en'])))) {
            $locale = $request->segment(1);
        }
        // Priorité 2: Langue en session
        elseif (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        // Priorité 3: Préférence utilisateur connecté
        elseif (auth()->check() && auth()->user()->locale) {
            $locale = auth()->user()->locale;
        }
        // Priorité 4: Langue du navigateur
        elseif ($request->header('Accept-Language')) {
            $locale = $this->parseAcceptLanguage($request->header('Accept-Language'));
        }
        // Priorité 5: Langue par défaut de l'application
        else {
            $locale = config('app.locale', 'fr');
        }

        // Valider que la langue est supportée
        if (!in_array($locale, array_keys(config('app.supported_locales', ['fr', 'en'])))) {
            $locale = config('app.fallback_locale', 'en');
        }

        // Définir la langue pour l'application
        App::setLocale($locale);
        
        // Stocker en session pour les requêtes futures
        Session::put('locale', $locale);

        // Définir les formats de localisation
        $this->setLocaleSettings($locale);

        return $next($request);
    }

    /**
     * Parse le header Accept-Language pour déterminer la langue préférée
     */
    private function parseAcceptLanguage($header)
    {
        $locales = config('app.supported_locales', ['fr', 'en']);
        $acceptedLanguages = explode(',', $header);
        
        foreach ($acceptedLanguages as $lang) {
            $lang = substr($lang, 0, 2); // Extraire les 2 premiers caractères (ex: 'fr' de 'fr-FR')
            if (in_array($lang, array_keys($locales))) {
                return $lang;
            }
        }
        
        return config('app.locale', 'fr');
    }

    /**
     * Configure les paramètres de localisation selon la langue
     */
    private function setLocaleSettings($locale)
    {
        switch ($locale) {
            case 'fr':
                // Formats français
                config(['app.date_format' => 'd/m/Y']);
                config(['app.datetime_format' => 'd/m/Y H:i']);
                config(['app.time_format' => 'H:i']);
                config(['app.decimal_separator' => ',']);
                config(['app.thousands_separator' => ' ']);
                config(['app.currency_symbol' => '€']);
                config(['app.currency_position' => 'after']);
                break;
                
            case 'en':
                // Formats anglais
                config(['app.date_format' => 'm/d/Y']);
                config(['app.datetime_format' => 'm/d/Y g:i A']);
                config(['app.time_format' => 'g:i A']);
                config(['app.decimal_separator' => '.']);
                config(['app.thousands_separator' => ',']);
                config(['app.currency_symbol' => '$']);
                config(['app.currency_position' => 'before']);
                break;
        }
    }
}
