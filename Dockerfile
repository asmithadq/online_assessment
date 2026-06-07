# ==========================================
# 1. Base PHP Apache Runtime Environment
# ==========================================
FROM php:8.2-apache

# ==========================================
# 2. Install System and Extension Modules
# ==========================================
# Added 'vim' for the vi editor and 'libmagickwand-dev' for ImageMagick (watermarking)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libmagickwand-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    vim \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    # Install core extensions including mbstring and math utils for CodeIgniter 3 stability
    && docker-php-ext-install -j$(nproc) intl gd pdo_mysql mysqli opcache mbstring bcmath zip \
    # Install and enable ImageMagick (Imagick) extension via PECL
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ==========================================
# 3. Server Configuration & URL Routing
# ==========================================
# Enable mod_rewrite for clean vanity URLs and index.php routing removals
RUN a2enmod rewrite

# Setup Document Root target pointing cleanly to the base web directory
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Force absolute AllowOverride activation at the bottom of the config for .htaccess usage
RUN echo '<Directory /var/www/html/>\n\tAllowOverride All\n</Directory>' >> /etc/apache2/apache2.conf

# Production configurations: Suppress deprecated warnings and notices globally for PHP 8.2
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_NOTICE" >> /usr/local/etc/php/conf.d/docker-errors.ini \
    && echo "display_errors = Off" >> /usr/local/etc/php/conf.d/docker-errors.ini

# ==========================================
# 4. Source Application Code Delivery
# ==========================================
# Replicate the exact subfolder environment your framework expects
WORKDIR /var/www/html

# Copy all repository source directories directly inside the nested layout
COPY . .

# ==========================================
# 5. Composer Package Installations
# ==========================================
# Fetches the latest stable Composer build binary and installs vendor packages
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ==========================================
# 6. Access Controls and Security Execution
# ==========================================
# Hand off directory ownership to the standard web-server user system
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose standard port 80 traffic out to the Traefik router engine
EXPOSE 80

CMD ["apache2-foreground"]
