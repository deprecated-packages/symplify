#!/usr/bin/env bash
git subsplit init git@github.com:symplify/symplify.git

git subsplit publish --heads="master" packages/ActionAutowire:git@github.com:Symplify/ActionAutowire.git
git subsplit publish --heads="master" packages/AutoServiceRegistration:git@github.com:Symplify/AutoServiceRegistration.git
git subsplit publish --heads="master" packages/CodingStandard:git@github.com:Symplify/CodingStandard.git
git subsplit publish --heads="master" packages/ControllerAutowire:git@github.com:Symplify/ControllerAutowire.git
git subsplit publish --heads="master" packages/DefaultAutowire:git@github.com:Symplify/DefaultAutowire.git
git subsplit publish --heads="master" packages/ModularDoctrineFilters:git@github.com:Symplify/ModularDoctrineFilters.git
git subsplit publish --heads="master" packages/ModularRouting:git@github.com:Symplify/ModularRouting.git
git subsplit publish --heads="master" packages/NetteAdapterForSymfonyBundles:git@github.com:Symplify/NetteAdapterForSymfonyBundles.git
git subsplit publish --heads="master" packages/TwitterBrandBuilder:git@github.com:Symplify/TwitterBrandBuilder.git
git subsplit publish --heads="master" packages/PHP7_Sculpin:git@github.com:Symplify/PHP7_Sculpin.git

rm -rf .subsplit/

# inspired by laravel: https://github.com/laravel/framework/blob/master/build/illuminate-split-full.sh
