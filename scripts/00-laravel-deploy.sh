#!/usr/bin/env bash
echo "Running composer"
composer global require hirak/prestissimo
composer install --no-dev --working-dir=/var/www/html

# echo "generating application key..."
php artisan key:generate --show

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force

echo "Storage Link..."
php artisan storage:link --force

# echo "Migrate fresh..."
# php artisan migrate:fresh --force

# echo "Seeding Data..."
# php artisan db:seed --force
