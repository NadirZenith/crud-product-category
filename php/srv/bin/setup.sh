#!/bin/sh


cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
composer install --no-interaction;

bin/console doctrine:database:drop -f --quiet;
bin/console doctrine:database:create --quiet;
bin/console doctrine:migrations:migrate --no-interaction --quiet;

bin/phpunit

exit 0;