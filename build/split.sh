#!/usr/bin/env bash
git subsplit init git@github.com:symplify/symplify.git

git subsplit publish --heads="master" --no-tags packages/ActionAutowire:git@github.com:Symplify/ActionAutowire.git
git subsplit publish --heads="master" --no-tags packages/AutoServiceRegistration:git@github.com:Symplify/AutoServiceRegistration.git
git subsplit publish --heads="master" --no-tags packages/CodingStandard:git@github.com:Symplify/CodingStandard.git
git subsplit publish --heads="master" --no-tags packages/ControllerAutowire:git@github.com:Symplify/ControllerAutowire.git
git subsplit publish --heads="master" --no-tags packages/DefaultAutowire:git@github.com:Symplify/DefaultAutowire.git
git subsplit publish --heads="master" --no-tags packages/ModularDoctrineFilters:git@github.com:Symplify/ModularDoctrineFilters.git
git subsplit publish --heads="master" --no-tags packages/ModularRouting:git@github.com:Symplify/ModularRouting.git
git subsplit publish --heads="master" --no-tags packages/NetteAdapterForSymfonyBundles:git@github.com:Symplify/NetteAdapterForSymfonyBundles.git
git subsplit publish --heads="master" --no-tags packages/Statie:git@github.com:Symplify/Statie.git
git subsplit publish --heads="master" --no-tags packages/SymfonyEventDispatcher:git@github.com:Symplify/SymfonyEventDispatcher.git
git subsplit publish --heads="master" --no-tags packages/SymfonySecurity:git@github.com:Symplify/SymfonySecurity.git

rm -rf .subsplit/

# inspired by laravel: https://github.com/laravel/framework/blob/master/build/illuminate-split.sh
