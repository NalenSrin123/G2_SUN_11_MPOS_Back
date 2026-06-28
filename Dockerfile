FROM php:8.4-cli

# Install system packages
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    zip \
    libzip-dev \
    libpq-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    ca-certificates \
    openssl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql zip gd \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy the ENTIRE Laravel project first
COPY . .

# Install dependencies
RUN composer install \
    --no-dev \
    --prefer-source \
    --optimize-autoloader \
    --no-interaction

EXPOSE 10000

CMD php artisan migrate:refresh --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-10000}