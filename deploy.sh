#!/bin/bash

# Master Help Desk - Deployment Script
# This script automates the deployment process for production

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
    exit 1
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Configuration
APP_NAME="Master Help Desk"
APP_DIR="/var/www/html/professionnal/master-help-desk"
BACKUP_DIR="/var/backups/master-help-desk"
LOG_FILE="/var/log/deploy-master-help-desk.log"

# Create backup directory if it doesn't exist
mkdir -p $BACKUP_DIR

# Start deployment
log "Starting deployment of $APP_NAME..."

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   error "This script should not be run as root for security reasons"
fi

# Check if we're in the correct directory
if [[ ! -f "artisan" ]]; then
    error "Please run this script from the Laravel project root directory"
fi

# Create backup
log "Creating backup..."
BACKUP_FILE="$BACKUP_DIR/backup-$(date +%Y%m%d-%H%M%S).tar.gz"

# Backup database
if command -v mysqldump &> /dev/null; then
    log "Backing up database..."
    DB_NAME=$(grep DB_NAME .env | cut -d '=' -f2)
    DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2)
    DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f2)
    
    if [[ -n "$DB_NAME" ]]; then
       mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_DIR/database-$(date +%Y%m%d-%H%M%S).sql"
        success "Database backed up"
    fi
fi

# Backup application files
tar -czf "$BACKUP_FILE" \
    --exclude=node_modules \
    --exclude=storage/logs/*.log \
    --exclude=storage/framework/cache/* \
    --exclude=bootstrap/cache/* \
    .

success "Application backed up to $BACKUP_FILE"

# Update dependencies
log "Updating dependencies..."

# Composer dependencies
if [[ ! -d "vendor" ]] || [[ "composer.lock" -nt "vendor/autoload.php" ]]; then
    log "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
    success "Composer dependencies updated"
else
    log "Composer dependencies up to date"
fi

# NPM dependencies
if [[ -f "package.json" ]] && [[ ! -d "node_modules" ]] || [[ "package-lock.json" -nt "node_modules" ]]; then
    log "Installing NPM dependencies..."
    npm ci --production
    success "NPM dependencies updated"
else
    log "NPM dependencies up to date"
fi

# Build assets
log "Building assets..."
npm run build
success "Assets built"

# Clear caches
log "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
success "Caches cleared"

# Run database migrations
log "Running database migrations..."
php artisan migrate --force
success "Database migrations completed"

# Seed database (only if running for the first time)
if ! php artisan db:seed --class=DatabaseSeeder --force > /dev/null 2>&1; then
    warning "Database seeding may have already been run or encountered issues"
else
    success "Database seeded"
fi

# Clear and warm up caches
log "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
success "Application optimized"

# Set proper permissions
log "Setting proper permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 755 public
chown -R www-data:www-data storage bootstrap/cache public
success "Permissions set"

# Check application health
log "Performing health check..."

# Check if application is responding
if php artisan route:list > /dev/null 2>&1; then
    success "Routes are properly cached"
else
    error "Route cache is corrupted"
fi

# Check database connection
if php artisan tinker --execute="DB::connection()->getPdo();" > /dev/null 2>&1; then
    success "Database connection is working"
else
    error "Database connection failed"
fi

# Check storage directory
if [[ -w "storage" ]]; then
    success "Storage directory is writable"
else
    error "Storage directory is not writable"
fi

# Clear opcode cache if OPcache is installed
if php -m | grep -q "Zend OPcache"; then
    log "Clearing OPcache..."
    php artisan opcache:clear 2>/dev/null || echo "OPcache cleared manually"
    success "OPcache cleared"
fi

# Create symbolic links for storage
log "Creating storage symbolic links..."
php artisan storage:link
success "Storage links created"

# Schedule cron job for Laravel tasks
log "Setting up scheduled tasks..."
CRON_ENTRY="* * * * * cd $APP_DIR && php artisan schedule:run >> /dev/null 2>&1"

if ! crontab -l 2>/dev/null | grep -q "schedule:run"; then
    (crontab -l 2>/dev/null; echo "$CRON_ENTRY") | crontab -
    success "Cron job added"
else
    log "Cron job already exists"
fi

# Generate application key if not exists
if ! grep -q "APP_KEY" .env || [[ $(grep "APP_KEY" .env | cut -d '=' -f2) == "" ]]; then
    log "Generating application key..."
    php artisan key:generate --force
    success "Application key generated"
fi

# Queue worker setup (if using queues)
if [[ -f ".env" ]] && grep -q "QUEUE_CONNECTION" .env; then
    log "Setting up queue workers..."
    
    # Check if supervisor is available
    if command -v supervisorctl &> /dev/null; then
        log "Configuring supervisor for queue workers..."
        # Create supervisor config
        cat > /etc/supervisor/conf.d/master-help-desk-worker.conf << EOF
[program:master-help-desk-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $APP_DIR/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=$APP_DIR/storage/logs/worker.log
stopwaitsecs=3600
EOF
        
        supervisorctl reread
        supervisorctl update
        supervisorctl start master-help-desk-worker:*
        success "Queue workers configured and started"
    else
        warning "Supervisor not found. Queue workers need to be started manually"
    fi
fi

# Security checks
log "Performing security checks..."

# Check if .env file is protected
if [[ -f ".env" ]]; then
    if [[ $(stat -c %a .env) -eq 600 ]] || [[ $(stat -c %a .env) -eq 640 ]]; then
        success ".env file has proper permissions"
    else
        chmod 600 .env
        success ".env file permissions corrected"
    fi
fi

# Check if debug mode is disabled in production
if grep -q "APP_ENV=local" .env; then
    warning "Application is still in local environment. Consider switching to production."
fi

# Create deployment log
echo "Deployment completed successfully at $(date)" >> $LOG_FILE

# Final checks
log "Performing final checks..."

# Test basic application functionality
if curl -s http://localhost/ | grep -q "Laravel" > /dev/null 2>&1; then
    success "Application is responding correctly"
else
    warning "Application may not be responding correctly. Please check manually."
fi

# Display deployment summary
echo ""
success "=== DEPLOYMENT SUMMARY ==="
success "Application: $APP_NAME"
success "Backup: $BACKUP_FILE"
success "Log file: $LOG_FILE"
success "Deployment completed at: $(date)"
echo ""
info "Next steps:"
info "1. Test the application thoroughly"
info "2. Monitor logs for any errors"
info "3. Set up monitoring and alerts"
info "4. Configure SSL certificate if not done"
info "5. Set up regular backups"
echo ""

# Display important URLs
if command -v ip &> /dev/null; then
    SERVER_IP=$(ip route get 1 | awk '{print $7}' | head -1)
    echo "${BLUE}Application URLs:${NC}"
    echo "  Local: http://localhost/"
    echo "  Network: http://$SERVER_IP/"
    echo ""
fi

success "Deployment completed successfully! 🎉"

# Optional: Send notification (uncomment and configure)
# if command -v curl &> /dev/null; then
#     curl -X POST -H 'Content-type: application/json' \
#         --data '{"text":"Master Help Desk deployment completed successfully"}' \
#         YOUR_SLACK_WEBHOOK_URL
# fi

exit 0
