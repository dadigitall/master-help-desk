<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CacheService
{
    /**
     * Durées de cache en secondes
     */
    const CACHE_DURATIONS = [
        'short' => 300,      // 5 minutes
        'medium' => 1800,    // 30 minutes
        'long' => 3600,      // 1 heure
        'very_long' => 86400, // 24 heures
    ];

    /**
     * Mettre en cache les résultats d'une requête
     */
    public static function remember(string $key, callable $callback, int $duration = self::CACHE_DURATIONS['medium'])
    {
        return Cache::remember($key, $duration, $callback);
    }

    /**
     * Mettre en cache les résultats d'une requête avec tags
     */
    public static function rememberWithTags(array $tags, string $key, callable $callback, int $duration = self::CACHE_DURATIONS['medium'])
    {
        return Cache::tags($tags)->remember($key, $duration, $callback);
    }

    /**
     * Mettre en cache les statistiques des tickets
     */
    public static function getTicketStats(int $userId = null): array
    {
        $key = $userId ? "ticket_stats_user_{$userId}" : 'ticket_stats_global';
        
        return self::remember($key, function () use ($userId) {
            $query = DB::table('tickets');
            
            if ($userId) {
                $query->where('assigned_to', $userId)
                      ->orWhere('created_by', $userId);
            }
            
            return [
                'total' => $query->count(),
                'open' => $query->clone()->where('status_id', 1)->count(),
                'in_progress' => $query->clone()->where('status_id', 2)->count(),
                'resolved' => $query->clone()->where('status_id', 3)->count(),
                'closed' => $query->clone()->where('status_id', 4)->count(),
                'high_priority' => $query->clone()->where('priority_id', 3)->count(),
                'created_today' => $query->clone()->whereDate('created_at', today())->count(),
                'created_this_week' => $query->clone()->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'created_this_month' => $query->clone()->whereMonth('created_at', now()->month)
                                             ->whereYear('created_at', now()->year)
                                             ->count(),
            ];
        }, self::CACHE_DURATIONS['short']);
    }

    /**
     * Mettre en cache les statistiques des projets
     */
    public static function getProjectStats(int $userId = null): array
    {
        $key = $userId ? "project_stats_user_{$userId}" : 'project_stats_global';
        
        return self::remember($key, function () use ($userId) {
            $query = DB::table('projects');
            
            if ($userId) {
                $query->whereHas('users', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                });
            }
            
            return [
                'total' => $query->count(),
                'active' => $query->clone()->where('is_active', true)->count(),
                'inactive' => $query->clone()->where('is_active', false)->count(),
                'created_this_month' => $query->clone()->whereMonth('created_at', now()->month)
                                                ->whereYear('created_at', now()->year)
                                                ->count(),
                'total_tickets' => DB::table('tickets')
                    ->when($userId, function ($q) use ($userId) {
                        return $q->where('assigned_to', $userId)
                               ->orWhere('created_by', $userId);
                    })
                    ->count(),
            ];
        }, self::CACHE_DURATIONS['medium']);
    }

    /**
     * Mettre en cache les données utilisateur
     */
    public static function getUserData(int $userId): array
    {
        return self::remember("user_data_{$userId}", function () use ($userId) {
            $user = DB::table('users')->find($userId);
            
            if (!$user) {
                return [];
            }
            
            return [
                'user' => $user,
                'roles' => DB::table('model_has_roles')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->where('model_has_roles.model_id', $userId)
                    ->where('model_has_roles.model_type', 'App\\Models\\User')
                    ->pluck('roles.name')
                    ->toArray(),
                'permissions' => DB::table('model_has_permissions')
                    ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
                    ->where('model_has_permissions.model_id', $userId)
                    ->where('model_has_permissions.model_type', 'App\\Models\\User')
                    ->pluck('permissions.name')
                    ->toArray(),
                'projects' => DB::table('project_user')
                    ->join('projects', 'project_user.project_id', '=', 'projects.id')
                    ->where('project_user.user_id', $userId)
                    ->select('projects.*', 'project_user.role as user_role')
                    ->get()
                    ->toArray(),
                'favorite_projects' => DB::table('favorite_projects')
                    ->join('projects', 'favorite_projects.project_id', '=', 'projects.id')
                    ->where('favorite_projects.user_id', $userId)
                    ->select('projects.*')
                    ->get()
                    ->toArray(),
            ];
        }, self::CACHE_DURATIONS['medium']);
    }

    /**
     * Mettre en cache les options de configuration
     */
    public static function getConfigurationOptions(): array
    {
        return self::remember('configuration_options', function () {
            return [
                'ticket_statuses' => DB::table('ticket_statuses')
                    ->orderBy('order')
                    ->get()
                    ->toArray(),
                'ticket_priorities' => DB::table('ticket_priorities')
                    ->orderBy('order')
                    ->get()
                    ->toArray(),
                'ticket_types' => DB::table('ticket_types')
                    ->orderBy('name')
                    ->get()
                    ->toArray(),
                'companies' => DB::table('companies')
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get()
                    ->toArray(),
                'icons' => DB::table('icons')
                    ->orderBy('name')
                    ->get()
                    ->toArray(),
            ];
        }, self::CACHE_DURATIONS['very_long']);
    }

    /**
     * Mettre en cache les données du tableau de bord
     */
    public static function getDashboardData(int $userId): array
    {
        return self::remember("dashboard_data_{$userId}", function () use ($userId) {
            return [
                'ticket_stats' => self::getTicketStats($userId),
                'project_stats' => self::getProjectStats($userId),
                'recent_tickets' => DB::table('tickets')
                    ->where(function ($q) use ($userId) {
                        $q->where('assigned_to', $userId)
                          ->orWhere('created_by', $userId);
                    })
                    ->join('ticket_statuses', 'tickets.status_id', '=', 'ticket_statuses.id')
                    ->join('ticket_priorities', 'tickets.priority_id', '=', 'ticket_priorities.id')
                    ->select('tickets.*', 'ticket_statuses.name as status_name', 'ticket_priorities.name as priority_name')
                    ->orderBy('tickets.created_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->toArray(),
                'recent_projects' => DB::table('project_user')
                    ->join('projects', 'project_user.project_id', '=', 'projects.id')
                    ->where('project_user.user_id', $userId)
                    ->orderBy('projects.updated_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->toArray(),
                'notifications' => DB::table('notifications')
                    ->where('notifiable_id', $userId)
                    ->where('notifiable_type', 'App\\Models\\User')
                    ->whereNull('read_at')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->toArray(),
            ];
        }, self::CACHE_DURATIONS['short']);
    }

    /**
     * Invalider le cache pour un utilisateur
     */
    public static function invalidateUserCache(int $userId): void
    {
        $patterns = [
            "user_data_{$userId}",
            "dashboard_data_{$userId}",
            "ticket_stats_user_{$userId}",
            "project_stats_user_{$userId}",
        ];
        
        foreach ($patterns as $key) {
            Cache::forget($key);
        }
        
        // Invalider aussi les caches avec tags
        Cache::tags(['user_' . $userId])->flush();
    }

    /**
     * Invalider le cache global
     */
    public static function invalidateGlobalCache(): void
    {
        $patterns = [
            'ticket_stats_global',
            'project_stats_global',
            'configuration_options',
        ];
        
        foreach ($patterns as $key) {
            Cache::forget($key);
        }
        
        // Invalider les caches avec tags
        Cache::tags(['global', 'tickets', 'projects'])->flush();
    }

    /**
     * Invalider le cache des tickets
     */
    public static function invalidateTicketCache(): void
    {
        Cache::tags(['tickets'])->flush();
        
        $patterns = [
            'ticket_stats_global',
        ];
        
        foreach ($patterns as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Invalider le cache des projets
     */
    public static function invalidateProjectCache(): void
    {
        Cache::tags(['projects'])->flush();
        
        $patterns = [
            'project_stats_global',
        ];
        
        foreach ($patterns as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Préchauffer le cache
     */
    public static function warmupCache(): void
    {
        // Préchauffer les options de configuration
        self::getConfigurationOptions();
        
        // Préchauffer les statistiques globales
        self::getTicketStats();
        self::getProjectStats();
        
        // Préchauffer les données des utilisateurs actifs
        $activeUsers = DB::table('users')
            ->where('email_verified_at', '!=', null)
            ->limit(100)
            ->pluck('id');
            
        foreach ($activeUsers as $userId) {
            self::getUserData($userId);
            self::getDashboardData($userId);
        }
    }

    /**
     * Obtenir les statistiques du cache
     */
    public static function getCacheStats(): array
    {
        if (class_exists(\Illuminate\Cache\CacheManager::class)) {
            return [
                'driver' => config('cache.default'),
                'prefix' => config('cache.prefix'),
                'tags_enabled' => config('cache.stores.redis.tags', false),
            ];
        }
        
        return [
            'driver' => 'array',
            'prefix' => 'laravel_cache',
            'tags_enabled' => false,
        ];
    }

    /**
     * Vider le cache
     */
    public static function clearCache(): void
    {
        Cache::flush();
    }

    /**
     * Mettre en cache les résultats de recherche
     */
    public static function rememberSearchResults(string $query, array $filters, callable $callback): array
    {
        $key = 'search_' . md5($query . serialize($filters));
        
        return self::remember($key, $callback, self::CACHE_DURATIONS['short']);
    }

    /**
     * Mettre en cache les rapports
     */
    public static function rememberReport(string $reportType, array $parameters, callable $callback): array
    {
        $key = 'report_' . $reportType . '_' . md5(serialize($parameters));
        
        return self::remember($key, $callback, self::CACHE_DURATIONS['long']);
    }
}
