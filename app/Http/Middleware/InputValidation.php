<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InputValidation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Nettoyage des entrées
        $this->sanitizeInputs($request);
        
        // Validation basique des entrées
        $this->validateInputs($request);
        
        // Protection contre les attaques courantes
        $this->protectAgainstAttacks($request);
        
        return $next($request);
    }
    
    /**
     * Nettoyer les entrées utilisateur
     */
    private function sanitizeInputs(Request $request): void
    {
        $allInputs = $request->all();
        
        foreach ($allInputs as $key => $value) {
            if (is_string($value)) {
                // Suppression des caractères dangereux
                $value = $this->cleanString($value);
                $request->merge([$key => $value]);
            } elseif (is_array($value)) {
                $request->merge([$key => $this->cleanArray($value)]);
            }
        }
    }
    
    /**
     * Nettoyer une chaîne de caractères
     */
    private function cleanString(string $string): string
    {
        // Suppression des espaces excessifs
        $string = trim($string);
        
        // Suppression des caractères de contrôle
        $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $string);
        
        // Normalisation des caractères Unicode
        $string = normalizer_normalize($string, \Normalizer::FORM_C);
        
        return $string;
    }
    
    /**
     * Nettoyer un tableau
     */
    private function cleanArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $array[$key] = $this->cleanString($value);
            } elseif (is_array($value)) {
                $array[$key] = $this->cleanArray($value);
            }
        }
        
        return $array;
    }
    
    /**
     * Validation basique des entrées
     */
    private function validateInputs(Request $request): void
    {
        $allInputs = $request->all();
        
        foreach ($allInputs as $key => $value) {
            if (is_string($value)) {
                // Vérifier la longueur maximale
                if (strlen($value) > 65000) {
                    abort(422, 'Lentrée est trop longue.');
                }
                
                // Vérifier les patterns suspects
                if ($this->containsSuspiciousPatterns($value)) {
                    abort(422, 'Lentrée contient du contenu non autorisé.');
                }
            }
        }
    }
    
    /**
     * Vérifier les patterns suspects
     */
    private function containsSuspiciousPatterns(string $string): bool
    {
        $suspiciousPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi',
            '/<object\b[^<]*(?:(?!<\/object>)<[^<]*)*<\/object>/mi',
            '/<embed\b[^<]*(?:(?!<\/embed>)<[^<]*)*<\/embed>/mi',
            '/javascript:/i',
            '/data:text\/html/i',
            '/vbscript:/i',
            '/onload\s*=/i',
            '/onerror\s*=/i',
            '/onclick\s*=/i',
            '/onmouseover\s*=/i',
            '/eval\s*\(/i',
            '/document\.cookie/i',
            '/document\.write/i',
            '/window\.location/i',
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $string)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Protection contre les attaques courantes
     */
    private function protectAgainstAttacks(Request $request): void
    {
        // Protection contre HTTP Parameter Pollution
        $this->protectAgainstParameterPollution($request);
        
        // Protection contre les grands payloads
        $this->protectAgainstLargePayloads($request);
        
        // Validation User-Agent
        $this->validateUserAgent($request);
        
        // Validation Referer
        $this->validateReferer($request);
    }
    
    /**
     * Protection contre HTTP Parameter Pollution
     */
    private function protectAgainstParameterPollution(Request $request): void
    {
        // Laravel gère déjà cela, mais ajoutons une validation supplémentaire
        $queryParameters = $request->query();
        
        foreach ($queryParameters as $key => $values) {
            if (is_array($values) && count($values) > 10) {
                abort(422, 'Trop de paramètres pour la clé : ' . $key);
            }
        }
    }
    
    /**
     * Protection contre les grands payloads
     */
    private function protectAgainstLargePayloads(Request $request): void
    {
        $contentLength = $request->header('Content-Length');
        $maxSize = 10 * 1024 * 1024; // 10MB
        
        if ($contentLength && $contentLength > $maxSize) {
            abort(413, 'Payload trop grand.');
        }
    }
    
    /**
     * Validation User-Agent
     */
    private function validateUserAgent(Request $request): void
    {
        $userAgent = $request->header('User-Agent');
        
        if (!$userAgent || strlen($userAgent) > 500) {
            abort(400, 'User-Agent invalide.');
        }
        
        // Vérifier les User-Agents suspects
        $suspiciousUAs = [
            'bot',
            'crawler',
            'spider',
            'scraper',
            'curl',
            'wget',
            'python',
            'perl',
            'java',
            'ruby',
        ];
        
        $userAgentLower = strtolower($userAgent);
        
        // Si c'est un bot de recherche légitime, on l'autorise
        $legitimateBots = [
            'googlebot',
            'bingbot',
            'slurp',
            'duckduckbot',
            'baiduspider',
            'yandexbot',
        ];
        
        foreach ($legitimateBots as $bot) {
            if (str_contains($userAgentLower, $bot)) {
                return;
            }
        }
        
        // Vérifier les autres bots
        foreach ($suspiciousUAs as $ua) {
            if (str_contains($userAgentLower, $ua) && 
                !str_contains($userAgentLower, 'google')) {
                // Pas de blocage direct, mais on pourrait logger
                break;
            }
        }
    }
    
    /**
     * Validation Referer
     */
    private function validateReferer(Request $request): void
    {
        $referer = $request->header('Referer');
        
        if ($referer && $request->isMethod('POST')) {
            $host = parse_url($referer, PHP_URL_HOST);
            $currentHost = $request->getHost();
            
            if ($host && $host !== $currentHost && !app()->environment('testing')) {
                // Pas de blocage direct, mais on pourrait logger
                // pour détecter les tentatives de CSRF
            }
        }
    }
}
