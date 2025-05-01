# Use an official PHP image with required extensions
FROM php:8.1-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Set working directory
WORKDIR /app

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer.json and composer.lock
COPY composer.* ./

# Install dependencies (without dev, with auto-scripts)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Copy the rest of the app
COPY . .

# Ensure var/ directory and tasks.db are writable (for SQLite)
RUN mkdir -p var && touch var/tasks.db && chmod -R 777 var

# Expose port
EXPOSE 8000

# Command to run Symfony app
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
