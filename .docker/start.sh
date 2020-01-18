cd ..

printf 'Up docker services...\n'
docker-compose up -d

printf 'Install composer...\n'
docker-compose exec 'php-fpm' sh -c 'composer install'

printf 'Clear cache...\n'
docker-compose exec 'php-fpm' sh -c 'php bin/console cache:clear'

printf 'Up database...\n'
docker-compose exec 'php-fpm' sh -c 'php bin/console doctrine:database:create --if-not-exists'
docker-compose exec 'php-fpm' sh -c 'php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration'

