# Base image with Apache and PHP
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install required PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        zip \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd mbstring pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules commonly needed
RUN a2enmod rewrite headers

# Copy Apache vhost config if present in the repo
# This will overwrite the default vhost inside the container
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy application code
# Keep structure so app is served from http://localhost/myapp/
COPY . /var/www/html/

# Set appropriate permissions for Apache
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# Expose Apache HTTP port
EXPOSE 80

# Start Apache in the foreground (default in php:apache)
CMD ["apache2-foreground"]
