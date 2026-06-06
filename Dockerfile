# ==========================================
# 1. Base PHP Apache Runtime Environment
# ==========================================
FROM php:8.2-apache

# ==========================================
# 2. Install System and Extension Modules
# ==========================================
# Installs core dependencies required for CodeIgniter framework features
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) intl gd pdo_mysql mysqli opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ==========================================
# 3. Server Configuration & URL Routing
# ==========================================
# Enable mod_rewrite for clean vanity URLs and index.php routing removals
RUN a2enmod rewrite

# Setup Document Root target pointing to the framework public assets 
# NOTE: If you use CodeIgniter 3, change "/public" down to just "/var/www/html"
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set production PHP defaults for security and optimization
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# ==========================================
# 4. Source Application Code Delivery
# ==========================================
WORKDIR /var/www/html

# Copy all repository source directories directly inside the layout
COPY . .

# ==========================================
# 5. Composer Package Installations (Optional)
# ==========================================
# If your project depends on Composer dependencies, uncomment the 2 lines below:
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# RUN composer install --no-dev --optimize-autoloader --no-interaction

# ==========================================
# 6. Access Controls and Security Execution
# ==========================================
# Hand off directory ownership to the standard web-server user system
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose standard port 80 traffic out to the Traefik router engine
EXPOSE 80

CMD ["apache2-foreground"]
