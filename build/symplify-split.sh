#!/usr/bin/env bash
git subsplit init git@github.com:symplify/symplify.git

git subsplit publish --heads="master" --no-tags src/ActionAutowire:git@github.com:Symplify/ActionAutowire.git
git subsplit publish --heads="master" --no-tags src/AutoServiceRegistration:git@github.com:Symplify/AutoServiceRegistration.git
git subsplit publish --heads="master" --no-tags src/CodingStandard:git@github.com:Symplify/CodingStandard.git
git subsplit publish --heads="master" --no-tags src/ControllerAutowire:git@github.com:Symplify/ControllerAutowire.git
git subsplit publish --heads="master" --no-tags src/DefaultAutowire:git@github.com:Symplify/DefaultAutowire.git
git subsplit publish --heads="master" --no-tags src/ModularDoctrineFilters:git@github.com:Symplify/ModularDoctrineFilters.git
git subsplit publish --heads="master" --no-tags src/ModularRouting:git@github.com:Symplify/ModularRouting.git
git subsplit publish --heads="master" --no-tags src/NetteAdapterForSymfonyBundles:git@github.com:Symplify/NetteAdapterForSymfonyBundles.git
git subsplit publish --heads="master" --no-tags src/TwitterBrandBuilder:git@github.com:Symplify/TwitterBrandBuilder.git
git subsplit publish --heads="master" --no-tags src/Statie:git@github.com:Symplify/Statie.git

rm -rf .subsplit/

# inspired by laravel: https://github.com/laravel/framework/blob/master/build/illuminate-split.sh
