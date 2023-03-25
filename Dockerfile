FROM php:apache-buster

# rewrite_module読み込み
RUN a2enmod rewrite

COPY ./src /var/www/app

COPY ./conf/virtual.conf /etc/apache2/sites-available/000-default.conf
