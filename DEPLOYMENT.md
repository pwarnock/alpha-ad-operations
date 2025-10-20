# Deployment Guide - Alpha Ad Operations

This guide covers deployment options for the Alpha Ad Operations Laravel SaaS platform.

## Laravel Cloud Deployment (Recommended)

Laravel Cloud provides the simplest deployment experience for this application.

### Prerequisites
- Git repository with your code
- Laravel Cloud account
- Environment variables ready

### Steps

1. **Connect Repository**
   - Login to Laravel Cloud dashboard
   - Connect your Git repository
   - Select the branch to deploy (usually `main` or `master`)

2. **Configure Environment**
   ```env
   APP_NAME="Alpha Ad Operations"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   
   # Database
   DB_CONNECTION=mysql
   DB_HOST=your-laravel-cloud-db-host
   DB_PORT=3306
   DB_DATABASE=alpha_ad_ops
   DB_USERNAME=your-db-username
   DB_PASSWORD=your-db-password
   
   # Other services
   MAIL_MAILER=smtp
   MAIL_HOST=your-smtp-host
   MAIL_PORT=587
   MAIL_USERNAME=your-email
   MAIL_PASSWORD=your-email-password
   ```

3. **Deploy**
   - Push to your connected branch
   - Laravel Cloud automatically builds and deploys
   - Monitor deployment logs in dashboard

4. **Post-Deployment**
   ```bash
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Traditional VPS/Server Deployment

### Server Requirements
- PHP 8.2+ with required extensions
- Web server (Nginx/Apache)
- Database (MySQL 8.0+ / PostgreSQL 12+)
- Composer
- Node.js & NPM

### Setup Steps

1. **Server Preparation**
   ```bash
   # Update system
   sudo apt update && sudo apt upgrade -y
   
   # Install PHP and extensions
   sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd php8.2-intl php8.2-sqlite3
   
   # Install Nginx
   sudo apt install nginx -y
   
   # Install Composer
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   
   # Install Node.js
   curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
   sudo apt-get install -y nodejs
   ```

2. **Application Setup**
   ```bash
   # Clone repository
   cd /var/www
   git clone <your-repo> alpha-ad-operations
   cd alpha-ad-operations
   
   # Install dependencies
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   
   # Set permissions
   sudo chown -R www-data:www-data /var/www/alpha-ad-operations
   sudo chmod -R 755 /var/www/alpha-ad-operations
   sudo chmod -R 777 /var/www/alpha-ad-operations/storage
   sudo chmod -R 777 /var/www/alpha-ad-operations/bootstrap/cache
   ```

3. **Nginx Configuration**
   ```nginx
   server {
       listen 80;
       server_name your-domain.com;
       root /var/www/alpha-ad-operations/public;
       index index.php;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
           include fastcgi_params;
       }
   }
   ```

4. **SSL Certificate (Let's Encrypt)**
   ```bash
   sudo apt install certbot python3-certbot-nginx -y
   sudo certbot --nginx -d your-domain.com
   ```

## Environment Configuration

### Production `.env`
```env
APP_NAME="Alpha Ad Operations"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://your-domain.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alpha_ad_ops
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## Database Setup

### MySQL Production Database
```sql
CREATE DATABASE alpha_ad_ops CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'alpha_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON alpha_ad_ops.* TO 'alpha_user'@'localhost';
FLUSH PRIVILEGES;
```

### Run Migrations
```bash
php artisan migrate --force
```

## Performance Optimization

### Production Commands
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Queue Setup (if using background jobs)
```bash
# Install Redis
sudo apt install redis-server -y

# Start Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server

# Configure Laravel to use Redis
# Already configured in .env above

# Start queue worker
php artisan queue:work --daemon --sleep=1 --tries=3
```

## Monitoring & Logging

### Application Monitoring
```bash
# Monitor logs
tail -f storage/logs/laravel.log

# Monitor queue
php artisan queue:monitor

# Check application status
php artisan about
```

### Health Checks
Add health check endpoint:
```php
// routes/web.php
Route::get('/health', function () {
    return [
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => app()->version(),
    ];
});
```

## Security Considerations

### File Permissions
```bash
# Secure sensitive files
sudo chmod 600 .env
sudo chown www-data:www-data .env

# Proper storage permissions
sudo chmod -R 775 storage/
sudo chmod -R 775 bootstrap/cache/
```

### Firewall Configuration
```bash
# Only allow necessary ports
sudo ufw allow 22/tcp   # SSH
sudo ufw allow 80/tcp   # HTTP
sudo ufw allow 443/tcp  # HTTPS
sudo ufw enable
```

## Backup Strategy

### Database Backups
```bash
# Daily backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u alpha_user -p alpha_ad_ops > /backups/alpha_ad_ops_$DATE.sql
find /backups -name "*.sql" -mtime +7 -delete
```

### Application Backups
```bash
# Backup entire application
tar -czf /backups/app_backup_$(date +%Y%m%d).tar.gz /var/www/alpha-ad-operations
```

## Troubleshooting

### Common Issues

1. **500 Internal Server Error**
   - Check Laravel logs: `storage/logs/laravel.log`
   - Verify file permissions
   - Ensure `.env` is properly configured

2. **Database Connection Issues**
   - Verify database credentials
   - Check database server status
   - Test connection manually

3. **Asset Loading Issues**
   - Run `npm run build`
   - Check `APP_URL` in `.env`
   - Verify web server configuration

4. **Queue Jobs Not Processing**
   - Check queue worker status
   - Verify Redis connection
   - Clear failed jobs: `php artisan queue:flush`

## Rollback Plan

### Quick Rollback
```bash
# Git rollback
git checkout previous_commit_hash

# Database rollback
php artisan migrate:rollback --step=1

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Full Deployment Rollback
1. Switch to previous working version in Git
2. Run database migrations if needed
3. Clear and rebuild caches
4. Restart services (Nginx, PHP-FPM, Redis)

## Support

For deployment issues:
1. Check this documentation first
2. Review Laravel logs for errors
3. Verify all environment variables
4. Test database connectivity
5. Check system resource usage

Remember to test thoroughly in staging before deploying to production!