<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Permissions de gestion des entreprises
            'companies.view',
            'companies.create',
            'companies.edit',
            'companies.delete',

            // Permissions de gestion des projets
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',
            'projects.assign',

            // Permissions de gestion des tickets
            'tickets.view',
            'tickets.create',
            'tickets.edit',
            'tickets.delete',
            'tickets.assign',
            'tickets.close',
            'tickets.reopen',

            // Permissions de gestion des commentaires
            'comments.view',
            'comments.create',
            'comments.edit',
            'comments.delete',
            'comments.moderate',

            // Permissions de gestion du chat
            'chat.view',
            'chat.send',
            'chat.moderate',

            // Permissions de gestion des utilisateurs
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.activate',
            'users.assign-roles',

            // Permissions d'administration
            'admin.dashboard',
            'admin.settings',
            'admin.analytics',
            'admin.logs',

            // Permissions de dashboard
            'dashboard.view',
            'dashboard.stats',
            'dashboard.export',

            // Permissions de gestion des configurations
            'manage.ticket.statuses',
            'manage.ticket.priorities',
            'manage.ticket.types',
            'manage.icons',

            // Permissions de gestion des queues et jobs
            'view_horizon',
            'manage_queues',
            'restart_queues',
            'view_failed_jobs',
            'retry_failed_jobs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Administrator role - all permissions
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $adminRole->syncPermissions(Permission::all());

        // Employee role - permissions pour les employés (Manager/Agent)
        $employeeRole = Role::firstOrCreate(['name' => 'Employee']);
        $employeeRole->syncPermissions([
            // Entreprises
            'companies.view',
            'companies.create',
            'companies.edit',
            
            // Projets
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',
            'projects.assign',
            
            // Tickets
            'tickets.view',
            'tickets.create',
            'tickets.edit',
            'tickets.delete',
            'tickets.assign',
            'tickets.close',
            'tickets.reopen',
            
            // Commentaires
            'comments.view',
            'comments.create',
            'comments.edit',
            'comments.delete',
            'comments.moderate',
            
            // Chat
            'chat.view',
            'chat.send',
            'chat.moderate',
            
            // Utilisateurs (limité)
            'users.view',
            'users.activate',
            
            // Dashboard
            'dashboard.view',
            'dashboard.stats',
        ]);

        // Customer role - permissions pour les clients
        $customerRole = Role::firstOrCreate(['name' => 'Customer']);
        $customerRole->syncPermissions([
            // Projets (limité à ses projets)
            'projects.view',
            
            // Tickets (limité à ses tickets)
            'tickets.view',
            'tickets.create',
            'tickets.edit',
            
            // Commentaires (limité à ses commentaires)
            'comments.view',
            'comments.create',
            'comments.edit',
            'comments.delete',
            
            // Chat (limité à ses tickets)
            'chat.view',
            'chat.send',
        ]);

        $this->command->info('Roles and permissions created successfully.');
    }
}
