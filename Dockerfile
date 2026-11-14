FROM php:8.2-cli

# Install dependencies sistem yang dibutuhkan Laravel & npm
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    nodejs \
    npm

# Install ekstensi PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set direktori kerja
WORKDIR /app

# Copy seluruh file project dari GitHub ke dalam server
COPY . .

# Install dependency PHP dan Node.js, lalu build Vite
RUN composer install --no-dev --optimize-autoloader
RUN npm install
RUN npm run build

# Beri izin akses untuk folder storage dan cache
RUN chmod -R 775 storage bootstrap/cache

# Jalankan server bawaan artisan (menyesuaikan port otomatis dari Render)
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
