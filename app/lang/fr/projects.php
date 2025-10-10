<?php

return [
    // Titres et en-têtes
    'projects' => 'Projets',
    'project' => 'Projet',
    'project_management' => 'Gestion des projets',
    'project_details' => 'Détails du projet',
    'project_overview' => 'Aperçu du projet',
    'project_timeline' => 'Chronologie du projet',
    'project_team' => 'Équipe du projet',
    'project_documents' => 'Documents du projet',
    
    // Statuts des projets
    'status' => [
        'planning' => 'Planification',
        'active' => 'Actif',
        'on_hold' => 'En suspens',
        'completed' => 'Terminé',
        'cancelled' => 'Annulé',
        'archived' => 'Archivé',
        'pending' => 'En attente',
        'in_progress' => 'En cours',
        'testing' => 'En test',
        'deployed' => 'Déployé',
    ],
    
    // Types de projets
    'type' => [
        'development' => 'Développement',
        'design' => 'Design',
        'marketing' => 'Marketing',
        'research' => 'Recherche',
        'consulting' => 'Consulting',
        'maintenance' => 'Maintenance',
        'training' => 'Formation',
        'support' => 'Support',
        'infrastructure' => 'Infrastructure',
        'other' => 'Autre',
    ],
    
    // Priorités des projets
    'priority' => [
        'low' => 'Basse',
        'normal' => 'Normale',
        'medium' => 'Moyenne',
        'high' => 'Haute',
        'urgent' => 'Urgente',
        'critical' => 'Critique',
    ],
    
    // Champs du formulaire
    'name' => 'Nom du projet',
    'description' => 'Description',
    'status' => 'Statut',
    'type' => 'Type',
    'priority' => 'Priorité',
    'start_date' => 'Date de début',
    'end_date' => 'Date de fin',
    'estimated_hours' => 'Heures estimées',
    'actual_hours' => 'Heures réelles',
    'budget' => 'Budget',
    'actual_cost' => 'Coût réel',
    'progress' => 'Progression',
    'manager' => 'Gestionnaire',
    'client' => 'Client',
    'company' => 'Entreprise',
    'team_members' => 'Membres de l\'équipe',
    'tags' => 'Étiquettes',
    'category' => 'Catégorie',
    'repository_url' => 'URL du dépôt',
    'demo_url' => 'URL de démo',
    'documentation_url' => 'URL de documentation',
    
    // Actions
    'create_project' => 'Créer un projet',
    'edit_project' => 'Modifier le projet',
    'delete_project' => 'Supprimer le projet',
    'view_project' => 'Voir le projet',
    'archive_project' => 'Archiver le projet',
    'restore_project' => 'Restaurer le projet',
    'duplicate_project' => 'Dupliquer le projet',
    'export_project' => 'Exporter le projet',
    'print_project' => 'Imprimer le projet',
    
    // Messages
    'project_created' => 'Projet créé avec succès',
    'project_updated' => 'Projet mis à jour avec succès',
    'project_deleted' => 'Projet supprimé avec succès',
    'project_archived' => 'Projet archivé avec succès',
    'project_restored' => 'Projet restauré avec succès',
    'member_added' => 'Membre ajouté avec succès',
    'member_removed' => 'Membre retiré avec succès',
    
    // Messages d'erreur
    'project_not_found' => 'Projet non trouvé',
    'cannot_delete_active_project' => 'Impossible de supprimer un projet actif',
    'invalid_date_range' => 'Plage de dates invalide',
    'budget_exceeded' => 'Budget dépassé',
    'end_date_before_start' => 'La date de fin ne peut pas être antérieure à la date de début',
    'no_permission' => 'Permission refusée',
    
    // Équipe
    'team' => 'Équipe',
    'team_members' => 'Membres de l\'équipe',
    'add_member' => 'Ajouter un membre',
    'remove_member' => 'Retirer un membre',
    'team_lead' => 'Chef d\'équipe',
    'developer' => 'Développeur',
    'designer' => 'Designer',
    'tester' => 'Testeur',
    'analyst' => 'Analyste',
    'stakeholder' => 'Partenaire',
    'observer' => 'Observateur',
    
    // Tâches
    'tasks' => 'Tâches',
    'task' => 'Tâche',
    'create_task' => 'Créer une tâche',
    'edit_task' => 'Modifier la tâche',
    'delete_task' => 'Supprimer la tâche',
    'complete_task' => 'Terminer la tâche',
    'task_status' => [
        'todo' => 'À faire',
        'in_progress' => 'En cours',
        'review' => 'En révision',
        'testing' => 'En test',
        'done' => 'Terminé',
    ],
    'task_priority' => [
        'low' => 'Basse',
        'medium' => 'Moyenne',
        'high' => 'Haute',
        'urgent' => 'Urgente',
    ],
    
    // Jalons
    'milestones' => 'Jalons',
    'milestone' => 'Jalon',
    'create_milestone' => 'Créer un jalon',
    'edit_milestone' => 'Modifier le jalon',
    'delete_milestone' => 'Supprimer le jalon',
    'complete_milestone' => 'Terminer le jalon',
    'milestone_completed' => 'Jalon terminé avec succès',
    
    // Temps
    'time_tracking' => 'Suivi du temps',
    'log_time' => 'Enregistrer le temps',
    'time_entries' => 'Entrées de temps',
    'total_hours' => 'Total des heures',
    'hours_logged' => 'Heures enregistrées',
    'hours_remaining' => 'Heures restantes',
    'time_sheet' => 'Feuille de temps',
    
    // Budget
    'budget_management' => 'Gestion du budget',
    'budget_used' => 'Budget utilisé',
    'budget_remaining' => 'Budget restant',
    'cost_tracking' => 'Suivi des coûts',
    'expenses' => 'Dépenses',
    'add_expense' => 'Ajouter une dépense',
    'edit_expense' => 'Modifier la dépense',
    'delete_expense' => 'Supprimer la dépense',
    
    // Documents
    'documents' => 'Documents',
    'upload_document' => 'Télécharger un document',
    'download_document' => 'Télécharger le document',
    'delete_document' => 'Supprimer le document',
    'document_types' => [
        'requirement' => 'Cahier des charges',
        'specification' => 'Spécification',
        'design' => 'Design',
        'proposal' => 'Proposition',
        'contract' => 'Contrat',
        'report' => 'Rapport',
        'presentation' => 'Présentation',
        'manual' => 'Manuel',
        'other' => 'Autre',
    ],
    
    // Rapports
    'project_report' => 'Rapport de projet',
    'progress_report' => 'Rapport de progression',
    'time_report' => 'Rapport de temps',
    'budget_report' => 'Rapport budgétaire',
    'team_report' => 'Rapport d\'équipe',
    'generate_report' => 'Générer un rapport',
    'export_report' => 'Exporter le rapport',
    
    // Statistiques
    'total_projects' => 'Total des projets',
    'active_projects' => 'Projets actifs',
    'completed_projects' => 'Projets terminés',
    'project_progress' => 'Progression du projet',
    'team_productivity' => 'Productivité de l\'équipe',
    'budget_utilization' => 'Utilisation du budget',
    'time_efficiency' => 'Efficacité temporelle',
    'projects_by_status' => 'Projets par statut',
    'projects_by_type' => 'Projets par type',
    'projects_by_priority' => 'Projets par priorité',
    
    // Filtres
    'filter_by_status' => 'Filtrer par statut',
    'filter_by_type' => 'Filtrer par type',
    'filter_by_priority' => 'Filtrer par priorité',
    'filter_by_manager' => 'Filtrer par gestionnaire',
    'filter_by_client' => 'Filtrer par client',
    'filter_by_date_range' => 'Filtrer par plage de dates',
    'filter_by_team_member' => 'Filtrer par membre d\'équipe',
    
    // Tri
    'sort_by_name' => 'Trier par nom',
    'sort_by_status' => 'Trier par statut',
    'sort_by_priority' => 'Trier par priorité',
    'sort_by_start_date' => 'Trier par date de début',
    'sort_by_end_date' => 'Trier par date de fin',
    'sort_by_progress' => 'Trier par progression',
    'sort_by_budget' => 'Trier par budget',
    
    // Vue tableau
    'board_view' => 'Vue tableau',
    'list_view' => 'Vue liste',
    'calendar_view' => 'Vue calendrier',
    'timeline_view' => 'Vue timeline',
    'gantt_view' => 'Vue Gantt',
    'kanban_view' => 'Vue Kanban',
    
    // Formulaire de création
    'create_new_project' => 'Créer un nouveau projet',
    'project_name_placeholder' => 'Entrez un nom pour votre projet...',
    'project_description_placeholder' => 'Décrivez votre projet en détail...',
    'select_status' => 'Sélectionnez un statut',
    'select_type' => 'Sélectionnez un type',
    'select_priority' => 'Sélectionnez une priorité',
    'set_start_date' => 'Définir la date de début',
    'set_end_date' => 'Définir la date de fin',
    'set_budget' => 'Définir le budget',
    'assign_manager' => 'Assigner un gestionnaire',
    'add_team_members' => 'Ajouter des membres d\'équipe',
    'add_tags' => 'Ajouter des étiquettes',
    
    // Liste des projets
    'projects_list' => 'Liste des projets',
    'my_projects' => 'Mes projets',
    'managed_projects' => 'Projets gérés',
    'archived_projects' => 'Projets archivés',
    'recent_projects' => 'Projets récents',
    'upcoming_deadlines' => 'Échéances à venir',
    'overdue_projects' => 'Projets en retard',
    'high_priority_projects' => 'Projets haute priorité',
    'no_projects_found' => 'Aucun projet trouvé',
    'search_projects' => 'Rechercher des projets...',
    
    // Notifications
    'notification_project_created' => 'Nouveau projet créé : :name',
    'notification_project_updated' => 'Projet mis à jour : :name',
    'notification_member_added' => 'Nouveau membre dans le projet : :project',
    'notification_task_assigned' => 'Nouvelle tâche assignée dans : :project',
    'notification_milestone_completed' => 'Jalon terminé dans : :project',
    'notification_project_completed' => 'Projet terminé : :name',
    'notification_deadline_approaching' => 'Échéance approchant pour : :project',
    
    // Modal et dialogues
    'confirm_delete_project' => 'Êtes-vous sûr de vouloir supprimer ce projet ?',
    'confirm_archive_project' => 'Êtes-vous sûr de vouloir archiver ce projet ?',
    'confirm_remove_member' => 'Êtes-vous sûr de vouloir retirer ce membre ?',
    'select_team_members' => 'Sélectionnez les membres de l\'équipe',
    
    // Templates
    'template' => 'Modèle',
    'project_template' => 'Modèle de projet',
    'use_template' => 'Utiliser un modèle',
    'create_template' => 'Créer un modèle',
    'template_name' => 'Nom du modèle',
    'template_description' => 'Description du modèle',
    'save_as_template' => 'Enregistrer comme modèle',
    
    // Workflow
    'workflow' => 'Workflow',
    'project_workflow' => 'Workflow du projet',
    'automations' => 'Automatisations',
    'create_automation' => 'Créer une automatisation',
    'trigger' => 'Déclencheur',
    'action' => 'Action',
    'condition' => 'Condition',
    
    // Collaboration
    'collaboration' => 'Collaboration',
    'project_chat' => 'Chat du projet',
    'discussions' => 'Discussions',
    'start_discussion' => 'Démarrer une discussion',
    'reply_discussion' => 'Répondre à la discussion',
    'activity_feed' => 'Fil d\'activité',
    'notifications' => 'Notifications',
    
    // Intégrations
    'integrations' => 'Intégrations',
    'git_integration' => 'Intégration Git',
    'slack_integration' => 'Intégration Slack',
    'calendar_integration' => 'Intégration calendrier',
    'email_integration' => 'Intégration email',
    'api_integration' => 'Intégration API',
    
    // Export et import
    'import_projects' => 'Importer des projets',
    'export_projects' => 'Exporter des projets',
    'import_from_csv' => 'Importer depuis CSV',
    'import_from_excel' => 'Importer depuis Excel',
    'export_to_pdf' => 'Exporter en PDF',
    'export_to_excel' => 'Exporter en Excel',
    'export_to_csv' => 'Exporter en CSV',
    
    // Divers
    'project_id' => 'ID du projet',
    'project_code' => 'Code du projet',
    'project_number' => 'Numéro du projet',
    'reference_number' => 'Numéro de référence',
    'client_reference' => 'Référence client',
    'internal_reference' => 'Référence interne',
    'project_url' => 'URL du projet',
    'project_logo' => 'Logo du projet',
    'project_color' => 'Couleur du projet',
    'project_icon' => 'Icône du projet',
    
    // Étiquettes prédéfinies
    'predefined_tags' => 'Étiquettes prédéfinies',
    'urgent' => 'Urgent',
    'important' => 'Important',
    'confidential' => 'Confidentiel',
    'internal' => 'Interne',
    'external' => 'Externe',
    'billable' => 'Facturable',
    'non_billable' => 'Non facturable',
    'research' => 'Recherche',
    'development' => 'Développement',
    'design' => 'Design',
    'testing' => 'Test',
    'deployment' => 'Déploiement',
    
    // Permissions
    'permissions' => 'Permissions',
    'view_project' => 'Voir le projet',
    'edit_project' => 'Modifier le projet',
    'delete_project' => 'Supprimer le projet',
    'manage_team' => 'Gérer l\'équipe',
    'manage_tasks' => 'Gérer les tâches',
    'manage_budget' => 'Gérer le budget',
    'view_reports' => 'Voir les rapports',
    'export_data' => 'Exporter les données',
    
    // Archive
    'archive' => 'Archive',
    'archived_projects' => 'Projets archivés',
    'restore_from_archive' => 'Restaurer de l\'archive',
    'permanent_delete' => 'Suppression définitive',
    'confirm_permanent_delete' => 'Êtes-vous sûr de vouloir supprimer définitivement ce projet ?',
];
