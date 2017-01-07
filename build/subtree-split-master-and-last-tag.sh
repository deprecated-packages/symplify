#!/usr/bin/env bash
git subsplit init git@github.com:symplify/symplify.git

LAST_TAG=$(git tag -l | tail -n1);

git subsplit publish --heads="master" --tags=$LAST_TAG packages/ActionAutowire:git@github.com:Symplify/ActionAutowire.git
git subsplit publish --heads="master" --tags=$LAST_TAG packages/AutoServiceRegistration:git@github.com:Symplify/AutoServiceRegistration.git
git subsplit publish --heads="master" --tags=$LAST_TAG packages/CodingStandard:git@github.com:Symplify/CodingStandard.git
git subsplit publish --heads="master" --tags=$LAST_TAG packages/ControllerAutowire:git@github.com:Symplify/ControllerAutowire.git
git subsplit publish --heads="master" --tags=$LAST_TAG packages/DefaultAutowire:git@github.com:Symplify/DefaultAutowire.git
git subsplit publish --heads="master" --tags=$LAST_TAG packages/ModularDoctrineFilters:git@github.com:Symplify/ModularDoctrineFilters.git
git subsplit publish --heads="master" --tags=$LAST_TAG packages/ModularRouting:git@github.com:Symplify/ModularRouting.git
git subsplit publish --heads="master" --tags=$LAST_TAG packages/ServiceDefinitionDecorator:git@github.com:Symplify/ServiceDefinitionDecorator.git
git subsplit publish --heads="master" --tags=$LAST_TAG packages/Statie:git@github.com:Symplify/Statie.git
git subsplit publish --heads="master" --tags=$LAST_TAG packages/SymfonyEventDispatcher:git@github.com:Symplify/SymfonyEventDispatcher.git
git subsplit publish --heads="master" --tags=$LAST_TAG packages/SymfonySecurity:git@github.com:Symplify/SymfonySecurity.git

rm -rf .subsplit/

# inspired by laravel: https://github.com/laravel/framework/blob/master/build/illuminate-split-full.sh
