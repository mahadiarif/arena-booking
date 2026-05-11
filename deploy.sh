#!/bin/bash

# ArenaBook - All-in-One Ubuntu Deployment Script
# Copyright (c) 2026 MetroNet Bangladesh Ltd.
# Targeted OS: Ubuntu 22.04 / 24.04

set -e

echo "----------------------------------------------------"
echo "   ArenaBook: Fresh Ubuntu Server Deployment"
echo "----------------------------------------------------"

# 1. Variables (User Input)
while [ -z "$DOMAIN" ]; do
    read -p "Enter your Domain Name (e.g., arenabook.com): " DOMAIN
    if [ -z "$DOMAIN" ]; then echo "Error: Domain name is required."; fi
done

# Remove trailing slash if exists
DOMAIN=$(echo $DOMAIN | sed 's/\/$//')

read -p "Enter Database Password for 'arenabook_user': " DB_PASS

DB_NAME="arenabook"
DB_USER="arenabook_user"
APP_DIR="/var/www/html"
REPO_URL="https://github.com/mahadiarif/arena-booking.git"

echo "----------------------------------------------------"
echo "Step 1: System Update & Essential Tools"
echo "----------------------------------------------------"
sudo apt update && sudo apt upgrade -y
sudo apt install -y curl git unzip zip nginx software-properties-common

echo "----------------------------------------------------"
echo "Step 2: Install PHP 8.2 & Extensions"
echo "----------------------------------------------------"
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-curl php8.2-xml php8.2-mbstring php8.2-intl php8.2-zip php8.2-bcmath php8.2-sqlite3 php8.2-gd

echo "----------------------------------------------------"
echo "Step 3: Install & Configure MySQL"
echo "----------------------------------------------------"
sudo apt install -y mysql-server
sudo mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'%' IDENTIFIED BY '$DB_PASS';"
sudo mysql -e "ALTER USER '$DB_USER'@'%' IDENTIFIED WITH mysql_native_password BY '$DB_PASS';"
sudo mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'%';"
sudo mysql -e "FLUSH PRIVILEGES;"

echo "----------------------------------------------------"
echo "Step 4: Install Composer & Node.js"
echo "----------------------------------------------------"
# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

echo "----------------------------------------------------"
echo "Step 5: Application Deployment"
echo "----------------------------------------------------"
# Clone repository if APP_DIR is empty or not a git repo
if [ ! -d "$APP_DIR/.git" ]; then
    echo "Cloning repository from $REPO_URL..."
    sudo rm -rf $APP_DIR/* || true
    sudo git clone $REPO_URL $APP_DIR
else
    echo "Repository already exists. Pulling latest changes..."
    cd $APP_DIR
    sudo git pull origin main
fi

cd $APP_DIR
sudo chown -R $USER:$USER $APP_DIR

# Laravel Setup
cp .env.example .env
sed -i "s|APP_URL=.*|APP_URL=https://$DOMAIN|g" .env
sed -i "s/APP_ENV=local/APP_ENV=production/g" .env
sed -i "s/APP_DEBUG=true/APP_DEBUG=false/g" .env

# Escape special characters for sed replacement
SAFE_DB_PASS=$(echo $DB_PASS | sed 's/[&/\]/\\&/g')

# Configure Database
sed -i "s/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/g" .env
sed -i "s/# DB_HOST=127.0.0.1/DB_HOST=127.0.0.1/g" .env
sed -i "s/# DB_PORT=3306/DB_PORT=3306/g" .env
sed -i "s|# DB_DATABASE=laravel|DB_DATABASE=$DB_NAME|g" .env
sed -i "s|# DB_USERNAME=root|DB_USERNAME=$DB_USER|g" .env
sed -i "s|# DB_PASSWORD=|DB_PASSWORD=$SAFE_DB_PASS|g" .env

# Run optimized setup
composer setup

# Optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "----------------------------------------------------"
echo "Step 6: Permissions & Nginx"
echo "----------------------------------------------------"
sudo chown -R www-data:www-data $APP_DIR/storage $APP_DIR/bootstrap/cache
sudo chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

# Create Nginx Config
NGINX_CONF="/etc/nginx/sites-available/arenabook"
sudo bash -c "cat > $NGINX_CONF <<EOF
server {
    listen 80;
    server_name $DOMAIN;
    root $APP_DIR/public;

    add_header X-Frame-Options \"SAMEORIGIN\";
    add_header X-Content-Type-Options \"nosniff\";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF"

sudo ln -sf $NGINX_CONF /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx

echo "----------------------------------------------------"
echo "Deployment Complete!"
echo "Your site should be available at: http://$DOMAIN"
echo "To enable SSL, run: sudo apt install certbot python3-certbot-nginx && sudo certbot --nginx -d $DOMAIN"
echo "Copyright (c) 2026 MetroNet Bangladesh Ltd."
echo "----------------------------------------------------"
