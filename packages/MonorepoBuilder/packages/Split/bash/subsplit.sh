#!/usr/bin/env bash
#
# git-subsplit.sh: Automate and simplify the process of managing one-way
# read-only subtree splits.
#
# Copyright (C) 2012 Dragonfly Development Inc.
#
# author: https://github.com/simensen
# source: https://github.com/dflydev/git-subsplit/blob/master/git-subsplit.sh
#
# includes merge of PRs
# - https://github.com/dflydev/git-subsplit/pull/30/files

OPTS_SPEC="\
subsplit.sh --from-directory=<from-directory> --to-repository=<to-repository> --repository=<repository> --branch=<branch> --tag=<tag>

For example:
subsplit.sh --from-directory=packages/MonorepoBuilder --to-repository=git@github.com:Symplify/MonorepoBuilder.git --branch=master --tag=v5.0
--
from-directory=   directory with the package to split, e.g. '--from-directory=packages/MonorepoBuilder'
to-repository=    repository to split into, e.g. '--to-repository=git@github.com:Symplify/MonorepoBuilder.git'
repository=       repository to split from, e.g. '--repository=.git' for current one
branch=           branch to publish, e.g '--branch=master'
tag=              tag to publish, e.g. '--tag=v5.0'
h,help            show the help
"

# show help if there are no params passed
if [ $# -eq 0 ]; then
    set -- -h
fi

eval "$(echo "$OPTS_SPEC" | git rev-parse --parseopt -- "$@" || echo exit $?)"

# We can run this from anywhere.
NONGIT_OK=1

PATH=$PATH:$(git --exec-path)

# git-sh-setup

COMMAND=
FROM_DIRECTORY=
TO_REPOSITORY=
REPOSITORY=
BRANCH=
TAG=
DRY_RUN=
GITHUB_TOKEN=

function main()
{
    processCommandLine "$@"

    # report missing required options
    if [ -z "$FROM_DIRECTORY" ]
    then
        die "Command requires --from-repository option to be filled"
    fi

    if [ -z "$TO_REPOSITORY" ]
    then
        die "Command requires --to-repository option to be filled"
    fi

    init
    publish

    exit $?
}

function processCommandLine()
{
    while [ $# -gt 0 ]; do
        opt="$1"
        shift
        case "$opt" in
            --from-directory) FROM_DIRECTORY="$1"; shift ;;
            --to-repository) TO_REPOSITORY="$1"; shift ;;
            --repository) REPOSITORY="$1"; shift ;;
            --branch) BRANCH="$1"; shift ;;
            --tag) TAG="$1"; shift ;;
            --) break ;;
            *) die "Unexpected option: '$opt'" ;;
        esac
    done
}

function init()
{
    # ref https://stackoverflow.com/a/5750463/1348344
    # print every command before its run
    set -o xtrace

    echo "Initializing subsplit from '${REPOSITORY}' to '${PWD}' directory"

    # clone with all branches
    # see: https://stackoverflow.com/a/13575102/1348344
    git clone --mirror "$REPOSITORY" .git || die "Could not clone repository"
    git config --unset core.bare
    git reset --hard
}

function publish()
{
    git remote remove origin
    git remote add origin "$TO_REPOSITORY" || die "Failed adding remote origin $TO_REPOSITORY"

    echo "Syncing '${FROM_DIRECTORY}' to '${TO_REPOSITORY}'"

    split_branch
    split_tag
}

function split_branch()
{
    if [ -n "$BRANCH" ]
    then
        LOCAL_BRANCH="local-${BRANCH}"

        echo " - syncing branch '${BRANCH}'"

        git checkout -b "${LOCAL_BRANCH}-checkout" "${BRANCH}" >/dev/null 2>&1 || die "Failed while git checkout branch '${BRANCH}'"
        git subtree split -q --prefix="$FROM_DIRECTORY" --branch="$LOCAL_BRANCH" "${BRANCH}" >/dev/null || die "Failed while git subtree split for '${BRANCH}'"

        git push -q --force origin ${LOCAL_BRANCH}:${BRANCH} || die "Failed pushing branch to remote repo"

        echo " - subtree split for '${BRANCH}' [DONE]"
    fi
}

function split_tag()
{
    if [ -n "$TAG" ]
    then
        LOCAL_TAG="tag-${TAG}"

        echo " - syncing tag '${TAG}'"

        git checkout -b "${LOCAL_TAG}-checkout" "tags/${TAG}" >/dev/null 2>&1 || die "Failed while git checkout tag '${TAG}'"

        # make sure that directory exists in that tag
        if [ ! -d "$FROM_DIRECTORY" ]
        then
            echo " - directory '${FROM_DIRECTORY}' is not yet present in tag '${TAG}' [SKIP]"
            continue
        else
            git subtree split -q --prefix="$FROM_DIRECTORY" --branch="$LOCAL_TAG" "$TAG" >/dev/null || die "Failed while git subtree split for '${TAG}'"
            git push -q --force origin ${LOCAL_TAG}:refs/tags/${TAG} || die "Failed pushing tag to remote repo"

            echo " - subtree split for '${TAG}' [DONE]"
        fi
    fi
}

# report error and exit
function die()
{
   echo ${1}
   exit 1
}

main "$@"
