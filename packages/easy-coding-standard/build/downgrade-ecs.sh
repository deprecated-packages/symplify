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

note "Running downgrade in '$BUILD_DIRECTORY' directory\n"

php -d memory_limit=-1 vendor/bin/rector process $BUILD_DIRECTORY --config packages/easy-coding-standard/build/config/config-downgrade.php -a $BUILD_DIRECTORY/vendor/autoload.php --ansi

# give time to print all the files, before the next process takes over newly printed content
sleep 2
