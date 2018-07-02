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
#
# exit code:
#   1 git add-remote/pull/fetch operation failed
#   2 git push operation failed
#   3 failed on git subtree command

OPTS_SPEC="\
subsplit.sh <splits> --repository=<repository> --branch=<branch> --tag=<tag>

For example:
subsplit.sh packages/MonorepoBuilder:git@github.com:Symplify/MonorepoBuilder.git --branch=master --tag=v5.0
--
repository=   repository to split from, e.g. '--repository=.git' for current one
branch=       publish branch, e.g '--branch=master'
tag=          publish tag, e.g. '--tags=v5.0'
debug         show debug output
h,help        show the help
"

# show help if there are no params passed
if [ $# -eq 0 ]; then
    set -- -h
fi

eval "$(echo "$OPTS_SPEC" | git rev-parse --parseopt -- "$@" || echo exit $?)"

# We can run this from anywhere.
NONGIT_OK=1
DEBUG="  :DEBUG >"

PATH=$PATH:$(git --exec-path)

# git-sh-setup

if [ "$(hash git-subtree &>/dev/null && echo OK)" = "" ]
then
    die "Git subsplit needs git subtree; install git subtree or upgrade git to >=1.7.11"
fi

COMMAND=
SPLITS=
REPOSITORY=
BRANCH=
TAG=
DRY_RUN=
VERBOSE=

subsplit_main()
{
    while [ $# -gt 0 ]; do
        opt="$1"
        shift
        case "$opt" in
            --debug) VERBOSE=1 ;;
            --branch) BRANCH="$1"; shift ;;
            --tag) TAG="$1"; shift ;;
            --repository) REPOSITORY="$1"; shift ;;
            --) break ;;
            *) die "Unexpected option: $opt" ;;
        esac
    done

    if [ $# -lt 1 ]; then die "publish command requires splits to be passed as first argument"; fi
    SPLITS="$1"
    shift
    subsplit_publish
}

subsplit_init()
{
    # ref https://stackoverflow.com/a/5750463/1348344
    # print every command before its run
    if [ -n "$VERBOSE" ]
    then
        set -o xtrace
    fi

    # current directory is empty => clone the repository here
    if [ ! "$(ls -A .)" ]
    then
        echo "Initializing subsplit from '${REPOSITORY}' to temp directory"
        git clone -q "$REPOSITORY" . || (echo "Could not clone repository" && exit 1)
        sleep 2
    fi
}

subsplit_publish()
{
    subsplit_init

    for SPLIT in $SPLITS
    do
        SUBPATH=$(echo "$SPLIT" | cut -f1 -d:)
        REMOTE_URL=$(echo "$SPLIT" | cut -f2- -d:)
        REMOTE_NAME=$(echo "$SPLIT" | git hash-object --stdin)

        if ! git remote | grep "^${REMOTE_NAME}$" >/dev/null
        then
            git remote add "$REMOTE_NAME" "$REMOTE_URL" || (echo "Failed adding remote $REMOTE_NAME $REMOTE_URL" && exit 1)
        fi

        echo "Syncing ${SUBPATH} -> ${REMOTE_URL}"

        # split for branch
        if [ -n "$BRANCH" ]
        then
            if ! git show-ref --quiet --verify -- "refs/remotes/origin/${BRANCH}"
            then
                echo " - skipping head '${BRANCH}' (does not exist)"
                continue
            fi
            LOCAL_BRANCH="${REMOTE_NAME}-branch-${BRANCH}"

            echo " - syncing branch '${BRANCH}'"

            git checkout master >/dev/null 2>&1 || (echo "Failed while git checkout master" && exit 1)
            git branch -D "$LOCAL_BRANCH" >/dev/null 2>&1
            git branch -D "${LOCAL_BRANCH}-checkout" >/dev/null 2>&1
            git checkout -b "${LOCAL_BRANCH}-checkout" "origin/${BRANCH}" >/dev/null 2>&1 || (echo "Failed while git checkout" && exit 1)
            git subtree split -q --prefix="$SUBPATH" --branch="$LOCAL_BRANCH" "origin/${BRANCH}" >/dev/null || (echo "Failed while git subtree split for ${BRANCH}" && exit 1)
            RETURNCODE=$?

            if [ $RETURNCODE -eq 0 ]
            then
                git push -q --force $REMOTE_NAME ${LOCAL_BRANCH}:${BRANCH} || (echo "Failed pushing branchs to remote repo" && exit 1)
            fi
        fi

        # split for tag
        if [ -n "$TAG" ]
        then
            if ! git show-ref --quiet --verify -- "refs/tags/${TAG}"
            then
                echo " - skipping tag '${TAG}' (does not exist)"
                continue
            fi
            LOCAL_TAG="${REMOTE_NAME}-tag-${TAG}"

            if git branch | grep "${LOCAL_TAG}$" >/dev/null
            then
                echo " - skipping tag '${TAG}' (already synced)"
                continue
            fi

            echo " - syncing tag '${TAG}'"
            echo " - deleting '${LOCAL_TAG}'"
            git branch -D "$LOCAL_TAG" >/dev/null 2>&1

            echo " - subtree split for '${TAG}'"
            git subtree split -q --prefix="$SUBPATH" --branch="$LOCAL_TAG" "$TAG" >/dev/null || (echo "Failed while git subtree split for TAGS" && exit 1)
            RETURNCODE=$?

            echo " - subtree split for '${TAG}' [DONE]"
            if [ $RETURNCODE -eq 0 ]
            then
                git push -q --force ${REMOTE_NAME} ${LOCAL_TAG}:refs/tags/${TAG} || (echo "Failed pushing tags to remote repo" && exit 1)
            fi
        fi
    done
}

subsplit_main "$@"
