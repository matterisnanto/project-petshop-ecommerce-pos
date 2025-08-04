# =================================================================
# TAHAP 1: Build dependensi dan kode aplikasi (disebut 'vendor')
# =================================================================
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

# Install ekstensi PHP
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set direktori kerja
WORKDIR /var/www

# ---> PERUBAHAN PENTING DI SINI <---
# Salin SEMUA file aplikasi DULU
COPY . .

# BARU jalankan composer install setelah semua file ada
RUN composer install --no-interaction --optimize-autoloader --no-dev


# =================================================================
# TAHAP 2: Bangun image aplikasi final
# =================================================================
FROM php:8.2-fpm

# Copy composer dari tahap sebelumnya
COPY --from=vendor /usr/bin/composer /usr/bin/composer

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

# Copy seluruh kode aplikasi (termasuk vendor) dari tahap build pertama
COPY --from=vendor /var/www /var/www

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Optimasi untuk produksi (jalankan lagi untuk memastikan semua link benar)
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache
RUN php artisan filament:optimize

# Expose port 9000 untuk PHP-FPM
EXPOSE 9000

# Jalankan PHP-FPM
CMD ["php-fpm"]