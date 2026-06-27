# Stage 1: PHP deps + composer
FROM php:8.3-cli AS composer_stage
WORKDIR /app

# Install system deps + PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev libpq-dev libgd-dev zip unzip git curl \
    && docker-php-ext-configure gd \
    && docker-php-ext-install zip pdo pdo_pgsql gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files and install deps
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction

# Copy full source
COPY . .

# Generate optimized autoload
RUN composer dump-autoload --optimize --no-dev

# Stage 2: Node build for Vite assets
FROM node:20-alpine AS node_stage
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 3: Production runtime (PHP-FPM + Nginx via supervisord)
FROM php:8.3-fpm-alpine AS production

# Install system deps + PHP extensions + nginx + supervisor
RUN apk add --no-cache \
    nginx \
    supervisor \
    libzip-dev zip \
    libpq-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install zip pdo pdo_pgsql gd

WORKDIR /var/www/html

# Copy app code from composer stage
COPY --from=composer_stage /app /var/www/html

# Copy built Vite assets
COPY --from=node_stage /app/public/build /var/www/html/public/build

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Copy supervisord config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
