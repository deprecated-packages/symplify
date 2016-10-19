#!/usr/bin/env bash
git subsplit init git@github.com:symplify/symplify.git
git subsplit publish --heads="master" --no-tags src/AutoServiceRegistration:git@github.com:Symplify/AutoServiceRegistration.git
git subsplit publish --heads="master" --no-tags src/ModularDoctrineFilters:git@github.com:Symplify/ModularDoctrineFilters.git
rm -rf .subsplit/

# inspired by laravel: https://github.com/laravel/framework/blob/master/build/illuminate-split.sh
