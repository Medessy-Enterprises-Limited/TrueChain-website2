#!/bin/sh
# Railway (and most container platforms) inject the port to listen on. Apache's
# default of 80 is only correct by coincidence, so bind to $PORT explicitly.
set -e

PORT="${PORT:-80}"

echo "Listen ${PORT}" > /etc/apache2/ports.conf
sed -ri "s!<VirtualHost \*:[0-9]+>!<VirtualHost *:${PORT}>!g" /etc/apache2/sites-available/000-default.conf

# The platform terminates TLS at its edge and forwards over plain HTTP. Without
# this, PHP sees an insecure request and drops the "secure" session cookie.
echo "SetEnvIf X-Forwarded-Proto https HTTPS=on" > /etc/apache2/conf-available/tcic-proxy.conf
a2enconf tcic-proxy >/dev/null 2>&1 || true

# A mounted volume arrives owned by root; the web server has to be able to write.
for dir in /var/www/html/uploads /var/www/html/app/storage; do
    mkdir -p "$dir"
    chown -R www-data:www-data "$dir" 2>/dev/null || true
done

exec "$@"
