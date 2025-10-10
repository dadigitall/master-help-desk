# Cahier des Charges - Help Desk

## 1. Présentation du Projet

### 1.1 Contexte
Master Help Desk est une application web open source de gestion de tickets de support développée en Laravel dont toutes les vues sont en livewire. Elle permet aux entreprises de soummetre leurs requetes de support et de communiquer avec leurs clients via une plateforme simple et élégante.

### 1.2 Objectifs
- Fournir une solution complète de gestion de tickets de support
- Faciliter la communication entre les clients et les équipes de support
- Offrir une interface moderne et intuitive
- Permettre une gestion multi-projets et multi-entreprises
- Assurer un suivi complet des activités et des notifications

### 1.3 Technologies Utilisées
- **Backend**: Laravel 12.x (PHP 8.4+)
- **Frontend**: Livewire 3.x, TailwindCSS 3.x (ou Bootstrap 5.x)
- **Base de données**: MySQL
- **Authentification**: Laravel Jetstream
- **Permissions**: Spatie Laravel Permission
- **Logging**: Spatie Laravel Activitylog
- **Filesystem**: Laravel Excel (pxlrbt/filament-excel)

## 2. Architecture Technique

### 2.1 Structure du Projet


### 2.2 Modèles de Données

#### 2.2.1 Utilisateurs (User)
- **Champs**: name, email, password, register_token, locale
- **Relations**: 
  - projects
  - tickets
  - assignedTickets
  - favoriteProjects
  - comments
  - companies
- **Fonctionnalités**: Soft deletes, Activity logging, Roles/Permissions

#### 2.2.2 Tickets (Ticket)
- **Champs**: title, content, status, priority, type, owner_id, responsible_id, project_id, number
- **Relations**: owner, responsible, project, comments, chat
- **Fonctionnalités**: Numérotation automatique, Soft deletes, Activity logging

#### 2.2.3 Projets (Project)
- **Champs**: name, description, owner_id, ticket_prefix, company_id
- **Relations**: owner, company, tickets, favoriteUsers
- **Fonctionnalités**: Soft deletes, Activity logging

#### 2.2.4 Commentaires (Comment)
- **Champs**: content, owner_id, ticket_id
- **Relations**: owner, ticket
- **Fonctionnalités**: Activity logging

#### 2.2.5 Entreprises (Company)
- **Champs**: name, responsible_id
- **Relations**: responsible, users

#### 2.2.6 Autres Modèles
- **TicketStatus**: Statuts des tickets
- **TicketPriority**: Priorités des tickets
- **TicketType**: Types de tickets
- **Chat**: Discussions associées aux tickets
- **FavoriteProject**: Projets favoris des utilisateurs
- **Icon**: Icônes système

## 3. Fonctionnalités Principales

### 3.1 Gestion des Tickets
- **Création de tickets**: Formulaire complet avec titre, contenu, type, priorité, projet
- **Numérotation automatique**: Format [PREFIX_PROJET][NUMERO_SÉQUENTIEL]
- **Statuts des tickets**: Configurables via l'administration
- **Priorités**: Configurables via l'administration
- **Types de tickets**: Configurables via l'administration
- **Assignation**: Possibilité d'assigner des tickets à des responsables
- **Suivi**: Historique complet des modifications

### 3.2 Gestion des Projets
- **Création de projets**: Nom, description, préfixe de tickets, entreprise
- **Projets favoris**: Marquage des projets comme favoris
- **Gestion multi-entreprises**: Association des projets à des entreprises

### 3.3 Système de Commentaires
- **Commentaires sur tickets**: Discussion threadée sur chaque ticket
- **Notifications**: Notifications en temps réel des nouveaux commentaires
- **Historique**: Suivi de toutes les modifications

### 3.4 Chat Intégré
- **Chat par ticket**: Discussion en temps réel associée à chaque ticket
- **Interface moderne**: Basée sur Livewire pour une expérience réactive

### 3.5 Tableau Kanban
- **Vue Kanban**: Organisation visuelle des tickets par statut
- **Drag & Drop**: Modification du statut par glisser-déposer
- **Filtres**: Filtrage par projet, utilisateur, etc.

### 3.6 Analytics et Statistiques
- **Tableau de bord**: Statistiques sur les tickets
- **Tickets assignés/non assignés**
- **Répartition par statuts**
- **Tendances et graphiques**

### 3.7 Gestion des Utilisateurs
- **Rôles et permissions**: Système complet de permissions granulaires
- **Profils utilisateurs**: Gestion des informations personnelles
- **Activation de comptes**: Système de tokens d'activation
- **Multi-entreprises**: Association des utilisateurs à des entreprises

## 4. Système de Permissions

### 4.1 Permissions Définies
#### Projets
- View all projects
- Update all projects
- Delete all projects
- Create projects
- View own projects
- Update own projects
- Delete own projects

#### Tickets
- View all tickets
- Update all tickets
- Delete all tickets
- Create tickets
- View own tickets
- Update own tickets
- Delete own tickets
- Assign tickets
- Change status tickets

#### Pages
- Can view Analytics page
- Can view Tickets page
- Can view Kanban page

#### Utilisateurs
- View all users
- View company users
- Create users
- Update users
- Delete users
- Assign permissions

#### Entreprises
- View all companies
- View own companies
- Create companies
- Update companies
- Delete companies

#### Administration
- Manage ticket statuses
- Manage ticket priorities
- Manage ticket types
- View activity log
- Manage user roles
- Create user roles
- Update user roles
- Delete user roles

### 4.2 Rôles Prédéfinis
- **Administrator**: Accès complet à toutes les fonctionnalités
- **Employee**: Accès limité aux tickets et projets assignés
- **Customer**: Accès limité à ses propres tickets

## 5. Système de Notifications

### 5.1 Types de Notifications
- **Création de ticket**: Notification lors de la création d'un nouveau ticket
- **Mise à jour de ticket**: Notification lors de la modification d'un ticket
- **Nouveau commentaire**: Notification lors de l'ajout d'un commentaire
- **Activation d'utilisateur**: Notification lors de l'activation d'un compte
- **Création d'utilisateur**: Notification lors de la création d'un compte

### 5.2 Canaux de Notification
- **Email**: Notifications détaillées par email
- **Database**: Notifications stockées en base pour affichage dans l'interface
- **Interface**: Centre de notifications dans l'application

### 5.3 Jobs en Arrière-plan
- **TicketCreatedJob**: Envoi des notifications de création de ticket
- **TicketUpdatedJob**: Envoi des notifications de mise à jour de ticket
- **CommentCreatedJob**: Envoi des notifications de nouveaux commentaires

## 6. Interface Utilisateur

### 6.1 Design et Thème
- **Framework**: TailwindCSS 3.x (ou Bootstrap 5.x)
- **Composants**: Livewire
- **Admin Panel**: - 
- **Responsive**: Design adaptatif mobile/desktop
- **Thème**: Personnalisable via SASS

### 6.2 Pages Principales
- **Dashboard**: Vue d'ensemble avec statistiques
- **Tickets**: Liste et gestion des tickets
- **Kanban**: Vue tableau kanban des tickets
- **Projets**: Gestion des projets
- **Analytics**: Statistiques et graphiques
- **Profil**: Gestion du profil utilisateur
- **Notifications**: Centre de notifications

### 6.3 Fonctionnalités UI
- **Recherche**: Recherche avancée de tickets
- **Filtres**: Filtrage multi-critères
- **Export**: Export Excel des données
- **Pagination**: Gestion efficiente des grandes listes
- **Modal Dialogs**: Formulaires modaux
- **Tooltips**: Aides contextuelles

## 7. Configuration et Déploiement

### 7.1 Configuration
- **Fichiers de config**: Configuration centralisée dans `config/`
- **Variables d'environnement**: Gestion via `.env`
- **Configuration système**: `config/system.php` pour les paramètres applicatifs

### 7.2 Déploiement
- **Docker**: Support Docker complet avec Docker Compose
- **Asset compilation**: Vite pour les assets frontend
- **Database migrations**: Gestion automatisée de la structure BDD

### 7.3 Internationalisation
- **Langues**: Support multilingue (Français, Anglais)
- **Localisation**: Fichiers de traduction dans `lang/`
- **Formatage**: Adaptation des formats de dates et nombres

## 8. Sécurité

### 8.1 Authentification
- **Laravel Sanctum**: API tokens sécurisés
- **Password hashing**: Hashage sécurisé des mots de passe
- **Email verification**: Vérification des adresses email
- **Password reset**: Réinitialisation sécurisée des mots de passe

### 8.2 Autorisations
- **Permissions granulaires**: Contrôle d'accès détaillé
- **Role-based access**: Contrôle basé sur les rôles
- **Middleware protection**: Protection des routes
- **CSRF protection**: Protection contre les attaques CSRF

### 8.3 Validation
- **Input validation**: Validation rigoureuse des entrées
- **Sanitization**: Nettoyage des données
- **SQL injection protection**: Protection via Eloquent ORM

## 9. Performance et Optimisation

### 9.1 Caching
- **Query caching**: Mise en cache des requêtes fréquentes
- **View caching**: Mise en cache des vues
- **Config caching**: Mise en cache de la configuration

### 9.2 Base de Données
- **Indexation**: Index optimisés sur les champs fréquemment recherchés
- **Soft deletes**: Suppression douce pour préserver les données
- **Relationships loading**: Chargement optimisé des relations

### 9.3 Frontend
- **Asset minification**: Minification des assets CSS/JS
- **Lazy loading**: Chargement différé des contenus
- **Vite build**: Compilation optimisée des assets

## 10. Monitoring et Logging

### 10.1 Activity Logging
- **Spatie Activitylog**: Journalisation complète des activités
- **User actions**: Suivi de toutes les actions utilisateur
- **Model changes**: Suivi des modifications de modèles
- **Audit trail**: Traçabilité complète

### 10.2 Error Handling
- **Laravel Ignition**: Page d'erreur détaillée en développement
- **Larabug**: Integration avec les systèmes de bug tracking
- **Logging**: Logs structurés pour le monitoring

### 10.3 Analytics
- **Usage statistics**: Statistiques d'utilisation
- **Performance metrics**: Métriques de performance
- **User behavior**: Analyse du comportement utilisateur

## 11. Tests et Qualité

### 11.1 Tests Automatisés
- **PHPUnit**: Tests unitaires et d'intégration
- **Feature tests**: Tests des fonctionnalités
- **Database tests**: Tests de la base de données

### 11.2 Qualité de Code
- **Laravel Pint**: Formatage automatique du code
- **Static analysis**: Analyse statique du code
- **Code standards**: Respect des standards PSR

### 11.3 Documentation
- **API Documentation**: Documentation des APIs
- **User Documentation**: Documentation utilisateur
- **Developer Documentation**: Documentation technique

## 12. Évolutions Possibles

### 12.1 Fonctionnalités Futures
- **API REST**: API complète pour l'intégration tierce
- **Mobile app**: Application mobile native
- **Advanced reporting**: Rapports avancés et personnalisation
- **SLA management**: Gestion des accords de niveau de service
- **Knowledge base**: Base de connaissances intégrée
- **Time tracking**: Suivi du temps passé sur les tickets

### 12.2 Améliorations Techniques
- **Microservices**: Architecture en microservices
- **WebSocket**: Communication temps réel
- **Elasticsearch**: Recherche avancée
- **Redis**: Cache distribué
- **Queue system**: Système de files d'attente avancé

## 13. Integrration 

### 13.1 Integration de l'API de www.tawk.to dans les plateformes client pour permettre à tous les utilisateurs des clients de soummetre leurs requetes sans quitter leurs plateformes metiers et les données des dites requetes serons enrégistré sur la plateforme pour etre traité 


