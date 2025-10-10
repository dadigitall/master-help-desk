# Master Help Desk - Guide d'Administration

## Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Installation et Configuration](#installation-et-configuration)
3. [Gestion des Utilisateurs](#gestion-des-utilisateurs)
4. [Gestion des Permissions](#gestion-des-permissions)
5. [Configuration du Système](#configuration-du-système)
6. [Maintenance et Monitoring](#maintenance-et-monitoring)
7. [Sauvegarde et Restauration](#sauvegarde-et-restauration)
8. [Sécurité](#sécurité)
9. [Performance](#performance)
10. [Dépannage Avancé](#dépannage-avancé)

---

## Vue d'ensemble

En tant qu'administrateur de Master Help Desk, vous êtes responsable de :

- 🔐 **Gérer les accès** et permissions des utilisateurs
- 🏢 **Administrer les entreprises** et projets
- 📊 **Surveiller les performances** du système
- 🔧 **Maintenir le système** opérationnel
- 🛡️ **Assurer la sécurité** des données
- 📈 **Analyser les tendances** d'utilisation

### Rôles et Responsabilités

#### Administrator (Super Admin)
- Accès complet à toutes les fonctionnalités
- Gestion des utilisateurs et permissions
- Configuration système
- Accès aux rapports et analytics avancés

#### Manager
- Gestion des entreprises et projets assignés
- Supervision des tickets de son équipe
- Rapports sur les performances
- Formation des utilisateurs

#### Employee
- Gestion des tickets qui lui sont assignés
- Création et suivi de tickets
- Communication avec les clients

#### Customer
- Création de tickets pour ses projets
- Suivi de ses propres tickets
- Communication sur les tickets

---

## Installation et Configuration

### Prérequis Système

#### Configuration Minimale
- **PHP** : 8.2 ou supérieur
- **MySQL/MariaDB** : 8.0 ou supérieur
- **Mémoire RAM** : 4GB minimum
- **Espace disque** : 20GB minimum
- **Navigateurs** : Chrome 90+, Firefox 88+, Safari 14+

#### Configuration Recommandée
- **PHP** : 8.3
- **MySQL/MariaDB** : 8.0+
- **Mémoire RAM** : 8GB
- **Espace disque** : 50GB
- **Redis** : Pour le cache et les sessions

### Installation Initiale

#### 1. Préparation du Serveur

```bash
# Installation PHP et extensions
sudo apt update
sudo apt install php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd

# Installation MySQL
sudo apt install mysql-server

# Installation Nginx
sudo apt install nginx

# Installation Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 2. Configuration de la Base de Données

```sql
CREATE DATABASE master_help_desk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mhd_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON master_help_desk.* TO 'mhd_user'@'localhost';
FLUSH PRIVILEGES;
```

#### 3. Déploiement de l'Application

```bash
# Cloner le projet
git clone <repository-url> /var/www/html/professionnal/master-help-desk
cd /var/www/html/professionnal/master-help-desk

# Installer les dépendances
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Configuration
cp .env.example .env
php artisan key:generate

# Configurer la base de données dans .env
# DB_DATABASE=master_help_desk
# DB_USERNAME=mhd_user
# DB_PASSWORD=secure_password

# Migration et seeding
php artisan migrate --force
php artisan db:seed --force

# Permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### 4. Configuration du Serveur Web

**Nginx Configuration** :
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

---

## Gestion des Utilisateurs

### Création d'un Utilisateur

#### Via l'Interface
1. Connectez-vous en tant qu'administrateur
2. Allez dans "Utilisateurs"
3. Cliquez sur "Nouvel Utilisateur"
4. Remplissez les informations requises
5. Assignez le rôle approprié
6. Configurez les permissions
7. Envoyez l'email d'invitation

#### Via la Console (Artisan)

```bash
# Créer un administrateur
php artisan make:admin --name="John Doe" --email="john@company.com" --password="secure_password"

# Créer un utilisateur avec rôle
php artisan user:create --name="Jane Smith" --email="jane@company.com" --role="Employee"
```

### Gestion des Rôles

#### Rôles Prédéfinis

| Rôle | Permissions par défaut | Description |
|------|------------------------|-------------|
| Administrator | Toutes | Accès complet au système |
| Manager | Gestion limitée | Gestion des entités assignées |
| Employee | Utilisateur standard | Gestion des tickets |
| Customer | Accès client | Voir et créer ses tickets |

#### Création de Rôles Personnalisés

```php
// Dans un seeder ou tinker
use Spatie\Permission\Models\Role;

$role = Role::create(['name' => 'Project Manager']);
$role->givePermissionTo([
    'companies.view',
    'projects.view',
    'projects.create',
    'tickets.view',
    'tickets.create',
    'tickets.assign'
]);
```

### Assignation d'Entreprises

#### Pour les Utilisateurs Non-Administrateurs
1. Éditez le profil de l'utilisateur
2. Allez dans l'onglet "Entreprises"
3. Sélectionnez les entreprises accessibles
4. Enregistrez les modifications

#### Impact sur les Accès
- **Visibilité** : L'utilisateur ne voit que les entreprises assignées
- **Tickets** : Uniquement les tickets des entreprises accessibles
- **Projets** : Uniquement les projets des entreprises accessibles

---

## Gestion des Permissions

### Système de Permissions

Master Help Desk utilise le package **Spatie Laravel Permission** pour la gestion des accès basée sur les rôles (RBAC).

### Permissions Disponibles

#### Gestion des Entreprises
- `companies.view` - Voir les entreprises
- `companies.create` - Créer des entreprises
- `companies.edit` - Modifier des entreprises
- `companies.delete` - Supprimer des entreprises

#### Gestion des Projets
- `projects.view` - Voir les projets
- `projects.create` - Créer des projets
- `projects.edit` - Modifier des projets
- `projects.delete` - Supprimer des projets
- `projects.assign` - Assigner des projets

#### Gestion des Tickets
- `tickets.view` - Voir les tickets
- `tickets.create` - Créer des tickets
- `tickets.edit` - Modifier les tickets
- `tickets.delete` - Supprimer les tickets
- `tickets.assign` - Assigner des tickets
- `tickets.close` - Fermer des tickets
- `tickets.reopen` - Rouvrir des tickets
- `tickets.export` - Exporter des tickets

#### Dashboard et Analytics
- `dashboard.view` - Voir le dashboard
- `dashboard.stats` - Voir les statistiques avancées
- `dashboard.export` - Exporter les analytics

### Configuration des Permissions

#### Via l'Interface
1. Allez dans "Utilisateurs"
2. Éditez un utilisateur
3. Allez dans l'onglet "Permissions"
4. Cochez les permissions appropriées
5. Enregistrez

#### Via le Code

```php
// Donner des permissions à un utilisateur
$user->givePermissionTo('tickets.create', 'tickets.edit');

// Donner un rôle à un utilisateur
$user->assignRole('Manager');

// Vérifier les permissions
if ($user->hasPermissionTo('tickets.delete')) {
    // L'utilisateur peut supprimer des tickets
}
```

### Permissions par Rôle

#### Administrator
```php
'companies.*',  // Toutes les permissions companies
'projects.*',   // Toutes les permissions projects
'tickets.*',    // Toutes les permissions tickets
'dashboard.*'   // Toutes les permissions dashboard
```

#### Manager
```php
'companies.view',
'projects.*',
'tickets.*',
'dashboard.view',
'dashboard.stats'
```

#### Employee
```php
'companies.view',
'projects.view',
'tickets.*',
'dashboard.view'
```

#### Customer
```php
'companies.view',
'projects.view',
'tickets.view',
'tickets.create'
```

---

## Configuration du Système

### Variables d'Environnement

#### Configuration Essentielle

```env
# Application
APP_NAME="Master Help Desk"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=master_help_desk
DB_USERNAME=mhd_user
DB_PASSWORD=secure_password

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Filesystem
FILESYSTEM_DISK=local

# Log
LOG_CHANNEL=stack
LOG_LEVEL=debug
```

### Configuration du Cache

#### Redis Configuration
```bash
# Installation Redis
sudo apt install redis-server

# Configuration Redis (redis.conf)
maxmemory 256mb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

#### Cache Optimization
```bash
# Optimiser le cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Configuration des Emails

#### Configuration SMTP

```env
# Gmail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls

# SendGrid
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

#### Templates d'Emails

Les templates d'emails sont situés dans :
- `resources/views/emails/` - Templates des emails
- `app/Mail/` - Classes de mail

### Configuration des Files d'Attente

#### Supervisor Configuration

```ini
[program:master-help-desk-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/professionnal/master-help-desk/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/professionnal/master-help-desk/storage/logs/worker.log
stopwaitsecs=3600
```

#### Configuration des Tâches Planifiées

```bash
# Ajouter au crontab
* * * * * cd /var/www/html/professionnal/master-help-desk && php artisan schedule:run >> /dev/null 2>&1
```

---

## Maintenance et Monitoring

### Monitoring de Base

#### Vérification de Santé

```bash
# Vérifier l'état de l'application
php artisan about

# Vérifier les routes
php artisan route:list

# Vérifier la configuration
php artisan config:show
```

#### Logs de l'Application

Les logs sont stockés dans `storage/logs/laravel.log`

```bash
# Voir les logs en temps réel
tail -f storage/logs/laravel.log

# Voir les erreurs récentes
grep ERROR storage/logs/laravel.log | tail -20
```

### Métriques à Surveiller

#### Performance
- **Temps de réponse** des pages
- **Temps de requête** base de données
- **Utilisation mémoire** PHP
- **Utilisation CPU** serveur

#### Business
- **Nombre de tickets** créés/jour
- **Temps de résolution** moyen
- **Taux de résolution** 
- **Satisfaction** utilisateurs

#### Système
- **Espace disque** disponible
- **Connexions** base de données
- **Taux d'erreurs** 5xx
- **Uptime** application

### Outils de Monitoring

#### Laravel Telescope

```bash
# Installation
composer require laravel/telescope
php artisan telescope:install
php artisan migrate
```

#### Configuration Monitoring

```php
// config/telescope.php
'watchers' => [
    Watchers\CacheWatcher::class => env('TELESCOPE_CACHE_WATCHER', true),
    Watchers\CommandWatcher::class => env('TELESCOPE_COMMAND_WATCHER', true),
    Watchers\DumpWatcher::class => env('TELESCOPE_DUMP_WATCHER', true),
    Watchers\EventWatcher::class => env('TELESCOPE_EVENT_WATCHER', true),
    Watchers\ExceptionWatcher::class => env('TELESCOPE_EXCEPTION_WATCHER', true),
    Watchers\JobWatcher::class => env('TELESCOPE_JOB_WATCHER', true),
    Watchers\LogWatcher::class => env('TELESCOPE_LOG_WATCHER', true),
    Watchers\MailWatcher::class => env('TELESCOPE_MAIL_WATCHER', true),
    Watchers\ModelWatcher::class => env('TELESCOPE_MODEL_WATCHER', true),
    Watchers\NotificationWatcher::class => env('TELESCOPE_NOTIFICATION_WATCHER', true),
    Watchers\QueryWatcher::class => env('TELESCOPE_QUERY_WATCHER', true),
    Watchers\RedisWatcher::class => env('TELESCOPE_REDIS_WATCHER', true),
    Watchers\RequestWatcher::class => env('TELESCOPE_REQUEST_WATCHER', true),
    Watchers\GateWatcher::class => env('TELESCOPE_GATE_WATCHER', true),
    Watchers\ScheduleWatcher::class => env('TELESCOPE_SCHEDULE_WATCHER', true),
],
```

---

## Sauvegarde et Restauration

### Stratégie de Sauvegarde

#### Fréquences Recommandées

| Type | Fréquence | Rétention |
|------|-----------|-----------|
| Base de données | Quotidienne | 30 jours |
| Fichiers d'upload | Hebdomadaire | 90 jours |
| Configuration | Mensuelle | 1 an |
| Logs | Quotidienne | 7 jours |

### Script de Sauvegarde Automatique

```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/master-help-desk"
APP_DIR="/var/www/html/professionnal/master-help-desk"

# Créer le répertoire de sauvegarde
mkdir -p $BACKUP_DIR

# Sauvegarde de la base de données
mysqldump -u mhd_user -p'secure_password' master_help_desk | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Sauvegarde des fichiers
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz \
    --exclude=node_modules \
    --exclude=storage/logs \
    --exclude=storage/framework/cache \
    $APP_DIR

# Nettoyer les anciennes sauvegardes
find $BACKUP_DIR -name "*.gz" -mtime +30 -delete

echo "Sauvegarde terminée: $DATE"
```

### Configuration Cron

```bash
# Sauvegarde quotidienne à 2h du matin
0 2 * * * /path/to/backup.sh >> /var/log/backup.log 2>&1
```

### Restauration

#### Base de Données

```bash
# Restaurer la base de données
gunzip < /var/backups/master-help-desk/db_backup_20231008_020000.sql.gz | mysql -u mhd_user -p'secure_password' master_help_desk
```

#### Fichiers

```bash
# Restaurer les fichiers
tar -xzf /var/backups/master-help-desk/files_backup_20231008_020000.tar.gz -C /
```

### Test de Restauration

```bash
# Script de test de restauration
#!/bin/bash
# test_restore.sh

TEST_DB="master_help_desk_test"
BACKUP_FILE=$1

if [ -z "$BACKUP_FILE" ]; then
    echo "Usage: $0 <backup_file>"
    exit 1
fi

# Créer la base de test
mysql -u root -p -e "CREATE DATABASE $TEST_DB"

# Restaurer dans la base de test
gunzip < $BACKUP_FILE | mysql -u mhd_user -p'secure_password' $TEST_DB

# Vérifier l'intégrité
php artisan tinker --execute="DB::connection()->database='$TEST_DB'; DB::select('SELECT COUNT(*) FROM users');"

echo "Test de restauration terminé"

# Nettoyer
mysql -u root -p -e "DROP DATABASE $TEST_DB"
```

---

## Sécurité

### Configuration de Sécurité

#### Variables d'Environnement

```env
# Désactiver le debug en production
APP_DEBUG=false

# Clé d'application forte
APP_KEY=base64:GENERATED_STRONG_KEY_HERE

# Configuration sécurisée de session
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
```

#### Permissions des Fichiers

```bash
# Permissions recommandées
chmod 755 /var/www/html/professionnal/master-help-desk
chmod 755 /var/www/html/professionnal/master-help-desk/storage
chmod 755 /var/www/html/professionnal/master-help-desk/bootstrap/cache
chmod 644 /var/www/html/professionnal/master-help-desk/.env
chmod 600 /var/www/html/professionnal/master-help-desk/storage/oauth-*.key

# Propriétaire
chown -R www-data:www-data /var/www/html/professionnal/master-help-desk
```

### Sécurité des Mots de Passe

#### Configuration

```php
// config/auth.php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
],

'passwords' => [
    'users' => [
        'provider' => 'users',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
    ],
],
```

#### Politique de Mots de Passe

```php
// App\Rules\StrongPassword
class StrongPassword implements Rule
{
    public function passes($attribute, $value)
    {
        return strlen($value) >= 8 &&
               preg_match('/[A-Z]/', $value) &&
               preg_match('/[a-z]/', $value) &&
               preg_match('/[0-9]/', $value) &&
               preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value);
    }

    public function message()
    {
        return 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.';
    }
}
```

### Protection contre les Attaques

#### CSRF

La protection CSRF est activée par défaut dans Laravel.

#### XSS

```php
// Échapper automatiquement les données dans les templates
{{ $user_input }} // Échappé automatiquement
{!! $safe_html !!} // Non échappé (utiliser avec précaution)
```

#### SQL Injection

Laravel protège automatiquement contre les injections SQL grâce à l'ELOQUENT ORM.

### HTTPS et SSL

#### Configuration Nginx

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com;
    
    ssl_certificate /path/to/your/certificate.crt;
    ssl_certificate_key /path/to/your/private.key;
    
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    ssl_prefer_server_ciphers off;
    
    add_header Strict-Transport-Security "max-age=63072000" always;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}

server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}
```

---

## Performance

### Optimisation de la Base de Données

#### Indexation

```sql
-- Index sur les tables principales
CREATE INDEX idx_tickets_status ON tickets(status);
CREATE INDEX idx_tickets_priority ON tickets(priority);
CREATE INDEX idx_tickets_project_id ON tickets(project_id);
CREATE INDEX idx_tickets_assigned_to ON tickets(assigned_to);
CREATE INDEX idx_tickets_created_at ON tickets(created_at);

CREATE INDEX idx_projects_company_id ON projects(company_id);
CREATE INDEX idx_users_email ON users(email);

-- Index composites
CREATE INDEX idx_tickets_status_priority ON tickets(status, priority);
CREATE INDEX idx_tickets_project_status ON tickets(project_id, status);
```

#### Optimisation des Requêtes

```php
// Utiliser l'eager loading
$tickets = Ticket::with(['project', 'assignedUser', 'creator'])->get();

// Pagination pour les grandes listes
$tickets = Ticket::paginate(20);

// Sélectionner uniquement les champs nécessaires
$users = User::select('id', 'name', 'email')->get();
```

### Cache

#### Configuration du Cache

```php
// config/cache.php
'default' => env('CACHE_DRIVER', 'redis'),

'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
    ],
],
```

#### Utilisation du Cache

```php
// Cache des statistiques du dashboard
use Illuminate\Support\Facades\Cache;

$stats = Cache::remember('dashboard_stats_' . $userId, 3600, function () {
    return $this->calculateUserStats($userId);
});

// Invalider le cache
Cache::forget('dashboard_stats_' . $userId);
```

### Optimisation Frontend

#### Minification des Assets

```bash
# Construire les assets pour la production
npm run build

# Optimiser les images
npm run optimize-images
```

#### Lazy Loading

```php
// Charger les composants uniquement lorsque nécessaire
Livewire::lazy(function () {
    return view('components.heavy-component');
});
```

---

## Dépannage Avancé

### Problèmes Communs

#### Erreur 500 - Internal Server Error

**Causes possibles :**
- Permissions incorrectes
- Configuration .env incorrecte
- Cache corrompu
- Erreur PHP

**Solutions :**
```bash
# Vérifier les permissions
ls -la storage/
ls -la bootstrap/cache/

# Vérifier la configuration
php artisan config:cache
php artisan route:cache

# Vider le cache
php artisan cache:clear
php artisan view:clear

# Vérifier les logs
tail -f storage/logs/laravel.log
```

#### Erreur de Connexion Base de Données

**Causes possibles :**
- Service MySQL arrêté
- Identifiants incorrects
- Firewall bloquant

**Solutions :**
```bash
# Vérifier le service MySQL
sudo systemctl status mysql

# Redémarrer MySQL
sudo systemctl restart mysql

# Tester la connexion
mysql -u mhd_user -p -h localhost

# Vérifier la configuration
php artisan tinker --execute="DB::connection()->getPdo();"
```

#### Performance Lente

**Causes possibles :**
- Index manquants
- Requêtes non optimisées
- Cache désactivé
- Serveur surchargé

**Solutions :**
```bash
# Activer le cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimiser la base de données
php artisan db:optimize

# Vérifier les requêtes lentes
php artisan tinker --execute="DB::enableQueryLog();"
```

### Outils de Diagnostic

#### Commandes Utiles

```bash
# Informations système
php artisan about

# Liste des routes
php artisan route:list

# Configuration
php artisan config:show

# Permissions
php artisan permission:list

# Cache
php artisan cache:status
```

#### Scripts de Diagnostic

```bash
#!/bin/bash
# diagnostic.sh

echo "=== Diagnostic Master Help Desk ==="

# Vérifier PHP
echo "Version PHP: $(php -v)"
echo "Extensions PHP:"
php -m | grep -E "(mysql|redis|curl|json|mbstring)"

# Vérifier MySQL
echo "Status MySQL:"
sudo systemctl status mysql --no-pager

# Vérifier Redis
echo "Status Redis:"
sudo systemctl status redis --no-pager

# Vérifier Nginx
echo "Status Nginx:"
sudo systemctl status nginx --no-pager

# Vérifier l'espace disque
echo "Espace disque:"
df -h

# Vérifier la mémoire
echo "Mémoire:"
free -h

echo "=== Fin du diagnostic ==="
```

### Monitoring Avancé

#### Configuration des Alertes

```php
// App\Providers\AppServiceProvider.php
use Illuminate\Support\Facades\Log;

public function boot()
{
    // Alerte si le temps de réponse dépasse 2 secondes
    if (request()->server('REQUEST_TIME_FLOAT') > 2.0) {
        Log::warning('Slow request detected', [
            'url' => request()->url(),
            'time' => microtime(true) - request()->server('REQUEST_TIME_FLOAT'),
            'user' => auth()->id()
        ]);
    }
}
```

#### Rapports Quotidiens

```bash
#!/bin/bash
# daily_report.sh

DATE=$(date +%Y-%m-%d)
REPORT_FILE="/var/reports/daily_report_$DATE.txt"

echo "=== Rapport Quotidien - $DATE ===" > $REPORT_FILE

# Statistiques des tickets
php artisan tinker --execute="
\$stats = [
    'total_tickets' => App\Models\Ticket::whereDate('created_at', today())->count(),
    'resolved_tickets' => App\Models\Ticket::whereDate('updated_at', today())->where('status', 'resolved')->count(),
    'active_users' => App\Models\User::whereDate('last_login_at', today())->count(),
];
echo json_encode(\$stats, JSON_PRETTY_PRINT);
" >> $REPORT_FILE

# Envoyer le rapport par email
mail -s "Rapport Quotidien Master Help Desk - $DATE" admin@company.com < $REPORT_FILE

echo "Rapport généré: $REPORT_FILE"
```

---

## Conclusion

Ce guide d'administration couvre les aspects essentiels pour gérer efficacement le système Master Help Desk. Pour un support technique avancé ou des questions spécifiques, n'hésitez pas à contacter l'équipe de support.

### Ressources Additionnelles

- **Documentation technique** : DOCUMENTATION.md
- **Guide utilisateur** : USER_GUIDE.md
- **Scripts de maintenance** : scripts/
- **Templates de configuration** : config/examples/

### Support Technique

- **Email** : admin-support@masterhelpdesk.com
- **Téléphone** : +33 1 XX XX XX XX
- **Documentation en ligne** : https://docs.masterhelpdesk.com

---

*Guide d'Administration Master Help Desk v1.0*  
*Dernière mise à jour : 08/10/2025*  
*Pour plus d'informations, contactez le support technique*
