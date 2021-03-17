FROM php:8-cli

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apt update && apt upgrade -y \
    && apt install -y git openssl zip \
    && mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
    && install-php-extensions curl intl \
    && echo "memory_limit=-1" >> "$PHP_INI_DIR/conf.d/zz-dev.ini" \
    && rm -rf /tmp/* /var/www/*

WORKDIR /var/www
CMD ["bash"]
