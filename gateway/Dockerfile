FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libprotobuf-dev \
    protobuf-compiler \
    && docker-php-ext-install sockets pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

CMD ["php", "run.php"]
