# Master Help Desk - Documentation Technique

## Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Architecture Technique](#architecture-technique)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Base de Données](#base-de-données)
6. [API et Composants](#api-et-composants)
7. [Sécurité](#sécurité)
8. [Performance](#performance)
9. [Déploiement](#déploiement)
10. [Maintenance](#maintenance)
11. [Dépannage](#dépannage)

---

## Vue d'ensemble

Master Help Desk est une application de gestion de tickets d'assistance développée avec Laravel 11 et Livewire 3. Elle permet aux entreprises de gérer efficacement les demandes de support, de suivre les tickets et d'analyser les performances.

### Fonctionnalités Principales

- **Gestion multi-entreprises** : Support pour plusieurs entreprises
- **Gestion de projets** : Organisation par projets
- **Système de tickets** : Création, suivi, et résolution de tickets
- **Système de commentaires** : Communication sur les tickets
- **Chat en temps réel** : Discussions instantanées
- **Dashboard analytics** : Statistiques et graphiques
- **Gestion des permissions** : RBAC (Role-Based Access Control)
- **Interface responsive** : Compatible mobile/desktop

### Stack Technique

- **Backend** : Laravel 11, PHP 8.2+
- **Frontend** : Livewire 3, Tailwind CSS, Alpine.js
- **Base de données** : MySQL/MariaDB 8.0+
- **Authentification** : Laravel Jetstream, Fortify
- **Permissions** : Spatie Laravel Permission
- **Tests** : PHPUnit, Livewire Testing

---

## Architecture Technique

### Structure du Projet

```
master-help-desk/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Livewire/
│   │   ├── Companies/
│   │   ├── Projects/
│   │   ├── Tickets/
│   │   └── Dashboard/
│   ├── Models/
│   └── Providers/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/
│   │   ├── livewire/
│   │   └── layouts/
│   └── js/
├── tests/
│   ├── Unit/
│   ├── Feature/
│   └── Performance/
└── public/
```

### Modèles de Données

#### Relations Principales

```
User (Utilisateur)
├── belongsToMany: Company (entreprises)
├── belongsToMany: FavoriteProject (projets favoris)
├── hasMany: Ticket (tickets créés)
└── hasMany: Ticket (tickets assignés)

Company (Entreprise)
├── hasMany: Project (projets)
└── belongsToMany: User (utilisateurs)

Project (Projet)
├── belongsTo: Company (entreprise)
├── hasMany: Ticket (tickets)
└── belongsToMany: User (utilisateurs favoris)

Ticket (Ticket)
├── belongsTo: Project (projet)
├── belongsTo: User (créateur)
├── belongsTo: User (assigné)
├── hasMany: Comment (commentaires)
└── hasMany: Chat (messages)

Comment (Commentaire)
├── belongsTo: Ticket (ticket)
└── belongsTo: User (auteur)

Chat (Message)
├── belongsTo: Ticket (ticket)
└── belongsTo: User (auteur)
```

### Composants Livewire

#### Companies
- `CompanyIndex` : Liste des entreprises
- `CompanyCreate` : Création d'entreprise
- `CompanyEdit` : Modification d'entreprise
- `CompanyShow` : Détails d'entreprise

#### Projects
- `ProjectIndex` : Liste des projets
- `ProjectCreate` : Création de projet
- `ProjectEdit` : Modification de projet
- `ProjectShow` : Détails de projet

#### Tickets
- `TicketIndex` : Liste des tickets avec filtres
- `TicketCreate` : Création de ticket
- `TicketEdit` : Modification de ticket
- `TicketShow` : Détails de ticket avec commentaires/chat

#### Dashboard
- `DashboardIndex` : Tableau de bord principal
- `DashboardCharts` : Graphiques analytics

---

## Installation

### Prérequis

- PHP 8.2 ou supérieur
- Composer 2.0+
- Node.js 18+
- MySQL/MariaDB 8.0+
- Nginx/Apache
- Extension PHP requises : 
  - php-fpm
  - php-mysql
  - php-xml
  - php-mbstring
  - php-curl
  - php-zip
  - php-bcmath
  - php-gd

### Étapes d'Installation

1. **Cloner le dépôt**
```bash
git clone <repository-url>
cd master-help-desk
```

2. **Installer les dépendances**
```bash
composer install
npm install
```

3. **Configurer l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurer la base de données**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=master_help_desk
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Exécuter les migrations**
```bash
php artisan migrate
php artisan db:seed
```

6. **Construire les assets**
```bash
npm run build
```

7. **Configurer les permissions**
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

8. **Créer les liens symboliques**
```bash
php artisan storage:link
```

---

## Configuration

### Configuration de l'Application

#### Variables d'Environnement Clés

```env
# Application
APP_NAME="Master Help Desk"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=master_help_desk
DB_USERNAME=username
DB_PASSWORD=password

# Cache
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls

# Filesystem
FILESYSTEM_DISK=local
```

### Configuration du Serveur Web

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/professionnal/master-help-desk/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

#### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/html/professionnal/master-help-desk/public

    <Directory /var/www/html/professionnal/master-help-desk/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## Base de Données

### Structure des Tables

#### Tables Principales

1. **users** : Utilisateurs
2. **companies** : Entreprises
3. **projects** : Projets
4. **tickets** : Tickets
5. **comments** : Commentaires
6. **chats** : Messages de chat
7. **permissions** : Permissions
8. **roles** : Rôles
9. **model_has_permissions** : Permissions des modèles
10. **model_has_roles** : Rôles des modèles

#### Index de Performance

```sql
-- Index sur les tickets
CREATE INDEX idx_tickets_status ON tickets(status);
CREATE INDEX idx_tickets_priority ON tickets(priority);
CREATE INDEX idx_tickets_project_id ON tickets(project_id);
CREATE INDEX idx_tickets_assigned_to ON tickets(assigned_to);
CREATE INDEX idx_tickets_created_at ON tickets(created_at);

-- Index sur les utilisateurs
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_name ON users(name);

-- Index sur les projets
CREATE INDEX idx_projects_company_id ON projects(company_id);
CREATE INDEX idx_projects_name ON projects(name);
```

### Sauvegarde et Restauration

#### Script de Sauvegarde

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/master-help-desk"

# Sauvegarde de la base de données
mysqldump -u username -p master_help_desk > $BACKUP_DIR/db_backup_$DATE.sql

# Sauvegarde des fichiers
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz \
    --exclude=node_modules \
    --exclude=storage/logs \
    /var/www/html/professionnal/master-help-desk
```

---

## API et Composants

### Composants Livewire

#### TicketIndex

**Props**
- `search` : Recherche textuelle
- `filters` : Filtres (status, priority, project, assigned_to)
- `sortField` : Champ de tri
- `sortDirection` : Direction de tri

**Méthodes**
- `clearFilters()` : Effacer les filtres
- `export()` : Exporter les résultats
- `bulkAssign()` : Assigner en masse
- `bulkStatusUpdate()` : Mettre à jour le statut en masse

#### DashboardIndex

**Props**
- `period` : Période d'analyse (day, week, month, year)
- `selectedCompany` : Entreprise sélectionnée
- `selectedProject` : Projet sélectionné

**Computed Properties**
- `overallStats` : Statistiques globales
- `ticketStatusDistribution` : Distribution par statut
- `priorityDistribution` : Distribution par priorité
- `timelineData` : Données temporelles
- `topPerformers` : Meilleurs performeurs

### Événements Livewire

#### Événements Disponibles

- `refreshDashboard` : Rafraîchir le dashboard
- `periodChanged` : Changement de période
- `filtersChanged` : Changement de filtres
- `ticket-assigned` : Ticket assigné
- `ticket-status-updated` : Statut de ticket mis à jour

---

## Sécurité

### Permissions et Rôles

#### Rôles Prédéfinis

1. **Administrator** : Accès complet à toutes les fonctionnalités
2. **Employee** : Gestion des tickets, projets, entreprises assignés
3. **Customer** : Accès limité à ses propres tickets

#### Permissions

```php
// Gestion des entreprises
'companies.view', 'companies.create', 'companies.edit', 'companies.delete'

// Gestion des projets
'projects.view', 'projects.create', 'projects.edit', 'projects.delete', 'projects.assign'

// Gestion des tickets
'tickets.view', 'tickets.create', 'tickets.edit', 'tickets.delete', 
'tickets.assign', 'tickets.close', 'tickets.reopen'

// Dashboard
'dashboard.view', 'dashboard.stats', 'dashboard.export'
```

### Middleware de Sécurité

#### CheckPermission

```php
class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        if (!auth()->user()->hasPermissionTo($permission)) {
            abort(403);
        }
        return $next($request);
    }
}
```

### Validation des Entrées

#### Règles de Validation

```php
// Ticket validation
'title' => 'required|string|max:255',
'description' => 'required|string',
'priority' => 'required|in:low,medium,high,urgent',
'status' => 'required|in:open,in_progress,pending,resolved,closed',
'project_id' => 'required|exists:projects,id',
```

---

## Performance

### Optimisation de la Base de Données

#### Stratégies

1. **Indexation** : Index sur les champs fréquemment interrogés
2. **Eager Loading** : Chargement anticipé des relations
3. **Pagination** : Pagination des grands ensembles de résultats
4. **Cache** : Mise en cache des requêtes fréquentes

#### Cache Configuration

```php
// Cache des statistiques du dashboard
$cacheKey = "dashboard_stats_{$period}_{$companyId}_{$projectId}";
$stats = Cache::remember($cacheKey, 3600, function() {
    return $this->calculateStats();
});
```

### Optimisation Frontend

#### Stratégies

1. **Lazy Loading** : Chargement différé des composants
2. **Debouncing** : Limitation des requêtes de recherche
3. **Virtual Scrolling** : Pour les grandes listes
4. **Asset Optimization** : Minification et compression

---

## Déploiement

### Script de Déploiement Automatisé

Le script `deploy.sh` automatise le processus de déploiement :

1. **Sauvegarde** : Base de données et fichiers
2. **Mise à jour** : Dépendances et assets
3. **Migration** : Base de données
4. **Optimisation** : Cache et permissions
5. **Vérification** : Tests de santé

### Utilisation

```bash
chmod +x deploy.sh
./deploy.sh
```

### Configuration de Production

#### Variables d'Environnement

```env
APP_ENV=production
APP_DEBUG=false
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

#### Supervisord (Queue Workers)

```ini
[program:master-help-desk-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/professionnal/master-help-desk/artisan queue:work
autostart=true
autorestart=true
user=www-data
numprocs=2
```

---

## Maintenance

### Tâches Planifiées

#### Configuration Cron

```bash
* * * * * cd /var/www/html/professionnal/master-help-desk && php artisan schedule:run >> /dev/null 2>&1
```

#### Tâches Automatiques

1. **Nettoyage des logs** : Rotation des fichiers de log
2. **Nettoyage du cache** : Suppression du cache expiré
3. **Sauvegardes** : Sauvegardes automatiques
4. **Rapports** : Génération de rapports hebdomadaires

### Monitoring

#### Métriques à Surveiller

1. **Performance** : Temps de réponse des pages
2. **Base de données** : Temps de requête, connexions
3. **Erreurs** : Rate d'erreurs 5xx
4. **Utilisateurs** : Nombre d'utilisateurs actifs
5. **Tickets** : Volume de tickets créés/résolus

#### Outils Recommandés

1. **Laravel Telescope** : Debugging et monitoring
2. **Laravel Horizon** : Monitoring des queues
3. **New Relic** : Monitoring performance
4. **Sentry** : Tracking des erreurs

---

## Dépannage

### Problèmes Communs

#### Erreur 500 - Internal Server Error

**Causes possibles :**
- Permissions incorrectes
- Configuration .env incorrecte
- Cache corrompu

**Solutions :**
```bash
# Vérifier les permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Vider le cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Vérifier la configuration .env
php artisan config:cache
```

#### Erreur de Connexion Base de Données

**Causes possibles :**
- Identifiants incorrects
- Service MySQL arrêté
- Firewall bloquant la connexion

**Solutions :**
```bash
# Vérifier le service MySQL
sudo systemctl status mysql

# Tester la connexion
mysql -u username -p -h localhost

# Redémarrer MySQL
sudo systemctl restart mysql
```

#### Performance Lente

**Causes possibles :**
- Index manquants
- Requêtes non optimisées
- Cache désactivé

**Solutions :**
```bash
# Activer le cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimiser la base de données
php artisan db:optimize

# Vérifier les logs
tail -f storage/logs/laravel.log
```

### Logs Utiles

#### Emplacements des Logs

1. **Application** : `storage/logs/laravel.log`
2. **Nginx** : `/var/log/nginx/error.log`
3. **PHP-FPM** : `/var/log/php8.2-fpm.log`
4. **MySQL** : `/var/log/mysql/error.log`

#### Commandes de Debug

```bash
# Vérifier les routes
php artisan route:list

# Vérifier la configuration
php artisan config:show

# Vérifier les permissions
php artisan permission:list

# Tests unitaires
php artisan test

# Tests de performance
php artisan test --testsuite=Performance
```

---

## Support

### Documentation Complémentaire

- [Laravel Documentation](https://laravel.com/docs)
- [Livewire Documentation](https://livewire.laravel.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)

### Contact Support

Pour toute question ou problème technique :

1. Consulter la documentation ci-dessus
2. Vérifier les logs d'erreurs
3. Créer un ticket sur le système de suivi
4. Contacter l'équipe technique

---

*Version : 1.0.0*  
*Dernière mise à jour : 08/10/2025*
