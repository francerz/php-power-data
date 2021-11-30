FROM php:5.6-cli

RUN apt-get update -y
RUN apt-get install -y git unzip
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

COPY . /usr/src/myapp
WORKDIR /usr/src/myapp
RUN composer remove --dev phpunit/phpunit
RUN composer require phpunit/phpunit
# RUN composer install --no-dev

CMD [ "sh", "-c", "./vendor/bin/phpunit" ]