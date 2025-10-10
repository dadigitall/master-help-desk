<?php

return [
    // Titres et en-têtes
    'admin' => 'Administration',
    'admin_panel' => 'Panneau d\'administration',
    'admin_dashboard' => 'Tableau de bord administrateur',
    'system_management' => 'Gestion système',
    'user_management' => 'Gestion des utilisateurs',
    'role_management' => 'Gestion des rôles',
    'permission_management' => 'Gestion des permissions',
    'system_configuration' => 'Configuration système',
    'system_logs' => 'Journaux système',
    'system_backup' => 'Sauvegarde système',
    'system_maintenance' => 'Maintenance système',
    
    // Tableau de bord administrateur
    'dashboard' => [
        'title' => 'Tableau de bord administrateur',
        'overview' => 'Aperçu',
        'statistics' => 'Statistiques',
        'recent_activity' => 'Activité récente',
        'system_status' => 'État du système',
        'quick_actions' => 'Actions rapides',
        'system_info' => 'Informations système',
    ],
    
    // Statistiques administrateur
    'stats' => [
        'total_users' => 'Total des utilisateurs',
        'active_users' => 'Utilisateurs actifs',
        'new_users_today' => 'Nouveaux utilisateurs aujourd\'hui',
        'total_companies' => 'Total des entreprises',
        'total_projects' => 'Total des projets',
        'total_tickets' => 'Total des tickets',
        'open_tickets' => 'Tickets ouverts',
        'closed_tickets' => 'Tickets fermés',
        'system_load' => 'Charge système',
        'disk_usage' => 'Utilisation disque',
        'memory_usage' => 'Utilisation mémoire',
        'database_size' => 'Taille de la base de données',
        'uptime' => 'Temps de fonctionnement',
        'last_backup' => 'Dernière sauvegarde',
    ],
    
    // Gestion des utilisateurs
    'users' => [
        'title' => 'Gestion des utilisateurs',
        'list' => 'Liste des utilisateurs',
        'create' => 'Créer un utilisateur',
        'edit' => 'Modifier l\'utilisateur',
        'delete' => 'Supprimer l\'utilisateur',
        'view' => 'Voir l\'utilisateur',
        'activate' => 'Activer l\'utilisateur',
        'deactivate' => 'Désactiver l\'utilisateur',
        'block' => 'Bloquer l\'utilisateur',
        'unblock' => 'Débloquer l\'utilisateur',
        'reset_password' => 'Réinitialiser le mot de passe',
        'send_welcome_email' => 'Envoyer l\'email de bienvenue',
        'impersonate' => 'Se connecter en tant que',
        'login_as' => 'Se connecter en tant que',
        'export' => 'Exporter les utilisateurs',
        'import' => 'Importer des utilisateurs',
        'bulk_actions' => 'Actions en masse',
        'search' => 'Rechercher des utilisateurs',
        'filter' => 'Filtrer les utilisateurs',
    ],
    
    // Champs utilisateur
    'user_fields' => [
        'name' => 'Nom',
        'email' => 'Email',
        'username' => 'Nom d\'utilisateur',
        'password' => 'Mot de passe',
        'password_confirmation' => 'Confirmation du mot de passe',
        'role' => 'Rôle',
        'permissions' => 'Permissions',
        'status' => 'Statut',
        'locale' => 'Langue',
        'timezone' => 'Fuseau horaire',
        'last_login' => 'Dernière connexion',
        'created_at' => 'Créé le',
        'updated_at' => 'Mis à jour le',
        'email_verified_at' => 'Email vérifié le',
        'two_factor_enabled' => 'Authentification à deux facteurs activée',
    ],
    
    // Statuts utilisateur
    'user_status' => [
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'pending' => 'En attente',
        'suspended' => 'Suspendu',
        'blocked' => 'Bloqué',
        'banned' => 'Banni',
    ],
    
    // Gestion des rôles
    'roles' => [
        'title' => 'Gestion des rôles',
        'list' => 'Liste des rôles',
        'create' => 'Créer un rôle',
        'edit' => 'Modifier le rôle',
        'delete' => 'Supprimer le rôle',
        'view' => 'Voir le rôle',
        'assign_permissions' => 'Assigner des permissions',
        'clone' => 'Cloner le rôle',
        'export' => 'Exporter les rôles',
        'import' => 'Importer des rôles',
    ],
    
    // Rôles prédéfinis
    'predefined_roles' => [
        'super_admin' => 'Super administrateur',
        'admin' => 'Administrateur',
        'manager' => 'Gestionnaire',
        'user' => 'Utilisateur',
        'guest' => 'Invité',
        'developer' => 'Développeur',
        'support' => 'Support',
        'sales' => 'Ventes',
        'accounting' => 'Comptabilité',
        'hr' => 'Ressources humaines',
    ],
    
    // Gestion des permissions
    'permissions' => [
        'title' => 'Gestion des permissions',
        'list' => 'Liste des permissions',
        'create' => 'Créer une permission',
        'edit' => 'Modifier la permission',
        'delete' => 'Supprimer la permission',
        'view' => 'Voir la permission',
        'group' => 'Groupe de permissions',
        'category' => 'Catégorie',
        'description' => 'Description',
        'export' => 'Exporter les permissions',
        'import' => 'Importer des permissions',
    ],
    
    // Catégories de permissions
    'permission_categories' => [
        'dashboard' => 'Tableau de bord',
        'users' => 'Utilisateurs',
        'roles' => 'Rôles',
        'permissions' => 'Permissions',
        'companies' => 'Entreprises',
        'projects' => 'Projets',
        'tickets' => 'Tickets',
        'reports' => 'Rapports',
        'system' => 'Système',
        'settings' => 'Paramètres',
        'logs' => 'Journaux',
        'backup' => 'Sauvegarde',
        'maintenance' => 'Maintenance',
    ],
    
    // Configuration système
    'settings' => [
        'title' => 'Configuration système',
        'general' => 'Paramètres généraux',
        'security' => 'Paramètres de sécurité',
        'email' => 'Configuration email',
        'storage' => 'Configuration stockage',
        'backup' => 'Configuration sauvegarde',
        'maintenance' => 'Configuration maintenance',
        'performance' => 'Configuration performance',
        'localization' => 'Configuration localisation',
        'integrations' => 'Configuration intégrations',
        'api' => 'Configuration API',
    ],
    
    // Paramètres généraux
    'general_settings' => [
        'app_name' => 'Nom de l\'application',
        'app_url' => 'URL de l\'application',
        'app_timezone' => 'Fuseau horaire de l\'application',
        'app_locale' => 'Langue par défaut',
        'app_fallback_locale' => 'Langue de secours',
        'app_debug' => 'Mode debug',
        'app_env' => 'Environnement',
        'max_file_size' => 'Taille maximale des fichiers',
        'allowed_file_types' => 'Types de fichiers autorisés',
        'session_lifetime' => 'Durée de vie de la session',
        'password_min_length' => 'Longueur minimale du mot de passe',
        'require_email_verification' => 'Vérification email requise',
        'enable_registration' => 'Activer l\'inscription',
        'enable_social_login' => 'Activer la connexion sociale',
    ],
    
    // Paramètres de sécurité
    'security_settings' => [
        'enable_two_factor' => 'Activer l\'authentification à deux facteurs',
        'require_two_factor' => 'Exiger l\'authentification à deux facteurs',
        'session_timeout' => 'Délai d\'expiration de session',
        'max_login_attempts' => 'Tentatives de connexion maximales',
        'lockout_duration' => 'Durée de verrouillage',
        'password_expiration' => 'Expiration du mot de passe',
        'password_history' => 'Historique des mots de passe',
        'enable_captcha' => 'Activer le CAPTCHA',
        'enable_ip_whitelist' => 'Activer la liste blanche IP',
        'enable_ip_blacklist' => 'Activer la liste noire IP',
        'log_failed_attempts' => 'Journaliser les tentatives échouées',
        'log_successful_attempts' => 'Journaliser les tentatives réussies',
    ],
    
    // Configuration email
    'email_settings' => [
        'mail_driver' => 'Driver email',
        'mail_host' => 'Hôte SMTP',
        'mail_port' => 'Port SMTP',
        'mail_username' => 'Nom d\'utilisateur SMTP',
        'mail_password' => 'Mot de passe SMTP',
        'mail_encryption' => 'Chiffrement SMTP',
        'mail_from_address' => 'Adresse d\'expéditeur',
        'mail_from_name' => 'Nom d\'expéditeur',
        'email_queue' => 'File d\'attente email',
        'email_throttle' => 'Limitation email',
    ],
    
    // Journaux système
    'logs' => [
        'title' => 'Journaux système',
        'application_logs' => 'Journaux d\'application',
        'error_logs' => 'Journaux d\'erreurs',
        'access_logs' => 'Journaux d\'accès',
        'security_logs' => 'Journaux de sécurité',
        'audit_logs' => 'Journaux d\'audit',
        'email_logs' => 'Journaux email',
        'system_logs' => 'Journaux système',
        'view_log' => 'Voir le journal',
        'download_log' => 'Télécharger le journal',
        'clear_log' => 'Effacer le journal',
        'search_logs' => 'Rechercher dans les journaux',
        'filter_logs' => 'Filtrer les journaux',
    ],
    
    // Niveaux de log
    'log_levels' => [
        'emergency' => 'Urgence',
        'alert' => 'Alerte',
        'critical' => 'Critique',
        'error' => 'Erreur',
        'warning' => 'Avertissement',
        'notice' => 'Notification',
        'info' => 'Information',
        'debug' => 'Debug',
    ],
    
    // Sauvegarde système
    'backup' => [
        'title' => 'Sauvegarde système',
        'create_backup' => 'Créer une sauvegarde',
        'restore_backup' => 'Restaurer une sauvegarde',
        'download_backup' => 'Télécharger une sauvegarde',
        'delete_backup' => 'Supprimer une sauvegarde',
        'schedule_backup' => 'Programmer une sauvegarde',
        'backup_settings' => 'Paramètres de sauvegarde',
        'backup_history' => 'Historique des sauvegardes',
        'backup_storage' => 'Stockage des sauvegardes',
        'backup_frequency' => 'Fréquence de sauvegarde',
        'backup_retention' => 'Rétention des sauvegardes',
    ],
    
    // Maintenance système
    'maintenance' => [
        'title' => 'Maintenance système',
        'enable_maintenance' => 'Activer le mode maintenance',
        'disable_maintenance' => 'Désactiver le mode maintenance',
        'maintenance_message' => 'Message de maintenance',
        'allowed_ips' => 'IP autorisées',
        'maintenance_schedule' => 'Programmation maintenance',
        'system_health' => 'Santé système',
        'performance_metrics' => 'Métriques de performance',
        'resource_usage' => 'Utilisation des ressources',
        'disk_cleanup' => 'Nettoyage disque',
        'cache_clear' => 'Vider le cache',
        'log_cleanup' => 'Nettoyage des journaux',
    ],
    
    // Messages
    'messages' => [
        'user_created' => 'Utilisateur créé avec succès',
        'user_updated' => 'Utilisateur mis à jour avec succès',
        'user_deleted' => 'Utilisateur supprimé avec succès',
        'user_activated' => 'Utilisateur activé avec succès',
        'user_deactivated' => 'Utilisateur désactivé avec succès',
        'user_blocked' => 'Utilisateur bloqué avec succès',
        'user_unblocked' => 'Utilisateur débloqué avec succès',
        'password_reset' => 'Mot de passe réinitialisé avec succès',
        'role_created' => 'Rôle créé avec succès',
        'role_updated' => 'Rôle mis à jour avec succès',
        'role_deleted' => 'Rôle supprimé avec succès',
        'permission_created' => 'Permission créée avec succès',
        'permission_updated' => 'Permission mise à jour avec succès',
        'permission_deleted' => 'Permission supprimée avec succès',
        'settings_updated' => 'Paramètres mis à jour avec succès',
        'backup_created' => 'Sauvegarde créée avec succès',
        'backup_restored' => 'Sauvegarde restaurée avec succès',
        'maintenance_enabled' => 'Mode maintenance activé',
        'maintenance_disabled' => 'Mode maintenance désactivé',
    ],
    
    // Messages d'erreur
    'errors' => [
        'user_not_found' => 'Utilisateur non trouvé',
        'role_not_found' => 'Rôle non trouvé',
        'permission_not_found' => 'Permission non trouvée',
        'cannot_delete_admin' => 'Impossible de supprimer un administrateur',
        'cannot_delete_last_admin' => 'Impossible de supprimer le dernier administrateur',
        'role_in_use' => 'Ce rôle est utilisé par des utilisateurs',
        'permission_in_use' => 'Cette permission est utilisée par des rôles',
        'invalid_email' => 'Email invalide',
        'email_already_exists' => 'Cet email existe déjà',
        'username_already_exists' => 'Ce nom d\'utilisateur existe déjà',
        'invalid_password' => 'Mot de passe invalide',
        'password_mismatch' => 'Les mots de passe ne correspondent pas',
        'backup_not_found' => 'Sauvegarde non trouvée',
        'backup_corrupted' => 'Sauvegarde corrompue',
        'insufficient_permissions' => 'Permissions insuffisantes',
        'system_error' => 'Erreur système',
    ],
    
    // Messages de confirmation
    'confirmations' => [
        'delete_user' => 'Êtes-vous sûr de vouloir supprimer cet utilisateur ?',
        'delete_role' => 'Êtes-vous sûr de vouloir supprimer ce rôle ?',
        'delete_permission' => 'Êtes-vous sûr de vouloir supprimer cette permission ?',
        'activate_user' => 'Êtes-vous sûr de vouloir activer cet utilisateur ?',
        'deactivate_user' => 'Êtes-vous sûr de vouloir désactiver cet utilisateur ?',
        'block_user' => 'Êtes-vous sûr de vouloir bloquer cet utilisateur ?',
        'reset_password' => 'Êtes-vous sûr de vouloir réinitialiser le mot de passe de cet utilisateur ?',
        'delete_backup' => 'Êtes-vous sûr de vouloir supprimer cette sauvegarde ?',
        'restore_backup' => 'Êtes-vous sûr de vouloir restaurer cette sauvegarde ?',
        'enable_maintenance' => 'Êtes-vous sûr de vouloir activer le mode maintenance ?',
        'clear_logs' => 'Êtes-vous sûr de vouloir effacer les journaux ?',
    ],
    
    // Actions en masse
    'bulk_actions' => [
        'delete_selected' => 'Supprimer la sélection',
        'activate_selected' => 'Activer la sélection',
        'deactivate_selected' => 'Désactiver la sélection',
        'block_selected' => 'Bloquer la sélection',
        'unblock_selected' => 'Débloquer la sélection',
        'send_email_selected' => 'Envoyer un email à la sélection',
        'assign_role_selected' => 'Assigner un rôle à la sélection',
        'remove_role_selected' => 'Retirer un rôle de la sélection',
        'export_selected' => 'Exporter la sélection',
    ],
    
    // Filtres
    'filters' => [
        'filter_by_status' => 'Filtrer par statut',
        'filter_by_role' => 'Filtrer par rôle',
        'filter_by_permissions' => 'Filtrer par permissions',
        'filter_by_date_range' => 'Filtrer par plage de dates',
        'filter_by_activity' => 'Filtrer par activité',
        'filter_by_login' => 'Filtrer par connexion',
        'filter_by_created_at' => 'Filtrer par date de création',
        'filter_by_updated_at' => 'Filtrer par date de mise à jour',
    ],
    
    // Tri
    'sorting' => [
        'sort_by_name' => 'Trier par nom',
        'sort_by_email' => 'Trier par email',
        'sort_by_role' => 'Trier par rôle',
        'sort_by_status' => 'Trier par statut',
        'sort_by_created_at' => 'Trier par date de création',
        'sort_by_updated_at' => 'Trier par date de mise à jour',
        'sort_by_last_login' => 'Trier par dernière connexion',
        'sort_ascending' => 'Tri croissant',
        'sort_descending' => 'Tri décroissant',
    ],
    
    // Recherche
    'search' => [
        'search_users' => 'Rechercher des utilisateurs...',
        'search_roles' => 'Rechercher des rôles...',
        'search_permissions' => 'Rechercher des permissions...',
        'search_logs' => 'Rechercher dans les journaux...',
        'search_backups' => 'Rechercher des sauvegardes...',
        'no_results' => 'Aucun résultat trouvé',
        'searching' => 'Recherche en cours...',
    ],
    
    // Export et import
    'export_import' => [
        'export_users' => 'Exporter les utilisateurs',
        'import_users' => 'Importer des utilisateurs',
        'export_roles' => 'Exporter les rôles',
        'import_roles' => 'Importer les rôles',
        'export_permissions' => 'Exporter les permissions',
        'import_permissions' => 'Importer les permissions',
        'export_to_csv' => 'Exporter en CSV',
        'export_to_excel' => 'Exporter en Excel',
        'export_to_json' => 'Exporter en JSON',
        'import_from_csv' => 'Importer depuis CSV',
        'import_from_excel' => 'Importer depuis Excel',
        'import_from_json' => 'Importer depuis JSON',
        'download_template' => 'Télécharger un modèle',
        'preview_import' => 'Aperçu de l\'importation',
        'validate_import' => 'Valider l\'importation',
        'import_results' => 'Résultats de l\'importation',
    ],
    
    // Notifications administrateur
    'notifications' => [
        'new_user_registered' => 'Nouvel utilisateur inscrit',
        'user_account_suspended' => 'Compte utilisateur suspendu',
        'system_error_occurred' => 'Erreur système survenue',
        'backup_completed' => 'Sauvegarde terminée',
        'backup_failed' => 'Sauvegarde échouée',
        'maintenance_scheduled' => 'Maintenance programmée',
        'security_alert' => 'Alerte de sécurité',
        'disk_space_low' => 'Espace disque faible',
        'memory_usage_high' => 'Utilisation mémoire élevée',
        'database_connection_failed' => 'Connexion base de données échouée',
        'email_queue_stuck' => 'File d\'attente email bloquée',
    ],
    
    // État du système
    'system_status' => [
        'healthy' => 'Sain',
        'warning' => 'Avertissement',
        'critical' => 'Critique',
        'offline' => 'Hors ligne',
        'maintenance' => 'Maintenance',
        'updating' => 'Mise à jour',
        'restarting' => 'Redémarrage',
    ],
    
    // Informations système
    'system_info' => [
        'php_version' => 'Version PHP',
        'laravel_version' => 'Version Laravel',
        'database_version' => 'Version base de données',
        'web_server' => 'Serveur web',
        'operating_system' => 'Système d\'exploitation',
        'memory_limit' => 'Limite mémoire',
        'max_execution_time' => 'Temps d\'exécution maximal',
        'upload_max_filesize' => 'Taille maximale d\'upload',
        'disk_space' => 'Espace disque',
        'cpu_usage' => 'Utilisation CPU',
        'memory_usage' => 'Utilisation mémoire',
        'network_status' => 'État réseau',
        'database_status' => 'État base de données',
        'cache_status' => 'État cache',
        'queue_status' => 'État file d\'attente',
    ],
    
    // Actions rapides
    'quick_actions' => [
        'create_user' => 'Créer un utilisateur',
        'view_logs' => 'Voir les journaux',
        'run_backup' => 'Exécuter une sauvegarde',
        'clear_cache' => 'Vider le cache',
        'check_updates' => 'Vérifier les mises à jour',
        'system_health_check' => 'Vérification santé système',
        'optimize_database' => 'Optimiser la base de données',
        'restart_services' => 'Redémarrer les services',
    ],
    
    // Outils système
    'tools' => [
        'php_info' => 'Informations PHP',
        'system_info' => 'Informations système',
        'database_info' => 'Informations base de données',
        'cache_manager' => 'Gestionnaire de cache',
        'queue_monitor' => 'Moniteur de file d\'attente',
        'log_viewer' => 'Visionneur de journaux',
        'route_list' => 'Liste des routes',
        'middleware_list' => 'Liste des middlewares',
        'event_list' => 'Liste des événements',
        'command_list' => 'Liste des commandes',
    ],
    
    // Sécurité
    'security' => [
        'security_center' => 'Centre de sécurité',
        'security_scan' => 'Analyse de sécurité',
        'vulnerability_scan' => 'Analyse de vulnérabilités',
        'firewall_rules' => 'Règles pare-feu',
        'ip_whitelist' => 'Liste blanche IP',
        'ip_blacklist' => 'Liste noire IP',
        'failed_login_attempts' => 'Tentatives de connexion échouées',
        'suspicious_activities' => 'Activités suspectes',
        'security_alerts' => 'Alertes de sécurité',
        'audit_trail' => 'Piste d\'audit',
    ],
    
    // Performances
    'performance' => [
        'performance_monitor' => 'Moniteur de performance',
        'page_load_time' => 'Temps de chargement des pages',
        'database_queries' => 'Requêtes base de données',
        'memory_usage' => 'Utilisation mémoire',
        'cpu_usage' => 'Utilisation CPU',
        'network_usage' => 'Utilisation réseau',
        'disk_io' => 'E/S disque',
        'cache_hit_rate' => 'Taux de succès du cache',
        'response_time' => 'Temps de réponse',
        'throughput' => 'Débit',
    ],
    
    // Divers
    'miscellaneous' => [
        'documentation' => 'Documentation',
        'help_center' => 'Centre d\'aide',
        'support' => 'Support',
        'feedback' => 'Feedback',
        'changelog' => 'Journal des modifications',
        'release_notes' => 'Notes de version',
        'api_documentation' => 'Documentation API',
        'developer_tools' => 'Outils développeur',
        'system_requirements' => 'Configuration requise',
        'license_info' => 'Informations licence',
    ],
];
