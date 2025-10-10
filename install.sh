#!/bin/bash

# Master Help Desk - Script d'Installation Automatisée
# Ce script installe et configure complètement le système Master Help Desk

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Configuration
APP_NAME="Master Help Desk"
APP_VERSION="1.0.0"
INSTALL_DIR="/var/www/html/professionnal/master-help-desk"
BACKUP_DIR="/var/backups/master-help-desk"
LOG_FILE="/var/log/master-help-desk-install.log"

# Default configuration
DB_NAME="master_help_desk"
DB_USER="mhd_user"
DB_PASS=""
APP_URL="http://localhost"
ADMIN_NAME="Administrator"
ADMIN_EMAIL="admin@masterhelpdesk.com"
ADMIN_PASS=""

# Logging function
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
    exit 1
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
}

info() {
    echo -e "${CYAN}[INFO]${NC} $1" | tee -a "$LOG_FILE"
}

# Banner
show_banner() {
    echo -e "${PURPLE}"
    echo "╔══════════════════════════════════════════════════════════════╗"
    echo "║                                                              ║"
    echo "║           Master Help Desk - Installation Script             ║"
    echo "║                                                              ║"
    echo "║                    Version $APP_VERSION                          ║"
    echo "║                                                              ║"
    echo "║  This script will install and configure Master Help Desk      ║"
    echo "║  with all dependencies and optimal settings.                 ║"
    echo "║                                                              ║"
    echo "╚══════════════════════════════════════════════════════════════╝"
    echo -e "${NC}"
}

# Check if running as root
check_root() {
    if [[ $EUID -eq 0 ]]; then
        error "This script should not be run as root for security reasons. Please run as a regular user with sudo privileges."
    fi
}

# Check system requirements
check_requirements() {
    log "Checking system requirements..."
    
    # Check OS
    if [[ ! -f /etc/os-release ]]; then
        error "Cannot determine operating system"
    fi
    
    source /etc/os-release
    log "Detected OS: $PRETTY_NAME"
    
    # Check if Ubuntu/Debian
    if [[ ! "$ID" =~ ^(ubuntu|debian)$ ]]; then
        warning "This script is optimized for Ubuntu/Debian. Other distributions may require manual adjustments."
    fi
    
    # Check available disk space (need at least 5GB)
    available_space=$(df / | awk 'NR==2 {print $4}')
    if [[ $available_space -lt 5242880 ]]; then  # 5GB in KB
        error "Insufficient disk space. At least 5GB required."
    fi
    
    # Check RAM (need at least 2GB)
    available_ram=$(free -m | awk 'NR==2{print $7}')
    if [[ $available_ram -lt 2048 ]]; then
        warning "Low RAM detected. At least 2GB recommended for optimal performance."
    fi
    
    success "System requirements check passed"
}

# Install system dependencies
install_dependencies() {
    log "Installing system dependencies..."
    
    # Update package list
    sudo apt update
    
    # Install basic utilities
    sudo apt install -y curl wget git unzip zip software-properties-common
    
    # Install PHP and extensions
    log "Installing PHP 8.2 and extensions..."
    sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd php8.2-intl php8.2-sqlite3 php8.2-dom
    
    # Install Nginx
    log "Installing Nginx..."
    sudo apt install -y nginx
    
    # Install MySQL
    log "Installing MySQL..."
    sudo apt install -y mysql-server
    
    # Install Redis
    log "Installing Redis..."
    sudo apt install -y redis-server
    
    # Install Composer
    log "Installing Composer..."
    if ! command -v composer &> /dev/null; then
        curl -sS https://getcomposer.org/installer | php
        sudo mv composer.phar /usr/local/bin/composer
        sudo chmod +x /usr/local/bin/composer
    fi
    
    # Install Node.js and npm
    log "Installing Node.js..."
    if ! command -v node &> /dev/null; then
        curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
        sudo apt install -y nodejs
    fi
    
    success "System dependencies installed"
}

# Configure database
configure_database() {
    log "Configuring database..."
    
    # Start MySQL service
    sudo systemctl start mysql
    sudo systemctl enable mysql
    
    # Generate random password if not provided
    if [[ -z "$DB_PASS" ]]; then
        DB_PASS=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-25)
    fi
    
    # Create database and user
    sudo mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" || error "Failed to create database"
    sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';" || error "Failed to create database user"
    sudo mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';" || error "Failed to grant privileges"
    sudo mysql -e "FLUSH PRIVILEGES;" || error "Failed to flush privileges"
    
    # Test database connection
    mysql -u "$DB_USER" -p"$DB_PASS" -e "USE $DB_NAME; SELECT 1;" || error "Database connection test failed"
    
    success "Database configured successfully"
}

# Setup application files
setup_application() {
    log "Setting up application files..."
    
    # Create directories
    sudo mkdir -p "$INSTALL_DIR"
    sudo mkdir -p "$BACKUP_DIR"
    
    # Copy application files (assuming script is run from project root)
    if [[ -f "artisan" ]]; then
        log "Installing from current directory..."
        sudo cp -r . "$INSTALL_DIR/"
    else
        log "Cloning from repository..."
        sudo git clone https://github.com/your-repo/master-help-desk.git "$INSTALL_DIR"
        cd "$INSTALL_DIR"
    fi
    
    # Set permissions
    sudo chown -R $USER:$USER "$INSTALL_DIR"
    cd "$INSTALL_DIR"
    
    # Install PHP dependencies
    log "Installing PHP dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
    
    # Install Node.js dependencies
    log "Installing Node.js dependencies..."
    npm install
    
    # Build assets
    log "Building assets..."
    npm run build
    
    success "Application files setup completed"
}

# Configure environment
configure_environment() {
    log "Configuring environment..."
    
    # Create .env file
    if [[ -f .env ]]; then
        sudo mv .env .env.backup
        warning "Existing .env file backed up as .env.backup"
    fi
    
    # Generate app key
    APP_KEY=$(php artisan key:generate --show)
    
    # Generate admin password if not provided
    if [[ -z "$ADMIN_PASS" ]]; then
        ADMIN_PASS=$(openssl rand -base64 32 | tr -d "=+/" | cut -c1-16)
    fi
    
    # Create .env file
    cat > .env << EOF
APP_NAME="$APP_NAME"
APP_ENV=production
APP_KEY=$APP_KEY
APP_DEBUG=false
APP_URL=$APP_URL

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=$DB_NAME
DB_USERNAME=$DB_USER
DB_PASSWORD=$DB_PASS

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="\${PUSHER_HOST}"
VITE_PUSHER_PORT="\${PUSHER_PORT}"
VITE_PUSHER_SCHEME="\${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"
EOF

    success "Environment configured"
}

# Run database migrations
run_migrations() {
    log "Running database migrations..."
    
    php artisan migrate --force || error "Database migration failed"
    php artisan db:seed --force || error "Database seeding failed"
    
    success "Database migrations completed"
}

# Create admin user
create_admin() {
    log "Creating administrator user..."
    
    php artisan tinker --execute="
use App\Models\User;
use Spatie\Permission\Models\Role;

\$user = User::create([
    'name' => '$ADMIN_NAME',
    'email' => '$ADMIN_EMAIL',
    'password' => bcrypt('$ADMIN_PASS'),
]);

\$user->assignRole('Administrator');

echo 'Admin user created successfully';
" || error "Failed to create admin user"
    
    success "Administrator user created"
}

# Configure web server
configure_webserver() {
    log "Configuring Nginx..."
    
    # Create Nginx configuration
    sudo tee /etc/nginx/sites-available/master-help-desk << EOF
server {
    listen 80;
    server_name _;
    root $INSTALL_DIR/public;
    index index.php index.html index.htm;

    client_max_body_size 100M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.ht {
        deny all;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml+rss
        application/atom+xml
        image/svg+xml;
}
EOF

    # Enable site
    sudo ln -sf /etc/nginx/sites-available/master-help-desk /etc/nginx/sites-enabled/
    sudo rm -f /etc/nginx/sites-enabled/default
    
    # Test Nginx configuration
    sudo nginx -t || error "Nginx configuration test failed"
    
    # Restart Nginx
    sudo systemctl restart nginx
    sudo systemctl enable nginx
    
    success "Nginx configured"
}

# Configure PHP-FPM
configure_php() {
    log "Configuring PHP-FPM..."
    
    # Update PHP-FPM configuration
    sudo sed -i 's/memory_limit = 128M/memory_limit = 512M/' /etc/php/8.2/fpm/php.ini
    sudo sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 100M/' /etc/php/8.2/fpm/php.ini
    sudo sed -i 's/post_max_size = 8M/post_max_size = 100M/' /etc/php/8.2/fpm/php.ini
    sudo sed -i 's/max_execution_time = 30/max_execution_time = 300/' /etc/php/8.2/fpm/php.ini
    
    # Restart PHP-FPM
    sudo systemctl restart php8.2-fpm
    sudo systemctl enable php8.2-fpm
    
    success "PHP-FPM configured"
}

# Configure Redis
configure_redis() {
    log "Configuring Redis..."
    
    # Update Redis configuration
    sudo sed -i 's/maxmemory 256mb/maxmemory 512mb/' /etc/redis/redis.conf
    sudo sed -i 's/maxmemory-policy allkeys-lru/maxmemory-policy allkeys-lru/' /etc/redis/redis.conf
    
    # Restart Redis
    sudo systemctl restart redis-server
    sudo systemctl enable redis-server
    
    success "Redis configured"
}

# Set file permissions
set_permissions() {
    log "Setting file permissions..."
    
    # Storage and cache directories
    sudo find storage -type f -exec chmod 644 {} \;
    sudo find storage -type d -exec chmod 755 {} \;
    sudo find bootstrap/cache -type f -exec chmod 644 {} \;
    sudo find bootstrap/cache -type d -exec chmod 755 {} \;
    
    # Set ownership
    sudo chown -R www-data:www-data storage bootstrap/cache
    
    # Set executable permissions
    sudo chmod +x artisan
    
    success "File permissions set"
}

# Optimize application
optimize_application() {
    log "Optimizing application..."
    
    # Clear caches
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    # Optimize for production
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan optimize
    
    success "Application optimized"
}

# Setup cron job
setup_cron() {
    log "Setting up scheduled tasks..."
    
    # Add cron job for Laravel scheduler
    (crontab -l 2>/dev/null; echo "* * * * * cd $INSTALL_DIR && php artisan schedule:run >> /dev/null 2>&1") | crontab -
    
    success "Cron job configured"
}

# Create admin commands
create_admin_commands() {
    log "Creating admin commands..."
    
    # Create admin management script
    sudo tee /usr/local/bin/mhd-admin << 'EOF'
#!/bin/bash

APP_DIR="/var/www/html/professionnal/master-help-desk"

case "$1" in
    "status")
        cd "$APP_DIR"
        php artisan about
        ;;
    "cache-clear")
        cd "$APP_DIR"
        php artisan cache:clear
        php artisan config:clear
        php artisan route:clear
        php artisan view:clear
        echo "Cache cleared"
        ;;
    "optimize")
        cd "$APP_DIR"
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        php artisan optimize
        echo "Application optimized"
        ;;
    "migrate")
        cd "$APP_DIR"
        php artisan migrate --force
        echo "Migration completed"
        ;;
    "backup")
        /var/www/html/professionnal/master-help-desk/scripts/backup.sh
        ;;
    "logs")
        tail -f "$APP_DIR/storage/logs/laravel.log"
        ;;
    *)
        echo "Usage: mhd-admin {status|cache-clear|optimize|migrate|backup|logs}"
        exit 1
        ;;
esac
EOF

    sudo chmod +x /usr/local/bin/mhd-admin
    
    success "Admin commands created"
}

# Create backup script
create_backup_script() {
    log "Creating backup script..."
    
    mkdir -p scripts
    
    # Create backup script
    tee scripts/backup.sh << EOF
#!/bin/bash

DATE=\$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/master-help-desk"
APP_DIR="/var/www/html/professionnal/master-help-desk"

# Create backup directory
mkdir -p "\$BACKUP_DIR"

# Backup database
mysqldump -u $DB_USER -p'$DB_PASS' $DB_NAME | gzip > "\$BACKUP_DIR/db_backup_\$DATE.sql.gz"

# Backup files
tar -czf "\$BACKUP_DIR/files_backup_\$DATE.tar.gz" \\
    --exclude=node_modules \\
    --exclude=storage/logs \\
    --exclude=storage/framework/cache \\
    "\$APP_DIR"

# Clean old backups (keep 30 days)
find "\$BACKUP_DIR" -name "*.gz" -mtime +30 -delete

echo "Backup completed: \$DATE"
EOF

    chmod +x scripts/backup.sh
    
    success "Backup script created"
}

# Final checks
final_checks() {
    log "Performing final checks..."
    
    # Check if application is responding
    if curl -s "http://localhost/" | grep -q "Laravel" > /dev/null 2>&1; then
        success "Application is responding correctly"
    else
        warning "Application may not be responding correctly. Please check manually."
    fi
    
    # Check database connection
    if php artisan tinker --execute="DB::connection()->getPdo();" > /dev/null 2>&1; then
        success "Database connection is working"
    else
        error "Database connection failed"
    fi
    
    # Check services status
    if systemctl is-active --quiet nginx; then
        success "Nginx is running"
    else
        error "Nginx is not running"
    fi
    
    if systemctl is-active --quiet php8.2-fpm; then
        success "PHP-FPM is running"
    else
        error "PHP-FPM is not running"
    fi
    
    if systemctl is-active --quiet mysql; then
        success "MySQL is running"
    else
        error "MySQL is not running"
    fi
    
    if systemctl is-active --quiet redis-server; then
        success "Redis is running"
    else
        error "Redis is not running"
    fi
}

# Display installation summary
show_summary() {
    echo ""
    echo -e "${GREEN}╔══════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║                    INSTALLATION COMPLETED                    ║${NC}"
    echo -e "${GREEN}╚══════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${CYAN}Application Information:${NC}"
    echo -e "  • Name: $APP_NAME"
    echo -e "  • Version: $APP_VERSION"
    echo -e "  • Installation Directory: $INSTALL_DIR"
    echo -e "  • URL: $APP_URL"
    echo ""
    echo -e "${CYAN}Database Information:${NC}"
    echo -e "  • Database Name: $DB_NAME"
    echo -e "  • Database User: $DB_USER"
    echo -e "  • Database Password: $DB_PASS"
    echo ""
    echo -e "${CYAN}Administrator Account:${NC}"
    echo -e "  • Name: $ADMIN_NAME"
    echo -e "  • Email: $ADMIN_EMAIL"
    echo -e "  • Password: $ADMIN_PASS"
    echo ""
    echo -e "${CYAN}Important Files:${NC}"
    echo -e "  • Application: $INSTALL_DIR"
    echo -e "  • Configuration: $INSTALL_DIR/.env"
    echo -e "  • Logs: $INSTALL_DIR/storage/logs/laravel.log"
    echo -e "  • Backups: $BACKUP_DIR"
    echo ""
    echo -e "${CYAN}Admin Commands:${NC}"
    echo -e "  • Status: mhd-admin status"
    echo -e "  • Clear Cache: mhd-admin cache-clear"
    echo -e "  • Optimize: mhd-admin optimize"
    echo -e "  • Backup: mhd-admin backup"
    echo -e "  • View Logs: mhd-admin logs"
    echo ""
    echo -e "${YELLOW}Next Steps:${NC}"
    echo -e "  1. 🌐 Open your browser and navigate to: $APP_URL"
    echo -e "  2. 🔐 Login with the administrator credentials above"
    echo -e "  3. ⚙️ Configure your organization settings"
    echo -e "  4. 👥 Create user accounts for your team"
    echo -e "  5. 🏢 Set up companies and projects"
    echo ""
    echo -e "${GREEN}🎉 Master Help Desk has been successfully installed!${NC}"
    echo ""
    echo -e "${YELLOW}⚠️  IMPORTANT: Save these credentials securely!${NC}"
    echo ""
}

# Interactive configuration
interactive_config() {
    echo -e "${CYAN}Let's configure your Master Help Desk installation...${NC}"
    echo ""
    
    # Database configuration
    read -p "Database name [$DB_NAME]: " input
    DB_NAME=${input:-$DB_NAME}
    
    read -p "Database user [$DB_USER]: " input
    DB_USER=${input:-$DB_USER}
    
    read -s -p "Database password [auto-generate]: " input
    echo ""
    if [[ -n "$input" ]]; then
        DB_PASS="$input"
    fi
    
    # Application configuration
    read -p "Application URL [$APP_URL]: " input
    APP_URL=${input:-$APP_URL}
    
    # Admin configuration
    read -p "Administrator name [$ADMIN_NAME]: " input
    ADMIN_NAME=${input:-$ADMIN_NAME}
    
    read -p "Administrator email [$ADMIN_EMAIL]: " input
    ADMIN_EMAIL=${input:-$ADMIN_EMAIL}
    
    read -s -p "Administrator password [auto-generate]: " input
    echo ""
    if [[ -n "$input" ]]; then
        ADMIN_PASS="$input"
    fi
    
    echo ""
    echo -e "${GREEN}Configuration completed!${NC}"
    echo ""
}

# Main installation function
main() {
    # Create log file
    sudo mkdir -p "$(dirname "$LOG_FILE")"
    sudo touch "$LOG_FILE"
    sudo chown $USER:$USER "$LOG_FILE"
    
    # Show banner
    show_banner
    
    # Check if interactive mode
    if [[ "$1" != "--auto" ]]; then
        interactive_config
    fi
    
    # Run installation steps
    check_root
    check_requirements
    install_dependencies
    configure_database
    setup_application
    configure_environment
    run_migrations
    create_admin
    configure_webserver
    configure_php
    configure_redis
    set_permissions
    optimize_application
    setup_cron
    create_admin_commands
    create_backup_script
    final_checks
    show_summary
    
    success "Installation completed successfully!"
}

# Handle command line arguments
case "$1" in
    "--help"|"-h")
        echo "Master Help Desk Installation Script"
        echo ""
        echo "Usage: $0 [OPTIONS]"
        echo ""
        echo "Options:"
        echo "  --auto    Run installation in non-interactive mode with defaults"
        echo "  --help    Show this help message"
        echo ""
        exit 0
        ;;
    "--auto")
        main --auto
        ;;
    *)
        main
        ;;
esac
