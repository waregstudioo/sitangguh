FROM php:8.3-cli

# Install dependencies sistem yang dibutuhkan Laravel, GD (JPEG/PNG/WEBP), & npm
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Konfigurasi GD agar mendukung JPEG, WEBP, PNG, dan Freetype untuk Intervention Image
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd

# Naikkan batas ukuran upload foto (maks 10MB di validasi) dan memori GD
RUN { \
    echo "upload_max_filesize = 16M"; \
    echo "post_max_size = 20M"; \
    echo "memory_limit = 256M"; \
    } > /usr/local/etc/php/conf.d/uploads.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set direktori kerja
WORKDIR /app

# Copy seluruh file project ke dalam container
COPY . .

# Install dependency PHP dan Node.js, lalu build Vite
RUN composer install --no-dev --optimize-autoloader \
    && npm install \
    && npm run build

# Buat direktori storage publik, symlink storage, dan beri izin akses
RUN mkdir -p storage/app/public/reports \
    && php artisan storage:link \
    && chmod -R 775 storage bootstrap/cache

# Jalankan migrasi (otomatis membuat admin/admin) lalu jalankan server sesuai port Render
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
