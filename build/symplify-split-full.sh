#!/usr/bin/env bash
git subsplit init git@github.com:symplify/symplify.git

git subsplit publish --heads="master" src/ActionAutowire:git@github.com:Symplify/ActionAutowire.git
git subsplit publish --heads="master" src/AutoServiceRegistration:git@github.com:Symplify/AutoServiceRegistration.git
git subsplit publish --heads="master" src/CodingStandard:git@github.com:Symplify/CodingStandard.git
git subsplit publish --heads="master" src/ControllerAutowire:git@github.com:Symplify/ControllerAutowire.git
git subsplit publish --heads="master" src/DefaultAutowire:git@github.com:Symplify/DefaultAutowire.git
git subsplit publish --heads="master" src/ModularDoctrineFilters:git@github.com:Symplify/ModularDoctrineFilters.git
git subsplit publish --heads="master" src/ModularRouting:git@github.com:Symplify/ModularRouting.git
git subsplit publish --heads="master" src/NetteAdapterForSymfonyBundles:git@github.com:Symplify/ModularRouting.git
git subsplit publish --heads="master" src/TwitterBrandBuilder:git@github.com:Symplify/TwitterBrandBuilder.git

rm -rf .subsplit/

# inspired by laravel: https://github.com/laravel/framework/blob/master/build/illuminate-split-full.sh
