<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\MonitoringService;
use App\Services\CacheService;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitoring
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);
        
        // Activer le monitoring des requêtes si en mode debug
        if (config('app.debug')) {
            DB::enableQueryLog();
        }
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        
        $duration = $endTime - $startTime;
        $memoryUsed = $endMemory - $startMemory;
        
        // Logger les performances
        MonitoringService::logPerformance('http_request', $duration, [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
            'memory_used' => $memoryUsed,
            'peak_memory' => memory_get_peak_usage(true),
            'query_count' => count(DB::getQueryLog()),
        ]);
        
        // Ajouter les headers de debugging
        if (config('app.debug')) {
            $response->headers->set('X-Response-Time', round($duration * 1000, 2) . 'ms');
            $response->headers->set('X-Memory-Usage', $this->formatBytes($memoryUsed));
            $response->headers->set('X-Query-Count', (string) count(DB::getQueryLog()));
            $response->headers->set('X-Cache-Hits', (string) Cache::get('cache_hits', 0));
        }
        
        // Logger les requêtes lentes
        if ($duration > 2.0) { // Plus de 2 secondes
            MonitoringService::logPerformance('slow_request', $duration, [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'status' => $response->getStatusCode(),
                'query_count' => count(DB::getQueryLog()),
                'queries' => DB::getQueryLog(),
            ]);
        }
        
        // Logger les requêtes avec beaucoup de requêtes DB
        $queryCount = count(DB::getQueryLog());
        if ($queryCount > 50) { // Plus de 50 requêtes
            MonitoringService::logPerformance('high_query_count', $duration, [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'query_count' => $queryCount,
                'queries' => DB::getQueryLog(),
            ]);
        }
        
        // Mettre à jour les statistiques du cache
        $cacheHits = Cache::get('cache_hits', 0);
        $cacheMisses = Cache::get('cache_misses', 0);
        
        // Logger l'activité utilisateur
        if (auth()->check()) {
            MonitoringService::logUserActivity('http_request', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'status' => $response->getStatusCode(),
                'duration' => $duration,
            ]);
        }
        
        return $response;
    }
    
    /**
     * Formater les octets en format lisible
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
