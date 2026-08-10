# True Chain Infrastructure Company - container image for Railway / Render / Fly.
FROM php:8.3-apache

# The base image already ships mbstring, fileinfo, session and openssl, which is
# everything the site uses apart from the MySQL driver.
RUN docker-php-ext-install -j"$(nproc)" pdo_mysql

# Exactly one MPM may be loaded. Debian enables mpm_event by default while
# mod_php requires the non-threaded mpm_prefork, and Apache refuses to start
# with "More than one MPM loaded" if both survive - so pin it explicitly rather
# than inheriting whatever the base image happens to leave enabled.
RUN a2dismod mpm_event mpm_worker >/dev/null 2>&1 || true; \
    a2enmod mpm_prefork

# The site's .htaccess files do the URL routing and lock down app/ and uploads/,
# so overrides have to be honoured.
RUN a2enmod rewrite headers expires \
    && printf '<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
       > /etc/apache2/conf-available/tcic.conf \
    && a2enconf tcic \
    && echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

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

# Fail the build on a broken Apache config rather than crash-looping on deploy.
# The MPM count is asserted explicitly: a second one loads fine at config-test
# time on some Apache builds and only blows up at startup.
RUN set -eux; \
    apache2ctl -t; \
    mpms="$(apache2ctl -M 2>/dev/null | grep -c 'mpm_.*_module' || true)"; \
    echo "MPMs loaded: ${mpms}"; \
    test "${mpms}" = "1"; \
    php -r 'exit(extension_loaded("pdo_mysql") ? 0 : 1);'

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
