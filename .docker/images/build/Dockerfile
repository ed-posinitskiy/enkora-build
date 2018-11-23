FROM ubuntu:16.04

ARG php_version=7.2
ARG composer_home=/composer
ARG build_dir=/build

RUN apt-get update && apt-get install -y --no-install-recommends \
curl \
wget \
build-essential \
software-properties-common \
locales \
git \
zip \
openssh-client

RUN locale-gen en_US.UTF-8
ENV LANG       en_US.UTF-8
ENV LC_ALL     en_US.UTF-8

RUN add-apt-repository -y ppa:ondrej/php

RUN apt-get update && apt-get install -y --no-install-recommends \
php${php_version} \
php${php_version}-fpm \
php${php_version}-xml \
php${php_version}-cli \
php${php_version}-intl \
php${php_version}-mbstring \
php${php_version}-curl \
php${php_version}-common \
php${php_version}-gd \
php${php_version}-zip \
php${php_version}-bcmath \
php${php_version}-bz2 \
php-pear \
php-xdebug

COPY ./config/cli/php.ini /etc/php/${php_version}/cli/php.ini

RUN ln -s /etc/php/${php_version}/ /usr/local/etc/php

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir ${composer_home}
RUN chmod 775 ${composer_home}

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME ${composer_home}

ENV PATH ${composer_home}/vendor/bin:$PATH

RUN /usr/local/bin/composer global require phing/phing

RUN curl --silent --location https://deb.nodesource.com/setup_10.x | bash -
RUN apt-get install --yes nodejs