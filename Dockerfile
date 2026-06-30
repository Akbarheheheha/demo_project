FROM php:8.3-fpm

# Install dependencies sistem dan ekstensi PHP yang dibutuhkan Laravel.
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install ekstensi Redis ke dalam kontainer PHP
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy seluruh file project ke dalam kontainer
COPY . .

# Set hak akses agar Nginx dan PHP bisa baca/tulis cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
