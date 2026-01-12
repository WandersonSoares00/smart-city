FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libprotobuf-dev \
    protobuf-compiler \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /devices

COPY composer.json composer.lock ./
COPY src ./src
COPY camera/run.php ./run.php

RUN composer install --no-dev --optimize-autoloader

CMD ["php", "run.php", "Camera-Praca-Central", "7103"]
