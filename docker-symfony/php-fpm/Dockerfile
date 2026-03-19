FROM alpine:3.19

RUN apk add --update --no-cache \
    coreutils \
    php82-fpm \
    php82-apcu \
    php82-ctype \
    php82-curl \
    php82-dom \
    php82-gd \
    php82-iconv \
    php82-mbstring \
    php82-opcache \
    php82-openssl \
    php82-pdo \
    php82-pdo_mysql \
    php82-mysqli \
    php82-xml \
    php82-zlib \
    php82-phar \
    php82-tokenizer \
    php82-session \
    php82-simplexml \
    php82-xdebug \
    php82-zip \
    php82-xmlwriter \
    php82-fileinfo \
    php82-intl \
    make \
    curl \
    && ln -sf /usr/bin/php82 /usr/bin/php

RUN echo "$(curl -sS https://composer.github.io/installer.sig) -" > composer-setup.php.sig \
        && curl -sS https://getcomposer.org/installer | tee composer-setup.php | sha384sum -c composer-setup.php.sig \
        && php composer-setup.php && rm composer-setup.php* \
        && chmod +x composer.phar && mv composer.phar /usr/bin/composer

COPY symfony.ini /etc/php82/conf.d/
COPY xdebug.ini  /etc/php82/conf.d/
COPY symfony.pool.conf /etc/php82/php-fpm.d/

CMD ["php-fpm82", "-F"]

WORKDIR /var/www/symfony
EXPOSE 9001
