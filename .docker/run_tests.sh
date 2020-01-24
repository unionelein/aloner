# exit when any command fails
set -e

cd ..

# output colors
DEFAULT_COLOR='\e[39m'
BLACK_COLOR='\e[30m'

DEFAULT_BGC='\e[49m'
GREEN_BGC='\e[42m'

GREEN_OK="${GREEN_BGC}${BLACK_COLOR} OK ${DEFAULT_BGC}${DEFAULT_COLOR}"

echo 'Start unit tests...'
docker-compose exec 'app' sh -c 'bin/phpunit --group=unit'

printf '\nSetup database...'
docker-compose exec 'app' sh -c 'php bin/console app:empty_schema:create --env=test --quiet'
printf " ${GREEN_OK}\n\n"

printf 'Start integration tests...'
docker-compose exec 'app' sh -c 'bin/phpunit --group=integration'

printf '\nLoad fixtures...'
docker-compose exec 'app' sh -c 'php bin/console doctrine:fixtures:load --env=test --no-interaction --quiet'
printf " ${GREEN_OK}\n\n"

printf 'Start functional tests...\n'
docker-compose exec 'app' sh -c 'bin/phpunit --group=functional'
