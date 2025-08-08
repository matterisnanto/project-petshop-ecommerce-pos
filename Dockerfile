# =================================================================
# SINGLE STAGE BUILD - VERSI FINAL DENGAN JALAN PINTAS APP_KEY
# =================================================================
FROM php:8.2-fpm

# Install SEMUA dependensi termasuk nginx dan supervisor
RUN apt-get update && apt-get install -y \
    build-essential libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    locales zip unzip git curl libonig-dev libzip-dev libicu-dev \
    nginx nano supervisor openssl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install semua ekstensi PHP
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd intl

# Salin file konfigurasi supervisor dan nginx yang sudah kita buat
COPY docker-config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker-config/nginx.conf /etc/nginx/sites-available/default

# Tetapkan direktori kerja
WORKDIR /var/www

# Salin semua file aplikasi ke dalam image
COPY . .

# Buat struktur direktori storage
RUN mkdir -p storage/app/public storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs

# Berikan kepemilikan SEMUA file ke www-data
RUN chown -R www-data:www-data /var/www

# Ganti user menjadi www-data
USER www-data

# Install dependensi Composer
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# ---> INI ADALAH PERUBAHAN UTAMA <---
# 2 & 3. Buat file .env dan generate APP_KEY secara MANUAL tanpa artisan
RUN cp .env.example .env && \
    sed -i '/^APP_KEY=/c\APP_KEY=base64:'"$(openssl rand -base64 32)" .env

# Jalankan semua perintah optimasi
RUN php artisan config:cache && php artisan route:cache && \
    php artisan view:cache && php artisan filament:optimize

# Expose port web (80)
EXPOSE 80

# Perintah akhir adalah menjalankan supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]