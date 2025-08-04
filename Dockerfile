# Gunakan base image PHP 8.2 dengan FPM
FROM php:8.2-fpm as vendor

# Install dependensi sistem yang dibutuhkan
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    libicu-dev \
    nodejs \
    npm

# Install ekstensi PHP yang umum untuk Laravel
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set direktori kerja
WORKDIR /var/www

# Copy file composer dan install dependensi vendor
COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Bangun image aplikasi final
FROM php:8.2-fpm

# Copy semua dari image vendor
COPY --from=vendor /usr/bin/composer /usr/bin/composer
COPY --from=vendor /var/www/vendor /var/www/vendor

# Install dependensi sistem yang dibutuhkan saat runtime
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    libicu-dev \
    nginx

# Install ekstensi PHP
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd intl

# Set direktori kerja
WORKDIR /var/www

# Copy seluruh kode aplikasi
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Bersihkan cache konfigurasi lama
RUN php artisan config:clear && php artisan route:clear && php artisan view:clear

# Optimasi untuk produksi
RUN composer install --no-interaction --optimize-autoloader --no-dev
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache
RUN php artisan filament:optimize

# Expose port 9000 untuk PHP-FPM
EXPOSE 9000

# Jalankan PHP-FPM
CMD ["php-fpm"]