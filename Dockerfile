FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libzip-dev libxml2-dev \
    libonig-dev libcurl4-openssl-dev libssl-dev \
    libicu-dev \
    nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring xml zip curl bcmath intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --optimize-autoloader --no-interaction --no-dev

RUN npm install && npm run build

# Fix storage permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD php artisan config:clear \
    && php artisan migrate --force \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}