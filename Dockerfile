#parallel extension needs Zend Thread Safety (zts)
FROM php:zts-buster

#Installing composer
RUN apt-get update && apt-get install wget -y \
    && export CHECK_SUM="$(wget -q -O - https://composer.github.io/installer.sig)" \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === '${CHECK_SUM}') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
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