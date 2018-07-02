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

# show help if there are no params passed
if [ $# -eq 0 ]; then
    set -- -h
fi

OPTS_SPEC="\
subsplit.sh <splits> --branches=<branches> --tags=<tags>

For example:
subsplit.sh packages/MonorepoBuilder:git@github.com:Symplify/MonorepoBuilder.git --branches=master --tags=v5.0
--
h,help        show the help
debug         show debug output
dry-run       do everything except actually send the updates
work-dir=     directory that contains the subsplit working directory
branches=     publish for listed branches, e.g '--branches=master', '--branches=master dev',
tags=         publish for listed tags, e.g. '--tags=v5.0', '--tags=v5.0 v5.5'
"
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
REPO_URL=".git"
WORK_DIR="${PWD}/.subsplit"
BRANCHES=
TAGS=
DRY_RUN=
VERBOSE=

subsplit_main()
{
    while [ $# -gt 0 ]; do
        opt="$1"
        shift
        case "$opt" in
            --debug) VERBOSE=1 ;;
            --branches) BRANCHES="$1"; shift ;;
            --tags) TAGS="$1"; shift ;;
            --dry-run) DRY_RUN="--dry-run" ;;
            --work-dir) WORK_DIR="$1"; shift ;;
            --) break ;;
            *) die "Unexpected option: $opt" ;;
        esac
    done

    if [ $# -lt 1 ]; then die "publish command requires splits to be passed as first argument"; fi
    SPLITS="$1"
    shift
    subsplit_publish
}

say()
{
    echo "$@" >&2
}

fatal()
{
    RC=${1:-1}
    shift
    say "${@:-## Error occurs}"
    # popd >/dev/null
    exit $RC
}

subsplit_init()
{
    # ref https://stackoverflow.com/a/5750463/1348344
    # print every command before its run
    if [ -n "$VERBOSE" ]
    then
        set -o xtrace
    fi

    if [ -e "$WORK_DIR" ]
    then
        die "Working directory already found at ${WORK_DIR}; please remove or run update"
    fi

    say "Initializing subsplit from origin (${REPO_URL})"

    git clone -q "$REPO_URL" "$WORK_DIR" || die "Could not clone repository"
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
            git remote add "$REMOTE_NAME" "$REMOTE_URL" || fatal 1 "## Failed adding remote $REMOTE_NAME $REMOTE_URL"
        fi

        say "Syncing ${SUBPATH} -> ${REMOTE_URL}"

        # split for branches
        for BRANCH in $BRANCHES
        do
            if ! git show-ref --quiet --verify -- "refs/remotes/origin/${BRANCH}"
            then
                say " - skipping head '${BRANCH}' (does not exist)"
                continue
            fi
            LOCAL_BRANCH="${REMOTE_NAME}-branch-${BRANCH}"

            say " - syncing branch '${BRANCH}'"

            git checkout master >/dev/null 2>&1 || fatal 1 "## Failed while git checkout master"
            git branch -D "$LOCAL_BRANCH" >/dev/null 2>&1
            git branch -D "${LOCAL_BRANCH}-checkout" >/dev/null 2>&1
            git checkout -b "${LOCAL_BRANCH}-checkout" "origin/${BRANCH}" >/dev/null 2>&1 || fatal 1 "## Failed while git checkout"
            git subtree split -q --prefix="$SUBPATH" --branch="$LOCAL_BRANCH" "origin/${BRANCH}" >/dev/null || fatal 3 "## Failed while git subtree split for BRANCHS"
            RETURNCODE=$?

            if [ $RETURNCODE -eq 0 ]
            then
                PUSH_CMD="git push -q ${DRY_RUN} --force $REMOTE_NAME ${LOCAL_BRANCH}:${BRANCH}"

                if [ -n "$DRY_RUN" ]
                then
                    echo \# $PUSH_CMD
                    $PUSH_CMD
                else
                    $PUSH_CMD || fatal 2 "## Failed pushing branchs to remote repo"
                fi
            fi
        done

        # split for tags
        for TAG in $TAGS
        do
            if ! git show-ref --quiet --verify -- "refs/tags/${TAG}"
            then
                say " - skipping tag '${TAG}' (does not exist)"
                continue
            fi
            LOCAL_TAG="${REMOTE_NAME}-tag-${TAG}"

            if git branch | grep "${LOCAL_TAG}$" >/dev/null
            then
                say " - skipping tag '${TAG}' (already synced)"
                continue
            fi

            say " - syncing tag '${TAG}'"
            say " - deleting '${LOCAL_TAG}'"
            git branch -D "$LOCAL_TAG" >/dev/null 2>&1

            say " - subtree split for '${TAG}'"
            git subtree split -q --prefix="$SUBPATH" --branch="$LOCAL_TAG" "$TAG" >/dev/null || fatal 3 "## Failed while git subtree split for TAGS"
            RETURNCODE=$?

            say " - subtree split for '${TAG}' [DONE]"
            if [ $RETURNCODE -eq 0 ]
            then
                PUSH_CMD="git push -q ${DRY_RUN} --force ${REMOTE_NAME} ${LOCAL_TAG}:refs/tags/${TAG}"

                if [ -n "$DRY_RUN" ]
                then
                    echo \# $PUSH_CMD
                    $PUSH_CMD
                else
                    $PUSH_CMD || fatal 2 "## Failed pushing tags to remote repo"
                fi
            fi
        done
    done

    popd >/dev/null
}

subsplit_main "$@"