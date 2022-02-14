#!/bin/sh

set -e

cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
composer install --no-interaction;

# else
#     # NO DEBUG - PRODUCTION
    # cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
    # composer install --no-dev --no-interaction --optimize-autoloader;
    # composer dump-autoload --no-dev --classmap-authoritative
# fi

# CONSOLE=/srv/bin/console

# if [ -f "$CONSOLE" ]; then
# #     # upate db from model data
    # bin/console doctrine:database:create --quiet;
#     # ./bin/console doctrine:migrations:migrate --no-interaction;
# fi

# if [ -d "var/cache/" ] && [ -d "var/log/" ]; then
#     chmod -R 777 var/cache/
#     chmod -R 777 var/log/
# fi
