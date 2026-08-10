# True Chain Infrastructure Company - container image for Railway / Render / Fly.
FROM php:8.3-apache

# The base image already ships mbstring, fileinfo, session and openssl, which is
# everything the site uses apart from the MySQL driver.
RUN docker-php-ext-install -j"$(nproc)" pdo_mysql

# The site's .htaccess files do the URL routing and lock down app/ and uploads/,
# so overrides have to be honoured.
RUN a2enmod rewrite headers expires \
    && printf '<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
       > /etc/apache2/conf-available/tcic.conf \
    && a2enconf tcic

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && printf 'expose_php = Off\nupload_max_filesize = 12M\npost_max_size = 12M\n' \
       > "$PHP_INI_DIR/conf.d/tcic.ini"

COPY --chown=www-data:www-data . /var/www/html/

# install.php is for shared hosting; a container configures itself from
# environment variables, so shipping the installer would only be a liability.
RUN rm -f /var/www/html/install.php /var/www/html/dev-router.php

RUN mkdir -p /var/www/html/uploads /var/www/html/app/storage \
    && chown -R www-data:www-data /var/www/html/uploads /var/www/html/app/storage

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
