<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Company;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\Comment;
use App\Models\User;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Gates pour les entreprises
        Gate::define('view-company', function (User $user, Company $company) {
            if ($user->hasPermissionTo('companies.view')) {
                return $user->hasRole('Administrator') || 
                       $user->companies()->where('companies.id', $company->id)->exists();
            }
            return false;
        });

        Gate::define('edit-company', function (User $user, Company $company) {
            if ($user->hasPermissionTo('companies.edit')) {
                return $user->hasRole('Administrator') || 
                       $user->companies()->where('companies.id', $company->id)->exists();
            }
            return false;
        });

        Gate::define('delete-company', function (User $user, Company $company) {
            return $user->hasRole('Administrator') && $user->hasPermissionTo('companies.delete');
        });

        // Gates pour les projets
        Gate::define('view-project', function (User $user, Project $project) {
            if ($user->hasPermissionTo('projects.view')) {
                return $user->hasRole('Administrator') || 
                       $project->users()->where('users.id', $user->id)->exists() ||
                       $user->companies()->where('companies.id', $project->company_id)->exists();
            }
            return false;
        });

        Gate::define('edit-project', function (User $user, Project $project) {
            if ($user->hasPermissionTo('projects.edit')) {
                return $user->hasRole('Administrator') || 
                       $project->user_id === $user->id ||
                       $user->companies()->where('companies.id', $project->company_id)->exists();
            }
            return false;
        });

        Gate::define('delete-project', function (User $user, Project $project) {
            if ($user->hasPermissionTo('projects.delete')) {
                return $user->hasRole('Administrator') || 
                       $project->user_id === $user->id;
            }
            return false;
        });

        // Gates pour les tickets
        Gate::define('view-ticket', function (User $user, Ticket $ticket) {
            if ($user->hasPermissionTo('tickets.view')) {
                return $user->hasRole('Administrator') || 
                       $ticket->user_id === $user->id || 
                       $ticket->assigned_to === $user->id ||
                       $user->can('view-project', $ticket->project);
            }
            return false;
        });

        Gate::define('edit-ticket', function (User $user, Ticket $ticket) {
            if ($user->hasPermissionTo('tickets.edit')) {
                return $user->hasRole('Administrator') || 
                       $ticket->user_id === $user->id || 
                       $ticket->assigned_to === $user->id;
            }
            return false;
        });

        Gate::define('delete-ticket', function (User $user, Ticket $ticket) {
            if ($user->hasPermissionTo('tickets.delete')) {
                return $user->hasRole('Administrator') || 
                       $ticket->user_id === $user->id;
            }
            return false;
        });

        Gate::define('assign-ticket', function (User $user, Ticket $ticket) {
            return $user->hasPermissionTo('tickets.assign') && 
                   ($user->hasRole('Administrator') || $user->can('view-project', $ticket->project));
        });

        Gate::define('close-ticket', function (User $user, Ticket $ticket) {
            return $user->hasPermissionTo('tickets.close') && 
                   ($user->hasRole('Administrator') || 
                    $ticket->assigned_to === $user->id || 
                    $user->can('edit-project', $ticket->project));
        });

        // Gates pour les commentaires
        Gate::define('view-comment', function (User $user, Comment $comment) {
            if ($user->hasPermissionTo('comments.view')) {
                return $user->hasRole('Administrator') || 
                       $user->can('view-ticket', $comment->ticket);
            }
            return false;
        });

        Gate::define('edit-comment', function (User $user, Comment $comment) {
            if ($user->hasPermissionTo('comments.edit')) {
                return $user->hasRole('Administrator') || 
                       $comment->user_id === $user->id;
            }
            return false;
        });

        Gate::define('delete-comment', function (User $user, Comment $comment) {
            if ($user->hasPermissionTo('comments.delete')) {
                return $user->hasRole('Administrator') || 
                       $comment->user_id === $user->id;
            }
            return false;
        });

        Gate::define('moderate-comment', function (User $user) {
            return $user->hasPermissionTo('comments.moderate');
        });

        // Gates pour les utilisateurs
        Gate::define('view-users', function (User $user) {
            return $user->hasPermissionTo('users.view');
        });

        Gate::define('edit-user', function (User $user, User $targetUser) {
            if ($user->hasPermissionTo('users.edit')) {
                return $user->hasRole('Administrator') || 
                       $user->id === $targetUser->id;
            }
            return false;
        });

        Gate::define('delete-user', function (User $user, User $targetUser) {
            return $user->hasRole('Administrator') && 
                   $user->hasPermissionTo('users.delete') && 
                   $user->id !== $targetUser->id;
        });

        Gate::define('activate-user', function (User $user) {
            return $user->hasPermissionTo('users.activate');
        });

        Gate::define('assign-roles', function (User $user) {
            return $user->hasPermissionTo('users.assign-roles');
        });

        // Gates pour l'administration
        Gate::define('access-admin', function (User $user) {
            return $user->hasRole('Administrator');
        });

        Gate::define('view-analytics', function (User $user) {
            return $user->hasPermissionTo('admin.analytics');
        });

        Gate::define('manage-settings', function (User $user) {
            return $user->hasPermissionTo('admin.settings');
        });

        Gate::define('view-logs', function (User $user) {
            return $user->hasPermissionTo('admin.logs');
        });

        // Gates pour la gestion des configurations
        Gate::define('manage-ticket-statuses', function (User $user) {
            return $user->hasPermissionTo('manage.ticket.statuses');
        });

        Gate::define('manage-ticket-priorities', function (User $user) {
            return $user->hasPermissionTo('manage.ticket.priorities');
        });

        Gate::define('manage-ticket-types', function (User $user) {
            return $user->hasPermissionTo('manage.ticket.types');
        });

        Gate::define('manage-icons', function (User $user) {
            return $user->hasPermissionTo('manage.icons');
        });
    }
}
