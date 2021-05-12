#!/usr/bin/env bash

composer update --no-progress --ansi

packages/monorepo-builder/bin/monorepo-builder localize-composer-paths packages/easy-coding-standard/composer.json --ansi

composer install --working-dir packages/easy-coding-standard --ansi --no-dev

cp -r packages/easy-coding-standard/. ecs-build

rm -rf ecs-build/tests ecs-build/packages-tests

# downgrade
sh packages/easy-coding-standard/build/downgrade-ecs.sh ecs-build

# prefix
sh packages/easy-coding-standard/build/build-ecs-scoped.sh ecs-build ecs-prefixed-downgraded
