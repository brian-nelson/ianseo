FROM php:7.3-apache
# Required dependencies
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        default-libmysqlclient-dev \
        libcurl4-openssl-dev \
        libmcrypt-dev \
        libzip-dev \
        libmagick++-dev \
        unzip \
        mariadb-client \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install curl \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install zip \
    && yes '' | pecl install mcrypt-1.0.2 \
    && yes '' | pecl install imagick \
#    && sh -c "echo -e '\n' | pecl install mcrypt-1.0.2" \
#    && sh -c "echo -e '\n' | pecl install imagick" \
    && docker-php-ext-enable mcrypt imagick \
    && apt-get clean all
# ianseo setup
COPY src/ /opt/ianseo
RUN chmod -R a+wX /opt/ianseo
# Apache settings
ENV APACHE_DOCUMENT_ROOT /opt/ianseo
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
  /etc/apache2/sites-available/*.conf && \
  sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
  /etc/apache2/apache2.conf /etc/apache2/conf-enabled/*.conf && \
  mv /opt/ianseo/Common/config.inc.php /opt/ianseo/Common/config.inc.bak
#  sed -i 's/localhost/ianseodb/' /opt/ianseo/Common/config.inc.php
COPY apache/ianseo.conf /etc/apache2/conf-enabled/
COPY apache/ianseo.ini /etc/apache2/conf-enabled/
COPY php/php.ini /usr/local/etc/php/
