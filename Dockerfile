FROM php:8.3-cli

# Instalar dependencias del sistema requeridas por Laravel + Node.js para compilar los assets
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd pcntl zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /app

# Copiar los archivos del proyecto
COPY . .

# Instalar las librerías de PHP
RUN composer install --no-dev --optimize-autoloader

# Compilar los assets de CSS y JS con Vite/Node
RUN npm install && npm run build

# Crear las carpetas internas que Laravel necesita
RUN mkdir -p storage/framework/sessions \
             storage/framework/views \
             storage/framework/cache/data \
             storage/logs \
             bootstrap/cache

# Dar permisos a las carpetas de Laravel
RUN chmod -R 777 storage bootstrap/cache

# Comando para iniciar el sistema usando el puerto que Render asigne
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
