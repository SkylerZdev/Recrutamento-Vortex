FROM php:8.5-apache
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev libonig-dev libsqlite3-dev unzip \
    && docker-php-ext-install pdo_pgsql pdo_sqlite mbstring \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
WORKDIR /var/www/html
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY . .
RUN composer install --prefer-dist --no-interaction --optimize-autoloader \
    && chown -R www-data:www-data storage bootstrap/cache
EXPOSE 80
