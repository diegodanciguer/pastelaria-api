FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev

# Configurar e instalar extensões do PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar composer.json e composer.lock
COPY composer.json composer.lock ./

# Instalar dependências do Composer
RUN composer install --optimize-autoloader --no-dev

# Copiar o restante do código
COPY . .

# Definir permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
