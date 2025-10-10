# Phase 3: Système de Permissions et Rôles - TODO LIST

## Étape 3.1: Configuration des permissions
- [x] Définir toutes les permissions selon le cahier des charges
- [x] Créer les permissions pour les projets
- [x] Créer les permissions pour les tickets
- [x] Créer les permissions pour les utilisateurs
- [x] Créer les permissions pour les entreprises
- [x] Créer les permissions d'administration

## Étape 3.2: Configuration des rôles
- [x] Créer le rôle Administrator
- [x] Créer le rôle Employee
- [x] Créer le rôle Customer
- [x] Assigner les permissions appropriées à chaque rôle

## Étape 3.3: Middleware et Gates
- [x] Configurer les middleware de protection
- [x] Créer les gates pour les autorisations personnalisées
- [ ] Mettre en place la protection des routes

## Étape 3.4: Tests et validation
- [ ] Tester l'assignation des rôles
- [ ] Tester les permissions sur les modèles
- [ ] Tester les middleware et gates
- [ ] Valider le système complet

---

## Permissions prévues

### Permissions de gestion des entreprises
- `companies.view` - Voir les entreprises
- `companies.create` - Créer des entreprises
- `companies.edit` - Modifier des entreprises
- `companies.delete` - Supprimer des entreprises

### Permissions de gestion des projets
- `projects.view` - Voir les projets
- `projects.create` - Créer des projets
- `projects.edit` - Modifier des projets
- `projects.delete` - Supprimer des projets
- `projects.assign` - Assigner des utilisateurs aux projets

### Permissions de gestion des tickets
- `tickets.view` - Voir les tickets
- `tickets.create` - Créer des tickets
- `tickets.edit` - Modifier les tickets
- `tickets.delete` - Supprimer des tickets
- `tickets.assign` - Assigner des tickets
- `tickets.close` - Fermer des tickets
- `tickets.reopen` - Rouvrir des tickets

### Permissions de gestion des commentaires
- `comments.view` - Voir les commentaires
- `comments.create` - Créer des commentaires
- `comments.edit` - Modifier ses commentaires
- `comments.delete` - Supprimer ses commentaires
- `comments.moderate` - Modérer tous les commentaires

### Permissions de gestion du chat
- `chat.view` - Voir le chat
- `chat.send` - Envoyer des messages
- `chat.moderate` - Modérer le chat

### Permissions de gestion des utilisateurs
- `users.view` - Voir les utilisateurs
- `users.create` - Créer des utilisateurs
- `users.edit` - Modifier les utilisateurs
- `users.delete` - Supprimer des utilisateurs
- `users.activate` - Activer/désactiver des utilisateurs
- `users.assign-roles` - Assigner des rôles

### Permissions d'administration
- `admin.dashboard` - Accéder au dashboard d'administration
- `admin.settings` - Gérer les paramètres système
- `admin.analytics` - Voir les analytics
- `admin.logs` - Voir les logs système

---

## Rôles prévus

### Administrator
- Accès à toutes les permissions
- Gestion complète du système

### Employee (Manager/Agent)
- `companies.view`, `companies.create`, `companies.edit`
- `projects.*` (toutes les permissions projets)
- `tickets.*` (toutes les permissions tickets)
- `comments.*` (toutes les permissions commentaires)
- `chat.*` (toutes les permissions chat)
- `users.view`, `users.activate` (limité aux utilisateurs de son entreprise)

### Customer
- `projects.view` (limité à ses projets)
- `tickets.view`, `tickets.create`, `tickets.edit` (limité à ses tickets)
- `comments.view`, `comments.create`, `comments.edit`, `comments.delete` (limité à ses commentaires)
- `chat.view`, `chat.send` (limité à ses tickets)

---

## Fichiers à créer/modifier

### Création
- `app/Providers/PermissionServiceProvider.php`
- `app/Http/Middleware/CheckPermission.php`
- `app/Http/Middleware/CheckRole.php`

### Modification
- `app/Providers/AuthServiceProvider.php` (ajouter les gates)
- `app/Http/Kernel.php` (enregistrer les middleware)
- `routes/web.php` (appliquer les middleware aux routes)
- `app/database/seeders/RoleAndPermissionSeeder.php` (mettre à jour)
