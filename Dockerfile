FROM php:8.4-apache
WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update \
 && apt-get install -y --no-install-recommends \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
 && docker-php-ext-install mysqli pdo pdo_mysql zip \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

RUN echo "session.upload_progress.enabled = Off" > /usr/local/etc/php/conf.d/security.ini

COPY . /var/www/html/
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENV COMPOSER_MEMORY_LIMIT=-1
RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
