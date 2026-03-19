FROM alpine:3.21

RUN apk add --update --no-cache \
    coreutils \
    php84-fpm \
    php84-apcu \
    php84-ctype \
    php84-curl \
    php84-dom \
    php84-gd \
    php84-iconv \
    php84-mbstring \
    php84-opcache \
    php84-openssl \
    php84-pdo \
    php84-pdo_mysql \
    php84-mysqli \
    php84-xml \
    php84-zlib \
    php84-phar \
    php84-tokenizer \
    php84-session \
    php84-simplexml \
    php84-xdebug \
    php84-zip \
    php84-xmlwriter \
    php84-fileinfo \
    php84-intl \
    make \
    curl \
    && ln -sf /usr/bin/php84 /usr/bin/php

RUN echo "$(curl -sS https://composer.github.io/installer.sig) -" > composer-setup.php.sig \
        && curl -sS https://getcomposer.org/installer | tee composer-setup.php | sha384sum -c composer-setup.php.sig \
        && php composer-setup.php && rm composer-setup.php* \
        && chmod +x composer.phar && mv composer.phar /usr/bin/composer

COPY symfony.ini /etc/php84/conf.d/
COPY xdebug.ini  /etc/php84/conf.d/
COPY symfony.pool.conf /etc/php84/php-fpm.d/

CMD ["php-fpm84", "-F"]

WORKDIR /var/www/symfony
EXPOSE 9001
