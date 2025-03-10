FROM php:8.3-apache

RUN apt update && apt upgrade -y
RUN apt install -y libzip-dev libapache2-mod-security2 zlib1g-dev libpng-dev unzip nano openssl libssl-dev
RUN docker-php-ext-install gd opcache pdo_mysql
RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2.7.9 /usr/bin/composer /usr/bin/composer

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY php_apache.ini /etc/php/8.3/apache2/php.ini
COPY php_cli.ini /etc/php/8.3/cli/php.ini
COPY 00-jwt.ini /usr/local/etc/php/conf.d/00-jwt.ini
COPY php-jwt /var/lib/php-jwt

RUN echo "ServerSignature Off" >> /etc/apache2/apache2.conf
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf

RUN chown -R www-data:www-data /var/www
RUN a2enmod rewrite
RUN a2enmod headers

WORKDIR /var/lib/php-jwt
RUN phpize && ./configure --with-openssl=/usr/bin/openssl
RUN make
RUN make install

RUN adduser --disabled-password webuser
RUN chown webuser:webuser -R /var/www/html
WORKDIR /var/www/html
USER webuser

EXPOSE 80
CMD apachectl -D FOREGROUND
