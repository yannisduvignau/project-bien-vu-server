# syntax=docker/dockerfile:1.5

# Use the official PHP image as the base image
FROM php:8.2-apache

ARG ROBOTS_TXT_DISALLOW=1

# Disable apt docker cleanup
RUN rm /etc/apt/apt.conf.d/docker-clean && \
    echo 'Binary::apt::APT::Keep-Downloaded-Packages "true";' > /etc/apt/apt.conf.d/keep-cache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies

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

# pecl install raphf propro
# docker-php-ext-enable raphf propro
# pecl install pecl_http
# echo "extension=http.so" > /usr/local/etc/php/conf.d/docker-php-ext-http.ini

sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
a2enmod rewrite

EOF

# Set higher upload size
RUN { \
    echo "upload_max_filesize=50M"; \
    echo "post_max_size=50M"; \
} > /usr/local/etc/php/conf.d/uploads.ini

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copy composer requirements
COPY composer.* .

# Run Composer without scripts
RUN --mount=type=cache,target=/root/.composer \
    composer install --optimize-autoloader --no-dev --no-scripts



# Copy existing application directory contents
COPY --chown=www-data:www-data . .

RUN <<EOF
set -ex

if [ "x${ROBOTS_TXT_DISALLOW}" = "x1" ]; then
    echo "User-agent: *\nDisallow: /" > public/robots.txt
fi


EOF

# Run Composer WITH scripts

RUN --mount=type=cache,target=/root/.composer \
    composer install --optimize-autoloader --no-dev

# Change current user to www
USER www-data

EXPOSE 80
ENTRYPOINT [ ".docker/entrypoint.sh" ]
CMD ["apache2-foreground"]
