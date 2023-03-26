FROM php:apache-buster

# rewrite_module
# a2enmod http2したいけど・・
RUN a2enmod rewrite

COPY ./src /var/www/app

COPY ./conf/virtual.conf /etc/apache2/sites-available/000-default.conf
