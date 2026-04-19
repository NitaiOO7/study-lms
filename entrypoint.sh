#!/bin/sh

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Start the PHP server
echo "Starting server..."
php -S 0.0.0.0:10000 -t public
