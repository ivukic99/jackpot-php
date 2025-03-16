FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev \
    git \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql sockets

RUN a2enmod rewrite

COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

RUN a2ensite 000-default.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

RUN mkdir -p /var/www/html/logs && \
    touch /var/www/html/logs/error.log && \
    chmod -R 777 /var/www/html/logs

RUN composer install --no-dev --no-interaction --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
