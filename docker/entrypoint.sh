#!/bin/sh
# Railway (and most container platforms) inject the port to listen on. Apache's
# default of 80 is only correct by coincidence, so bind to $PORT explicitly.
set -e

PORT="${PORT:-80}"

# Apache refuses to start when more than one MPM is loaded (AH00534), and
# mod_php requires the non-threaded mpm_prefork. Enforce that here as well as at
# build time: this runs against whatever image is actually deployed, including
# one built before the Dockerfile pinned it. Symlinks are managed directly
# because a2dismod exits non-zero in cases that are not failures, and its
# dependency handling can decline to remove an MPM.
MODS_AVAILABLE=/etc/apache2/mods-available
MODS_ENABLED=/etc/apache2/mods-enabled

echo "[entrypoint] volume mount path: ${RAILWAY_VOLUME_MOUNT_PATH:-none}"

echo "[entrypoint] MPMs enabled on entry:$(ls "$MODS_ENABLED" 2>/dev/null | grep '^mpm_' | sed 's/^/ /' | tr -d '\n')"

rm -f "$MODS_ENABLED"/mpm_event.* "$MODS_ENABLED"/mpm_worker.*
for ext in load conf; do
    if [ -f "$MODS_AVAILABLE/mpm_prefork.$ext" ]; then
        ln -sf "$MODS_AVAILABLE/mpm_prefork.$ext" "$MODS_ENABLED/mpm_prefork.$ext"
    fi
done

echo "[entrypoint] MPMs enabled after fix:$(ls "$MODS_ENABLED" 2>/dev/null | grep '^mpm_' | sed 's/^/ /' | tr -d '\n')"

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
