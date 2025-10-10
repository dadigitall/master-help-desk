<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class MonitoringService
{
    /**
     * Niveaux de log
     */
    const LOG_LEVELS = [
        'debug' => 'DEBUG',
        'info' => 'INFO',
        'warning' => 'WARNING',
        'error' => 'ERROR',
        'critical' => 'CRITICAL',
    ];

    /**
     * Logger une activité utilisateur
     */
    public static function logUserActivity(string $action, array $context = [], string $level = 'info'): void
    {
        $user = auth()->user();
        
        $logData = [
            'timestamp' => now()->toISOString(),
            'level' => self::LOG_LEVELS[$level] ?? self::LOG_LEVELS['info'],
            'type' => 'user_activity',
            'action' => $action,
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'context' => $context,
        ];

        Log::channel('activity')->log($level, $action, $logData);
    }

    /**
     * Logger une erreur système
     */
    public static function logError(\Throwable $exception, array $context = []): void
    {
        $logData = [
            'timestamp' => now()->toISOString(),
            'level' => self::LOG_LEVELS['error'],
            'type' => 'system_error',
            'exception' => [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ],
            'request' => [
                'url' => Request::fullUrl(),
                'method' => Request::method(),
                'ip' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ],
            'user_id' => auth()->id(),
            'context' => $context,
        ];

        Log::channel('errors')->error('System Error', $logData);
        
        // Envoyer une alerte si c'est une erreur critique
        if ($exception instanceof \Error || $exception instanceof \RuntimeException) {
            self::sendAlert('System Error', $exception->getMessage(), $logData);
        }
    }

    /**
     * Logger une performance
     */
    public static function logPerformance(string $action, float $duration, array $context = []): void
    {
        $logData = [
            'timestamp' => now()->toISOString(),
            'level' => self::LOG_LEVELS['info'],
            'type' => 'performance',
            'action' => $action,
            'duration_ms' => round($duration * 1000, 2),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'user_id' => auth()->id(),
            'context' => $context,
        ];

        // Alerte si la performance est mauvaise
        if ($duration > 5.0) { // Plus de 5 secondes
            Log::channel('performance')->warning('Slow Performance', $logData);
        } else {
            Log::channel('performance')->info('Performance', $logData);
        }
    }

    /**
     * Logger une événement de sécurité
     */
    public static function logSecurityEvent(string $event, array $context = [], string $level = 'warning'): void
    {
        $logData = [
            'timestamp' => now()->toISOString(),
            'level' => self::LOG_LEVELS[$level] ?? self::LOG_LEVELS['warning'],
            'type' => 'security',
            'event' => $event,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'user_id' => auth()->id(),
            'context' => $context,
        ];

        Log::channel('security')->log($level, $event, $logData);
        
        // Envoyer une alerte pour les événements de sécurité critiques
        if (in_array($event, ['brute_force_attempt', 'sql_injection_attempt', 'xss_attempt'])) {
            self::sendAlert('Security Alert', $event, $logData);
        }
    }

    /**
     * Logger une événement métier
     */
    public static function logBusinessEvent(string $event, array $context = [], string $level = 'info'): void
    {
        $logData = [
            'timestamp' => now()->toISOString(),
            'level' => self::LOG_LEVELS[$level] ?? self::LOG_LEVELS['info'],
            'type' => 'business',
            'event' => $event,
            'user_id' => auth()->id(),
            'context' => $context,
        ];

        Log::channel('business')->log($level, $event, $logData);
    }

    /**
     * Obtenir les métriques de performance
     */
    public static function getPerformanceMetrics(): array
    {
        return [
            'memory_usage' => [
                'current' => self::formatBytes(memory_get_usage(true)),
                'peak' => self::formatBytes(memory_get_peak_usage(true)),
            ],
            'database' => [
                'connections' => DB::select('SHOW STATUS LIKE "Threads_connected"')[0]->Value ?? 0,
                'queries_per_second' => self::getQueriesPerSecond(),
            ],
            'cache' => [
                'hits' => Cache::get('cache_hits', 0),
                'misses' => Cache::get('cache_misses', 0),
                'hit_rate' => self::calculateCacheHitRate(),
            ],
            'system' => [
                'uptime' => self::getSystemUptime(),
                'cpu_usage' => sys_getloadavg()[0] ?? 0,
                'disk_usage' => self::getDiskUsage(),
            ],
        ];
    }

    /**
     * Obtenir les statistiques des logs
     */
    public static function getLogStats(int $hours = 24): array
    {
        $since = now()->subHours($hours);
        
        return [
            'total_logs' => self::countLogs(['activity', 'errors', 'security', 'business'], $since),
            'errors' => self::countLogs(['errors'], $since),
            'warnings' => self::countLogs(['activity', 'security'], $since, ['level' => 'WARNING']),
            'user_activities' => self::countLogs(['activity'], $since),
            'security_events' => self::countLogs(['security'], $since),
            'business_events' => self::countLogs(['business'], $since),
        ];
    }

    /**
     * Vérifier la santé du système
     */
    public static function healthCheck(): array
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'checks' => [],
        ];

        // Vérification de la base de données
        try {
            DB::select('SELECT 1');
            $health['checks']['database'] = ['status' => 'healthy', 'message' => 'Database connection OK'];
        } catch (\Exception $e) {
            $health['checks']['database'] = ['status' => 'unhealthy', 'message' => $e->getMessage()];
            $health['status'] = 'unhealthy';
        }

        // Vérification du cache
        try {
            $testKey = 'health_check_' . time();
            Cache::put($testKey, 'test', 60);
            Cache::get($testKey);
            Cache::forget($testKey);
            $health['checks']['cache'] = ['status' => 'healthy', 'message' => 'Cache working OK'];
        } catch (\Exception $e) {
            $health['checks']['cache'] = ['status' => 'unhealthy', 'message' => $e->getMessage()];
            $health['status'] = 'unhealthy';
        }

        // Vérification de l'espace disque
        $diskUsage = self::getDiskUsage();
        if ($diskUsage > 90) {
            $health['checks']['disk'] = ['status' => 'critical', 'message' => 'Disk usage critical: ' . $diskUsage . '%'];
            $health['status'] = 'critical';
        } elseif ($diskUsage > 80) {
            $health['checks']['disk'] = ['status' => 'warning', 'message' => 'Disk usage high: ' . $diskUsage . '%'];
            if ($health['status'] === 'healthy') $health['status'] = 'warning';
        } else {
            $health['checks']['disk'] = ['status' => 'healthy', 'message' => 'Disk usage OK: ' . $diskUsage . '%'];
        }

        // Vérification de la mémoire
        $memoryUsage = memory_get_usage(true) / 1024 / 1024; // MB
        $memoryLimit = ini_get('memory_limit');
        if ($memoryUsage > 500) { // Plus de 500MB
            $health['checks']['memory'] = ['status' => 'warning', 'message' => 'High memory usage: ' . round($memoryUsage, 2) . 'MB'];
            if ($health['status'] === 'healthy') $health['status'] = 'warning';
        } else {
            $health['checks']['memory'] = ['status' => 'healthy', 'message' => 'Memory usage OK: ' . round($memoryUsage, 2) . 'MB'];
        }

        return $health;
    }

    /**
     * Envoyer une alerte
     */
    private static function sendAlert(string $title, string $message, array $context): void
    {
        $alert = [
            'title' => $title,
            'message' => $message,
            'timestamp' => now()->toISOString(),
            'context' => $context,
        ];

        // Logger l'alerte
        Log::channel('alerts')->critical('Alert', $alert);

        // Ici, vous pourriez intégrer avec un service externe comme Slack, Email, etc.
        // Par exemple : Slack::send($title . ': ' . $message);
    }

    /**
     * Formater les octets en format lisible
     */
    private static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Obtenir le nombre de requêtes par seconde
     */
    private static function getQueriesPerSecond(): float
    {
        try {
            $queries = DB::select('SHOW STATUS LIKE "Questions"')[0]->Value ?? 0;
            $uptime = DB::select('SHOW STATUS LIKE "Uptime"')[0]->Value ?? 1;
            
            return round($queries / $uptime, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calculer le taux de hit du cache
     */
    private static function calculateCacheHitRate(): float
    {
        $hits = Cache::get('cache_hits', 0);
        $misses = Cache::get('cache_misses', 0);
        $total = $hits + $misses;
        
        return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
    }

    /**
     * Obtenir le temps de fonctionnement du système
     */
    private static function getSystemUptime(): string
    {
        if (function_exists('sys_getloadavg')) {
            $uptime = shell_exec('uptime');
            return trim($uptime);
        }
        
        return 'N/A';
    }

    /**
     * Obtenir l'utilisation du disque
     */
    private static function getDiskUsage(): float
    {
        $totalSpace = disk_total_space('/');
        $freeSpace = disk_free_space('/');
        
        if ($totalSpace && $freeSpace) {
            return round((($totalSpace - $freeSpace) / $totalSpace) * 100, 2);
        }
        
        return 0;
    }

    /**
     * Compter les logs
     */
    private static function countLogs(array $channels, Carbon $since, array $filters = []): int
    {
        // Ceci est une implémentation simplifiée
        // En production, vous utiliseriez probablement un système comme ELK ou Graylog
        $count = 0;
        
        foreach ($channels as $channel) {
            $logFile = storage_path("logs/laravel-{$channel}.log");
            if (file_exists($logFile)) {
                $content = file_get_contents($logFile);
                $lines = explode("\n", $content);
                
                foreach ($lines as $line) {
                    if (empty($line)) continue;
                    
                    try {
                        $logEntry = json_decode($line, true);
                        if ($logEntry && isset($logEntry['timestamp'])) {
                            $logTime = Carbon::parse($logEntry['timestamp']);
                            if ($logTime->gte($since)) {
                                $count++;
                            }
                        }
                    } catch (\Exception $e) {
                        // Ignorer les lignes invalides
                    }
                }
            }
        }
        
        return $count;
    }

    /**
     * Monitoring des requêtes lentes
     */
    public static function monitorSlowQuery(float $threshold = 1.0): callable
    {
        return function ($sql, $bindings, $time) use ($threshold) {
            if ($time > $threshold) {
                self::logPerformance('slow_query', $time, [
                    'sql' => $sql,
                    'bindings' => $bindings,
                    'threshold' => $threshold,
                ]);
            }
        };
    }

    /**
     * Middleware de monitoring
     */
    public static function logRequest($request, $response): void
    {
        $duration = microtime(true) - LARAVEL_START;
        
        self::logPerformance('http_request', $duration, [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
            'size' => strlen($response->getContent()),
        ]);
    }
}
