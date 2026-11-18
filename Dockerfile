# Imagen inicial de instalación
FROM php:8.4-apache-bookworm

# Define las variables de entorno
ENV APP_ENV=dev \
    APP_DEBUG=1 \
    APACHE_DOCUMENT_ROOT=/var/www/html/public

# Ejecuta comandos para actualizar sistema, instalar dependencias necesarias,
# configurar apache, entre otros
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        libicu-dev \
        unzip \
    && docker-php-ext-install -j"$(nproc)" intl pdo_mysql \
    && a2enmod rewrite headers \
    && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && printf '%s\n' \
        '<Directory /var/www/html/public>' \
        '    AllowOverride All' \
        '    Require all granted' \
        '</Directory>' \
        > /etc/apache2/conf-available/symfony.conf \
    && a2enconf symfony \
    && rm -rf /var/lib/apt/lists/*

# Copia los archivos de composer desde la imagen oficial de composer a este contenedor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo, donde se van a copiar los archivos de mi app
WORKDIR /var/www/html

# Copia desde mi equipo anfitrión al directorio de trabajo del contenedor
COPY composer.json composer.lock symfony.lock .env ./
COPY assets/ assets/
COPY bin/ bin/
COPY config/ config/
COPY importmap.php ./importmap.php
COPY migrations/ migrations/
COPY public/ public/
COPY src/ src/
COPY templates/ templates/
COPY tests/ tests/
COPY translations/ translations/
COPY phpunit.dist.xml ./phpunit.dist.xml

# Ejecuta composer install para generar la carpeta vendor
RUN composer install \
        --no-interaction \
        --prefer-dist \
        --no-progress \
    && mkdir -p var/cache var/log var/share \
    && chown -R www-data:www-data var

# Puerto donde el contenedor escuchará conexiones
EXPOSE 80

# Inicia apache en el contenedor en primer plano
CMD ["apache2-foreground"]
