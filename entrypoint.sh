#!/bin/sh

# Run migrations and seeders
echo "Running migrations and seeders..."
php artisan migrate --force
php artisan db:seed --force

# Start the PHP server
echo "Starting server..."
php -S 0.0.0.0:10000 -t public
