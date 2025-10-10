<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    /**
     * Change la langue de l'application
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request, $locale)
    {
        $supportedLocales = array_keys(config('app.supported_locales', ['fr', 'en']));
        
        // Valider que la langue est supportée
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.locale', 'fr');
        }

        // Stocker la langue en session
        Session::put('locale', $locale);

        // Si l'utilisateur est connecté, mettre à jour sa préférence
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        // Rediriger vers la page précédente ou vers le dashboard
        return Redirect::back()->with('success', __('messages.language_switched'));
    }

    /**
     * Retourne la langue actuelle au format JSON
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function current()
    {
        return response()->json([
            'locale' => app()->getLocale(),
            'supported_locales' => config('app.supported_locales', ['fr' => 'Français', 'en' => 'English']),
            'date_format' => config('app.date_format', 'd/m/Y'),
            'datetime_format' => config('app.datetime_format', 'd/m/Y H:i'),
            'time_format' => config('app.time_format', 'H:i'),
        ]);
    }

    /**
     * Retourne les traductions pour le frontend
     *
     * @param  string  $locale
     * @return \Illuminate\Http\JsonResponse
     */
    public function translations($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        $translations = [
            // Messages généraux
            'messages' => [
                'welcome' => __('messages.welcome'),
                'language_switched' => __('messages.language_switched'),
                'saved' => __('messages.saved'),
                'deleted' => __('messages.deleted'),
                'updated' => __('messages.updated'),
                'created' => __('messages.created'),
                'loading' => __('messages.loading'),
                'error' => __('messages.error'),
                'success' => __('messages.success'),
                'confirm' => __('messages.confirm'),
                'cancel' => __('messages.cancel'),
                'yes' => __('messages.yes'),
                'no' => __('messages.no'),
            ],
            
            // Navigation
            'nav' => [
                'dashboard' => __('nav.dashboard'),
                'tickets' => __('nav.tickets'),
                'projects' => __('nav.projects'),
                'companies' => __('nav.companies'),
                'users' => __('nav.users'),
                'reports' => __('nav.reports'),
                'admin' => __('nav.admin'),
                'profile' => __('nav.profile'),
                'logout' => __('nav.logout'),
            ],
            
            // Actions
            'actions' => [
                'create' => __('actions.create'),
                'edit' => __('actions.edit'),
                'delete' => __('actions.delete'),
                'view' => __('actions.view'),
                'save' => __('actions.save'),
                'cancel' => __('actions.cancel'),
                'search' => __('actions.search'),
                'filter' => __('actions.filter'),
                'export' => __('actions.export'),
                'import' => __('actions.import'),
            ],
            
            // Validation
            'validation' => [
                'required' => __('validation.required'),
                'email' => __('validation.email'),
                'unique' => __('validation.unique'),
                'min' => __('validation.min'),
                'max' => __('validation.max'),
                'between' => __('validation.between'),
                'confirmed' => __('validation.confirmed'),
            ],
        ];

        return response()->json($translations);
    }
}
