# ==========================================
# 1. Base PHP Apache Runtime Environment
# ==========================================
FROM php:8.2-apache

# ==========================================
# 2. Install System and Extension Modules
# ==========================================
# FIXED: Added rsync to safely merge our multi-source vendor assets together
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
    rsync \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) intl gd pdo_mysql mysqli opcache mbstring bcmath zip \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ==========================================
# 3. Server Configuration & URL Routing
# ==========================================
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN echo "PassEnv CI_ENV" >> /etc/apache2/apache2.conf

RUN echo '<Directory /var/www/html/>' >> /etc/apache2/apache2.conf && \
    echo '    AllowOverride All' >> /etc/apache2/apache2.conf && \
    echo '    Require all granted' >> /etc/apache2/apache2.conf && \
    echo '</Directory>' >> /etc/apache2/apache2.conf

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_NOTICE" >> /usr/local/etc/php/conf.d/docker-errors.ini \
    && echo "display_errors = Off" >> /usr/local/etc/php/conf.d/docker-errors.ini

# ==========================================
# 4. Source Application Code Delivery
# ==========================================
WORKDIR /var/www/html
COPY . .

# ==========================================
# 5. Composer Package Installations
# ==========================================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# FIXED: Safely isolates your repository assets, runs composer, then combines them flawlessly via rsync
RUN mv /var/www/html/vendor /var/www/html/vendor_static || true && \
    composer install --no-dev --optimize-autoloader --no-interaction && \
    mkdir -p /var/www/html/vendor && \
    if [ -d "/var/www/html/vendor_static" ]; then rsync -av /var/www/html/vendor_static/ /var/www/html/vendor/; fi && \
    rm -rf /var/www/html/vendor_static

# ==========================================
# 6. Access Controls and Security Execution
# ==========================================
RUN mkdir -p /var/www/html/uploads /var/www/html/downloads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
