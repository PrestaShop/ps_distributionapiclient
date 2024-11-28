#!/bin/bash
PS_VERSION=$1
PHP_VERSION=$2

set -e

# Docker images prestashop/prestashop may be used, even if the shop remains uninstalled
echo "Pull PrestaShop files (Tag ${PS_VERSION})"

docker rm -f temp-ps || true
docker volume rm -f ps-volume || true

docker run -tid --rm -v ps-volume:/var/www/html -e DISABLE_MAKE=1 --name temp-ps prestashop/prestashop:$PS_VERSION-$PHP_VERSION

# WAit for docker initialization (it may be longer for containers based on branches since they must install dependencies)
until docker exec temp-ps ls /var/www/html/vendor/autoload.php 2> /dev/null; do
  echo Waiting for docker initialization...
  sleep 5
done

# Clear previous instance of the module in the PrestaShop volume
echo "Clear previous module"

docker exec -t temp-ps rm -rf /var/www/html/modules/ps_distributionapiclient

# Run a container for PHPStan, having access to the module content and PrestaShop sources.
# This tool is outside the composer.json because of the compatibility with PHP 5.6
echo "Run PHPStan using phpstan-${PS_VERSION}.neon file"

docker run --rm --volumes-from temp-ps \
       -v $PWD:/var/www/html/modules/ps_distributionapiclient \
       -e _PS_ROOT_DIR_=/var/www/html \
       -e DISABLE_MAKE=1 \
       --workdir=/var/www/html/modules/ps_distributionapiclient ghcr.io/phpstan/phpstan:1.10.45-php${PHP_VERSION} \
       analyse \
       --configuration=/var/www/html/modules/ps_distributionapiclient/tests/phpstan/phpstan-${PS_VERSION}.neon
