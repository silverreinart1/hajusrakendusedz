FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libxml2-dev \
    libonig-dev libcurl4-openssl-dev libssl-dev \
    nodejs npm \
    && docker-php-ext-install pdo pdo_mysql mbstring xml zip curl bcmath intl \
    && apt-get clean

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy files
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-interaction --no-dev

# Install JS dependencies and build
RUN npm install && npm run build

# Cache Laravel config
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

EXPOSE 8000

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}