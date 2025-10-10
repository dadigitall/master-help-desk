<?php

return [
    // Titres et en-têtes
    'tickets' => 'Tickets',
    'ticket' => 'Ticket',
    'ticket_management' => 'Gestion des tickets',
    'ticket_details' => 'Détails du ticket',
    'ticket_history' => 'Historique du ticket',
    'ticket_comments' => 'Commentaires du ticket',
    'ticket_attachments' => 'Pièces jointes du ticket',
    
    // Statuts des tickets
    'status' => [
        'open' => 'Ouvert',
        'in_progress' => 'En cours',
        'pending' => 'En attente',
        'resolved' => 'Résolu',
        'closed' => 'Fermé',
        'reopened' => 'Rouvert',
        'cancelled' => 'Annulé',
        'on_hold' => 'En suspens',
    ],
    
    // Priorités des tickets
    'priority' => [
        'low' => 'Basse',
        'normal' => 'Normale',
        'medium' => 'Moyenne',
        'high' => 'Haute',
        'urgent' => 'Urgente',
        'critical' => 'Critique',
    ],
    
    // Types de tickets
    'type' => [
        'bug' => 'Bogue',
        'feature_request' => 'Demande de fonctionnalité',
        'enhancement' => 'Amélioration',
        'question' => 'Question',
        'support' => 'Support',
        'incident' => 'Incident',
        'maintenance' => 'Maintenance',
        'other' => 'Autre',
    ],
    
    // Champs du formulaire
    'title' => 'Titre',
    'description' => 'Description',
    'status' => 'Statut',
    'priority' => 'Priorité',
    'type' => 'Type',
    'assigned_to' => 'Assigné à',
    'project' => 'Projet',
    'company' => 'Entreprise',
    'created_by' => 'Créé par',
    'created_at' => 'Créé le',
    'updated_at' => 'Mis à jour le',
    'closed_at' => 'Fermé le',
    'resolved_at' => 'Résolu le',
    'due_date' => 'Date d\'échéance',
    'estimated_time' => 'Temps estimé',
    'actual_time' => 'Temps réel',
    'tags' => 'Étiquettes',
    'category' => 'Catégorie',
    
    // Actions
    'create_ticket' => 'Créer un ticket',
    'edit_ticket' => 'Modifier le ticket',
    'delete_ticket' => 'Supprimer le ticket',
    'view_ticket' => 'Voir le ticket',
    'close_ticket' => 'Fermer le ticket',
    'reopen_ticket' => 'Rouvrir le ticket',
    'assign_ticket' => 'Assigner le ticket',
    'unassign_ticket' => 'Désassigner le ticket',
    'escalate_ticket' => 'Escalader le ticket',
    'merge_ticket' => 'Fusionner le ticket',
    'split_ticket' => 'Diviser le ticket',
    'duplicate_ticket' => 'Dupliquer le ticket',
    'export_ticket' => 'Exporter le ticket',
    'print_ticket' => 'Imprimer le ticket',
    
    // Messages
    'ticket_created' => 'Ticket créé avec succès',
    'ticket_updated' => 'Ticket mis à jour avec succès',
    'ticket_deleted' => 'Ticket supprimé avec succès',
    'ticket_closed' => 'Ticket fermé avec succès',
    'ticket_reopened' => 'Ticket rouvert avec succès',
    'ticket_assigned' => 'Ticket assigné avec succès',
    'ticket_unassigned' => 'Ticket désassigné avec succès',
    'ticket_escalated' => 'Ticket escaladé avec succès',
    'ticket_merged' => 'Tickets fusionnés avec succès',
    
    // Messages d'erreur
    'ticket_not_found' => 'Ticket non trouvé',
    'cannot_delete_assigned_ticket' => 'Impossible de supprimer un ticket assigné',
    'cannot_close_open_tasks' => 'Impossible de fermer un ticket avec des tâches ouvertes',
    'invalid_status_transition' => 'Transition de statut invalide',
    'no_permission' => 'Permission refusée',
    'attachment_too_large' => 'Pièce jointe trop volumineuse',
    'invalid_file_type' => 'Type de fichier invalide',
    
    // Filtres
    'filter_by_status' => 'Filtrer par statut',
    'filter_by_priority' => 'Filtrer par priorité',
    'filter_by_type' => 'Filtrer par type',
    'filter_by_project' => 'Filtrer par projet',
    'filter_by_company' => 'Filtrer par entreprise',
    'filter_by_assigned_to' => 'Filtrer par assigné à',
    'filter_by_created_by' => 'Filtrer par créé par',
    'filter_by_date_range' => 'Filtrer par plage de dates',
    'filter_by_tags' => 'Filtrer par étiquettes',
    
    // Tri
    'sort_by_title' => 'Trier par titre',
    'sort_by_status' => 'Trier par statut',
    'sort_by_priority' => 'Trier par priorité',
    'sort_by_type' => 'Trier par type',
    'sort_by_created_date' => 'Trier par date de création',
    'sort_by_updated_date' => 'Trier par date de mise à jour',
    'sort_by_due_date' => 'Trier par date d\'échéance',
    
    // Commentaires
    'add_comment' => 'Ajouter un commentaire',
    'edit_comment' => 'Modifier le commentaire',
    'delete_comment' => 'Supprimer le commentaire',
    'comment_added' => 'Commentaire ajouté avec succès',
    'comment_updated' => 'Commentaire mis à jour avec succès',
    'comment_deleted' => 'Commentaire supprimé avec succès',
    'no_comments' => 'Aucun commentaire',
    'comment_placeholder' => 'Écrivez votre commentaire ici...',
    
    // Pièces jointes
    'add_attachment' => 'Ajouter une pièce jointe',
    'remove_attachment' => 'Supprimer la pièce jointe',
    'download_attachment' => 'Télécharger la pièce jointe',
    'attachment_added' => 'Pièce jointe ajoutée avec succès',
    'attachment_removed' => 'Pièce jointe supprimée avec succès',
    'no_attachments' => 'Aucune pièce jointe',
    'max_file_size' => 'Taille maximale du fichier : :size',
    'allowed_file_types' => 'Types de fichiers autorisés : :types',
    
    // Temps
    'time_tracking' => 'Suivi du temps',
    'add_time_entry' => 'Ajouter une entrée de temps',
    'edit_time_entry' => 'Modifier l\'entrée de temps',
    'delete_time_entry' => 'Supprimer l\'entrée de temps',
    'time_logged' => 'Temps enregistré',
    'total_time' => 'Temps total',
    'time_spent' => 'Temps passé',
    'time_remaining' => 'Temps restant',
    'hours' => 'heures',
    'minutes' => 'minutes',
    
    // Notifications
    'notification_ticket_created' => 'Nouveau ticket créé : :title',
    'notification_ticket_updated' => 'Ticket mis à jour : :title',
    'notification_ticket_assigned' => 'Ticket assigné : :title',
    'notification_ticket_commented' => 'Nouveau commentaire sur : :title',
    'notification_ticket_closed' => 'Ticket fermé : :title',
    'notification_ticket_reopened' => 'Ticket rouvert : :title',
    
    // Rapports
    'ticket_report' => 'Rapport de tickets',
    'generate_ticket_report' => 'Générer un rapport de tickets',
    'export_to_pdf' => 'Exporter en PDF',
    'export_to_excel' => 'Exporter en Excel',
    'export_to_csv' => 'Exporter en CSV',
    
    // Statistiques
    'total_tickets' => 'Total des tickets',
    'open_tickets' => 'Tickets ouverts',
    'closed_tickets' => 'Tickets fermés',
    'tickets_this_week' => 'Tickets cette semaine',
    'tickets_this_month' => 'Tickets ce mois-ci',
    'average_resolution_time' => 'Temps de résolution moyen',
    'tickets_by_priority' => 'Tickets par priorité',
    'tickets_by_status' => 'Tickets par statut',
    'tickets_by_type' => 'Tickets par type',
    'tickets_by_project' => 'Tickets par projet',
    'tickets_by_company' => 'Tickets par entreprise',
    
    // Formulaire de création
    'create_new_ticket' => 'Créer un nouveau ticket',
    'ticket_title_placeholder' => 'Entrez un titre descriptif pour votre ticket...',
    'ticket_description_placeholder' => 'Décrivez en détail votre problème ou votre demande...',
    'select_priority' => 'Sélectionnez une priorité',
    'select_type' => 'Sélectionnez un type',
    'select_project' => 'Sélectionnez un projet',
    'select_company' => 'Sélectionnez une entreprise',
    'assign_to_user' => 'Assigner à un utilisateur',
    'set_due_date' => 'Définir une date d\'échéance',
    'add_tags' => 'Ajouter des étiquettes',
    
    // Liste des tickets
    'tickets_list' => 'Liste des tickets',
    'my_tickets' => 'Mes tickets',
    'assigned_to_me' => 'Assignés à moi',
    'unassigned_tickets' => 'Tickets non assignés',
    'recent_tickets' => 'Tickets récents',
    'overdue_tickets' => 'Tickets en retard',
    'high_priority_tickets' => 'Tickets haute priorité',
    'no_tickets_found' => 'Aucun ticket trouvé',
    'search_tickets' => 'Rechercher des tickets...',
    
    // Modal et dialogues
    'confirm_delete_ticket' => 'Êtes-vous sûr de vouloir supprimer ce ticket ?',
    'confirm_close_ticket' => 'Êtes-vous sûr de vouloir fermer ce ticket ?',
    'confirm_reopen_ticket' => 'Êtes-vous sûr de vouloir rouvrir ce ticket ?',
    'select_tickets_to_merge' => 'Sélectionnez les tickets à fusionner',
    'merge_with_ticket' => 'Fusionner avec le ticket n° :id',
    
    // Templates
    'template' => 'Modèle',
    'use_template' => 'Utiliser un modèle',
    'create_template' => 'Créer un modèle',
    'template_name' => 'Nom du modèle',
    'template_description' => 'Description du modèle',
    'save_as_template' => 'Enregistrer comme modèle',
    
    // Workflow
    'workflow' => 'Workflow',
    'automations' => 'Automatisations',
    'create_automation' => 'Créer une automatisation',
    'edit_automation' => 'Modifier l\'automatisation',
    'delete_automation' => 'Supprimer l\'automatisation',
    'trigger' => 'Déclencheur',
    'action' => 'Action',
    'condition' => 'Condition',
    
    // SLA (Service Level Agreement)
    'sla' => 'SLA',
    'sla_breach' => 'Violation SLA',
    'sla_warning' => 'Alerte SLA',
    'sla_met' => 'SLA respecté',
    'response_time' => 'Temps de réponse',
    'resolution_time' => 'Temps de résolution',
    'first_response' => 'Première réponse',
    
    // Connaissances
    'knowledge_base' => 'Base de connaissances',
    'related_articles' => 'Articles connexes',
    'suggested_solutions' => 'Solutions suggérées',
    'frequent_questions' => 'Questions fréquentes',
    
    // Étiquettes prédéfinies
    'predefined_tags' => 'Étiquettes prédéfinies',
    'bug_report' => 'Rapport de bogue',
    'feature_request' => 'Demande de fonctionnalité',
    'urgent' => 'Urgent',
    'customer_issue' => 'Problème client',
    'internal' => 'Interne',
    'high_impact' => 'Impact élevé',
    'low_impact' => 'Impact faible',
    
    // Vue kanban
    'kanban_view' => 'Vue Kanban',
    'board_view' => 'Vue tableau',
    'list_view' => 'Vue liste',
    'card_view' => 'Vue carte',
    'drag_and_drop' => 'Glisser-déposer',
    
    // Vue calendrier
    'calendar_view' => 'Vue calendrier',
    'timeline_view' => 'Vue timeline',
    'gantt_view' => 'Vue Gantt',
    
    // Export et import
    'import_tickets' => 'Importer des tickets',
    'import_from_csv' => 'Importer depuis CSV',
    'import_from_excel' => 'Importer depuis Excel',
    'import_preview' => 'Aperçu de l\'importation',
    'import_mapping' => 'Mappage des champs',
    'import_results' => 'Résultats de l\'importation',
    
    // Divers
    'ticket编号' => 'Numéro du ticket',
    'reference_number' => 'Numéro de référence',
    'case_number' => 'Numéro de dossier',
    'incident_number' => 'Numéro d\'incident',
    'request_number' => 'Numéro de demande',
    'work_order' => 'Ordre de travail',
];
