cd ..

printf 'Start unit tests...\n'
docker-compose exec 'php-fpm' sh -c 'bin/phpunit --group=unit'

printf 'Setup database...\n'
docker-compose exec 'php-fpm' sh -c 'php bin/console doctrine:database:drop --env=test --force --if-exists --quiet'
docker-compose exec 'php-fpm' sh -c 'php bin/console doctrine:database:create --env=test --quiet'
docker-compose exec 'php-fpm' sh -c 'php bin/console doctrine:schema:update --env=test --force --quiet'

printf 'Start integration tests...\n'
docker-compose exec 'php-fpm' sh -c 'bin/phpunit --group=integration'

printf 'Load fixtures...\n'
docker-compose exec 'php-fpm' sh -c 'php bin/console doctrine:fixtures:load --env=test --no-interaction --quiet'

printf 'Start functional tests...\n'
docker-compose exec 'php-fpm' sh -c 'bin/phpunit --group=functional'