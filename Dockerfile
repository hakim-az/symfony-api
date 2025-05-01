# Base PHP image with necessary extensions
FROM php:8.2-cli

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libsqlite3-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory inside container
WORKDIR /app

# Copy application files
COPY . .

# Install Composer dependencies (production mode)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Ensure var/ directory and tasks.db are writable (SQLite needs write perms)
RUN mkdir -p var && touch var/tasks.db && chmod -R 777 var

# Expose port 8080
EXPOSE 8080

# Start Symfony app using built-in PHP server (prod mode)
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
