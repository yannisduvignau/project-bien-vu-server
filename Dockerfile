# syntax=docker/dockerfile:1.5

# Utiliser l'image PHP officielle
FROM php:8.2-apache

ARG ROBOTS_TXT_DISALLOW=1

# Désactiver le nettoyage de apt
RUN rm /etc/apt/apt.conf.d/docker-clean && \
    echo 'Binary::apt::APT::Keep-Downloaded-Packages "true";' > /etc/apt/apt.conf.d/keep-cache

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer les dépendances système

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN --mount=type=cache,target=/var/cache/apt --mount=type=cache,target=/var/lib/apt <<EOF
set -ex

apt-get update
apt-get install -y  \
    git  \
    curl  \
    libpng-dev  \
    libjpeg-dev  \
    libfreetype6-dev  \
    libonig-dev  \
    libxml2-dev  \
    libpq-dev \
    zip  \
    libcurl4-openssl-dev \
    unzip

docker-php-ext-configure gd --with-freetype --with-jpeg
docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

chmod +x /usr/local/bin/install-php-extensions && sync
install-php-extensions http zip

sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
a2enmod rewrite

EOF

# Augmenter la taille d'upload
RUN { \
    echo "upload_max_filesize=50M"; \
    echo "post_max_size=50M"; \
} > /usr/local/etc/php/conf.d/uploads.ini

# Installer Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copier les fichiers composer.* depuis le répertoire server
COPY ./server/composer.* .

# Exécuter Composer sans scripts
RUN --mount=type=cache,target=/root/.composer \
    composer install --optimize-autoloader --no-dev --no-scripts

# Copier le contenu de l'application depuis le dossier server
COPY --chown=www-data:www-data ./server /var/www/html

RUN <<EOF
set -ex

if [ "x${ROBOTS_TXT_DISALLOW}" = "x1" ]; then
    echo "User-agent: *\nDisallow: /" > public/robots.txt
fi
EOF

# Exécuter Composer avec scripts
RUN --mount=type=cache,target=/root/.composer \
    composer install --optimize-autoloader --no-dev

COPY ./server/.docker/entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

# Changer l'utilisateur courant en www-data
USER www-data

EXPOSE 80

ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
