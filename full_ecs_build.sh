#!/usr/bin/env bash

composer update --no-progress --ansi

packages/monorepo-builder/bin/monorepo-builder localize-composer-paths packages/easy-coding-standard/composer.json --ansi

composer install --working-dir packages/easy-coding-standard --ansi --no-dev

cp -r packages/easy-coding-standard/. ecs-build

rm -rf ecs-build/tests ecs-build/packages-tests

# downgrade
vendor/bin/rector process ecs-build --config packages/easy-coding-standard/build/config/config-downgrade.php -a ecs-build/vendor/autoload.php --ansi

# prefix
sh packages/easy-coding-standard/build/build-ecs-scoped.sh ecs-build ecs-prefixed-downgraded
