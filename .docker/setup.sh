set -e # exit when any command fails

cd ..

printf 'Up docker services...\n'
docker-compose build && docker-compose up -d

printf 'Install composer...\n'
docker-compose exec 'app' sh -c 'composer install'

printf 'Clear cache...\n'
docker-compose exec 'app' sh -c 'php bin/console cache:clear'

printf 'Build webpack...\n'
docker-compose exec 'app' sh -c 'yarn && yarn build'

printf 'Up database...\n'
docker-compose exec 'app' sh -c 'php bin/console doctrine:database:create --if-not-exists'
docker-compose exec 'app' sh -c 'php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration'

