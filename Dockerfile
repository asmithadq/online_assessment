# ==========================================
# 1. Base PHP Apache Runtime Environment
# ==========================================
FROM php:8.2-apache

# ==========================================
# 2. Install System Libraries and PHP Modules
# ==========================================
# Added low-level system dependencies for FFI, XSL, BZ2, Curl, and Graphics engines
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libmagickwand-dev \
    libonig-dev \
    libzip-dev \
    libxslt1-dev \
    libbz2-dev \
    libcurl4-openssl-dev \
    libffi-dev \
    ffmpeg \
    zip \
    unzip \
    git \
    vim \
    rsync \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    # FIXED: Compiles every explicit extension module from your list
    && docker-php-ext-install -j$(nproc) \
        intl gd pdo_mysql mysqli opcache mbstring bcmath zip \
        exif fileinfo bz2 calendar ftp gettext iconv shmop \
        sockets sysvmsg sysvsem sysvshm xsl dom ctype posix curl ffi \
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

# Production configurations & PHP Ini Flag Modifiers
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_NOTICE" > /usr/local/etc/php/conf.d/docker-errors.ini \
    && echo "display_errors = Off" >> /usr/local/etc/php/conf.d/docker-errors.ini \
    && echo "short_open_tag = On" > /usr/local/etc/php/conf.d/docker-shorttags.ini \
    && echo "upload_max_filesize = 512M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 1032M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 1024M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 600" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_time = 600" >> /usr/local/etc/php/conf.d/uploads.ini
# ==========================================
# 4. Source Application Code Delivery
# ==========================================
WORKDIR /var/www/html
COPY . .

# ==========================================
# 5. Composer Package Installations
# ==========================================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# This merges unblocked vendor directories cleanly alongside your automated composer builds
RUN if [ -d "/var/www/html/vendor" ]; then mv /var/www/html/vendor /var/www/html/vendor_static; fi && \
    composer install --no-dev --optimize-autoloader --no-interaction && \
    mkdir -p /var/www/html/vendor && \
    if [ -d "/var/www/html/vendor_static" ]; then rsync -av --ignore-existing /var/www/html/vendor_static/ /var/www/html/vendor/; fi && \
    rm -rf /var/www/html/vendor_static

# ==========================================
# 6. Access Controls and Security Execution
# ==========================================
RUN mkdir -p /var/www/html/uploads /var/www/html/downloads /var/www/html/uploads/assessors_checklist_documents/temp \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN mkdir -p /var/log/temp/mpdf \
    && chown -R www-data:www-data /var/log/temp/mpdf \
    && chmod -R 775 /var/log/temp/mpdf    

EXPOSE 80

CMD ["apache2-foreground"]
# end of the fiie
