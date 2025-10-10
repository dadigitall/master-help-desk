<?php

use Illuminate\Support\Facades\Route;

// Language routes
Route::controller(\App\Http\Controllers\LanguageController::class)->prefix('language')->name('language.')->group(function () {
    Route::get('/switch/{locale}', 'switch')->name('switch');
    Route::get('/current', 'current')->name('current');
    Route::get('/translations/{locale?}', 'translations')->name('translations');
});

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard routes
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard')->middleware('permission:dashboard.view');

    // Companies routes
    Route::get('/companies', function () {
        return view('companies');
    })->name('companies')->middleware('permission:companies.view');

    // Projects routes
    Route::get('/projects', function () {
        return view('projects');
    })->name('projects')->middleware('permission:projects.view');

    // Tickets routes
    Route::get('/tickets', function () {
        return view('tickets');
    })->name('tickets')->middleware('permission:tickets.view');
});

// Admin routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Admin
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard')->middleware('permission:admin.dashboard.view');

    // Users Management
    Route::controller(\App\Http\Controllers\Admin\UserController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('/', 'index')->name('index')->middleware('permission:view users');
        Route::get('/create', 'create')->name('create')->middleware('permission:create users');
        Route::post('/', 'store')->name('store')->middleware('permission:create users');
        Route::get('/{user}', 'show')->name('show')->middleware('permission:view users');
        Route::get('/{user}/edit', 'edit')->name('edit')->middleware('permission:edit users');
        Route::put('/{user}', 'update')->name('update')->middleware('permission:edit users');
        Route::delete('/{user}', 'destroy')->name('destroy')->middleware('permission:delete users');
        Route::post('/{user}/toggle-status', 'toggleStatus')->name('toggleStatus')->middleware('permission:edit users');
        Route::post('/{user}/reset-password', 'resetPassword')->name('resetPassword')->middleware('permission:edit users');
        Route::post('/{user}/restore', 'restore')->name('restore')->middleware('permission:restore users');
        Route::delete('/{user}/force', 'forceDelete')->name('forceDelete')->middleware('permission:delete users');
        Route::get('/export', 'export')->name('export')->middleware('permission:view users');
    });

    // Companies Management
    Route::controller(\App\Http\Controllers\Admin\CompanyController::class)->prefix('companies')->name('companies.')->group(function () {
        Route::get('/', 'index')->name('index')->middleware('permission:view companies');
        Route::get('/create', 'create')->name('create')->middleware('permission:create companies');
        Route::post('/', 'store')->name('store')->middleware('permission:create companies');
        Route::get('/{company}', 'show')->name('show')->middleware('permission:view companies');
        Route::get('/{company}/edit', 'edit')->name('edit')->middleware('permission:edit companies');
        Route::put('/{company}', 'update')->name('update')->middleware('permission:edit companies');
        Route::delete('/{company}', 'destroy')->name('destroy')->middleware('permission:delete companies');
        Route::post('/{company}/restore', 'restore')->name('restore')->middleware('permission:restore companies');
        Route::delete('/{company}/force', 'forceDelete')->name('forceDelete')->middleware('permission:delete companies');
        Route::get('/export', 'export')->name('export')->middleware('permission:view companies');
    });

    // System Configuration
    Route::controller(\App\Http\Controllers\Admin\SystemController::class)->prefix('system')->name('system.')->group(function () {
        Route::get('/config', 'config')->name('config')->middleware('permission:system.config');
        Route::post('/config', 'updateConfig')->name('config.update')->middleware('permission:system.config');
        
        // Ticket Statuses
        Route::get('/statuses', 'statusesIndex')->name('statuses.index')->middleware('permission:system.config');
        Route::post('/statuses', 'statusesStore')->name('statuses.store')->middleware('permission:system.config');
        Route::put('/statuses/{status}', 'statusesUpdate')->name('statuses.update')->middleware('permission:system.config');
        Route::delete('/statuses/{status}', 'statusesDestroy')->name('statuses.destroy')->middleware('permission:system.config');
        
        // Ticket Priorities
        Route::get('/priorities', 'prioritiesIndex')->name('priorities.index')->middleware('permission:system.config');
        Route::post('/priorities', 'prioritiesStore')->name('priorities.store')->middleware('permission:system.config');
        Route::put('/priorities/{priority}', 'prioritiesUpdate')->name('priorities.update')->middleware('permission:system.config');
        Route::delete('/priorities/{priority}', 'prioritiesDestroy')->name('priorities.destroy')->middleware('permission:system.config');
        
        // Ticket Types
        Route::get('/types', 'typesIndex')->name('types.index')->middleware('permission:system.config');
        Route::post('/types', 'typesStore')->name('types.store')->middleware('permission:system.config');
        Route::put('/types/{type}', 'typesUpdate')->name('types.update')->middleware('permission:system.config');
        Route::delete('/types/{type}', 'typesDestroy')->name('types.destroy')->middleware('permission:system.config');
        
        // Roles and Permissions
        Route::get('/roles', 'rolesIndex')->name('roles.index')->middleware('permission:system.roles');
        Route::post('/roles', 'rolesStore')->name('roles.store')->middleware('permission:system.roles');
        Route::put('/roles/{role}', 'rolesUpdate')->name('roles.update')->middleware('permission:system.roles');
        Route::delete('/roles/{role}', 'rolesDestroy')->name('roles.destroy')->middleware('permission:system.roles');
        Route::get('/permissions', 'permissionsIndex')->name('permissions.index')->middleware('permission:system.roles');
        
        // Icons
        Route::get('/icons', 'iconsIndex')->name('icons.index')->middleware('permission:system.config');
        Route::post('/icons', 'iconsStore')->name('icons.store')->middleware('permission:system.config');
        Route::put('/icons/{icon}', 'iconsUpdate')->name('icons.update')->middleware('permission:system.config');
        Route::delete('/icons/{icon}', 'iconsDestroy')->name('icons.destroy')->middleware('permission:system.config');
    });

    // Analytics and Reports
    Route::controller(\App\Http\Controllers\Admin\AnalyticsController::class)->prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', 'index')->name('index')->middleware('permission:analytics.view');
        Route::get('/users', 'usersAnalytics')->name('users')->middleware('permission:analytics.view');
        Route::get('/tickets', 'ticketsAnalytics')->name('tickets')->middleware('permission:analytics.view');
        Route::get('/projects', 'projectsAnalytics')->name('projects')->middleware('permission:analytics.view');
        Route::get('/companies', 'companiesAnalytics')->name('companies')->middleware('permission:analytics.view');
        Route::get('/reports', 'reports')->name('reports')->middleware('permission:analytics.reports');
        Route::post('/reports/generate', 'generateReport')->name('reports.generate')->middleware('permission:analytics.reports');
        Route::get('/reports/{report}/download', 'downloadReport')->name('reports.download')->middleware('permission:analytics.reports');
    });
});

// Reports routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('reports')->name('reports.')->group(function () {
    Route::controller(\App\Http\Controllers\ReportController::class)->group(function () {
        Route::get('/', 'index')->name('index')->middleware('permission:dashboard.export');
        Route::post('/generate', 'generate')->name('generate')->middleware('permission:dashboard.export');
        Route::get('/download/{filename}', 'download')->name('download')->middleware('permission:dashboard.export');
        Route::delete('/{filename}', 'destroy')->name('destroy')->middleware('permission:dashboard.export');
    });
});
