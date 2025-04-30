FROM php:8.1-cli

# Install required dependencies for SQLite
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_sqlite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory inside the container
WORKDIR /app

# Copy the application files into the container
COPY . .

# Set environment variables
ENV APP_ENV=prod
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install the PHP dependencies using Composer
RUN composer install --no-dev --optimize-autoloader

# Ensure the SQLite database file exists and set proper permissions
RUN mkdir -p var/db && touch var/db/app.db && chown -R www-data:www-data var/db && chmod -R 755 var/db

# Clear cache to ensure everything is initialized correctly
RUN php bin/console cache:clear --env=prod

# Expose port 8000 for the Symfony application
EXPOSE 8000

# Run the Symfony application with PHP's built-in server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
