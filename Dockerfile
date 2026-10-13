FROM php:8.2-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . .

RUN rm -f .env

RUN if [ -f composer.json ]; then \
        php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" \
        && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
        && composer install --no-dev --optimize-autoloader --no-interaction; \
    fi

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]