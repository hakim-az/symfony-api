# Use official PHP image
FROM php:8.1-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

# Install Composer globally
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create non-root user
RUN useradd -m appuser

# Set working directory
WORKDIR /app

# Copy composer files first (for caching)
COPY composer.* ./

# Allow symfony/flex plugin for this project (non-root config)
RUN composer config --global allow-plugins.symfony/flex true

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# Copy the rest of the application code
COPY . .

# Set permissions for var/ directory
RUN mkdir -p var && chmod -R 777 var

# Change ownership of app files to appuser
RUN chown -R appuser:appuser /app

# Expose port (if needed)
EXPOSE 8000

# Switch to non-root user
USER appuser

# Run Symfony app using PHP's built-in server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
