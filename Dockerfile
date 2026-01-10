FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y git unzip libicu-dev libpq-dev libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql intl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
