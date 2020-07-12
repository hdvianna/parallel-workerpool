#parallel extension needs Zend Thread Safety (zts)
FROM php:zts-buster

#Installing composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'e5325b19b381bfd88ce90a5ddb7823406b2a38cff6bb704b0acc289a09c8128d4a8ce2bbafcd1fcbdc38666422fe2806') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer \
    && rm -f composer-install.sh

#Installing libcurl
RUN apt-get update \
    && apt-get install libcurl4-openssl-dev -y  \
    && rm -rf /var/lib/apt/lists/*

#Installing curl
RUN docker-php-ext-install curl

#Installing parallel
RUN pecl install parallel \
    && docker-php-ext-enable parallel

#Creating work folder
RUN mkdir -p /var/local/parallel-workpool
WORKDIR /var/local/parallel-workpool
VOLUME /var/local/parallel-workpool