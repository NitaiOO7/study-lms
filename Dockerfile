FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev \
    libjpeg-dev libfreetype6-dev sqlite3 libsqlite3-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd intl zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Create SQLite DB
RUN mkdir -p database && touch database/database.sqlite

# Permissions
RUN chmod -R 775 storage bootstrap/cache

# Generate app key
# RUN php artisan key:generate

# Run migrations
# RUN php artisan migrate --force

# Expose port
EXPOSE 10000

# Start Laravel server
CMD php -S 0.0.0.0:10000 -t public