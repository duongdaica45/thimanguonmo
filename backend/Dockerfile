FROM php:8.2-apache

# Cài thư viện cần cho PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Enable apache rewrite
RUN a2enmod rewrite

# Copy source code
COPY . /var/www/html/

# Set quyền
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
