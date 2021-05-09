#!/usr/bin/env bash

# inspired by https://raw.githubusercontent.com/rectorphp/rector/main/build/downgrade-rector.sh

# see https://stackoverflow.com/questions/66644233/how-to-propagate-colors-from-bash-script-to-github-action?noredirect=1#comment117811853_66644233
export TERM=xterm-color

# show errors
set -e

# script fails if trying to access to an undefined variable
set -u


# functions
note()
{
    MESSAGE=$1;
    printf "\n";
    echo "\033[0;33m[NOTE] $MESSAGE\033[0m";
}


# configure - 1st argument, use like
# sh build/downgrade-ecs.sh <directory-to-downgrade>
BUILD_DIRECTORY=$1

#---------------------------------------------

# 1. downgrade it
note "Running downgrade in '$BUILD_DIRECTORY' directory\n"

# 2. provide directories to downgrade, joined by spaces to run at once
directories="vendor/symfony vendor/psr;vendor/symplify config bin src packages;vendor/composer;vendor/doctrine vendor/friendsofphp vendor/nette;vendor/php-cs-fixer vendor/sebastian vendor/squizlabs"

# split array see https://stackoverflow.com/a/1407098/1348344
export IFS=";"

# 4. downgrade the directories
for directory in $directories; do
    note "Downgrading '$directory' directory\n"

    # --working-dir is needed, so "SKIP" parameter is applied in absolute path of nested directory
    php -d memory_limit=-1 vendor/bin/rector process $directory --config packages/easy-coding-standard/build/config/config-downgrade.php --working-dir $BUILD_DIRECTORY --ansi
done
